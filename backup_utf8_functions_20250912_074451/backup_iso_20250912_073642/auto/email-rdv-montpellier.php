<? include("/home/madeinpr/www/CRM/olympe-mariage/inc/param_auto.php"); 

	// Envoyer en cron tous les matins à 9h
	// On recupere les rendez vous à venir dans 3 jours pour envoyer un rappel....
	
	$date_debut = "2018-05-08 00:00:00";
		
	$sql = "select * from rendez_vous r, clients c, showrooms s, users u where r.client_num=c.client_num and c.user_num=u.user_num and c.showroom_num=s.showroom_num and rdv_date>='" . $date_debut . "' and type_num IN (1,4,5,6,7,8,9)";
	$sql .= " and s.showroom_num=1";
	echo $sql . "<br>";
	$cc = mysql_query($sql);
	while ($rcc=mysql_fetch_array($cc)) {
		echo $rcc["client_mail"] . "<br>";
	}
	
	
?>
