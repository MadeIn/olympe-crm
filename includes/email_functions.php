<?php
/**
 * Fonctions de gestion des emails
 * Version modernisée avec classes d'email modernes et gestion d'erreurs
 */

/**
 * Envoie un email avec template moderne (fonction principale mise à jour)
 */
function SendMail(string $email, string $titre, string $message, int $user, int $client_num = 0): bool {
    try {
        $db = Database::getInstance();
        
        // Récupérer les informations utilisateur et showroom
        $sql = "SELECT u.*, s.* 
                FROM users u 
                LEFT JOIN showrooms s ON u.showroom_num = s.showroom_num 
                WHERE u.user_num = ? AND u.showroom_num IN (1,2,3,5,6)";
        
        $user_data = $db->queryRow($sql, [$user]);
        
        if (!$user_data) {
            error_log("Utilisateur non trouvé pour l'envoi d'email: $user");
            return false;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide: $email");
            return false;
        }
        
        // Configuration email
        $email_config = getEmailConfig();
        
        // Préparation du contenu avec template
        $email_content = buildEmailTemplate($message, $user_data);
        
        // Envoi avec PHPMailer moderne
        $success = sendEmailWithModernPHPMailer($email, $titre, $email_content, $user_data, $email_config);
        
        // Enregistrer en base si succès
        if ($success) {
            logEmailSent($titre, $message, $client_num, $user);
        }
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Erreur SendMail: " . $e->getMessage());
        return false;
    }
}

/**
 * Envoi avec PHPMailer moderne (nouvelle fonction)
 */
function sendEmailWithModernPHPMailer(string $to_email, string $subject, string $message, array $user_data, array $config): bool {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
        
        // Debug en développement seulement
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
            $mail->Debugoutput = 'error_log';
        }
        
        // Expéditeur
        $from_name = $user_data["user_prenom"] . ' Olympe';
        $mail->setFrom($config['from_email'], $from_name);
        $mail->addReplyTo($user_data["user_email"], $from_name);
        
        // Destinataire
        $mail->addAddress($to_email);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Options supplémentaires
        $mail->WordWrap = 70;
        $mail->Priority = 3;
        
        return $mail->send();
        
    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log("Erreur PHPMailer moderne: " . $e->getMessage());
        return sendEmailFallback($to_email, $subject, $message, $user_data);
    } catch (Exception $e) {
        error_log("Erreur générale email: " . $e->getMessage());
        return sendEmailFallback($to_email, $subject, $message, $user_data);
    }
}

/**
 * Construit le template HTML d'email
 */
function buildEmailTemplate(string $message, array $user_data): string {
    $logo_url = "https://crm.olympe-mariage.com/mails/img/olympe-mariage-logo.jpg";
    
    $template = '
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif;">
        <center>
            <table width="600" cellpadding="0" cellspacing="0" style="border-bottom: solid 1px #EAEAEA; margin-bottom: 25px;">
                <tbody>
                    <tr>
                        <td align="center">
                            <a href="https://www.olympe-mariage.com" style="display: block;">
                                <img src="' . h($logo_url) . '" alt="Olympe Mariage" style="max-width: 300px;">
                            </a><br>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <table width="600" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td style="padding: 0 20px;">
                            <div style="line-height: 21px; font: normal 14px Helvetica,Arial,Sans-Serif; color: #666; text-align: justify; line-height: 20px">
                                ' . $message . '
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table width="600" cellpadding="0" cellspacing="0" style="border-top: solid 1px #EAEAEA; margin-top: 25px;">
                <tbody>
                    <tr>
                        <td align="center" style="font: normal 15px Helvetica,Arial,Sans-Serif; color: #666; padding: 20px;">
                            <strong style="color: #333;">' . h($user_data["user_prenom"]) . '</strong><br>
                            <hr style="border: none; border-top: solid 1px #EAEAEA; width: 20%; margin: 10px 40%;">
                            Olympe Mariage - ' . h($user_data["showroom_ville"]) . '<br>
                            <small style="font-size: .85em; color: #999;">
                                ' . h($user_data["showroom_adr1"]) . ', ' . h($user_data["showroom_cp"]) . ' ' . h($user_data["showroom_ville"]) . '<br>
                                <a href="tel:' . h($user_data["showroom_tel"]) . '" style="text-decoration: none; color: #999;">' . h($user_data["showroom_tel"]) . '</a> - 
                                <a href="https://www.olympe-mariage.com" style="text-decoration: none; color: #999;">www.olympe-mariage.com</a><br>
                                <hr size="1" color="#EAEAEA" style="margin: 10px 0;">
                                Facebook : <a href="https://www.facebook.com/olympemariage/" style="color: #999;">olympemariage</a> - 
                                Instagram : <a href="https://www.instagram.com/olympemariage/" style="color: #999;">@olympemariage</a>
                            </small>			
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>
    </body>
    </html>';
    
    return $template;
}

