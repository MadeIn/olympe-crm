<?php
/**
 * Fichier de sécurisation pour les tâches CRON
 * À inclure au début de chaque script CRON
 */

// Vérifier que le script est exécuté en ligne de commande OU avec un token secret
function validateCronAccess(): bool {
    // Méthode 1: Exécution en ligne de commande (recommandée)
    if (php_sapi_name() === 'cli') {
        return true;
    }
    
    // Méthode 2: Token secret dans l'URL ou header (pour web cron)
    $valid_token = 'olympe_cron_2025_secret_token_change_me'; // À changer !
    
    $provided_token = $_GET['token'] ?? $_SERVER['HTTP_X_CRON_TOKEN'] ?? '';
    
    if (hash_equals($valid_token, $provided_token)) {
        return true;
    }
    
    // Méthode 3: Adresse IP autorisée (localhost uniquement)
    $allowed_ips = ['127.0.0.1', '::1'];
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    if (in_array($client_ip, $allowed_ips)) {
        return true;
    }
    
    return false;
}

// Vérifier l'accès
if (!validateCronAccess()) {
    http_response_code(403);
    die('Accès interdit - Tâche CRON protégée');
}

// Configuration pour les tâches CRON
ini_set('max_execution_time', 300); // 5 minutes max
ini_set('memory_limit', '256M');

// Désactiver l'affichage des erreurs pour les tâches en production
if ($_SERVER['SERVER_NAME'] !== 'localhost') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Inclure les classes et fonctions nécessaires
require_once dirname(dirname(__FILE__)) . '/classes/Database.php';
require_once dirname(dirname(__FILE__)) . '/includes/functions.php';
require_once dirname(dirname(__FILE__)) . '/includes/date_functions.php';
require_once dirname(dirname(__FILE__)) . '/includes/email_functions.php';

/**
 * Log spécifique pour les tâches CRON
 */
function log_cron(string $task_name, string $message, string $level = 'info'): void {
    $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
    $log_file = $app_config['app_root'] . '/logs/cron.log';
    
    $log_entry = sprintf(
        "[%s] [%s] Task: %s - %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $task_name,
        $message
    );
    
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Démarre une tâche CRON avec logging
 */
function start_cron_task(string $task_name): void {
    log_cron($task_name, 'Début de la tâche');
}

/**
 * Termine une tâche CRON avec logging
 */
function end_cron_task(string $task_name, array $stats = []): void {
    $stats_text = empty($stats) ? '' : ' - Stats: ' . json_encode($stats);
    log_cron($task_name, 'Fin de la tâche' . $stats_text);
}

// Fonction utilitaire pour les tâches sans authentification utilisateur
function init_cron_database(): Database {
    return Database::getInstance();
}
?>