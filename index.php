<?php declare(strict_types=1);

/**
 * Front-controller (routeur) — PHP 8
 * - Résout les URLs propres vers /pages/<route>.php
 * - Par défaut : /            → /pages/home
 * - Public   : /login         → /pages/login/index.php
 * - Logout   : /logout        (legacy logout.php si présent, sinon géré ici)
 * - Compatible sous-dossier via $BASE_PATH auto
 * - Bootstrap global : config, autoload, helpers, secure_register_globals()
 */

/////////////////////////////////
// Bootstrap global
/////////////////////////////////
$ROOT       = __DIR__;
$PAGES_DIR  = $ROOT . '/pages';
$VENDOR     = $ROOT . '/vendor';

if (is_file($VENDOR . '/autoload.php')) {
    require $VENDOR . '/autoload.php';
}

$app_config = require $ROOT . '/config/app.php';
$chemins = (array)$app_config['paths'] ?? [];

require_once $ROOT . '/includes/functions.php';

// Gestion des erreurs selon l'env
$env = (string)($app_config['environment'] ?? 'prod');
if ($env === 'dev') {
    ini_set('display_errors', '1');
    error_reporting((int)($app_config['error_reporting']['dev'] ?? E_ALL));
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    if (!empty($app_config['app_root'])) {
        ini_set('error_log', rtrim($app_config['app_root'], '/').'/logs/error.log');
    }
    error_reporting((int)($app_config['error_reporting']['prod'] ?? 0));
}

// Classes cœur (si pas autoloadées)
require_once $ROOT . '/classes/Database.php';
require_once $ROOT . '/classes/User.php';
require_once $ROOT . '/classes/Auth.php';

// Helpers & fonctions (tes fichiers existants)
require_once $ROOT . '/includes/helpers.php';
require_once $ROOT . '/includes/text_functions.php';
require_once $ROOT . '/includes/date_functions.php';
require_once $ROOT . '/includes/crypto_functions.php';
require_once $ROOT . '/includes/image_functions.php';
require_once $ROOT . '/includes/email_functions.php';
require_once $ROOT . '/includes/product_functions.php';

// Sessions (utile pour login/logout)
Auth::initSession();

// Drapeau : application correctement bootstrappée
define('APP_BOOTSTRAPPED', true);

// Chemin des templates
define('TEMPLATE_PATH', $ROOT . '/templates/');
/////////////////////////////////
// Helpers locaux routeur
/////////////////////////////////
function go(string $to, int $code = 302): never { header("Location: $to", true, $code); exit; }
function error_page(int $code, string $fallback = ''): never {
    http_response_code($code);
    $file = __DIR__ . "/{$code}.php";
    if (is_file($file)) { require $file; }
    else { echo $fallback !== '' ? $fallback : "Error {$code}"; }
    exit;
}

/**
 * Assure la compat GET/POST façon "register_globals" sécurisée.
 * Utilise secure_value/secure_array provenant de includes/functions.php
 */
if (!function_exists('secure_register_globals')) {
    function secure_register_globals(bool $dontOverrideExisting = true): void {
        $data = array_merge($_GET ?? [], $_POST ?? []);
        static $reserved = [
            'GLOBALS','_GET','_POST','_COOKIE','_FILES','_SERVER','_ENV','_REQUEST','_SESSION',
            'argv','argc','this','php_errormsg'
        ];
        foreach ($data as $key => $value) {
            if (!is_string($key) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) continue;
            if (in_array($key, $reserved, true)) continue;
            if ($dontOverrideExisting && array_key_exists($key, $GLOBALS)) continue;

            if (is_array($value)) {
                $GLOBALS[$key] = function_exists('secure_array') ? secure_array($value) : $value;
            } else {
                $GLOBALS[$key] = function_exists('secure_value') ? secure_value($value) : $value;
            }
        }
    }
}

/////////////////////////////////
// Base path (si app servie sous /crm/ par ex.)
/////////////////////////////////
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$BASE_PATH = ($scriptDir === '' || $scriptDir === '.') ? '/' : $scriptDir . '/';

/////////////////////////////////
// Normalisation de la route
/////////////////////////////////
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($BASE_PATH !== '/' && str_starts_with($uriPath, $BASE_PATH)) {
    $uriPath = substr($uriPath, strlen($BASE_PATH));
}
$path = trim($uriPath, '/');

// Compat index legacy
if ($path === '' || $path === 'index' || $path === 'index.php') {
    $path = 'home/index';
}

// Sécurité & nettoyage
if (str_contains($path, '..')) { error_page(400, 'Bad request'); }
$path = preg_replace('~[^a-zA-Z0-9/_-]~', '', $path);
if ($path !== '' && str_ends_with($path, '/')) { $path .= 'index'; }

/////////////////////////////////
// Routes spéciales
/////////////////////////////////

// Logout centralisé (prend en charge legacy logout.php si présent)
if ($path === 'logout') {
    $legacyLogout = $ROOT . '/logout.php';
    if (is_file($legacyLogout)) {
        require $legacyLogout;
        exit;
    }
    // Sinon : logout propre
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    go($BASE_PATH . 'login');
}

// Routes publiques (pas d’auth requise)
$publicRoutes = [
    'login',
    'login/index',
];

// Si route publique et déjà connecté → redirige vers l’accueil adapté
if (in_array($path, $publicRoutes, true) && Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    if ($user) {
        $dest = $user->isCouturiere() ? 'show/index' : 'home/index';
        go($BASE_PATH . $dest);
    }
}

/////////////////////////////////
// Résolution de la page dans /pages
/////////////////////////////////
/*
 * Règles:
 *   /home            -> /pages/home
 *   /home/index      -> /pages/home
 *   /client/edit     -> /pages/client/edit.php
 *   /login           -> /pages/login/index.php
 */
$target = $PAGES_DIR . '/' . $path;

if (is_dir($target)) {
    $target = rtrim($target, '/\\') . '/index.php';
} elseif (!str_ends_with($target, '.php')) {
    $try = $target . '.php';
    if (is_file($try)) {
        $target = $try;
    } else {
        $idx = rtrim($target, '/\\') . '/index.php';
        if (is_file($idx)) {
            $target = $idx;
        }
    }
}

// Confinement à /pages
$realPagesDir = realpath($PAGES_DIR);
$real         = realpath($target);
if ($real === false || $realPagesDir === false || !str_starts_with($real, $realPagesDir . DIRECTORY_SEPARATOR)) {
    error_page(404);
}

/////////////////////////////////
// Compat paramètres GET/POST globaux
/////////////////////////////////
secure_register_globals(); // une seule fois pour toute la requête

/////////////////////////////////
// Inclusion finale
/////////////////////////////////
require $real;