/**
 * Configuration email centralisée
 */
function getEmailConfig(): array {
    return [
        'smtp_host' => 'smtp.olympe-mariage.com',
        'smtp_port' => 587,
        'smtp_username' => 'showroom@olympe-mariage.com',
        'smtp_password' => '@montpellier2019', // TODO: Déplacer dans config sécurisée
        'from_email' => 'showroom@olympe-mariage.com',
        'smtp_secure' => 'tls'
    ];
}

/**
 * Envoi via PHPMailer moderne
 */
function sendEmailViaPHPMailer(string $to_email, string $subject, string $message, array $user_data, array $config): bool {
    try {
        // Vérifier si PHPMailer est disponible
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Fallback vers mail() PHP
            return sendEmailFallback($to_email, $subject, $message, $user_data);
        }
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // Expéditeur
        $from_name = $user_data["user_prenom"] . ' Olympe';
        $mail->setFrom($config['from_email'], $from_name);
        $mail->addReplyTo($user_data["user_email"], $from_name);
        
        // Destinataire
        $mail->addAddress($to_email);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Erreur PHPMailer: " . $e->getMessage());
        return sendEmailFallback($to_email, $subject, $message, $user_data);
    }
}

/**
 * Envoi fallback avec mail() PHP
 */
function sendEmailFallback(string $to_email, string $subject, string $message, array $user_data): bool {
    $from_name = $user_data["user_prenom"] . ' Olympe';
    $from_email = $user_data["user_email"];
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $from_email,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to_email, $subject, $message, implode("\r\n", $headers));
}

/**
 * Enregistre l'email envoyé en base
 */
function logEmailSent(string $titre, string $message, int $client_num, int $user): void {
    try {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO mails (mail_date, mail_titre, mail_contenu, client_num, user_num) 
                VALUES (?, ?, ?, ?, ?)";
        
        $db->insert($sql, [
            date('Y-m-d H:i:s'),
            $titre,
            $message,
            $client_num,
            $user
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur logEmailSent: " . $e->getMessage());
    }
}

/**
 * Remplace les variables dans un template d'email
 */
function replaceEmailVariables(string $template, array $variables): string {
    foreach ($variables as $key => $value) {
        $template = str_replace('[' . strtoupper($key) . ']', $value, $template);
    }
    
    return $template;
}

/**
 * Valide une adresse email
 */
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Nettoie le contenu HTML pour email
 */
function sanitizeEmailContent(string $content): string {
    // Autoriser seulement les balises HTML sûres pour email
    $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><table><tr><td><th><img>';
    
    return strip_tags($content, $allowed_tags);
}

/**
 * Génère un email de test
 */
function sendTestEmail(string $to_email, int $user_id): bool {
    $message = '<p>Ceci est un email de test pour vérifier la configuration.</p>';
    $message .= '<p>Envoyé le ' . date('d/m/Y à H:i') . '</p>';
    
    return SendMail($to_email, 'Test - Configuration Email', $message, $user_id);
}

/**
 * Récupère un template d'email depuis le fichier de configuration
 */
function getEmailTemplate(int $type_id, int $gender = 0): array {
    static $templates = null;
    
    // Charger les templates une seule fois
    if ($templates === null) {
        $config_path = dirname(dirname(__FILE__)) . '/config/email_templates.php';
        if (file_exists($config_path)) {
            $templates = require $config_path;
        } else {
            error_log("Fichier de templates d'emails non trouvé: " . $config_path);
            return ['titre' => '', 'message' => ''];
        }
    }
    
    // Déterminer le genre (female = 0, male = 1)
    $gender_key = $gender === 1 ? 'male' : 'female';
    
    // Retourner le template demandé
    if (isset($templates[$gender_key][$type_id])) {
        return $templates[$gender_key][$type_id];
    }
    
    // Fallback : essayer l'autre genre si pas trouvé
    $fallback_key = $gender === 1 ? 'female' : 'male';
    if (isset($templates[$fallback_key][$type_id])) {
        return $templates[$fallback_key][$type_id];
    }
    
    error_log("Template email non trouvé: type_id=$type_id, gender=$gender");
    return ['titre' => '', 'message' => ''];
}

/**
 * Envoie un email avec template prédéfini
 */
function sendTemplateEmail(string $email, int $template_id, array $variables, int $user_id, int $client_num = 0): bool {
    $template = getEmailTemplate($template_id);
    
    if (empty($template['titre']) || empty($template['message'])) {
        error_log("Template email non trouvé: $template_id");
        return false;
    }
    
    $subject = replaceEmailVariables($template['titre'], $variables);
    $message = replaceEmailVariables($template['message'], $variables);
    
    return SendMail($email, $subject, $message, $user_id, $client_num);
}
?>