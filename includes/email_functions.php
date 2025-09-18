<?php
/**
 * Système d'envoi d'emails avec Brevo API
 * Version modernisée pour remplacer PHPMailer
 * Nécessite: composer require getbrevo/brevo-php
 */

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailSender;
use Brevo\Client\Model\SendSmtpEmailReplyTo;
use Brevo\Client\Api\SendersApi;
use Brevo\Client\Api\AccountApi;
use GuzzleHttp\Client;

/**
 * Configuration Brevo centralisée
 */
class BrevoConfig {
    private static $config = null;
    
    public static function getConfig(): array {
        if (self::$config === null) {
          
            self::$config = [
                'api_key' => env('BREVO_API_KEY', ''), 
                'sender_email' => 'montpellier@olympe-mariage.com', // Email vérifié dans Brevo
                'sender_name' => 'Olympe Mariage',
                'api_url' => 'https://api.brevo.com/v3'
            ];
        }
        return self::$config;
    }
    
    /**
     * Alternative : initialisation avec config externe
     */
    public static function initWithConfig(array $app_config): void {
        self::$config = [
            'api_key' => $app_config['BREVO_API_KEY'] ?? '', 
            'sender_email' => 'montpellier@olympe-mariage.com', // Email vérifié dans Brevo
            'sender_name' => 'Olympe Mariage',
            'api_url' => 'https://api.brevo.com/v3'
        ];
    }
}

/**
 * Classe principale pour l'envoi d'emails via Brevo
 */
class BrevoEmailService {
    private $api;
    private $config;
    
