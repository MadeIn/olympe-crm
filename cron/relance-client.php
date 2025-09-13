<?php
/**
 * Tâche CRON - Rappel des rendez-vous Montpellier
 * Envoie un rappel 3 jours avant le rendez-vous
 */

// Sécurisation et initialisation
require_once 'cron_security.php';

start_cron_task('relance-client');

try {
    $db = init_cron_database();

	// Envoyer en cron tous les matins à 9h
	// On recupere les 1er ou 2eme rendez vous d'il y a 30 jours et qui n'ont pas de commande pour les relancer
	
	$date_debut = date("Y-m-d", strtotime("-30 days")) . " 00:00:00";
	$date_fin = date("Y-m-d", strtotime("-30 days")) . " 23:59:59";
	
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num=1";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	$cc = $base->query($sql);
	foreach ($cc as $rcc) {
		
		// On test si la cliente n'a pas un 2e RDV prévu plus tard
		$sql = "select * from rendez_vous where client_num='" . $rcc["client_num"] . "' and type_num=6 and rdv_date>='" . Date("Y-m-d") . " 00:00:00'";
		$rr = $base->query($sql);
		$nbr_rdv = count($rr);
		if ($nbr_rdv==0) {
			// On test si la cliente n'a pas commandé 
			$sql = "select * from commandes where commande_num!=0 and client_num='" . $rcc["client_num"] . "'";
			$tt = $base->query($sql);
			$nbr_commande = count($tt);
			if ($nbr_commande==0) {
				// On envoi le mail selon le type de RDV
				$template = getEmailTemplate(12,$rcc["client_genre"]);
				$titre_mail = $template["titre"];
				$message_mail = $template["message"];
				$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
				$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
				
				echo $rcc["client_mail"] . "<br>";
				
				// On envoi le mail
				//SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
			}
		}
	}
} catch (Exception $e) {
    log_cron('relance-client', "Erreur fatale: " . $e->getMessage(), 'error');
    
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit(1);
}	
	
?>
