<?php include("/home/madeinpr/www/CRM/olympe-mariage/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les rendez vous à venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = date("Y-m-d", strtotime("-15 days"));
	
	/*$sql = "select distinct(c.client_num), client_mail, client_genre, client_prenom, showroom_ville, u.user_num from clients c, showrooms s, users u, commandes co, commandes_produits cp, md_produits p where c.client_num=co.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and co.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27,29) and facture_num!=0 and client_date_mariage='" . $date_debut . "'";
	$sql .= " and s.showroom_num IN (1,3,2,5)";
	echo $sql;
	$cc = mysql_query($sql);
	while ($rcc=mysql_fetch_array($cc)) {
		
		// On envoi le mail selon le type de RDV
		$titre_mail = $mail_type[16][$rcc["client_genre"]]["titre"];
		$titre_mail = str_replace("[VILLE]",$rcc["showroom_ville"],$titre_mail);
		$message_mail = $mail_type[16][$rcc["client_genre"]]["message"];
		$message_mail = str_replace("[PRENOM]",$rcc["client_prenom"],$message_mail);
				
		echo $rcc["client_mail"] . "<br>";
		
		echo $titre_mail . "<hr>" . $message_mail . "<hr>";
		
		// On envoi le mail
		SendMail($rcc["client_mail"],$titre_mail,$message_mail,$rcc["user_num"],$rcc["client_num"]);
	}*/
		
?>