    public function __construct() {
        $this->config = BrevoConfig::getConfig();
        
        if (empty($this->config['api_key'])) {
            throw new Exception('Clé API Brevo manquante');
        }
        
        // Configuration du client Brevo
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->config['api_key']);
        $this->api = new TransactionalEmailsApi(new Client(), $config);
    }
    
    /**
     * Envoie un email via l'API Brevo
     */
    public function sendEmail(string $to_email, string $subject, string $html_content, array $sender_info = []): bool {
        try {
            // Validation de l'email
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email invalide: $to_email");
            }
            
            // Préparation de l'expéditeur
            $sender_name = !empty($sender_info['name']) ? $sender_info['name'] : $this->config['sender_name'];
            $sender_email = !empty($sender_info['email']) ? $sender_info['email'] : $this->config['sender_email'];
            $reply_to_email = $sender_info['reply_to'] ?? $sender_email;
            
            // Construction de l'objet email
            $sendSmtpEmail = new SendSmtpEmail();
            
            // Expéditeur
            $sender = new SendSmtpEmailSender();
            $sender->setEmail($sender_email);
            $sender->setName($sender_name);
            $sendSmtpEmail->setSender($sender);
            
            // Reply-To
            $replyTo = new SendSmtpEmailReplyTo();
            $replyTo->setEmail($reply_to_email);
            $replyTo->setName($sender_name);
            $sendSmtpEmail->setReplyTo($replyTo);
            
            // Destinataire
            $to = new SendSmtpEmailTo();
            $to->setEmail($to_email);
            $sendSmtpEmail->setTo([$to]);
            
            // Contenu
            $sendSmtpEmail->setSubject($subject);
            $sendSmtpEmail->setHtmlContent($html_content);
            $sendSmtpEmail->setTextContent(strip_tags($html_content));
            
            // Envoi
            $result = $this->api->sendTransacEmail($sendSmtpEmail);
                        
            return true;
            
        } catch (\Brevo\Client\ApiException $e) {
            $error_details = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'response_body' => $e->getResponseBody()
            ];
            error_log("Erreur Brevo API: " . json_encode($error_details));
            return false;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email avec template
     */
    public function sendTemplateEmail(string $to_email, int $template_id, array $params = []): bool {
        try {
            $sendSmtpEmail = new SendSmtpEmail();
            
            // Destinataire
            $to = new SendSmtpEmailTo();
            $to->setEmail($to_email);
            $sendSmtpEmail->setTo([$to]);
            
            // Template Brevo
            $sendSmtpEmail->setTemplateId($template_id);
            
            // Paramètres du template
            if (!empty($params)) {
                $sendSmtpEmail->setParams($params);
            }
            
            $result = $this->api->sendTransacEmail($sendSmtpEmail);
            
            error_log("Email template envoyé via Brevo - Template ID: $template_id, Message ID: " . $result->getMessageId());
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi template Brevo: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Fonction principale SendMail modernisée pour Brevo
 */
function SendMail(string $email, string $titre, string $message, int $user, int $client_num = 0): bool {
    try {
        $base = Database::getInstance();
        
        // Récupérer les informations utilisateur et showroom
        $sql = "SELECT u.*, s.* 
                FROM users u 
                LEFT JOIN showrooms s ON u.showroom_num = s.showroom_num 
                WHERE u.user_num = ? AND u.showroom_num IN (1,2,3,5,6)";
        
        $user_data = $base->queryRow($sql, [$user]);
        
        if (!$user_data) {
            error_log("Utilisateur non trouvé pour l'envoi d'email: $user");
            return false;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide: $email");
            return false;
        }
        
        // Préparation du contenu avec template
        $email_content = buildEmailTemplate($message, $user_data);
        
                
        $showroom_num = $user_data['showroom_num'] ?? 1;
        $sender_email = $user_data['user_email'] ?? 'montpellier@olympe-mariage.com';
        
        // Informations expéditeur avec email vérifié
        $sender_info = [
            'name' => $user_data["user_prenom"] . ' - Olympe Mariage',
            'email' => $sender_email,
            'reply_to' => $user_data["user_email"] ?? $sender_email
        ];
        
        // Envoi via Brevo
        $brevo = new BrevoEmailService();
        $success = $brevo->sendEmail($email, $titre, $email_content, $sender_info);
        
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
 * Envoi d'email avec template Brevo prédéfini
 */
function sendBrevoTemplateEmail(string $email, int $template_id, array $variables, int $user_id, int $client_num = 0): bool {
    try {
        $base = Database::getInstance();
        
        // Récupérer les données utilisateur
        $sql = "SELECT u.*, s.* 
                FROM users u 
                LEFT JOIN showrooms s ON u.showroom_num = s.showroom_num 
                WHERE u.user_num = ?";
        
        $user_data = $base->queryRow($sql, [$user_id]);
        
        if (!$user_data) {
            error_log("Utilisateur non trouvé: $user_id");
            return false;
        }
        
        // Ajouter les données utilisateur aux variables du template
        $template_vars = array_merge($variables, [
            'USER_PRENOM' => $user_data['user_prenom'],
            'SHOWROOM_VILLE' => $user_data['showroom_ville'],
            'SHOWROOM_TEL' => $user_data['showroom_tel'],
            'SHOWROOM_ADRESSE' => $user_data['showroom_adr1'] . ', ' . $user_data['showroom_cp'] . ' ' . $user_data['showroom_ville']
        ]);
        
        $brevo = new BrevoEmailService();
        $success = $brevo->sendTemplateEmail($email, $template_id, $template_vars);
        
        if ($success) {
            // Log en base avec template ID
            logEmailSent("Template Brevo #$template_id", json_encode($template_vars), $client_num, $user_id);
        }
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Erreur sendBrevoTemplateEmail: " . $e->getMessage());
        return false;
    }
}

/**
 * Construit le template HTML d'email (version optimisée)
 */
function buildEmailTemplate(string $message, array $user_data): string {
    $logo_url = "https://crm.olympe-mariage.com/mails/img/olympe-mariage-logo.jpg";
    
    // Template HTML responsive et compatible avec tous les clients email
    $template = '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>' . h($user_data["user_prenom"]) . ' - Olympe Mariage</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Helvetica, Arial, sans-serif; background-color: #f8f9fa;">
        <div style="width: 100%; background-color: #f8f9fa; padding: 20px 0;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                
                <!-- Header avec logo -->
                <div style="text-align: center; padding: 30px 20px; border-bottom: 2px solid #f1f1f1;">
                    <a href="https://www.olympe-mariage.com" style="display: inline-block; text-decoration: none;">
                        <img src="' . h($logo_url) . '" alt="Olympe Mariage" style="max-width: 250px; height: auto; display: block;">
                    </a>
                </div>
                
                <!-- Contenu principal -->
                <div style="padding: 30px 20px;">
                    <div style="line-height: 1.6; font-size: 14px; color: #333333; text-align: left;">
                        ' . $message . '
                    </div>
                </div>
                
                <!-- Footer signature -->
                <div style="background-color: #f8f9fa; padding: 25px 20px; text-align: center; border-top: 2px solid #f1f1f1;">
                    <div style="font-size: 16px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">
                        ' . h($user_data["user_prenom"]) . '
                    </div>
                    
                    <div style="width: 50px; height: 1px; background-color: #bdc3c7; margin: 15px auto;"></div>
                    
                    <div style="font-size: 14px; font-weight: bold; color: #34495e; margin-bottom: 8px;">
                        Olympe Mariage - ' . h($user_data["showroom_ville"]) . '
                    </div>
                    
                    <div style="font-size: 12px; color: #7f8c8d; line-height: 1.4;">
                        ' . h($user_data["showroom_adr1"]) . '<br>
                        ' . h($user_data["showroom_cp"]) . ' ' . h($user_data["showroom_ville"]) . '<br>
                        <a href="tel:' . h($user_data["showroom_tel"]) . '" style="color: #3498db; text-decoration: none;">' . h($user_data["showroom_tel"]) . '</a><br>
                        <a href="https://www.olympe-mariage.com" style="color: #3498db; text-decoration: none;">www.olympe-mariage.com</a>
                    </div>
                    
                    <div style="margin-top: 15px; font-size: 12px; color: #95a5a6;">
                        <a href="https://www.facebook.com/olympemariage/" style="color: #3b5998; text-decoration: none; margin-right: 10px;">Facebook</a>
                        <a href="https://www.instagram.com/olympemariage/" style="color: #e4405f; text-decoration: none;">Instagram</a>
                    </div>
                </div>
                
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}

/**
 * Enregistre l'email envoyé en base (version améliorée)
 */
function logEmailSent(string $titre, string $message, int $client_num, int $user): void {
    try {
        $base = Database::getInstance();
        
        $sql = "INSERT INTO mails (mail_date, mail_titre, mail_message, client_num, user_num) 
                VALUES (?, ?, ?, ?, ?)";
        
        $base->insert($sql, [
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
 * Test de connexion à l'API Brevo
 */
function testBrevoConnection(): array {
    try {
        $config = BrevoConfig::getConfig();
        
        if (empty($config['api_key'])) {
            return [
                'success' => false,
                'message' => 'Clé API Brevo manquante dans la configuration',
                'data' => null
            ];
        }
        
        $brevo = new BrevoEmailService();
        
        // Test simple avec l'API account
        $api_config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $config['api_key']);
        $api = new \Brevo\Client\Api\AccountApi(new Client(), $api_config);
        
        $account_info = $api->getAccount();
        
        return [
            'success' => true,
            'message' => 'Connexion Brevo réussie',
            'data' => [
                'email' => $account_info->getEmail(),
                'company' => $account_info->getCompanyName(),
                'plan' => $account_info->getPlan()
            ]
        ];
        
    } catch (\Brevo\Client\ApiException $e) {
        $error_body = $e->getResponseBody();
        $error_message = "Erreur API Brevo (Code: " . $e->getCode() . ")";
        
        // Analyser l'erreur spécifique
        if ($e->getCode() === 401) {
            if (strpos($error_body, 'unrecognised IP address') !== false) {
                $error_message = "IP non autorisée. Allez dans votre compte Brevo > Profile > Security > Désactivez 'Block unknown IP addresses' ou autorisez votre IP";
            } else {
                $error_message = "Clé API invalide ou non autorisée";
            }
        }
        
        return [
            'success' => false,
            'message' => $error_message,
            'data' => [
                'error_code' => $e->getCode(),
                'error_body' => $error_body
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur connexion Brevo: ' . $e->getMessage(),
            'data' => null
        ];
    }
}

/**
 * Envoie un email de test via Brevo
 */
function sendTestEmailBrevo(string $to_email, int $user_id): bool {
    try {
        // Vérifier que l'utilisateur existe
        $base = Database::getInstance();
        $sql = "SELECT u.*, s.* 
                FROM users u 
                LEFT JOIN showrooms s ON u.showroom_num = s.showroom_num 
                WHERE u.user_num = ?";
        
        $user_data = $base->queryRow($sql, [$user_id]);
        
        if (!$user_data) {
            error_log("❌ Utilisateur $user_id non trouvé pour le test");
            return false;
        }
        
        error_log("✅ Utilisateur trouvé: " . $user_data['user_prenom'] . " - Showroom: " . $user_data['showroom_ville']);
        
        $message = '<h2>Test de configuration Brevo</h2>';
        $message .= '<p>Ceci est un email de test pour vérifier la configuration avec l\'API Brevo.</p>';
        $message .= '<p><strong>Envoyé le:</strong> ' . date('d/m/Y à H:i') . '</p>';
        $message .= '<p><strong>Utilisateur:</strong> ' . htmlspecialchars($user_data['user_prenom']) . '</p>';
        $message .= '<p><strong>Showroom:</strong> ' . htmlspecialchars($user_data['showroom_ville']) . '</p>';
        $message .= '<p><em>Si vous recevez cet email, la configuration fonctionne parfaitement !</em></p>';
        
        error_log("🚀 Tentative d'envoi d'email de test...");
        $result = SendMail($to_email, 'Test - Configuration Brevo API', $message, $user_id);
        
        if ($result) {
            error_log("✅ Test email envoyé avec succès");
        } else {
            error_log("❌ Échec envoi test email");
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("❌ Erreur dans sendTestEmailBrevo: " . $e->getMessage());
        return false;
    }
}

/**
 * Migration des anciens templates vers Brevo
 * À utiliser une seule fois pour migrer les templates existants
 */
function migrateTemplatesToBrevo(): array {
    $results = [];
    
    try {
        // Ici vous pourrez créer vos templates dans Brevo
        // et mapper les anciens IDs vers les nouveaux
        
        $template_mapping = [
            // Ancien ID => Nouveau ID Brevo
            1 => 'brevo_template_1',
            2 => 'brevo_template_2',
            // Ajouter vos mappings
        ];
        
        $results['mapping'] = $template_mapping;
        $results['success'] = true;
        $results['message'] = 'Migration des templates préparée';
        
    } catch (Exception $e) {
        $results['success'] = false;
        $results['message'] = 'Erreur migration: ' . $e->getMessage();
    }
    
    return $results;
}

/**
 * Gestion des webhooks Brevo pour le tracking
 */
function handleBrevoWebhook(): void {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data && isset($data['event'])) {
        error_log("Webhook Brevo reçu: " . json_encode($data));
        
        // Traiter selon le type d'événement
        switch ($data['event']) {
            case 'delivered':
                // Email livré
                break;
            case 'opened':
                // Email ouvert
                break;
            case 'clicked':
                // Lien cliqué
                break;
            case 'bounced':
                // Email rejeté
                break;
        }
    }
}
?>