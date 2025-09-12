<?php declare(strict_types=1);
/**
 * param_invite.php — Bootstrap léger pour pages publiques (invités)
 * - N'exige PAS d'authentification
 * - Suppose que le routeur a déjà bootstrappé (APP_BOOTSTRAPPED)
 * - Fallback minimal si le fichier est inclus sans passer par le routeur
 */

// Si le routeur a déjà tout bootstrappé, on récupère juste la config
if (defined('APP_BOOTSTRAPPED')) {
    // Dispo via index.php ; si besoin, charge la config locale
    if (!isset($app_config)) {
        $app_config = require __DIR__ . '/../config/app.php';
    }
} else {
    // Fallback : appel direct (rare), on charge le minimum vital
    $ROOT = dirname(__DIR__);

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

    // Helpers/fonctions courantes
    require_once $ROOT . '/includes/functions.php';
    require_once $ROOT . '/includes/text_functions.php';
    require_once $ROOT . '/includes/date_functions.php';
    require_once $ROOT . '/includes/crypto_functions.php';
    require_once $ROOT . '/includes/image_functions.php';
    require_once $ROOT . '/includes/email_functions.php';
    require_once $ROOT . '/includes/product_functions.php';

    // Optionnel: session si tu utilises des flash/CSRF même pour invités
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Compat GET/POST façon register_globals si dispo
    if (function_exists('secure_register_globals')) {
        secure_register_globals();
    }
}

// Constantes utiles
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', APP_ROOT . '/templates/');
}
if (!defined('BASE_PATH')) {
    // Si le routeur ne l'a pas défini, on calcule grossièrement
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('BASE_PATH', ($scriptDir === '' || $scriptDir === '.') ? '/' : $scriptDir . '/');
}

// Helpers d’échappement si besoin
if (!function_exists('h')) {
    function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

// (Optionnel) entêtes no-cache si tu veux éviter la mise en cache des pages invitées
// header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
// header('Pragma: no-cache');
