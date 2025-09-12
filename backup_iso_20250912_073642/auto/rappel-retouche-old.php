<? include("/home/madeinpr/www/CRM/olympe-mariage/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les rendez vous à venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = date("Y-m-d", strtotime("+30 days")) . " 00:00:00";
	$date_fin = date("Y-m-d", strtotime("+30 days")) . " 23:59:59";
	
	//$date_debut = "2018-02-01 00:00:00";
	//$date_fin = "2018-02-15 00:00:00";
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num=2 and rdv_mail_relance=0";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	//echo $sql . "<br><hr>";
	$cc = mysql_query($sql);
	while ($rcc=mysql_fetch_array($cc)) {
		
		$sql = "select * from rendez_vous where client_num='" . $rcc["client_num"] . "' and type_num IN (4,5)";
		$tt = mysql_query($sql);
		$test = mysql_num_rows($tt);
		if ($test==0) {
			// On regarde la commande
			$sql = "select * from commandes c, commandes_produits cp where c.id=cp.id and taille_num=35 and client_num='" . $rcc["client_num"] . "'";
			$ta = mysql_query($sql);
			$nbr_ta = mysql_num_rows($ta);
			if ($nbr_ta==0) {
				if ($rcc["client_genre"]==0)
					$message = "pour la remise de votre robe";
				else
					$message = "pour la remise de votre costume";
			}
			else
				$message = "pour le premier essayage avec notre couturière";
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[13][$rcc["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[13][$rcc["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
			$date_reception = format_date($rcc["rdv_date"],0,1);
			$message_mail = str_replace("[DATE_RECEPTION]",$date_reception,$message_mail);
			$message_mail = str_replace("[RETOUCHE]",$message,$message_mail);
			
			echo $rcc["client_mail"] . "<br>";
			//echo $message_mail . "<br><hr><br>";
			// On envoi le mail
			SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
			
			$sql = "update rendez_vous set rdv_mail_relance=1, rdv_mail_relance_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $rcc["rdv_num"] . "'";
			mysql_query($sql);
		}
	}
	
	
?>
