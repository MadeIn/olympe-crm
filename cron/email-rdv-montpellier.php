<?php
/**
 * Tâche CRON - Rappel des rendez-vous Montpellier
 * Envoie un rappel 3 jours avant le rendez-vous
 */

// Sécurisation et initialisation
require_once 'cron_security.php';

start_cron_task('email-rdv-montpellier');

try {
    $db = init_cron_database();
    
    // Date de début (3 jours à partir d'aujourd'hui)
    $date_debut = date('Y-m-d H:i:s', strtotime('+3 days'));
    $date_fin = date('Y-m-d H:i:s', strtotime('+3 days 23:59:59'));
    
    log_cron('email-rdv-montpellier', "Recherche des RDV entre {$date_debut} et {$date_fin}");
    
    // Requête modernisée avec PDO
    $sql = "SELECT r.*, c.*, s.*, u.* 
            FROM rendez_vous r 
            INNER JOIN clients c ON r.client_num = c.client_num 
            INNER JOIN users u ON c.user_num = u.user_num 
            INNER JOIN showrooms s ON c.showroom_num = s.showroom_num 
            WHERE r.rdv_date >= ? 
            AND r.rdv_date <= ? 
            AND r.type_num IN (1,4,5,6,7,8,9) 
            AND s.showroom_num = 1 
            AND c.client_mail IS NOT NULL 
            AND c.client_mail != ''
            ORDER BY r.rdv_date ASC";
    
    $rendez_vous = $db->query($sql, [$date_debut, $date_fin]);
    
    $emails_sent = 0;
    $errors = 0;
    
    foreach ($rendez_vous as $rdv) {
        try {
            // Vérifier si un rappel n'a pas déjà été envoyé
            $check_sql = "SELECT COUNT(*) as count 
                         FROM mails 
                         WHERE client_num = ? 
                         AND mail_titre LIKE '%Rappel Rendez-vous%' 
                         AND DATE(mail_date) = CURDATE()";
            
            $already_sent = $db->queryRow($check_sql, [$rdv['client_num']]);
            
            if ($already_sent['count'] > 0) {
                log_cron('email-rdv-montpellier', "Rappel déjà envoyé aujourd'hui pour client {$rdv['client_num']}");
                continue;
            }
            
            // Préparer les variables pour le template
            $variables = [
                'PRENOM' => $rdv['client_prenom'],
                'DATE_HEURE' => format_date($rdv['rdv_date'], 2, 1), // Format français avec heure
                'SHOWROOM_NOM' => $rdv['showroom_nom'],
                'SHOWROOM_ADRESSE' => $rdv['showroom_adr1'],
                'SHOWROOM_CP' => $rdv['showroom_cp'],
                'SHOWROOM_VILLE' => $rdv['showroom_ville'],
                'SHOWROOM_TEL' => $rdv['showroom_tel'],
                'SHOWROOM_ACCES' => $rdv['showroom_acces'] ?? '',
                'VILLE' => $rdv['showroom_ville']
            ];
            
            // Utiliser le template d'email de rappel (ID 11)
            $success = sendTemplateEmail(
                $rdv['client_mail'],
                11, // ID du template de rappel
                $variables,
                $rdv['user_num'],
                $rdv['client_num']
            );
            
            if ($success) {
                $emails_sent++;
                log_cron('email-rdv-montpellier', "Email envoyé à {$rdv['client_mail']} pour RDV du " . format_date($rdv['rdv_date'], 6, 1));
            } else {
                $errors++;
                log_cron('email-rdv-montpellier', "Erreur envoi email à {$rdv['client_mail']}", 'error');
            }
            
            // Pause pour éviter la surcharge du serveur SMTP
            usleep(500000); // 0.5 seconde
            
        } catch (Exception $e) {
            $errors++;
            log_cron('email-rdv-montpellier', "Erreur traitement RDV {$rdv['rdv_num']}: " . $e->getMessage(), 'error');
        }
    }
    
    $stats = [
        'rdv_found' => count($rendez_vous),
        'emails_sent' => $emails_sent,
        'errors' => $errors
    ];
    
    end_cron_task('email-rdv-montpellier', $stats);
    
    // Sortie pour le cron (optionnelle)
    if (php_sapi_name() !== 'cli') {
        echo json_encode([
            'success' => true,
            'message' => "Rappels envoyés: {$emails_sent}, Erreurs: {$errors}",
            'stats' => $stats
        ]);
    }
    
} catch (Exception $e) {
    log_cron('email-rdv-montpellier', "Erreur fatale: " . $e->getMessage(), 'error');
    
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit(1);
}
?>