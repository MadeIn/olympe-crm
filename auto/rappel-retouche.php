<?php include("/home/madeinpr/www/CRM/olympe-mariage/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les rendez vous à venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = date("Y-m-d", strtotime("+30 days")) . " 00:00:00";
	$date_fin = date("Y-m-d", strtotime("+30 days")) . " 23:59:59";
	
	//$date_debut = "2018-02-01 00:00:00";
	//$date_fin = "2018-02-15 00:00:00";
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num=2 and rdv_mail_relance=0";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	
	/*$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and type_num=2 and c.client_num=12519";
	$sql .= " and s.showroom_num IN (1,3,2,5)";*/
	//echo $sql . "<br><hr>";
	$cc = $base->query($sql);
	foreach ($cc as $rcc) {
		
		$sql = "select * from rendez_vous where client_num='" . $rcc["client_num"] . "' and type_num IN (4,5)";
		$tt = $base->query($sql);
		$test = count($tt);
		if ($test==0) {
			$info_paiement = "";
			// On regarde la commande
			$sql = "select * from commandes c, paiements p, commandes_produits cp, showrooms sh where c.paiement_num=p.paiement_num and c.id=cp.id and c.showroom_num=sh.showroom_num and taille_num=35 and client_num='" . $rcc["client_num"] . "'";
			$ta = $base->query($sql);
			$nbr_ta = count($ta);
			if ($nbr_ta==0) {
				if ($rcc["client_genre"]==0) {
					$message = "pour la remise de votre robe";
				}
				else
					$message = "pour la remise de votre costume";
			}
			else {
				$message = "pour le premier essayage avec notre couturière";
				$rco = mysql_fetch_array($ta);
				// On va chercher les infos de paiements
				if ($rco["paiement_nombre"]>1) { // ON affiche les acomptes
					$montant_a_payer = $rco["commande_ttc"];
					$echeance = explode("/",$rco["paiement_modele"]);
					$echeance_desc = explode("/",$rco["paiement_description"]);
					$acompte_num = 0;
					foreach ($echeance as $val) {
						$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
						$acompte_num++;
						if ($acompte_num==2) { // On recupere la somme du deuxième acompte
							$acompte2 = $acompte_val;
						}
					}				
					// On construite le message
					$info_paiement = '<ul>
										<li><b>Montant à payer avant notre rendez-vous</b> : ' . $acompte2 . ' &euro;</li>
										<li>Règler votre acompte par virement :
											<ul>
												<li>IBAN : ' .  $rco["banque_iban"] . '</li>
												<li>Code BIC / SWIFT : ' . $rco["banque_swift"] . '</li>
											</ul>
										</li>
										<li>Retrouvez les détails de votre commande en <a href="https://crm.olympe-mariage.com/commandes/index.php?cde=' . crypte($rco["commande_num"]) . '">cliquant ici</a></li>
									</ul';										
				}
			}
			
			if ($rcc["client_genre"]==0) { // FEMME
				$type = 18;
			}
			else {
				$type = 13;
			}
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[$type][$rcc["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[$type][$rcc["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
			$date_reception = format_date($rcc["rdv_date"],0,1);
			$message_mail = str_replace("[DATE_RECEPTION]",$date_reception,$message_mail);
			$message_mail = str_replace("[RETOUCHE]",$message,$message_mail);
			$message_mail = str_replace("[INFO_PAIEMENT]",$info_paiement,$message_mail);
						
			
			echo $rcc["client_mail"] . "<br>";
			//echo $message_mail . "<br><hr><br>";
			// On envoi le mail
			//SendMail("gcottret@madein.net",$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
			SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
			
			$sql = "update rendez_vous set rdv_mail_relance=1, rdv_mail_relance_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $rcc["rdv_num"] . "'";
			$base->query($sql);
		}
	}
	
	
?>
