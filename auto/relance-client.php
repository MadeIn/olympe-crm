<? 
	echo $_SERVER['DOCUMENT_ROOT'];
	exit();
	include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les 1er ou 2eme rendez vous d'il y a 30 jours et qui n'ont pas de commande pour les relancer
	
	$date_debut = date("Y-m-d", strtotime("-30 days")) . " 00:00:00";
	$date_fin = date("Y-m-d", strtotime("-30 days")) . " 23:59:59";
	
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num=1";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	$cc = mysql_query($sql);
	while ($rcc=mysql_fetch_array($cc)) {
		
		// On test si la cliente n'a pas un 2e RDV prévu plus tard
		$sql = "select * from rendez_vous where client_num='" . $rcc["client_num"] . "' and type_num=6 and rdv_date>='" . Date("Y-m-d") . " 00:00:00'";
		$rr = mysql_query($sql);
		$nbr_rdv = mysql_num_rows($rr);
		if ($nbr_rdv==0) {
			// On test si la cliente n'a pas commandé 
			$sql = "select * from commandes where commande_num!=0 and client_num='" . $rcc["client_num"] . "'";
			$tt = mysql_query($sql);
			$nbr_commande = mysql_num_rows($tt);
			if ($nbr_commande==0) {
				// On envoi le mail selon le type de RDV
				$titre_mail = $mail_type[12][$rcc["client_genre"]]["titre"];
				$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
				$message_mail = $mail_type[12][$rcc["client_genre"]]["message"];
				$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
				
				echo $rcc["client_mail"] . "<br>";
				
				// On envoi le mail
				//SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
			}
		}
	}
	
	
?>
