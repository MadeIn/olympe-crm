<?php
// Assurez-vous que ce script est accessible via une URL publique.

// Récupération du corps de la requête
$content = file_get_contents("php://input");
// Conversion du JSON en objet PHP
$data = json_decode($content, true);

// Vous pouvez ici ajouter des logiques pour gérer les données, par exemple :
file_put_contents("calendly_events.log", print_r($data, true), FILE_APPEND);

// Réponse simple pour confirmer la réception
//echo "Webhook received";
?>