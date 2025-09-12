<?php
/**
 * Fichier d'inclusion pour toutes les pages protégées
 * À inclure en haut de chaque page nécessitant une authentification
 */

// Configuration et autoloading
require_once dirname(__FILE__) . '/classes/Database.php';
require_once dirname(__FILE__) . '/classes/User.php';
require_once dirname(__FILE__) . '/classes/Auth.php';
require_once dirname(__FILE__) . '/includes/functions.php';

// Inclusions des fonctions thématiques modernisées
require_once dirname(__FILE__) . '/includes/text_functions.php';
require_once dirname(__FILE__) . '/includes/date_functions.php';
require_once dirname(__FILE__) . '/includes/crypto_functions.php';
require_once dirname(__FILE__) . '/includes/image_functions.php';
require_once dirname(__FILE__) . '/includes/email_functions.php';
require_once dirname(__FILE__) . '/includes/product_functions.php';
require_once dirname(__FILE__) . '/vendor/autoload.php';

// Chemin des templates
define('TEMPLATE_PATH', dirname(__FILE__) . '/templates/');

// Configuration de l'application
$app_config = require dirname(__FILE__) . '/config/app.php';

// Configuration des erreurs selon l'environnement
if ($app_config['environment'] === 'dev') {
    ini_set('display_errors', $app_config['display_errors']['dev']);
    error_reporting($app_config['error_reporting']['dev']);
} else {
    ini_set('display_errors', $app_config['display_errors']['prod']);
    ini_set('log_errors', 1);
    ini_set('error_log', $app_config['app_root'] . '/logs/error.log');
    error_reporting($app_config['error_reporting']['prod']);
}

// Configuration UTF-8
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Initialisation des sessions sécurisées
Auth::initSession();

// Vérification de l'authentification
Auth::requireAuth();

// Récupération de l'utilisateur connecté
$u = Auth::getCurrentUser();

if (!$u) {
    // Si pas d'utilisateur en session, rediriger vers la connexion
    Auth::redirect('/index.php');
}

// Instance de base de données (compatible avec l'ancien code)
$base = Database::getInstance();

// Variables de compatibilité pour l'ancien code
$rep_serveur = $app_config['app_root']; // Remplace l'ancien chemin


foreach ($legacy_includes as $file) {
    if (file_exists(dirname(__FILE__) . '/' . $file)) {
        include_once dirname(__FILE__) . '/' . $file;
    }
}

// Log de l'accès à la page (optionnel, à activer si souhaité)
if ($app_config['environment'] === 'dev') {
    log_page_access();
}

// Simulation sécurisée de register_globals (compatible avec l'ancien code)
secure_register_globals();
?>