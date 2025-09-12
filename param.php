<?php declare(strict_types=1);
/**
 * param.php — garde d’accès (ACL) pour pages protégées
 * À inclure tout en haut des pages nécessitant une authentification.
 *
 * Le routeur index.php fait déjà le bootstrap global (autoload, config, helpers, secure_register_globals).
 * Ici, on ne fait que garantir la session + l'auth, et exposer $u, $base, $rep_serveur, $app_config.
 */

// Si app non bootstrappée (appel direct à la page), on charge le minimum vital.
if (!defined('APP_BOOTSTRAPPED')) {
    $ROOT = __DIR__;

    if (is_file($ROOT . '/vendor/autoload.php')) {
        require $ROOT . '/vendor/autoload.php';
    }

    // Config & erreurs
    $app_config = require $ROOT . '/config/app.php';
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

    // Classes & helpers minimaux
    require_once $ROOT . '/classes/Database.php';
    require_once $ROOT . '/classes/User.php';
    require_once $ROOT . '/classes/Auth.php';
    require_once $ROOT . '/includes/functions.php';

    // (Optionnel) les autres helpers si besoin en accès direct:
    require_once $ROOT . '/includes/text_functions.php';
    require_once $ROOT . '/includes/date_functions.php';
    require_once $ROOT . '/includes/crypto_functions.php';
    require_once $ROOT . '/includes/image_functions.php';
    require_once $ROOT . '/includes/email_functions.php';
    require_once $ROOT . '/includes/product_functions.php';
    if (!defined('TEMPLATE_PATH')) {
        define('TEMPLATE_PATH', __DIR__ . '/templates/');
    }
    // Sessions
    Auth::initSession();

    // Compat GET/POST
    if (function_exists('secure_register_globals')) {
        secure_register_globals();
    }
} else {
    // Si bootstrap fait par le routeur, on récupère la config globale si pas déjà disponible.
    if (!isset($app_config)) {
        $app_config = require __DIR__ . '/config/app.php';
    }
}

// Exige l’authentification
Auth::requireAuth();

// Utilisateur courant
$u = Auth::getCurrentUser();
if (!$u) {
    // Si pas d'utilisateur en session, on renvoie vers login
    Auth::redirect('/login'); // ou '/index.php' si tu préfères
    exit;
}

// Accès DB si nécessaire dans la page
$base = Database::getInstance();

// Compat héritage
$rep_serveur = $app_config['app_root'] ?? __DIR__;

// (Optionnel) log des accès en dev
if (($app_config['environment'] ?? 'prod') === 'dev' && function_exists('log_page_access')) {
    log_page_access();
}

// (Optionnel) legacy includes si tu en gardes encore
// foreach ($legacy_includes as $file) {
//     if (file_exists(__DIR__ . '/' . $file)) {
//         require_once __DIR__ . '/' . $file;
//     }
// }
