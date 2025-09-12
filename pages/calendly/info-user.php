<?php
// Token d'accès pour l'API
$accessToken = 'eyJraWQiOiIxY2UxZTEzNjE3ZGNmNzY2YjNjZWJjY2Y4ZGM1YmFmYThhNjVlNjg0MDIzZjdjMzJiZTgzNDliMjM4MDEzNWI0IiwidHlwIjoiUEFUIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJodHRwczovL2F1dGguY2FsZW5kbHkuY29tIiwiaWF0IjoxNzIxNjU1Mjg2LCJqdGkiOiIxMDE2ZWFhNi0wMWIwLTRjZGEtYWFjZi03MTlkMTdiZTM4Y2EiLCJ1c2VyX3V1aWQiOiJkZjdhZWY1ZS1lZWY5LTRiMmMtOTQ3Zi1mNmRkYTc5YjIzY2IifQ.ylSY5F-Njtvpwg1Y2YhH18LQ-sTusSwPvpV1x1-IAWQspnUsYycCoFg0gNIHcG0E20LCAiv8IZDnxA59LcC2Jw';

// Initialiser une session cURL
$ch = curl_init();

// Configurer les options cURL
curl_setopt($ch, CURLOPT_URL, "https://api.calendly.com/users/me");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer {$accessToken}"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Exécuter la requête cURL
$response = curl_exec($ch);

// Vérifier si une erreur s'est produite
if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
} else {
    // Afficher la réponse de l'API
    echo $response;
}

// Fermer la session cURL
curl_close($ch);
?>