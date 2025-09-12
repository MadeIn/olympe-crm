<?php
/**
 * Page de blocage pour le dossier CRON
 */
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès interdit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #d32f2f; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 Accès interdit</h1>
        <p>Ce répertoire contient des tâches automatisées et n'est pas accessible publiquement.</p>
        <p><small>Si vous êtes administrateur, utilisez les commandes CRON appropriées.</small></p>
    </div>
</body>
</html>