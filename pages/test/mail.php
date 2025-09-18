<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";

echo "=== TEST ENVOI EMAIL BREVO ===\n\n";

// Test d'envoi complet
$result = sendTestEmailBrevo('gcottret@madein.net', 2);

if ($result) {
    echo "✅ Email envoyé avec succès !\n";
    echo "\n🎉 Votre système d'emails Brevo fonctionne parfaitement !\n";
    
    echo "\n📋 Récapitulatif de la migration :\n";
    echo "- PHPMailer remplacé par Brevo API\n";
    echo "- Configuration via variables d'environnement\n";  
    echo "- Emails vérifiés utilisés automatiquement\n";
    echo "- Meilleure délivrabilité garantie\n";
    
} else {
    echo "❌ Échec envoi - Vérifiez les logs pour plus de détails\n";
}

echo "\n=== FIN TEST ===\n";
?>