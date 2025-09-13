<?php
/**
 * Configuration générale de l'application
 */

return [
    'app_name' => 'CRM Olympe Mariage',
    'app_version' => '2.0',
    'base_url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
    'app_root' => dirname(dirname(__FILE__)),
    
    // Environnement (dev/prod)
    'environment' => 'dev',
    
    // Sécurité
    'session_name' => 'OLYMPE_CRM_SESSION',
    'csrf_token_name' => 'csrf_token',
    
    // Chemins
    'paths' => [
        'logs' => '/logs',
        'assets' => '/assets',
        'uploads' => '/uploads',
        'photos'  => '/photos'
    ],
    
    // Configuration des erreurs selon l'environnement
    'error_reporting' => [
        'dev' => E_ALL,
        'prod' => E_ALL & ~E_NOTICE & ~E_DEPRECATED
    ],
    
    'display_errors' => [
        'dev' => true,
        'prod' => false
    ]
];
?>