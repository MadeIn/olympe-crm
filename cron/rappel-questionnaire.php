<?php
/**
 * Tâche CRON - Rappel des rendez-vous Montpellier
 * Envoie un rappel 3 jours avant le rendez-vous
 */

// Sécurisation et initialisation
require_once 'cron_security.php';

start_cron_task('rappel-questionnaire');

try {
    $db = init_cron_database();

	// Envoyer en cron tous les matins au RDV du samedi dans 7 jours
	// On recupere les rendez vous � venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = date("Y-m-d", strtotime("+7 days"));
	$tabDate = explode('-', $date_debut);
	
	$timestamp = mktime(0, 0, 0, $tabDate[1], $tabDate[2], $tabDate[0]);
	$jour = date('N', $timestamp);
	
	echo $jour;
	
	//if ($jour=="6") {
		$date_debut = date("Y-m-d", strtotime("+7 days")) . " 00:00:00";
		$date_fin = date("Y-m-d", strtotime("+7 days")) . " 23:59:59";
		
		$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num IN (1) and client_genre=0";
		$sql .= " and s.showroom_num IN (1,2,5)";
		//echo $sql;
		$cc = $base->query($sql);
		
		foreach ($cc as $rcc) {
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[17][$rcc["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[17][$rcc["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
			
			echo $rcc["client_mail"] . "<br>";
			
			// On envoi le mail
			SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
		}
	//}
} catch (Exception $e) {
    log_cron('rappel-questionnaire', "Erreur fatale: " . $e->getMessage(), 'error');
    
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit(1);
}	
	
?>
