<?php declare(strict_types=1);
date_default_timezone_set('Europe/Paris');
/**
 * param_invite.php — Bootstrap pour pages publiques (invités)
 * - N'exige PAS d'authentification
 * - Charge TOUTES les fonctions helpers + classes nécessaires
 * - Fonctionne avec ou sans passage par le front-controller
 */

// ────────────────────────────────────────────────────────────
// 1) Détermination racine & autoload
// ────────────────────────────────────────────────────────────
$ROOT = __DIR__;// racine projet

if (!defined('APP_ROOT')) {
    define('APP_ROOT', $ROOT);
}

if (is_file(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

// ────────────────────────────────────────────────────────────
// 2) Config & erreurs
// ────────────────────────────────────────────────────────────
if (!isset($app_config) || !is_array($app_config)) {
    $app_config = require APP_ROOT . '/config/app.php';
}

require_once APP_ROOT . '/includes/functions.php';

$__env = (string)($app_config['environment'] ?? 'prod');
if ($__env === 'dev') {
    ini_set('display_errors', '1');
    error_reporting((int)($app_config['error_reporting']['dev'] ?? E_ALL));
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    $logPath = rtrim((string)($app_config['app_root'] ?? APP_ROOT), '/') . '/logs/error.log';
    ini_set('error_log', $logPath);
    error_reporting((int)($app_config['error_reporting']['prod'] ?? 0));
}

// UTF-8
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// ────────────────────────────────────────────────────────────
// 3) Helpers / includes thématiques (TOUS chargés)
// ────────────────────────────────────────────────────────────
require_once APP_ROOT . '/includes/helpers.php';           // ton fichier helpers central si présent
require_once APP_ROOT . '/includes/text_functions.php';
require_once APP_ROOT . '/includes/math_functions.php';
require_once APP_ROOT . '/includes/date_functions.php';
require_once APP_ROOT . '/includes/crypto_functions.php';
require_once APP_ROOT . '/includes/image_functions.php';
require_once APP_ROOT . '/includes/email_functions.php';
require_once APP_ROOT . '/includes/product_functions.php';

// ────────────────────────────────────────────────────────────
// 4) Classes nécessaires
// ────────────────────────────────────────────────────────────
require_once APP_ROOT . '/classes/Database.php';
require_once APP_ROOT . '/classes/User.php';   // utile si certaines helpers s’en servent
require_once APP_ROOT . '/classes/Auth.php';   // utile pour CSRF ou autres utilitaires

// ────────────────────────────────────────────────────────────
// 5) Sessions (facultatives côté invité, mais utile pour CSRF/flash)
// ────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    // session simple (pas d’options strictes d’Auth ici)
    session_start();
}

// ────────────────────────────────────────────────────────────
/** 6) Compat GET/POST façon register_globals si disponible */
// ────────────────────────────────────────────────────────────
if (function_exists('secure_register_globals')) {
    secure_register_globals();
}

// ────────────────────────────────────────────────────────────
// 7) Constantes utilitaires
// ────────────────────────────────────────────────────────────
if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', APP_ROOT . '/templates/');
}

if (!defined('BASE_PATH')) {
    // calcule un base path si non fourni par le routeur
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('BASE_PATH', ($scriptDir === '' || $scriptDir === '.') ? '/' : $scriptDir . '/');
}

// Helper d’échappement (si non défini par ailleurs)
if (!function_exists('h')) {
    function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

// ────────────────────────────────────────────────────────────
// 8) Instance DB dispo partout comme dans param.php
// ────────────────────────────────────────────────────────────
if (!isset($base) || !is_object($base)) {
    $base = Database::getInstance();
}

// ────────────────────────────────────────────────────────────
// 9) (Optionnel) méta CSRF global pour tes formulaires publics
//    À mettre dans ton <head>: <meta name="csrf-token" content="<?= h(Auth::generateCSRFToken()) ? >">
// ────────────────────────────────────────────────────────────

// ────────────────────────────────────────────────────────────
// 10) (Optionnel) bloquer l’accès aux utilisateurs connectés
//     Décommente si ta page publique ne doit PAS être accessible
//     une fois connecté (redirige vers l’accueil connectés).
// ────────────────────────────────────────────────────────────
// if (class_exists('Auth') && Auth::isLoggedIn()) {
//     header('Location: ' . BASE_PATH . 'home', true, 302);
//     exit;
// }
