<?php
/**
 * Configuration de la base de données
 */

return [
    'host' => 'localhost',
    'database' => 'crm_dev',
    'username' => 'crm_dev',
    'password' => 'C0r7ex34170', // À configurer selon votre environnement
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];
?>