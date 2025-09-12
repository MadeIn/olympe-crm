<? include("/home/madeinpr/www/CRM/olympe-mariage/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les rendez vous à venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = date("Y-m-d", strtotime("+3 days")) . " 00:00:00";
	$date_fin = date("Y-m-d", strtotime("+3 days")) . " 23:59:59";
	
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and type_num IN (1,4,5,6,7,8,9) and rdv_mail_relance=0";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	$cc = mysql_query($sql);
	while ($rcc=mysql_fetch_array($cc)) {
		
		// On envoi le mail selon le type de RDV
		$titre_mail = $mail_type[11][$rcc["client_genre"]]["titre"];
		$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
		$message_mail = $mail_type[11][$rcc["client_genre"]]["message"];
		$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
		$message_mail = str_replace("[DATE_HEURE]",format_date($rcc["rdv_date"],2,1),$message_mail);
		$message_mail = str_replace("[SHOWROOM_NOM]",$rcc["showroom_nom"],$message_mail);
		$adresse = $rcc["showroom_adr1"];
		if ($rcc["showroom_adr2"]!="")
			$adresse .= "<br>" . $rcc["showroom_adr2"];
		$adresse = $adresse;
		$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
		$message_mail = str_replace("[SHOWROOM_CP]",$rcc["showroom_cp"],$message_mail);
		$message_mail = str_replace("[SHOWROOM_VILLE]",$rcc["showroom_ville"],$message_mail);
		$message_mail = str_replace("[SHOWROOM_TEL]",$rcc["showroom_tel"],$message_mail);
		$message_mail = str_replace("[SHOWROOM_ACCES]",$rcc["showroom_acces"],$message_mail);
		
		echo $rcc["client_mail"] . "<br>";
		
		// On envoi le mail
		SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
		
		$sql = "update rendez_vous set rdv_mail_relance=1, rdv_mail_relance_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $rcc["rdv_num"] . "'";
		mysql_query($sql);		
	}
	
	
?>
