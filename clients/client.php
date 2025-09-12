<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 

if (!isset($tab)) {
	if ($u->mGroupe!=0)
		$tab="tab_1_1";
	else
		$tab="tab_1_6";
}

if (isset($modifier)) { // On modifie les infos 
	$remarques = str_replace("'","\'",$remarques);
	$sql = "update clients set client_genre='" . $genre . "',client_nom='" . $nom . "',client_prenom='" . $prenom . "',client_adr1='" . $adr1 . "',client_adr2='" . $adr2 . "',client_cp='" . $cp . "',client_ville='" . $ville . "',client_tel='" . $tel . "',client_mail='" . $mail . "',client_date_mariage='" . $date . "',client_lieu_mariage='" . $lieu . "',client_remarque='" . $remarques . "',connaissance_num='" . $connaissance . "',client_datemodification='" . Date("Y-m-d H:i:s") . "',poitrine='" . $poitrine . "',sous_poitrine='" . $sous_poitrine . "',taille='" . $taille . "',hanche1='" . $hanche1 . "',hanche2='" . $hanche2 . "',carrure_avant='" . $carrure_avant . "',carrure_dos='" . $carrure_dos . "',biceps='" . $biceps . "',taille_sol='" . $taille_sol . "',longueur_dos='" . $longueur_dos . "',pointure='" . $pointure . "',tour_taille='" . $tour_taille ."',interet='" . $interet . "', user_num='" . $user_suivi . "', couturiere_num='" . $couturiere . "', showroom_num='" . $showroom_modif . "' where client_num='" . decrypte($client_num) . "'";
	mysql_query($sql);
}

$sql = "select * from clients where client_num='" . decrypte($client_num) . "'";
$cl = mysql_query($sql);
if (!$rcl = mysql_fetch_array($cl)) {
	header("location:/home.php");
}

if ($rcl["client_genre"]==0)
	$genre = "Mme";
else
	$genre = "Mr";

$client_nom_complet = str_replace("'","\'",$rcl["client_nom"]) . " " . $rcl["client_prenom"];


if (isset($rdv_num)) {
	$num = decrypte($rdv_num);
	if ($num!=0) { // On efface l'ancien RDV pour le modifier
		$sql = "delete from rendez_vous where rdv_num='" . $num . "'";
		mysql_query($sql);
		
		$sql = "delete from calendriers where rdv_num='" . $num . "'";
		mysql_query($sql);
	}
	// On insere un Rendez vous
	$date_rdv = $date . " " . $time;
	$sql = "insert into rendez_vous values(0,'" . decrypte($client_num) . "','" . $type_num . "','" . $date_rdv . "','" . $remarque . "',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00','" . $u->mNum . "')";
	mysql_query($sql);
	
	$num = mysql_insert_id();
	
	// On ajoute dans le calendrier du user
	switch ($type_num) {
		case 1: // 1er RDV
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+75 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[1][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[1][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
			
		break;
		
		case 6: // 2e RDV
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+60 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "2e RDV " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[1][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[1][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
			
		break;
		
		case 8: // 3eme RDV
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+60 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "3e RDV " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[1]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[1]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
			
		break;
		
		case 7: // RDV Accessoires
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+30 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "RDV Acc. " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[1][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[1][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
			
		break;
		
		case 2: // Date de reception prévu
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[2][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[2][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE]",format_date($date,0,1),$message_mail);
			if ($remarque!="")
				$remarque = " de " . $remarque;
			$message_mail = str_replace("[REMARQUE]",$remarque,$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
		
		case 3: // Date de réception
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[3][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[3][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
		
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
		
		case 4: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "Retouche " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[14][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[14][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			// Si on est à Montpellier on envoie aussi à la couturière
			if ($rcl["showroom_num"]==1) {
				SendMail("lilietcie34@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			if ($rcl["showroom_num"]==2) {
				SendMail("margotla1982@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
		
		case 5: // RDV Remise
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "Remise " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[5][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[5][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			if ($dernier_acompte>0) {
				$sql = "select * from paiements_modes p, showrooms_paiements s where p.mode_num=s.mode_num and showroom_num='" . $rcl["showroom_num"] . "' order by mode_ordre ASC";
				$pa = mysql_query($sql);
				$nbr_paiement = mysql_num_rows($pa);
				$moyen_paiement = "";
				$nbr_mode = 0;
				while ($rpa=mysql_fetch_array($pa)) {
					$moyen_paiement .= utf8_encode($rpa["mode_nom"]);
					$nbr_mode++;
					if ($nbr_mode<$nbr_paiement) {
						if ($nbr_mode==(intval($nbr_paiement)-1))
							$moyen_paiement .=  " ou ";
						else
							$moyen_paiement .= ", ";
					}
				}
				$message_acompte = '<p>Ce rendez-vous s\'accompagne du paiement d\'un acompte de ' . $dernier_acompte . '&euro;, que vous pouvez régler par ' . $moyen_paiement . '.</p>';
				$message_mail = str_replace("[ACOMPTE_VALEUR]",utf8_decode($message_acompte),$message_mail);
			} else {
				$message_mail = str_replace("[ACOMPTE_VALEUR]","",$message_mail);
			}
						
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
		
		case 9: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "2e RDV Retouche " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[14][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[14][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = utf8_decode($adresse);
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			// Si on est à Montpellier on envoie aussi à la couturière
			if ($rcl["showroom_num"]==1) {
				SendMail("lilietcie34@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
		
		case 10: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "RDV Retouche Marseille" . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0,'" . $date_deb . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . decrypte($client_num) . "','" . $num . "')";
			mysql_query($sql);
			
			// On envoi le mail selon le type de RDV
			$titre_mail = $mail_type[15][$rcl["client_genre"]]["titre"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = $mail_type[15][$rcl["client_genre"]]["message"];
			$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
						
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
			mysql_query($sql);
		break;
	}
}

if (isset($selection_devis)) {
	// ON cherche le numero de devis 
	$devis_deb = Date("Y") * 10000;
	$sql = "select max(devis_num) val from commandes where devis_num>'" . $devis_deb . "'";
	$dd = mysql_query($sql);
	if ($rdd=mysql_fetch_array($dd)) {
		if ($rdd["val"]>0)
			$devis_num = $rdd["val"]+1;
		else
			$devis_num = $devis_deb + 1 ;
	} else {
		$devis_num = $devis_deb + 1 ;
	}
	
	// On insere le devis
	$sql = "insert into commandes values(0,'" . decrypte($client_num) . "','" . $devis_num . "','" . Date("Y-m-d H:i:s") . "','0','0000-00-00 00:00:00','0','0000-00-00 00:00:00','0','0','0','0','0','1','" . $u->mNum . "','" . $u->mShowroom . "')";
	mysql_query($sql);
	
	$id = mysql_insert_id();
	
	// ON insere les produits contenu dans la sélection
	$sql = "select * from selections_produits where selection_num='" . decrypte($selection_devis) . "'";
	$dd = mysql_query($sql);
	
	$montant_total_ht = 0;
	$montant_total_tva = 0;
	$montant_total_ttc = 0;
	
	while ($rdd=mysql_fetch_array($dd)) {
		$prixProduit = RecupPrix($rdd["produit_num"]);
		$sql = "insert into commandes_produits values ('" . $id . "','" . $rdd["produit_num"] . "','-1',1,'" . $prixProduit["montant_ht"] . "','" . $prixProduit["montant_tva"] . "','" . $prixProduit["montant_ttc"] . "','" . $prixProduit["montant_remise"] . "','" . $prixProduit["montant_remise_type"] . "','" . $prixProduit["montant_ht_remise"] . "','" . $prixProduit["montant_tva_remise"] . "','" . $prixProduit["montant_ttc_remise"] . "','0','0')";
		mysql_query($sql);
		
		if ($prixProduit["montant_remise_type"]==0) {
			$montant_total_ht += $prixProduit["montant_ht"];
			$montant_total_tva += $prixProduit["montant_tva"];
			$montant_total_ttc += $prixProduit["montant_ttc"];
		} else {
			$montant_total_ht += $prixProduit["montant_ht_remise"];
			$montant_total_tva += $prixProduit["montant_tva_remise"];
			$montant_total_ttc += $prixProduit["montant_ttc_remise"];
		}
	}
	
	// On upadte le montant
	$sql = "update commandes set commande_ht='" . $montant_total_ht . "', commande_tva='" . $montant_total_tva . "', commande_ttc='" . $montant_total_ttc . "' where id='" . $id . "'";
	mysql_query($sql);
}

if (isset($selection_envoi)) {
	// On envoi le mail à la cliente avec sa sélection
	
	$titre_mail = $mail_type[7][$rcl["client_genre"]]["titre"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$message_mail = $mail_type[7][$rcl["client_genre"]]["message"];
	$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
	$message_mail = str_replace("[SELECTION_NUM]",$selection_envoi,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "update selections set selection_envoye=1, selection_envoye_date='" . Date("Y-m-d H:i:s") . "' where selection_num='" . decrypte($selection_envoi) . "'";
	mysql_query($sql);
}

if (isset($commande_passage)) {
	// On test si toutes les tailles sont renseignées
	$sql = "select * from commandes_produits where id='" . decrypte($commande_passage) . "' and taille_num='-1'";
	$tt = mysql_query($sql);
	$nbr = mysql_num_rows($tt);
	if ($nbr==0) { // On passe la commande
		// On recupere le numero de devis pour le mettre dans commande
		$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . decrypte($commande_passage) . "'";
		$co = mysql_query($sql);
		if ($rco = mysql_fetch_array($co)) {
			$commande_num = $rco["devis_num"];
			
			// ON regarde si il y a une date de commande pour la modifier ou pas
			if ($rco["commande_date"]!="0000-00-00 00:00:00")
				$commande_modif_date = $rco["commande_date"];
			else
				$commande_modif_date = Date("Y-m-d H:i:s");
			
			// On modifie la commande
			$sql = "update commandes set commande_num='" . $commande_num . "', commande_date='" . $commande_modif_date . "' where id='" . decrypte($commande_passage) . "'";
			mysql_query($sql);
			
			$commande_modif = $commande_passage;
			
			// On regarde si un paiement comptant pour directement inséré le suivi paiement
			$commande = montantCommande($rco["id"]);
			if ($rco["paiement_nombre"]==1) {
				$echeance = explode("/",$rde["paiement_modele"]);
				if ($commande["remise"]==0) { 
					$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
				} else { 
					$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
				}
				$sql = "delete from commandes_paiements where id='" . decrypte($commande_passage) . "'";
				mysql_query($sql);
				
				// On insere le paiement
				$sql = "insert into commandes_paiements values('" . decrypte($commande_passage) . "','1','" . Date("Y-m-d H:i:s") . "','" . $montant_a_payer . "','1','',0,'0000-00-00 00:00:00')";
				mysql_query($sql);
				
				if ($rco["facture_num"]==0) {
					// ON genere le numero de facture
					$facture_deb = Date("Y") * 100000 + Date("n") * 1000;
					$sql = "select max(facture_num) val from commandes where facture_num>'" . $facture_deb . "' and showroom_num='" . $rco["showroom_num"] . "'";
					$dd = mysql_query($sql);
					if ($rdd=mysql_fetch_array($dd)) {
						if ($rdd["val"]>0)
							$facture_num = $rdd["val"]+1;
						else
							$facture_num = $facture_deb + 1 ;
					} else {
						$facture_num = $facture_deb + 1 ;
					}
					
					$sql = "update commandes set facture_num='" . $facture_num . "',facture_date='" . Date("Y-m-d H:i:s") . "' where id='" . decrypte($commande_passage) . "'";
					mysql_query($sql);
					
					// On decroit les stocks
					$sql = "select * from commandes where id='" . decrypte($commande_passage) . "'";
					$cc = mysql_query($sql);
					if ($rcc = mysql_fetch_array($cc)) {
						$showroom_num = $rcc["showroom_num"];
						// On recupere les produits de la commande pour les enlever du stock
						$sql = "select * from commandes_produits where id='" . decrypte($commande_passage) . "'";
						$co = mysql_query($sql);
						while ($rco=mysql_fetch_array($co)) {
							$sql = "select * from stocks where produit_num='" . $rco["produit_num"] . "' and taille_num='" . $rco["taille_num"] . "' and showroom_num='" . $showroom_num . "'";
							$ss = mysql_query($sql);
							if ($rss=mysql_fetch_array($ss)) {
								// On update les stocks
								$stock_virtuel = $rss["stock_virtuel"] - $rco["qte"];
								$stock_reel = $rss["stock_reel"] - $rco["qte"];
								
								$sql = "update stocks set stock_virtuel='" . $stock_virtuel . "', stock_reel='" . $stock_reel . "' where produit_num='" . $rco["produit_num"] . "' and taille_num='" . $rco["taille_num"] . "' and showroom_num='" . $showroom_num . "'";
								mysql_query($sql);
							}
						}							
					}
					
					// Si on est dans le showroom de Montpellier on decroit les stock du WEBSHOP
					if ($showroom_num==1) {
						majStockWeb($commande_passage);
					}
				}
			}
			$tab = "tab_1_4";
		}
	} else {
		$message_erreur_devis = "Vous devez renseigner toutes les tailles avant de passer la commande !";
		$devis_modif = $commande_passage;
		$tab = "tab_1_3";
	}
}

if (isset($paiement)) {
	$montant = str_replace(",",".",$montant);
	// On regarde si c'est une modification
	if ($modif=="ok") {
		$sql = "update commandes_paiements set paiement_montant='" . $montant . "', mode_num='" . $mode . "', cheque_num='" . $num . "' where id='" . decrypte($commande_modif) . "' and paiement_num='" . $echeance . "'";
		mysql_query($sql);
	} else {
		if ($nbr_echeance>1) {
			
			if ($montant<=$reste_a_payer) {
				$sql = "delete from commandes_paiements where id='" . decrypte($commande_modif) . "' and paiement_num='" . $echeance . "'";
				mysql_query($sql);
				
				// On insere le paiement
				$sql = "insert into commandes_paiements values('" . decrypte($commande_modif) . "','" . $echeance . "','" . $date . "','" . $montant . "','" . $mode . "','" . $num . "',0,'0000-00-00 00:00:00')";
				mysql_query($sql);
				
				if ($echeance==$nbr_echeance) { // Le paiement est terminé, on génére la facture
					// On regarde si il n'y a pas déjà un numero de facture 
					$sql = "select * from commandes where id='" . decrypte($commande_modif) . "'";
					$co = mysql_query($sql);
					if ($rco=mysql_fetch_array($co)) {
						if ($rco["facture_num"]==0) {
							// ON cherche le numero de facture
							$facture_deb = Date("Y") * 100000 + Date("n") * 1000;
							$sql = "select max(facture_num) val from commandes where facture_num>'" . $facture_deb . "' and showroom_num='" . $rco["showroom_num"] . "'";
							$dd = mysql_query($sql);
							if ($rdd=mysql_fetch_array($dd)) {
								if ($rdd["val"]>0)
									$facture_num = $rdd["val"]+1;
								else
									$facture_num = $facture_deb + 1 ;
							} else {
								$facture_num = $facture_deb + 1 ;
							}
							
							$sql = "update commandes set facture_num='" . $facture_num . "',facture_date='" . Date("Y-m-d H:i:s") . "' where id='" . decrypte($commande_modif) . "'";
							mysql_query($sql);
							
							// On decroit les stocks
							$sql = "select * from commandes where id='" . decrypte($commande_modif) . "'";
							$cc = mysql_query($sql);
							if ($rcc = mysql_fetch_array($cc)) {
								$showroom_num = $rcc["showroom_num"];
								// On recupere les produits de la commande pour les enlever du stock
								$sql = "select * from commandes_produits where id='" . decrypte($commande_modif) . "'";
								$co = mysql_query($sql);
								while ($rco=mysql_fetch_array($co)) {
									$sql = "select * from stocks where produit_num='" . $rco["produit_num"] . "' and taille_num='" . $rco["taille_num"] . "' and showroom_num='" . $showroom_num . "'";
									$ss = mysql_query($sql);
									if ($rss=mysql_fetch_array($ss)) {
										// On update les stocks
										$stock_virtuel = $rss["stock_virtuel"] - $rco["qte"];
										$stock_reel = $rss["stock_reel"] - $rco["qte"];
										
										$sql = "update stocks set stock_virtuel='" . $stock_virtuel . "', stock_reel='" . $stock_reel . "' where produit_num='" . $rco["produit_num"] . "' and taille_num='" . $rco["taille_num"] . "' and showroom_num='" . $showroom_num . "'";
										mysql_query($sql);
									}
								}							
							}
							
							// Si on est dans le showroom de Montpellier on decroit les stock du WEBSHOP
							if ($showroom_num==1) {
								majStockWeb($commande_passage);
							}
						}
					}
				}
			} else {
				$message_erreur_paiement = "Attention le montant de l'acompte est supérieur au reste à régler !";
			}
		} else { // La facture a déjà été réglé c'est juste une modif du mode de paiement comptant
			$sql = "update commandes_paiements set mode_num='" . $mode . "', cheque_num='" . $num . "' where id='" . decrypte($commande_modif) . "' and paiement_num='" . $echeance . "'";
			mysql_query($sql);
		}
	}
}

if (isset($suppr_rdv_num)) {
	$sql = "delete from rendez_vous where rdv_num='" . decrypte($suppr_rdv_num) . "'";
	mysql_query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from calendriers where rdv_num='" . decrypte($suppr_rdv_num) . "'";
	mysql_query($sql);
}

if (isset($selection_suppr)) {
	$sql = "delete from selections where selection_num='" . decrypte($selection_suppr) . "'";
	mysql_query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from selections_produits where selection_num='" . decrypte($selection_suppr) . "'";
	mysql_query($sql);
}

if (isset($devis_suppr)) {
	$sql = "delete from commandes where id='" . decrypte($devis_suppr) . "'";
	mysql_query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from commandes_produits where id='" . decrypte($devis_suppr) . "'";
	mysql_query($sql);
}

if (isset($commande_suppr)) {
	$sql = "update commandes set commande_num='0' where id='" . decrypte($commande_suppr) . "'";
	mysql_query($sql);
	
	$devis_modif = $commande_suppr;
	$tab = "tab_1_3";
}

if (isset($paiement_suppr)) {
	$sql = "delete from commandes_paiements where id='" . decrypte($paiement_suppr) . "' and paiement_num='" . $echeance . "'";
	mysql_query($sql);
	$commande_modif = $paiement_suppr;
}

if (isset($devis)) {
	// ON cherche le numero de devis 
	$devis_deb = Date("Y") * 10000;
	$sql = "select max(devis_num) val from commandes where devis_num>'" . $devis_deb . "'";
	$dd = mysql_query($sql);
	if ($rdd=mysql_fetch_array($dd)) {
		if ($rdd["val"]>0)
			$devis_num = $rdd["val"]+1;
		else
			$devis_num = $devis_deb + 1 ;
	} else {
		$devis_num = $devis_deb + 1 ;
	}
	
	// On créé un devis
	$sql = "insert into commandes values(0,'" . decrypte($client_num) . "','" . $devis_num . "','" . Date("Y-m-d H:i:s") . "','0','0000-00-00 00:00:00','0','0000-00-00 00:00:00','0','0','0','0','0','3','" . $u->mNum . "','" . $u->mShowroom . "')";
	mysql_query($sql);
}

if (isset($devis_envoi)) { // ON envoie le devis par mail
	// On test si toutes les tailles sont renseignées
	$sql = "select * from commandes_produits where id='" . decrypte($devis_envoi) . "' and taille_num='-1'";
	$tt = mysql_query($sql);
	$nbr = mysql_num_rows($tt);
	if ($nbr==0) { // On passe la commande
		// On envoi le mail avec le devis
		$titre_mail = $mail_type[8][$rcl["client_genre"]]["titre"];
		$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
		$message_mail = $mail_type[8][$rcl["client_genre"]]["message"];
		$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
		$message_mail = str_replace("[DEVIS_NUM]",$devis_envoi,$message_mail);
		
		$sql = "select * from commandes co, paiements p where co.paiement_num=p.paiement_num and id='" . decrypte($devis_envoi) . "'";
		$de = mysql_query($sql);
		if ($rde=mysql_fetch_array($de)) {
			$commande = montantCommande($rde["id"]);
			if ($rde["paiement_nombre"]>1) {
				$echeance = explode("/",$rde["paiement_modele"]);
				if ($commande["remise"]==0) { 
					$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
					$acompte = number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				} else { 
					$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
					$acompte = number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				}
				$message_acompte = "accompagné du paiement du premier acompte de " . $echeance[0] . "% (" . $acompte . " &euro;)";
				$message_mail = str_replace("[ACOMPTE_VALEUR]",utf8_decode($message_acompte),$message_mail);
						
				$message_suite_acompte = "</p>Pour information, nous vous demanderons ensuite les écheances de paiement suivantes : ";
								
				$echeance_desc = explode("/",$rde["paiement_description"]);
				for ($i=1;$i<$rde["paiement_nombre"];$i++) {
					$acompte_val = number_format(($montant_a_payer*($echeance[$i]/100)),2,"."," ");
					$message_suite_acompte .= $echeance[$i] .'% ' . utf8_encode($echeance_desc[$i]) . ' ('. $acompte_val . '&euro;)';
					if ($i<($rde["paiement_nombre"]-1))
						$message_suite_acompte .= " et ";
				}
				$message_suite_acompte .= ".</p>";
				$message_mail = str_replace("[ACOMPTE_SUITE]",utf8_decode($message_suite_acompte),$message_mail);
			}
		}
		
		// ON regarde si il y a une robe et si elle est sur mesure
		$sql = "select * from commandes_produits where taille_num='35' and id='" . decrypte($devis_envoi) . "'";
		$tt = mysql_query($sql);
		if ($rtt=mysql_fetch_array($tt)) {
			$message_retouche = "";
		} else {
			$message_retouche = "";
		}
		
		$message_mail = str_replace("[RETOUCHE]",utf8_decode($message_retouche),$message_mail);
		
		// On envoi le mail
		SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));

		$sql = "delete from commandes_mails where id='" . decrypte($devis_envoi) . "'";
		mysql_query($sql);
		
		$sql = "insert into commandes_mails values('" . decrypte($devis_envoi) . "','1','" . Date("Y-m-d H:i:s") . "',0,'0000-00-00 00:00:00')";
		mysql_query($sql);
	} else {
		$message_erreur_devis = "Vous devez renseigner toutes les tailles avant d'envoyer le devis !";
		$devis_modif = $devis_envoi;
		$tab = "tab_1_3";
	}
}

if (isset($facture_envoi)) { // ON envoie le devis par mail
	// On envoi le mail avec le devis
	$titre_mail = $mail_type[9][$rcl["client_genre"]]["titre"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$message_mail = $mail_type[9][$rcl["client_genre"]]["message"];
	$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
	$message_mail = str_replace("[FACTURE_NUM]",$facture_envoi,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "select * from commandes_mails where id='" . decrypte($facture_envoi) . "'";
	$tt = mysql_query($sql);
	if ($rtt=mysql_fetch_array($tt)) {
		$sql = "update commandes_mails set facture_mail=1, facture_mail_date='" . Date("Y-m-d H:i:s") . "' where id='" . decrypte($facture_envoi) . "'";
		mysql_query($sql);
	} else {
		$sql = "insert into commandes_mails values('" . decrypte($facture_envoi) . "',0,'0000-00-00 00:00:00','1','" . Date("Y-m-d H:i:s") . "')";
		mysql_query($sql);
	}
}

if (isset($acompte_envoi)) { // ON envoie le devis par mail
	// On envoi le mail avec le devis
	$titre_mail = $mail_type[10][$rcl["client_genre"]]["titre"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$titre_mail = str_replace("[PAIEMENT_NUM]",$paiement,$titre_mail);
	$message_mail = $mail_type[10][$rcl["client_genre"]]["message"];
	$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
	$message_mail = str_replace("[COMMANDE_NUM]",$acompte_envoi,$message_mail);
	$message_mail = str_replace("[PAIEMENT_NUM]",$paiement,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "update commandes_paiements set paiement_mail=1, paiement_mail_date='" . Date("Y-m-d H:i:s") . "' where id='" . decrypte($acompte_envoi) . "' and paiement_num='" . $paiement . "'";
	mysql_query($sql);
	
	$commande_modif = $acompte_envoi;
}

if (isset($selection)) {
	// On créé une sélection
	$sql = "insert into selections values(0,'" . Date("Y-m-d H:i:s") . "','0','0000-00-00','" . decrypte($client_num) . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "')";
	mysql_query($sql);
}

if (isset($cdefournisseur)) {
	// On efface la commande fournisseur en cours
	$sql = "delete from commandes_fournisseurs where id='" . decrypte($id) . "' and produit_num='" . decrypte($produit) . "'";
	mysql_query($sql);
	
	$sql = "insert into commandes_fournisseurs values('" . decrypte($id) . "','" . decrypte($produit) . "','" . $marque . "','" . $livraison . "','" . $fournisseur_commande_ref . "','" . $fournisseur_remarque . "','" . $fournisseur_poitrine . "','" . $fournisseur_sous_poitrine . "','" . $fournisseur_taille . "','" . $fournisseur_hanche1 . "','" . $fournisseur_hanche2 . "','" . $fournisseur_biceps . "','" . $fournisseur_carrure_avant . "','" . $fournisseur_carrure_dos . "','" . $fournisseur_longueur_dos . "','" . $fournisseur_taille_sol . "','" . $fournisseur_taille_choisie . "','" . $fournisseur_montant . "','" . $fournisseur_commande_date . "',0,1,'" . Date("Y-m-d H:i:s") . "')";
	mysql_query($sql);
}

if (isset($paiementfournisseur)) {
	// On efface le paiement pour le réinsérer
	$sql = "delete from commandes_fournisseurs_paiements where id='" . decrypte($id) . "' and produit_num='" . decrypte($produit) . "'";
	mysql_query($sql);
	
	$sql = "insert into commandes_fournisseurs_paiements values('" . decrypte($id) . "','" . decrypte($produit) . "','" . $fournisseur_paiement1 . "','" . $fournisseur_paiement2 . "','" . $fournisseur_paiement3 . "','" . $fournisseur_paiement1_date . "','" . $fournisseur_paiement2_date . "','" . $fournisseur_paiement3_date . "')";
	mysql_query($sql);
}

$titre_page = "Client " . $rcl["client_nom"] . " " . $rcl["client_prenom"] . " - Olympe Mariage";
$desc_page = "Client " . $rcl["client_nom"] . " " . $rcl["client_prenom"] . " - Olympe Mariage";
?>
<? 
$link_plugin = '<link href="/assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />';
include( $chemin . "/mod/head.php"); ?>
<script language="Javascript">
function confirme_annulation_rdv() {
	if (confirm("Etes vous sur de vouloir annuler ce rendez-vous ?"))
		return true;
	else 
		return false;
}

function confirme() {
	if (confirm("Etes vous sur de vouloir supprimer cette sélection ?"))
		return true;
	else 
		return false;
}

function confirmeDevis() {
	if (confirm("Etes vous sur de vouloir transformer cette sélection en devis ?"))
		return true;
	else 
		return false;
}

function confirmeSupprDevis() {
	if (confirm("Etes vous sur de vouloir supprimer ce devis ?"))
		return true;
	else 
		return false;
}

function confirmeSupprCommande() {
	if (confirm("Etes vous sur de vouloir modifier cette commande, celle-ci repassera en devis ?"))
		return true;
	else 
		return false;
}

function confirmeSupprPaiement() {
	if (confirm("Etes vous sur de vouloir supprimer ce paiement ?"))
		return true;
	else 
		return false;
}


function displayReponse(sText, place) {
	var info = document.getElementById(place);
	info.innerHTML = sText;
}

function addWidget(selection,pdt,mode) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	affichage_retour = "select_" + selection;
	
	// Mode 1 : Insertion 2 : Delete
	link = "display.php?pdt=" + pdt + "&selection=" + selection + "&mode=" + mode;

	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, affichage_retour);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, affichage_retour);
				}
		}
	};
	oXmlHttp.send(null);
}

function addPdtDevis(devis,pdt,mode) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	affichage_retour = "devis_" + devis;
	
	// Mode 1 : Insertion 2 : Delete
	link = "display.php?pdt=" + pdt + "&devis=" + devis + "&mode=" + mode;

	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, affichage_retour);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, affichage_retour);
				}
		}
	};
	oXmlHttp.send(null);
}

function modifTaille(devis,pdt,taille) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	var select = 'taille_' + pdt + '_' + taille;
	taille_new = document.getElementById(select).options[document.getElementById(select).selectedIndex].value;
			
	link = "display.php?pdt=" + pdt + "&devis=" + devis + "&taille=" + taille + "&taille_new=" + taille_new + "&mode=3";

	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					//displayReponse(oXmlHttp.responseText, affichage_retour);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					//displayReponse("Erreur : " + oXmlHttp.statusText, affichage_retour);
				}
		}
	};
	oXmlHttp.send(null);
}

function remiseCommande(devis) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	remise_montant = document.getElementById("remise_montant").value;
	remise_type = document.getElementById("remise_type").options[document.getElementById("remise_type").selectedIndex].value;
			
	link = "display.php?devis=" + devis + "&remise_montant=" + remise_montant + "&remise_type=" + remise_type + "&mode=7";
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "devis_" + devis);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "devis_" + devis);
				}
		}
	};
	oXmlHttp.send(null);
}

function remiseProduit(devis,produit,taille) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	remise_montant = document.getElementById("remise_produit_" + produit).value;
	remise_type = document.getElementById("remise_type_produit_" + produit).options[document.getElementById("remise_type_produit_" + produit).selectedIndex].value;
    taille = document.getElementById("taille_" + produit + "_" + taille).options[document.getElementById("taille_" + produit + "_" + taille).selectedIndex].value;
	
	link = "display.php?devis=" + devis + "&remise_montant=" + remise_montant + "&remise_type=" + remise_type + "&produit=" + produit + "&taille=" + taille + "&mode=8";
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "devis_" + devis);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "devis_" + devis);
				}
		}
	};
	oXmlHttp.send(null);
}

function modifPaiement(devis) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	var select = 'paiement_' + devis;
	paiement_new = document.getElementById(select).options[document.getElementById(select).selectedIndex].value;
			
	link = "display.php?devis=" + devis + "&paiement=" + paiement_new + "&mode=5";

	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "devis_" + devis);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "devis_" + devis);
				}
		}
	};
	oXmlHttp.send(null);
}

function modifDateCommande(devis) {
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	date_commande = document.getElementById("date_bdc").value;
			
	link = "display.php?devis=" + devis + "&date_commande=" + date_commande + "&mode=10";

	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					//displayReponse(oXmlHttp.responseText, "devis_" + devis);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					//displayReponse("Erreur : " + oXmlHttp.statusText, "devis_" + devis);
				}
		}
	};
	oXmlHttp.send(null);
}

function modifQte(devis,pdt,taille) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	var select = 'qte_' + pdt + '_' + taille;
	var select_taille = 'taille_' + pdt + '_' + taille;
	qte_new = document.getElementById(select).options[document.getElementById(select).selectedIndex].value;
	taille = document.getElementById(select_taille).options[document.getElementById(select_taille).selectedIndex].value;
	
	link = "display.php?pdt=" + pdt + "&devis=" + devis + "&taille=" + taille + "&qte_new=" + qte_new + "&mode=4";
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "devis_" + devis);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "devis_" + devis);
				}
		}
	};
	oXmlHttp.send(null);
}

function commandeFournisseur(id) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	var select = 'fournisseur_' + id;
	var val = 0;
	if (document.getElementById(select).checked==1)
		val = 1;	
	
	link = "display.php?id=" + id + "&val=" + val + "&mode=9";
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "fournisseur_date_" + id);
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "fournisseur_date_" + id);
				}
		}
	};
	oXmlHttp.send(null);
}

function confirme_commande(id) {
   paiement = document.getElementById("paiement_" + id).options[document.getElementById("paiement_" + id).selectedIndex].text;
   message = "Voulez vous passer le devis en commande avec le mode de paiement suivant : " + paiement + " ?";
   if (confirm(message)) {
	   return true;
   } else
		return false;
}
</script>

    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <? include( $chemin . "/mod/top.php"); ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1><? echo $rcl["client_nom"] . " " . $rcl["client_prenom"] ?></h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Client <? echo $rcl["client_nom"] . " " . $rcl["client_prenom"] ?></li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
						<!-- BEGIN PAGE BASE CONTENT -->
						<div class="row">
							<div class="col-md-12">
								<!-- BEGIN PROFILE SIDEBAR -->
								<div class="profile-sidebar">
									<!-- PORTLET MAIN -->
									<div class="portlet light profile-sidebar-portlet bordered">
										<!-- SIDEBAR USER TITLE -->
										<div class="profile-usertitle">
											<div class="profile-usertitle-name"> <? echo $rcl["client_nom"] . " " . $rcl["client_prenom"] ?> </div>
											<div class="profile-usertitle-job"> <? if ($rcl["client_genre"]==0) echo "Femme"; else echo "Homme"; ?> </div>
										</div>
										<!-- SIDEBAR MENU -->
										<div class="profile-usermenu">
											<ul class="nav">
												<li>
													<a href="mailto:<? echo $rcl["client_email"] ?>">
														<i class="fa fa-envelope"></i> <? echo $rcl["client_mail"] ?> </a>
												</li>
												<li>
													<a href="tel:<? echo $rcl["client_tel"] ?>">
														<i class="fa fa-phone"></i> <? echo $rcl["client_tel"] ?> </a>
												</li>
												<li>
													<a href="#">
														<i class="fa fa-heart"></i> <? echo format_date($rcl["client_date_mariage"],11,1) ?> </a>
												</li>
												<li>
													<a href="#">
														<i class="fa fa-map-marker"></i> <? echo $rcl["client_lieu_mariage"]  ?> </a>
												</li>
												<? 
													$sql = "select * from users where user_num='" . $rcl["user_num"] . "'";
													$tt = mysql_query($sql);
													if ($rtt=mysql_fetch_array($tt)) {
														echo '<li>
															<a href="#">
																<i class="fa fa-eye"></i> Suivi par : ' . $rtt["user_prenom"] . ' ' . $rtt["user_nom"]  . '</a>
														</li>';
													}
												?>
												<? 
													$sql = "select * from users where user_num='" . $rcl["couturiere_num"] . "'";
													$tt = mysql_query($sql);
													if ($rtt=mysql_fetch_array($tt)) {
														echo '<li>
															<a href="#">
																<i class="fa fa-odnoklassniki"></i> Couturière : ' . $rtt["user_prenom"] . ' ' . $rtt["user_nom"]  . '</a>
														</li>';
													}
												?>
											</ul>
										</div>
										<!-- END MENU -->
										<!-- END SIDEBAR USER TITLE -->
									</div>
									<!-- END PORTLET MAIN -->
									<!-- PORTLET MAIN -->
									<?
											$sql = "select * from selections where client_num='" . decrypte($client_num) . "' order by selection_date DESC";
											$ss = mysql_query($sql);
											$nbr_selection = mysql_num_rows($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num) . "' and devis_num>0 and commande_num=0 and facture_num=0 order by commande_date DESC";
											$ss = mysql_query($sql);
											$nbr_devis = mysql_num_rows($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num) . "' and devis_num>0 and commande_num>0 order by commande_date DESC";
											$ss = mysql_query($sql);
											$nbr_commande = mysql_num_rows($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num) . "' and devis_num>0 and commande_num>0 order by commande_date DESC";
											$ss = mysql_query($sql);
											$commande_ttc = 0;
											while ($rss=mysql_fetch_array($ss)) 
												$commande_ttc += montantCommandeTTC($rss["id"]);
									?>
									<div class="portlet light bordered">
										<!-- STAT -->
										<div class="row list-separated profile-stat">
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <? echo $nbr_selection ?> </div>
												<div class="uppercase profile-stat-text"> Sél. </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <? echo $nbr_devis ?> </div>
												<div class="uppercase profile-stat-text"> Devis </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <? echo $nbr_commande ?> </div>
												<div class="uppercase profile-stat-text"> Com. </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <? echo $commande_ttc ?>€ </div>
												<div class="uppercase profile-stat-text"> C.A. </div>
											</div>
										</div>
										<!-- END STAT -->
									</div>
									<!-- END PORTLET MAIN -->
								</div>
								<!-- END BEGIN PROFILE SIDEBAR -->
								<!-- BEGIN PROFILE CONTENT -->
								<div class="profile-content">
									<div class="row">
										<div class="col-md-12">
											<div class="portlet light bordered">
												<div class="portlet-title tabbable-line">
													<ul class="nav nav-tabs">
													<? if ($u->mGroupe!=0) { ?>
														<li<? if ($tab=="tab_1_1") echo ' class="active"'?>>
															<a href="#tab_1_1" data-toggle="tab">Prise de RDV</a>
														</li>
														<li<? if ($tab=="tab_1_2") echo ' class="active"'?>>
															<a href="#tab_1_2" data-toggle="tab">Selection</a>
														</li>
														<li<? if ($tab=="tab_1_3") echo ' class="active"'?>>
															<a href="#tab_1_3" data-toggle="tab">Devis</a>
														</li>
														<li<? if ($tab=="tab_1_4") echo ' class="active"'?>>
															<a href="#tab_1_4" data-toggle="tab">Commande</a>
														</li>
													<? } ?>
														<li<? if ($tab=="tab_1_6") echo ' class="active"'?>>
															<a href="#tab_1_6" data-toggle="tab">Modifier</a>
														</li>
													</ul>
												</div>
												<div class="portlet-body">
													<div class="tab-content">
														<? if ($u->mGroupe!=0) { ?>
														<div class="tab-pane<? if ($tab=="tab_1_1") echo " active"?>" id="tab_1_1">
														<? 
															$sql = "select * from rdv_types order by type_pos ASC";
															$tt = mysql_query($sql);
															while ($rtt=mysql_fetch_array($tt)) { 
																// On test si on a déjà rentré dans la base le RDV
																$sql = "select * from rendez_vous where client_num='" . decrypte($client_num) . "' and type_num='" . $rtt["type_num"] . "'";
																$cc = mysql_query($sql);
																$etat=0;
																$num=0;
																$remarque = "";
																$mail = 0;
																$mail_relance = 0;
																$date = "";
																$heure = "";
																if ($rcc=mysql_fetch_array($cc)) {
																	$etat=1;
																	$num = $rcc["rdv_num"];
																	$date = format_date($rcc["rdv_date"],7,1);
																	$heure = format_date($rcc["rdv_date"],12,1);
																	$remarque = $rcc["rdv_remarques"];
																	$mail = $rcc["rdv_mail"];
																	$mail_date = $rcc["rdv_mail_date"];
																	$mail_relance = $rcc["rdv_mail_relance"];
																	$mail_relance_date = $rcc["rdv_mail_relance_date"];
																}
															?>
																<form name="ajouter" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
																<input type="hidden" name="tab" value="tab_1_1">
																<input type="hidden" name="client_num" value="<? echo $client_num ?>">
																<input type="hidden" name="type_num" value="<? echo $rtt["type_num"] ?>">
																<input type="hidden" name="rdv_num" value="<? echo crypte($num) ?>">
																<table class="table table-hover table-advance table-striped">
																	<thead>
																		<tr>
																			<th class="font-blue-steel"><strong><? echo utf8_encode($rtt["type_nom"]) ?></strong></th>
																			<th> </th>
																			<th> </th>
																			<th> </th>
																		</tr>
																	</thead>
																	<tbody>
																	<tr>
																		<td><input type="date" name="date" class="form-inline" placeholder="" value="<? echo $date ?>">
																			<input type="time" name="time" class="form-inline" placeholder="" value="<? echo $heure ?>">
																		</td>
																		<td>
																			<?	
																				if ($rtt["type_num"]==2) {
																					echo 'Atelier de <input type="text" name="remarque" value="' . $remarque . '" class="form-inline">';
																					echo '<input type="hidden" name="dernier_acompte" value="0">';
																				} else {
																					echo '<input type="hidden" name="remarque" value="">';
																					if ($rtt["type_num"]==5) {
																						// On recherche les commandes en cours non facturée
																						$sql = "select * from commandes where client_num='" . decrypte($client_num) . "' and devis_num!=0 and commande_num!=0 and facture_num=0 order by commande_date DESC";
																						$co = mysql_query($sql);
																						$nbr_commande = mysql_num_rows($co);
																						if ($nbr_commande>0) {
																							echo '<select name="dernier_acompte" class="form-control">';
																							while ($rco=mysql_fetch_array($co)) {
																								$dernier_acompte = number_format(resteAPayerCommande($rco["id"]),2,"."," ");
																								echo '<option value="' . $dernier_acompte . '">Commande : ' . $rco["commande_num"] . ' - Acompte : ' . $dernier_acompte . ' €</option>';
																							}
																							echo '</select>';
																						} else
																							echo '<input type="hidden" name="dernier_acompte" value="0">';
																					} else 
																						echo '<input type="hidden" name="dernier_acompte" value="0">';
																				}
																			?>
																		</td>
																		<td>
																			<? if ($etat==0) { ?>
																				<input type="submit" value="Ok" class="btn btn-outline btn-circle btn-sm purple">
																			<? } else { ?>
																				<input type="submit" value="Modifier" class="btn btn-outline btn-circle btn-sm purple"> 
																				<a href="client.php?client_num=<? echo crypte($rcc["client_num"]) ?>&suppr_rdv_num=<? echo crypte($num) ?>"  class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme_annulation_rdv()"> Annuler</a>
																			<? } ?>
																		</td>
																		<td>
																			<? 
																				if (($mail==1) && ($mail_date!="0000-00-00 00:00:00")) { echo '<small><strong>Mail envoyé le : </strong>' . utf8_encode(format_date($mail_date,2,1)) . '</small>';}
																				if (($mail_relance==1) && ($mail_relance_date!="0000-00-00 00:00:00")) { echo '<br><small><strong>Mail relance envoyé le : </strong>' . utf8_encode(format_date($mail_date,2,1)) . '</small>';}
																			?>
																		</td>
																	</tr>
																	</tbody>
																</table>
																</form>
														<?	} ?>
														</div>
														<!-- END CHANGE AVATAR TAB -->
														<!-- CHANGE PASSWORD TAB -->
														<div class="tab-pane<? if ($tab=="tab_1_2") echo " active"?>" id="tab_1_2">
															<h4><i class="fa fa-plus"></i> Liste des sélections</h4>
															<?
																$sql = "select * from selections where client_num='" . decrypte($client_num) . "' order by selection_date DESC";
																$ss = mysql_query($sql);
																$nbr_selection = mysql_num_rows($ss);
																if ($nbr_selection>0) {
																	echo '<table class="table table-bordered table-striped">
																			<thead>
																				<th>Date</th>
																				<th>Sélection</th>
																				<th></th>
																			</thead>
																			<tbody>';
																	while ($rss=mysql_fetch_array($ss)) {
																		echo '<tr>
																				<td>' . format_date($rss["selection_date"],11,1) . '</td>
																				<td id="select_' . $rss["selection_num"] . '">
																					<div class="mt-element-card mt-element-overlay">';
																		// On affiche les produits sélectionnés
																		$sql = "select * from selections_produits s, md_produits p where s.produit_num=p.produit_num and selection_num='" . $rss["selection_num"] . "'";
																		$pp = mysql_query($sql);
																		$nbr_pp = mysql_num_rows($pp);
																		if ($nbr_pp>0) {
																			while ($rpp=mysql_fetch_array($pp)) {
																				$sql = "select * from md_produits_photos where produit_num='" . $rpp["produit_num"] . "' and photo_pos=1";
																				$ph = mysql_query($sql);
																				if ($rph=mysql_fetch_array($ph)) {
																					$image_pdt = "/photos/produits/min/" . $rph["photo_chemin"];
																				} else 
																					$image_pdt = "http://www.placehold.it/50x50/EFEFEF/AAAAAA&amp;text=no+image";
																				echo '<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
																						<div class="mt-card-item">
																							<div class="mt-card-avatar mt-overlay-1">
																								<figure style="height:100px;overflow:hidden;position:relative;line-height:100px;">
																									<img src="' . $image_pdt . '" />
																								</figure>
																								<div class="mt-overlay">
																									<ul class="mt-info">
																										<li>
																											<a class="btn default btn-outline" href="javascript:addWidget(' . $rss["selection_num"] . ',' . $rpp["produit_num"] . ',2)">
																												<i class="fa fa-trash"></i>
																											</a>
																										</li>
																									</ul>
																								</div>
																							</div>
																							<div class="mt-card-content">
																								<h5><small>' . $rpp["produit_nom"] . '</small></h5>
																							</div>
																						</div>
																					</div>';
																			}
																		} else {
																			echo '<p><i>Aucun produit dans votre sélection</i></p>';
																		}
																		echo '		</div>
																				</td>
																				<td>
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&selection_ajout=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-plus"></i> Ajouter</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&selection_envoi=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&selection_devis=' . crypte($rss["selection_num"]) . '&tab=tab_1_3" onClick="return confirmeDevis()" class="btn btn-outline btn-circle dark btn-sm purple"><i class="fa fa-euro"></i> Devis</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&selection_suppr=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" onClick="return confirme()" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>';
																		if ($rss["selection_envoye"]==1) echo "<hr><p><small>Envoyée par mail le : <strong>" . format_date($rss["selection_envoye_date"],0,1) . "</strong></small></p>";
																		echo '	</td>';
																	}
																	echo '	</tbody>
																		  </table>';
																} else {
																	echo '<p><i>Aucune sélection</i></p>';
																}
															?>
															<hr>
															<? if (!isset($selection_ajout)) { ?>
																<center><a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&selection=ok&tab=tab_1_2" class="btn btn-lg red"> <i class="fa fa-plus"></i> Créer une sélection</a></center>
															<? } else { ?>
																<h4><i class="fa fa-plus"></i> Ajouter des produits à la sélection</h4>
																<div class="row">
																	<div class="col-md-4">
																		<form name="rechercher" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
																			<input type="hidden" name="recherche_produit" value="ok">
																			<input type="hidden" name="client_num" value="<? echo $client_num ?>">
																			<input type="hidden" name="selection_ajout" value="<? echo $selection_ajout ?>">
																			<input type="hidden" name="tab" value="tab_1_2">
																			<table class="table table-striped table-bordered table-advance table-hover">
																				<tbody>
																					<tr>
																						<td><label>Nom</label>
																						<div class="input-group">
																							<span class="input-group-addon">
																								<i class="fa fa-list"></i>
																							</span>
																							<input type="text" name="nom" class="form-control" value="<? echo $nom ?>"></div></td>
																					</tr>
																					<tr>
																						<td><label>Categorie</label>
																						<div class="input-group">
																							<select name="categorie">
																							<option value="0">-----------------</option>
																							<? 
																							$sql = "select * from categories order by categorie_nom ASC";
																							$cc = mysql_query($sql);
																							while ($rcc=mysql_fetch_array($cc))
																							{
																								echo "<option value=\"" . $rcc["categorie_num"] . "\"";
																								if ($categorie==$rcc["categorie_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["categorie_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><label>Marques</label>
																						<div class="input-group">
																							<select name="marque">
																							<option value="0">-----------------</option>
																							<? 
																							$sql = "select * from marques order by marque_nom ASC";
																							$cc = mysql_query($sql);
																							while ($rcc=mysql_fetch_array($cc))
																							{
																								echo "<option value=\"" . $rcc["marque_num"] . "\"";
																								if ($marque==$rcc["marque_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["marque_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><input type="submit" value="Rechercher" class="btn blue"> <a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_2" class="btn red">Annuler</a></td>
																					</tr>
																				</tbody>
																			</table>									
																		</form>
																	</div>
																	<div class="col-md-8">
																		<div class="mt-element-card mt-element-overlay">
																			<div class="row">
																			<? if (isset($recherche_produit)) { 
																					$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and produit_etat='1'";
																					if ($categorie!=0)
																						$sql .= " and p.categorie_num='" . $categorie . "'";
																					if ($marque!=0)
																						$sql .= " and p.marque_num='" . $marque . "'";
																					if ($nom!="") {
																						$nom = str_replace("'","\'",$nom);
																						$sql .= " and produit_nom like '%" . $nom . "%'";
																					}
																					$sql .= " order by categorie_nom ASC, produit_nom ASC";
																					$cc = mysql_query($sql);
																					$nbr_produit = mysql_num_rows($cc);
																					if ($nbr_produit>0) {
																						while ($rcc=mysql_fetch_array($cc)) { 
																							$sql = "select * from md_produits_photos where produit_num='" . $rcc["produit_num"] . "' and photo_pos=1";
																							$pp = mysql_query($sql);
																							if ($rpp=mysql_fetch_array($pp)) {
																								$image_pdt = "/photos/produits/min/" . $rpp["photo_chemin"];
																							} else 
																								$image_pdt = "http://www.placehold.it/200x200/EFEFEF/AAAAAA&amp;text=no+image";
																							//echo '<div class="col-md-3"><a href=""><figure><figcaption>' . $rcc["produit_nom"] . '</figcaption><img src="' . $image_pdt . '" class="img-responsive"></figure></div>';
																							echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
																								<div class="mt-card-item">
																									<div class="mt-card-avatar mt-overlay-1">
																										<figure style="height:200px;overflow:hidden;position:relative;line-height:200px;">
																											<img src="' . $image_pdt . '" />
																										</figure>
																										<div class="mt-overlay">
																											<ul class="mt-info">
																												<li>
																													<a class="btn default btn-outline" href="javascript:addWidget(' . decrypte($selection_ajout) . ',' . $rcc["produit_num"] . ',1)">
																														<i class="fa fa-plus"></i>
																													</a>
																												</li>
																											</ul>
																										</div>
																									</div>
																									<div class="mt-card-content">
																										<h5>' . $rcc["produit_nom"] . '</h5>
																									</div>
																								</div>
																							</div>';
																						}		
																					} else { 
																						echo '<p><i>Pas de résultat !!</i></p>';
																					}
																				} 
																			?>
																			</div>
																		</div>
																	</div>
																</div>
															<? } ?>
														</div>
														<!-- END CHANGE PASSWORD TAB -->
														<!-- PRIVACY SETTINGS TAB -->
														<div class="tab-pane<? if ($tab=="tab_1_3") echo " active"?>" id="tab_1_3">
															<h4><i class="fa fa-plus"></i> Liste des devis en cours</h4>
															<?
																$sql = "select * from commandes where devis_num!=0 and commande_num=0 and facture_num=0 and client_num='" . decrypte($client_num) . "' order by devis_date DESC";
																$ss = mysql_query($sql);
																$nbr_devis = mysql_num_rows($ss);
																if ($nbr_devis>0) {
																	echo '<table class="table table-bordered table-striped">
																			<thead>
																				<th>N°</th>
																				<th>Date</th>
																				<th>Nbr Produit</th>
																				<th>Montant TTC</th>
																				<th> </th>
																				<th> </th>
																			</thead>
																			<tbody>';
																	while ($rss=mysql_fetch_array($ss)) {
																		$sql = "select * from commandes_produits where id='" . $rss["id"] . "'";
																		$pp = mysql_query($sql);
																		$nbr_produit = mysql_num_rows($pp);
																		
																		echo '<tr>
																				<td>' . $rss["devis_num"] . '</td>
																				<td>' . format_date($rss["devis_date"],11,1) . '</td>
																				<td>' . $nbr_produit . '</td>
																				<td>' . number_format(montantCommandeTTC($rss["id"]),2) . ' €</td>
																				<td class="text-center"> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&devis_modif=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-plus"></i> Modifier</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&devis_consulte=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm green"><i class="fa fa-book"></i> Consulter</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&devis_envoi=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer</a> 
																					<a href="#" onClick="window.open(\'/devis/index.php?devis=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Imprimer</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&devis_suppr=' . crypte($rss["id"]) . '&tab=tab_1_3" onClick="return confirmeSupprDevis()" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>
																				</td>
																				<td>';
																				$sql = "select * from commandes_mails where id='" . $rss["id"] . "' and devis_mail=1";
																				$dm = mysql_query($sql);
																				if ($rdm = mysql_fetch_array($dm)) {
																					echo '<small><strong>Devis envoyé le : </strong>' . utf8_encode(format_date($rdm["devis_mail_date"],2,1)) . '</small>';
																				}
																		echo '	</td>
																			</tr>';
																	}
																	echo '	</tbody>
																		</table>';
																} else {
																	echo '<p><i>Aucun devis en cours</i></p>';
																}
															?>
															<? if ((!isset($devis_ajout)) && (!isset($devis_modif)) && (!isset($devis_consulte)))  { ?>
																<center><a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&devis=ok&tab=tab_1_3" class="btn btn-lg red"> <i class="fa fa-plus"></i> Créer un devis</a></center>
															<? } ?>
															
															<? if (isset($devis_consulte)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . decrypte($devis_consulte) . "'";
																	$cc = mysql_query($sql);
																	if ($rcc=mysql_fetch_array($cc)) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Devis n° : <? echo $rcc["devis_num"] ?></h4>
																<table class="table table-bordered table-striped">
																	<thead>
																		<th colspan="2">Produit</th>
																		<th>Taille</th>
																		<th>Prix Unitaire</th>
																		<th><center>Qte</center></th>
																		<th><center>Remise</center></th>
																		<th>Montant</th>
																	</thead>
																	<tbody>
															<? 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($devis_consulte) . "'";
																		$pp = mysql_query($sql);
																		while ($rpp=mysql_fetch_array($pp)) {
																			$image_pdt = RecupPhotoProduit($rpp["produit_num"]);
																			$prix_total_ttc = $rpp["montant_ttc"]*$rpp["qte"];
																			//$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
																			switch ($rpp["commande_produit_remise_type"]) {
																				case 1: // Remise en %
																					$prix_total_ttc = $prix_total_ttc*(1-($rpp["commande_produit_remise"]/100));
																				break;
																				
																				case 2: // Remise en euro
																					$prix_total_ttc = $prix_total_ttc - $rpp["commande_produit_remise"];
																				break;
																			}			
																			echo '<tr>
																				<td><img src="' . $image_pdt["min"] . '" style="width:90px"/></td>
																				<td>' . $rpp["categorie_nom"] . '<br>' . $rpp["marque_nom"] . '<br><strong>' . $rpp["produit_nom"] . '</strong></td>
																				<td>' . $rpp["taille_nom"] . '</td>
																				<td>' . $rpp["montant_ttc"] . '</td>
																				<td align="center">' . $rpp["qte"] . '</td>
																				<td align="center">';
																				if ($rpp["commande_produit_remise_type"]!=0) {
																					if ($rpp["commande_produit_remise_type"]==1)
																						echo $rpp["commande_produit_remise"] . '%';
																					else
																						echo '-' . $rpp["commande_produit_remise"] . '€';
																				} else {
																					echo ' ';
																				}																				
																				echo '</td>
																				<td>';
																				if (number_format($prix_total_ttc,2)<=0)
																					echo "OFFERT";
																				else
																					echo number_format($prix_total_ttc,2) . ' €';
																				echo '</td>
																			</tr>';
																		} ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total HT</strong></td>
																			<td><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>TVA (20%)</strong></td>
																			<td><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</td>
																		</tr>
																		<? if ($commande["remise"]==0) { 
																				$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<? } else { 
																				$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="6" align="right"><strong>Remise</strong></td>
																			<td><? echo $commande["remise"] ?></td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>Total à payer</strong></td>
																			<td><? echo number_format($commande["commande_remise_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<? } ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Méthode de paiement</strong></td>
																			<td><? echo utf8_encode($rcc["paiement_titre"]) ?></td>
																		</tr>
																		<?
																			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				$echeance = explode("/",$rcc["paiement_modele"]);
																				$acompte_num = 1;
																				foreach ($echeance as $val) {
																					$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
																					echo '<tr>
																							<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																							<td>' . $acompte_val . ' €</td>
																						</tr>';
																					$acompte_num++;
																				}
																			}
																		?>																		
																		<tr><td colspan="7" align="right"><a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_3" class="btn red">Fermer</a></td></tr>
																	</tbody>
																</table>
															<?		}
																} ?>
															
															<? if (isset($devis_modif)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . decrypte($devis_modif) . "'";
																	$cc = mysql_query($sql);
																	if ($rcc=mysql_fetch_array($cc)) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Devis n° : <? echo $rcc["devis_num"] ?> <? if ($message_erreur_devis!="") echo ' - <i class="fa fa-warning"></i> <font class="font-red-thunderbird"><strong>' . $message_erreur_devis . '</strong></font>'; ?></h4>
																<table class="table table-bordered table-striped">
																	<thead>
																		<th colspan="2">Produit</th>
																		<th>Taille</th>
																		<th>Prix Unitaire</th>
																		<th><center>Qte</center></th>
																		<th>Montant</th>
																		<th>Remise</th>
																	</thead>
																	<tbody id="devis_<? echo $rcc["id"] ?>">
															<? 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($devis_modif) . "'";
																		$pp = mysql_query($sql);
																		while ($rpp=mysql_fetch_array($pp)) {
																			$image_pdt = RecupPhotoProduit($rpp["produit_num"]);
																			$prix_total_ttc = $rpp["montant_ttc"]*$rpp["qte"];
																			//$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
																			
																			switch ($rpp["commande_produit_remise_type"]) {
																				case 1: // Remise en %
																					$prix_total_ttc = $prix_total_ttc*(1-($rpp["commande_produit_remise"]/100));
																				break;
																				
																				case 2: // Remise en euro
																					$prix_total_ttc = $prix_total_ttc - $rpp["commande_produit_remise"];
																				break;
																			}			
																			
																			// On verifie les stocke pour chaque produit
																			$sql = "select * from stocks where taille_num=" . $rpp["taille_num"] . " and produit_num=" . $rpp["produit_num"] . " and showroom_num='" . $u->mShowroom . "'";
																			$ss = mysql_query($sql);
																			if ($rss=mysql_fetch_array($ss)) {
																				$stock = $rss["stock_virtuel"];
																			}
																			else { // Pour tester tant qu'il n'y a pas de stock, on met 10...
																				$stock = 10;
																			}
																			echo '<tr>
																				<td><img src="' . $image_pdt["min"] . '" style="width:90px"/></td>
																				<td>' . $rpp["categorie_nom"] . '<br>' . $rpp["marque_nom"] . '<br><strong>' . $rpp["produit_nom"] . '</strong></td>
																				<td><select name="taille_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" id="taille_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" onChange="modifTaille(' . decrypte($devis_modif) . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"] . ');">';
																			echo '<option value="-1">A renseigner</option>';	
																			$sql = "select * from tailles t, categories_tailles c where t.taille_num=c.taille_num and c.categorie_num=" . $rpp["categorie_num"];
																			$ss = mysql_query($sql);
																			while ($st = mysql_fetch_array($ss)) {
																				echo '<option value="' . $st["taille_num"] . '"';
																				if ($st["taille_num"]==$rpp["taille_num"])
																					echo " SELECTED";
																				echo '>' . $st["taille_nom"] . '</option>';
																			}
																			echo '</select></td>
																				<td>' . number_format($rpp["montant_ttc"],2,"."," ") . ' €</td>
																				<td align="center"><select name="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" id="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" onChange="modifQte(' . decrypte($devis_modif) . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"] . ');">';
																				for ($i=0;$i<=$stock;$i++) {
																					echo '<option value="' . $i . '"';
																					if ($i==$rpp["qte"])
																						echo " SELECTED";
																					echo '>' . $i . '</option>';
																				}
																			echo '</select></td>
																				<td>';
																					if (number_format($prix_total_ttc,2)<=0)
																						echo "OFFERT";
																					else
																						echo number_format($prix_total_ttc,2,"."," ") . ' €';
																			echo '</td>	
																				<td><input type="text" name="remise_produit_' . $rpp["produit_num"] . '" id="remise_produit_' . $rpp["produit_num"] . '" value="' . $rpp["commande_produit_remise"] . '" class="form-inline input-xsmall"> 
																				<select name="remise_type_produit_' . $rpp["produit_num"] . '" id="remise_type_produit_' . $rpp["produit_num"] . '" class="form-inline input-xsmall" onChange="remiseProduit(' . $rcc["id"] . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"]  . ')">
																					<option value="0">--</option>
																					<option value="1"';
																				if ($rpp["commande_produit_remise_type"]==1) echo " SELECTED"; 
																					echo '>%</option>
																					<option value="2"';
																				if ($rpp["commande_produit_remise_type"]==2) echo " SELECTED";
																					echo '>€</option>
																				</select>	
																				</td>
																			</tr>';
																		} ?>
																		<tr>
																			<td colspan="5" align="right"><strong>Total HT</strong></td>
																			<td colspan="2"><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>TVA (20%)</strong></td>
																			<td colspan="2"><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>Total TTC</strong></td>
																			<td colspan="2"><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="5" align="right"><strong>Remise</strong></td>
																			<td colspan="2"><input type="text" name="remise_montant" id="remise_montant" value="<? echo $commande["commande_remise"] ?>" class="form-inline input-xsmall"> 
																				<select name="remise_type" id="remise_type" class="form-inline input-xsmall" onChange="remiseCommande(<? echo $rcc["id"]?>)">
																					<option value="0">--</option>
																					<option value="1"<? if ($commande["commande_remise_type"]==1) echo " SELECTED"; ?>>%</option>
																					<option value="2"<? if ($commande["commande_remise_type"]==2) echo " SELECTED"; ?>>€</option>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>Total à payer</strong></td>
																			<td colspan="2"><? 
																				if ($commande["commande_remise_type"]!=0) {
																					echo number_format($commande["commande_remise_ttc"],2,"."," ");
																					$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
																				}
																				else {
																					echo number_format($commande["commande_ttc"],2,"."," ");
																					$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
																				}
																				?> €
																			</td>
																		</tr>	
																		<tr>
																			<td colspan="5" align="right"><strong>Méthode de paiement</strong></td>
																			<td colspan="2">
																				<select name="paiement_<? echo $rcc["id"] ?>" id="paiement_<? echo $rcc["id"] ?>" onChange="modifPaiement(<? echo $rcc["id"] ?>)">
																				<?
																					$sql = "select * from paiements order by paiement_pos ASC";
																					$pp = mysql_query($sql);
																					while ($rpp=mysql_fetch_array($pp)) {
																						echo '<option value="' . $rpp["paiement_num"] . '"';
																						if ($rpp["paiement_num"]==$rcc["paiement_num"])
																							echo " SELECTED";
																						echo '>' . utf8_encode($rpp["paiement_titre"]) . '</option>';
																					}
																				?>
																				</select>
																			</td>
																		</tr>
																		<? if ($rcc["commande_date"]!="0000-00-00 00:00:00") {// On met la modification
																				$date_bdc_commande = substr($rcc["commande_date"],0,10);

																		?>
																		<tr>
																			<td colspan="5" align="right"><strong>Date de commande</strong></td>
																			<td colspan="2">
																				<input type="date" name="date_bdc" id="date_bdc" value="<? echo $date_bdc_commande ?>" onChange="modifDateCommande(<? echo $rcc["id"]?>)">
																			</td>
																		</tr>
																		<? } ?>
																		<?
																			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				$echeance = explode("/",$rcc["paiement_modele"]);
																				$acompte_num = 1;
																				foreach ($echeance as $val) {
																					$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
																					echo '<tr>
																							<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																							<td>' . $acompte_val . ' €</td>
																						</tr>';
																					$acompte_num++;
																				}
																			}
																		?>
																		<tr><td colspan="7" align="right"><a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_3" class="btn red">Fermer</a> <a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_4&commande_passage=<? echo crypte($rcc["id"]) ?>" class="btn blue" onClick="return confirme_commande(<? echo $rcc["id"] ?>)">Passer la commande</a></td></tr>
																	</tbody>
																</table>
															<?		} ?>
																<h4><i class="fa fa-plus"></i> Ajouter des produits au devis</h4>
																<div class="row">
																	<div class="col-md-4">
																		<form name="rechercher" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
																			<input type="hidden" name="recherche_produit" value="ok">
																			<input type="hidden" name="client_num" value="<? echo $client_num ?>">
																			<input type="hidden" name="devis_modif" value="<? echo $devis_modif ?>">
																			<input type="hidden" name="tab" value="tab_1_3">
																			<table class="table table-striped table-bordered table-advance table-hover">
																				<tbody>
																					<tr>
																						<td><label>Nom</label>
																						<div class="input-group">
																							<span class="input-group-addon">
																								<i class="fa fa-list"></i>
																							</span>
																							<input type="text" name="nom" class="form-control" value="<? echo $nom ?>"></div></td>
																					</tr>
																					<tr>
																						<td><label>Categorie</label>
																						<div class="input-group">
																							<select name="categorie">
																							<option value="0">-----------------</option>
																							<? 
																							$sql = "select * from categories order by categorie_nom ASC";
																							$cc = mysql_query($sql);
																							while ($rcc=mysql_fetch_array($cc))
																							{
																								echo "<option value=\"" . $rcc["categorie_num"] . "\"";
																								if ($categorie==$rcc["categorie_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["categorie_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><label>Marques</label>
																						<div class="input-group">
																							<select name="marque">
																							<option value="0">-----------------</option>
																							<? 
																							$sql = "select * from marques order by marque_nom ASC";
																							$cc = mysql_query($sql);
																							while ($rcc=mysql_fetch_array($cc))
																							{
																								echo "<option value=\"" . $rcc["marque_num"] . "\"";
																								if ($marque==$rcc["marque_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["marque_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><input type="submit" value="Rechercher" class="btn blue"> <a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_3" class="btn red">Annuler</a></td>
																					</tr>
																				</tbody>
																			</table>									
																		</form>
																	</div>
																	<div class="col-md-8">
																		<div class="mt-element-card mt-element-overlay">
																			<div class="row">
																			<? if (isset($recherche_produit)) { 
																					$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and produit_etat='1'";
																					if ($categorie!=0)
																						$sql .= " and p.categorie_num='" . $categorie . "'";
																					if ($marque!=0)
																						$sql .= " and p.marque_num='" . $marque . "'";
																					if ($nom!="") {
																						$nom = str_replace("'","\'",$nom);
																						$sql .= " and produit_nom like '%" . $nom . "%'";
																					}
																					$sql .= " order by categorie_nom ASC, produit_nom ASC";
																					$cc = mysql_query($sql);
																					$nbr_produit = mysql_num_rows($cc);
																					if ($nbr_produit>0) {
																						while ($rcc=mysql_fetch_array($cc)) { 
																							$sql = "select * from md_produits_photos where produit_num='" . $rcc["produit_num"] . "' and photo_pos=1";
																							$pp = mysql_query($sql);
																							if ($rpp=mysql_fetch_array($pp)) {
																								$image_pdt = "/photos/produits/min/" . $rpp["photo_chemin"];
																							} else 
																								$image_pdt = "http://www.placehold.it/200x200/EFEFEF/AAAAAA&amp;text=no+image";
																							//echo '<div class="col-md-3"><a href=""><figure><figcaption>' . $rcc["produit_nom"] . '</figcaption><img src="' . $image_pdt . '" class="img-responsive"></figure></div>';
																							echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
																								<div class="mt-card-item">
																									<div class="mt-card-avatar mt-overlay-1">
																										<figure style="height:200px;overflow:hidden;position:relative;line-height:200px;">
																											<img src="' . $image_pdt . '" />
																										</figure>
																										<div class="mt-overlay">
																											<ul class="mt-info">
																												<li>
																													<a class="btn default btn-outline" href="javascript:addPdtDevis(' . decrypte($devis_modif) . ',' . $rcc["produit_num"] . ',6)">
																														<i class="fa fa-plus"></i>
																													</a>
																												</li>
																											</ul>
																										</div>
																									</div>
																									<div class="mt-card-content">
																										<h5>' . $rcc["produit_nom"] . '</h5>
																									</div>
																								</div>
																							</div>';
																						}		
																					} else { 
																						echo '<p><i>Pas de résultat !!</i></p>';
																					}
																				} 
																			?>
																			</div>
																		</div>
																	</div>
																</div>
															<?	} ?>
														</div>
														<!-- CHANGE COMMANDE TAB -->
														<div class="tab-pane<? if ($tab=="tab_1_4") echo " active"?>" id="tab_1_4">
															<h4><i class="fa fa-plus"></i> Liste des commandes</h4>
															<?
																$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and devis_num!=0 and commande_num!=0 and client_num='" . decrypte($client_num) . "' order by commande_date DESC";
																$ss = mysql_query($sql);
																$nbr_commande = mysql_num_rows($ss);
																if ($nbr_commande>0) {
																	echo '<table class="table table-bordered table-striped">
																			<thead>
																				<th>N°</th>
																				<th>Date</th>
																				<th>Nbr Produit</th>
																				<th>Montant TTC</th>
																				<th>Echeances</th>
																				<th>Payé</th>
																				<th>Reste à payer</th>
																				<th>Facture</th>
																				<th> </th>
																			</thead>
																			<tbody>';
																	while ($rss=mysql_fetch_array($ss)) {
																		$nbr_echeance = $rss["paiement_nombre"];
																		
																		// On regarde le nombre de paiement effectué
																		$sql = "select * from commandes_paiements where id='" . $rss["id"] . "'";
																		$pa = mysql_query($sql);
																		$nbr_paiement = mysql_num_rows($pa);
																		
																		// On calcul la somme déjà payé
																		$montant_paye = 0;
																		$sql = "select sum(paiement_montant) val from commandes_paiements where id='" . $rss["id"] . "'";
																		$pa = mysql_query($sql);
																		if ($rpa=mysql_fetch_array($pa))
																			$montant_paye = $rpa["val"];
																		
																		$reste_a_paye = number_format(abs(montantCommandeTTC($rss["id"]) - $montant_paye),2,"."," ");
																																				
																		$sql = "select * from commandes_produits where id='" . $rss["id"] . "'";
																		$pp = mysql_query($sql);
																		$nbr_produit = mysql_num_rows($pp);
																		
																		$facture_num = "-";
																		if ($rss["facture_num"]!="")
																			$facture_num = $rss["facture_num"];
																		
																		echo '<tr>
																				<td>' . $rss["commande_num"] . '</td>
																				<td>' . format_date($rss["commande_date"],11,1) . '</td>
																				<td class="text-center">' . $nbr_produit . '</td>
																				<td>' . number_format(montantCommandeTTC($rss["id"]),2,"."," ") . ' €</td>
																				<td class="text-center">' . $nbr_paiement . '/' . $nbr_echeance . '</td>
																				<td>' . number_format($montant_paye,2) . ' €</td>
																				<td>' . $reste_a_paye . ' €</td>
																				<td align="center">' . $facture_num . '</td>
																				<td> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&commande_modif=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-euro"></i> Paiements</a> 
																					<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&commande_consulte=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm green"><i class="fa fa-book"></i> Consulter</a>';
																		if ($rss["facture_num"]!=0) {
																			// ON regarde si la facture a été envoyé par mail
																			$sql = "select * from commandes_mails where id='" . $rss["id"] . "' and facture_mail=1";
																			$ff = mysql_query($sql);
																			$envoye = "";
																			if ($rff = mysql_fetch_array($ff)) {
																				$envoye = " le " . format_date($rff["facture_mail_date"],11,1);
																			}
																			
																			echo '<a href="#" onClick="window.open(\'/facture/index.php?facture=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Facture</a> ';
																			echo '<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&facture_envoi=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer' . $envoye . '</a> ';
																			echo '<a href="#" onClick="window.open(\'/bon-de-reception/index.php?facture=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm"><i class="fa fa-print"></i> Bon de reception</a> ';
																		}
																		if ($rss["facture_num"]=="0")
																			echo '		<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&commande_suppr=' . crypte($rss["id"]) . '&tab=tab_1_4" onClick="return confirmeSupprCommande()" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Modifier</a>
																				</td>
																			</tr>';
																	}
																	echo '	</tbody>
																		</table>';
																} else {
																	echo '<p><i>Aucune commande en cours</i></p>';
																}
															?>															
															<? if (isset($commande_consulte)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . decrypte($commande_consulte) . "'";
																	$cc = mysql_query($sql);
																	if ($rcc=mysql_fetch_array($cc)) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Commande n° : <? echo $rcc["commande_num"] ?></h4>
																<table class="table table-bordered table-striped">
																	<thead>
																		<th colspan="2">Produit</th>
																		<th>Taille</th>
																		<th>Prix Unitaire</th>
																		<th><center>Qte</center></th>
																		<th><center>Remise</center></th>
																		<th>Montant</th>
																	</thead>
																	<tbody>
															<? 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($commande_consulte) . "'";
																		$pp = mysql_query($sql);
																		while ($rpp=mysql_fetch_array($pp)) {
																			$image_pdt = RecupPhotoProduit($rpp["produit_num"]);
																			//$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
																			$prix_total_ttc = $rpp["montant_ttc"]*$rpp["qte"];
																			switch ($rpp["commande_produit_remise_type"]) {
																				case 1: // Remise en %
																					$prix_total_ttc = $prix_total_ttc*(1-($rpp["commande_produit_remise"]/100));
																				break;
																				
																				case 2: // Remise en euro
																					$prix_total_ttc = $prix_total_ttc - $rpp["commande_produit_remise"];
																				break;
																			}			
																			echo '<tr>
																				<td><img src="' . $image_pdt["min"] . '" style="width:90px"/></td>
																				<td>' . $rpp["categorie_nom"] . '<br>' . $rpp["marque_nom"] . '<br><strong>' . $rpp["produit_nom"] . '</strong></td>
																				<td>' . $rpp["taille_nom"] . '</td>
																				<td>' . number_format($rpp["montant_ttc"],2,".", " ") . ' €' . '</td>
																				<td align="center">' . $rpp["qte"] . '</td>
																				<td align="center">';
																				if ($rpp["commande_produit_remise_type"]!=0) {
																					if ($rpp["commande_produit_remise_type"]==1)
																						echo $rpp["commande_produit_remise"] . '%';
																					else
																						echo '-' . $rpp["commande_produit_remise"] . '€';
																				} else {
																					echo ' ';
																				}																				
																				echo '</td>
																				<td>';
																				if (number_format($prix_total_ttc,2)<=0)
																					echo "OFFERT";
																				else
																					echo number_format($prix_total_ttc,2,".", " ") . ' €';
																				echo '</td>
																			</tr>';
																		} ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total HT</strong></td>
																			<td><? echo number_format($commande["commande_ht"],2,".", " ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>TVA (20%)</strong></td>
																			<td><? echo number_format($commande["commande_tva"],2,".", " ") ?> €</td>
																		</tr>
																		<? if ($commande["remise"]==0) { 
																				$montant_a_payer = number_format($commande["commande_ttc"],2,".", "");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><? echo number_format($commande["commande_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<? } else { 
																				$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".", "");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><? echo number_format($commande["commande_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="6" align="right"><strong>Remise</strong></td>
																			<td><? echo $commande["remise"] ?></td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>Total à payer</strong></td>
																			<td><? echo number_format($commande["commande_remise_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<? } ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Méthode de paiement</strong></td>
																			<td><? echo utf8_encode($rcc["paiement_titre"]) ?></td>
																		</tr>
																		<?
																			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				// On regarde si il y a déjà eu des paiments
																				$sql = "select * from commandes_paiements where id='" . $rcc["id"] . "'";
																				$pa = mysql_query($sql);
																				$nbr_paiement = mysql_num_rows($pa);
																				if ($nbr_paiement==0) {
																					$echeance = explode("/",$rcc["paiement_modele"]);
																					$acompte_num = 1;
																					foreach ($echeance as $val) {
																						$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
																						echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																								<td>' . $acompte_val . ' €</td>
																							</tr>';
																						$acompte_num++;
																					}
																				} else {
																					$acompte_num = 0;
																					$montant_paye = 0;
																					while ($rpa = mysql_fetch_array($pa)) {
																						$acompte_num++;
																						echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . '</strong></td>
																								<td>' . number_format($rpa["paiement_montant"],2,"."," ") . ' €</td>
																							</tr>';
																						$montant_paye += number_format($rpa["paiement_montant"],2,".","");
																					}
																					$reste_a_payer = $montant_a_payer - $montant_paye;
																					if ($acompte_num<$rcc["paiement_nombre"]) {
																						$echeance_restante = $rcc["paiement_nombre"]-$acompte_num;
																						$reste_acompte_a_payer = $reste_a_payer/$echeance_restante;
																						for ($zz=$acompte_num+1;$zz<=$rcc["paiement_nombre"];$zz++) {
																							echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $zz . '</strong></td>
																								<td>' . number_format($reste_acompte_a_payer,2,"."," ") . ' €</td>
																							</tr>';
																						}
																					}
																				}
																			}
																		?>
																		<tr>
																			<td colspan="7" align="right"><a href="/commandes/index.php?cde=<? echo crypte($rcc["commande_num"]) ?>" class="btn blue" target="_blank">Imprimer</a> <a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_4" class="btn red">Fermer</a></td>
																		</tr>
																	</tbody>
																</table>
															<?		}
																} ?>
															<? if (isset($commande_modif)) { 
																	// On recupere le nombre d'écheance
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . decrypte($commande_modif) . "'";
																	$pa = mysql_query($sql);
																	if ($rpa=mysql_fetch_array($pa)) {
																		$nbr_echeance = $rpa["paiement_nombre"];
																		
																		// On regarde le nombre de paiement effectué
																		$sql = "select * from commandes_paiements where id='" . $rpa["id"] . "'";
																		$pa = mysql_query($sql);
																		$nbr_paiement = mysql_num_rows($pa);
																		
																		// On calcul la somme déjà payé
																		$montant_paye = 0;
																		$sql = "select sum(paiement_montant) val from commandes_paiements where id='" . $rpa["id"] . "'";
																		$pp = mysql_query($sql);
																		if ($rpp=mysql_fetch_array($pp))
																			$montant_paye = $rpp["val"];
																		
																		$reste_a_paye = number_format(abs(montantCommandeTTC($rpa["id"]) - $montant_paye),2,".","");
															?>
																<h4><i class="fa fa-plus"></i> Paiement commande : <? echo $rpa["commande_num"] ?> <? if ($message_erreur_paiement!="") echo ' - <i class="fa fa-warning"></i> <font class="font-red-thunderbird"><strong>' . $message_erreur_paiement . '</strong></font>'; ?></h4>
																<table class="table table-bordered table-striped">
																	<thead>
																		<th>Echéance</th>
																		<th>Date</th>
																		<th>Montant (€ TTC)</th>
																		<th>Mode de paiement</th>
																		<th>Num Transaction</th>
																		<th> </th>
																	</thead>
																	<tbody>
																<?
																	$echeance=1;
																	$sql = "select * from commandes_paiements c, paiements_modes m where c.mode_num=m.mode_num and id='" . decrypte($commande_modif) . "'";
																	$pp = mysql_query($sql);
																	while ($rpp=mysql_fetch_array($pp)) {
																		echo '<form name="paiement_' . $e . '" action="' . $_SERVER["PHP_SELF"] . '" method="POST">
																			<input type="hidden" name="paiement" value="ok">
																			<input type="hidden" name="modif" value="ok">
																			<input type="hidden" name="echeance" value="' . $echeance . '">
																			<input type="hidden" name="nbr_echeance" value="' . $nbr_echeance . '">
																			<input type="hidden" name="reste_a_payer" value="' . $reste_a_paye . '">
																			<input type="hidden" name="client_num" value="' . $client_num . '">
																			<input type="hidden" name="commande_modif" value="' . $commande_modif . '">
																			<input type="hidden" name="tab" value="tab_1_4">';
																		echo '<tr>
																				<td align="center">' . $echeance . '</td>
																				<td><input type="date" class="form-control" name="date" value="' . $rpp["paiement_date"] . '"></td>
																				<td><input type="text" class="form-control input-small" name="montant" value="' . $rpp["paiement_montant"] . '" required></td>
																				<td><select name="mode" class="form-control">';
																		$sql = "select * from paiements_modes order by mode_ordre ASC";
																		$mm = mysql_query($sql);
																		while ($rmm=mysql_fetch_array($mm)) {
																			echo '<option value="' . $rmm["mode_num"] . '"';
																			if ($rmm["mode_num"]==$rpp["mode_num"])
																				echo ' SELECTED';
																			echo '>' . utf8_encode($rmm["mode_nom"]) . '</option>';
																		}
																		echo '	</select></td>
																				<td><input type="text" name="num" value="' . $rpp["cheque_num"] . '" class="form-control"></td>
																				<td> 
																					<button type="submit"  class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-check"></i> Ok</button>'; 
																		if ($nbr_echeance>1) {
																			// ON regarde si la facture a été envoyé par mail
																			$envoye = "";
																			if ($rpp["paiement_mail"]==1) {
																				$envoye = " le " . format_date($rpp["paiement_mail_date"],11,1);
																			}
																			echo '		<a href="#" onClick="window.open(\'/acompte/index.php?id=' . $commande_modif . '&paiement=' . $rpp["paiement_num"] . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Facture</a> 
																			<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&acompte_envoi=' . crypte($rpp["id"]) . '&paiement=' . $rpp["paiement_num"] . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer ' . $envoye . '</a> 
																						<a href="' . $_SERVER["PHP_SELF"] . '?client_num=' . $client_num . '&paiement_suppr=' . crypte($rpp["id"]) . '&echeance=' . $echeance . '&tab=tab_1_4" onClick="return confirmeSupprPaiement()" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>';
																		}																					
																		echo '		</td>
																			  </tr>
																			  </form>';
																		$echeance++;
																	}
																	// On complete les echeances
																	for ($e=$echeance;$e<=$nbr_echeance;$e++) {
																		echo '<form name="paiement_' . $e . '" action="' . $_SERVER["PHP_SELF"] . '" method="POST">
																			<input type="hidden" name="paiement" value="ok">
																			<input type="hidden" name="ajout" value="ok">
																			<input type="hidden" name="echeance" value="' . $e . '">
																			<input type="hidden" name="nbr_echeance" value="' . $nbr_echeance . '">
																			<input type="hidden" name="reste_a_payer" value="' . $reste_a_paye . '">
																			<input type="hidden" name="client_num" value="' . $client_num . '">
																			<input type="hidden" name="commande_modif" value="' . $commande_modif . '">
																			<input type="hidden" name="tab" value="tab_1_4">';
																		echo '<tr>
																				<td align="center">' . $e . '</td>
																				<td><input type="date" class="form-control" name="date" value="' . Date("Y-m-d") . '"></td>
																				<td><input type="text" class="form-control input-small" name="montant" value="" required></td>
																				<td><select name="mode" class="form-control">';
																		$sql = "select * from paiements_modes order by mode_ordre ASC";
																		$mm = mysql_query($sql);
																		while ($rmm=mysql_fetch_array($mm)) {
																			echo '<option value="' . $rmm["mode_num"] . '"';
																			echo '>' . utf8_encode($rmm["mode_nom"]) . '</option>';
																		}
																		echo '	</select></td>
																				<td><input type="text" class="form-control" name="num" value=""></td>
																				<td> 
																					<button type="submit"  class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-check"></i> Ok</button> 
																				</td>
																			  </tr>
																			  </form>';
																	}
																?>
																	<tr><td colspan="6" align="right"><a href="<? echo $_SERVER["PHP_SELF"] ?>?client_num=<? echo $client_num ?>&tab=tab_1_4" class="btn red">Fermer</a></td></tr>
																	</tbody>
																</table>
															<? 
																}
															} ?>
														</div>
														<!-- END CHANGE PASSWORD TAB -->
														<? } ?>
														<!-- PERSONAL INFO TAB -->
														<div class="tab-pane<? if ($tab=="tab_1_6") echo " active"?>" id="tab_1_6">
															<p> Modifier les informations personnelles du client </p>
															<form name="ajouter" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
															<input type="hidden" name="modifier" value="ok">
															<input type="hidden" name="tab" value="tab_1_6">
															<input type="hidden" name="client_num" value="<? echo $client_num ?>">
															<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
																<div class="form-group">
																	<label>Genre</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-intersex"></i>
																		</span>
																		<select name="genre" class="form-control">
																			<option value="0"<? if ($rcl["client_genre"]==0) echo " SELECTED"; ?>>Femme</option>
																			<option value="1"<? if ($rcl["client_genre"]==1) echo " SELECTED"; ?>>Homme</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Nom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="nom" class="form-control" placeholder="Nom" value="<? echo $rcl["client_nom"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Prenom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="prenom" class="form-control" placeholder="Prénom" value="<? echo $rcl["client_prenom"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr1" class="form-control" placeholder="Adresse"  value="<? echo $rcl["client_adr1"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Complément d'adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr2" class="form-control" placeholder="Complément d'adresse"  value="<? echo $rcl["client_adr2"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>CP</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-search"></i>
																		</span>
																		<input type="text" name="cp" class="form-control" placeholder="Code Postal"  value="<? echo $rcl["client_cp"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Ville</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-shield"></i>
																		</span>
																		<input type="text" name="ville" class="form-control" placeholder="Ville" value="<? echo $rcl["client_ville"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Tel</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-mobile-phone"></i>
																		</span>
																		<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<? echo $rcl["client_tel"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Email</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-envelope"></i>
																		</span>
																		<input type="email" name="mail" class="form-control" placeholder="Email" value="<? echo $rcl["client_mail"] ?>" required> </div>
																</div>												
															</div>
															<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
																<div class="form-group">
																	<label>Mensuration</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-odnoklassniki-square"></i>
																		</span>
																		<table>
																			<tr>
																				<td>Tour Taille<br><input type="text" name="taille" class="form-control" value="<? echo $rcl["taille"] ?>"></td>
																				<td>Poitrine<br><input type="text" name="poitrine" class="form-control" value="<? echo $rcl["poitrine"] ?>"></td>
																				<td>Ss poitrine<br><input type="text" name="sous_poitrine" class="form-control" value="<? echo $rcl["sous_poitrine"] ?>"></td>
																				<td>Lg Dos<br><input type="text" name="longueur_dos" class="form-control" value="<? echo $rcl["longueur_dos"] ?>"></td>
																				<td>Biceps<br><input type="text" name="biceps" class="form-control" value="<? echo $rcl["biceps"] ?>"></td>
																				<td>Taille-sol talons<br><input type="text" name="taille_sol" class="form-control" value="<? echo $rcl["taille_sol"] ?>"></td>
																			</tr>
																			<tr>
																				<td>Hanche 1<br><input type="text" name="hanche1" class="form-control" value="<? echo $rcl["hanche1"] ?>"></td>
																				<td>Hanche 2<br><input type="text" name="hanche2" class="form-control" value="<? echo $rcl["hanche2"] ?>"></td>
																				<td>Carrure Av<br><input type="text" name="carrure_avant" class="form-control" value="<? echo $rcl["carrure_avant"] ?>"></td>
																				<td>Carrure Dos<br><input type="text" name="carrure_dos" class="form-control" value="<? echo $rcl["carrure_dos"] ?>"></td>
																				<td>Pointure<br><input type="text" name="pointure" class="form-control" value="<? echo $rcl["pointure"] ?>"></td>
																				<td>Taille<br><input type="text" name="tour_taille" class="form-control" value="<? echo $rcl["tour_taille"] ?>"></td>
																			</tr>
																		</table>
																	</div>
																</div>
																<div class="form-group">
																	<label>Date du mariage</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-calendar-check-o"></i>
																		</span>
																		<input type="date" name="date" class="form-control" placeholder="Date du mariage" value="<? echo $rcl["client_date_mariage"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Lieu de mariage</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-black-tie"></i>
																		</span>
																		<input type="text" name="lieu" class="form-control" placeholder="Lieu du mariage"  value="<? echo $rcl["client_lieu_mariage"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Remarques</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-book"></i>
																		</span>
																		<textarea class="form-control" rows="4" name="remarques"><? echo $rcl["client_remarque"] ?></textarea> </div>
																</div>
																<div class="form-group">
																	<label>Comment avez vous connu Olympe ?</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-meh-o"></i>
																		</span>
																		<select name="connaissance" class="form-control">
																			<option value="0">----------------</option>
																			<option value="1"<? if ($rcl["connaissance_num"]==1) echo " SELECTED";?>>Publicité</option>
																			<option value="2"<? if ($rcl["connaissance_num"]==2) echo " SELECTED";?>>Sur Internet</option>
																			<option value="3"<? if ($rcl["connaissance_num"]==3) echo " SELECTED";?>>Bouche à oreille</option>
																			<option value="4"<? if ($rcl["connaissance_num"]==4) echo " SELECTED";?>>Autres</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Client intéressé</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-meh-o"></i>
																		</span>
																		<select name="interet" class="form-control">
																			<option value="0">----------------</option>
																			<option value="1"<? if ($rcl["interet"]==1) echo " SELECTED";?>>Bof</option>
																			<option value="2"<? if ($rcl["interet"]==2) echo " SELECTED";?>>Intéressé</option>
																			<option value="3"<? if ($rcl["interet"]==3) echo " SELECTED";?>>Très intéressé</option>
																			<option value="4"<? if ($rcl["interet"]==4) echo " SELECTED";?>>Non</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Suivi par :</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<select name="user_suivi" class="form-control">
																			<?
																				$sql = "select * from users where showroom_num='" . $rcl["showroom_num"] . "' and user_etat=1";
																				$uu = mysql_query($sql);
																				while ($ruu=mysql_fetch_array($uu)) {
																					echo '<option value="' . $ruu["user_num"] . '"';
																					if ($ruu["user_num"]==$rcl["user_num"])
																						echo " SELECTED";
																					echo '>' . $ruu["user_prenom"] . ' ' . $ruu["user_nom"] . '</option>';
																				}
																			?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Couturiere :</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<select name="couturiere" class="form-control">
																		<option value="0">----------------------</option>
																			<?
																				$sql = "select * from users where showroom_num='" . $rcl["showroom_num"] . "' and user_etat=1";
																				$uu = mysql_query($sql);
																				while ($ruu=mysql_fetch_array($uu)) {
																					echo '<option value="' . $ruu["user_num"] . '"';
																					if ($ruu["user_num"]==$rcl["couturiere_num"])
																						echo " SELECTED";
																					echo '>' . $ruu["user_prenom"] . ' ' . $ruu["user_nom"] . '</option>';
																				}
																			?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Showroom :</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-industry"></i>
																		</span>
																		<select name="showroom_modif" class="form-control">
																		<option value="0">-----------------------</option>
																			<?
																				$sql = "select * from showrooms";
																				$uu = mysql_query($sql);
																				while ($ruu=mysql_fetch_array($uu)) {
																					echo '<option value="' . $ruu["showroom_num"] . '"';
																					if ($ruu["showroom_num"]==$rcl["showroom_num"])
																						echo " SELECTED";
																					echo '>' . $ruu["showroom_nom"] . ' ' . $ruu["showroom_vill"] . '</option>';
																				}
																			?>
																		</select>
																	</div>
																</div>
																<div class="form-actions">
																	<button type="submit" class="btn blue">Enregistrer</button>
																</div>										
															</div>
															</form>
															<hr>
															<p> Commandes fournisseurs </p>
															<hr>
															<div class="row">
																<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
																	<table class="table table-bordered table-hover">
																		<thead>
																			<tr>
																				<th>N° Commande</th>
																				<th>Commande Date</th>
																				<th>Commande Montant (TTC)</th>
																				<th>Date</th>
																				<th>Etat</th>
																				<th></th>
																			</tr>
																		</thead>
																		<tbody>
																		<?
																			$sql = "select * from commandes c, commandes_produits cd where c.id=cd.id and commande_num>0 and client_num='" . decrypte($client_num) . "' order by commande_date ASC, c.id ASC";
																			$co = mysql_query($sql);
																			while ($rco=mysql_fetch_array($co)) {
																				$checked = "";
																				$date_fournisseur = "";
																				$montant = 0;
																				$sql = "select * from commandes_fournisseurs where id='" . $rco["id"] . "' and produit_num='" . $rco["produit_num"] . "'";
																				$tt = mysql_query($sql);
																				if ($rtt = mysql_fetch_array($tt)) {
																					$checked = " CHECKED";
																					$date_fournisseur = format_date($rtt["commande_fournisseur_date"],11,1);
																					$montant = number_format($rtt["commande_montant"],2,"."," ");
																				}
																				echo '<tr>
																						<td>' . $rco["commande_num"] . '</td>
																						<td>' . format_date($rco["commande_date"],11,1) . '</td>';
																				echo '	</td>
																						<td>' . $montant . ' €<br>';
																				if ($checked!="") {
																						// On regarde si il y a des paiements
																						$paiement1 = "";
																						$paiement2 = "";
																						$paiement3 = "";
																						$paiement1_date = "";
																						$paiement2_date = "";
																						$paiement3_date = "";
																						$sql = "select * from commandes_fournisseurs_paiements where id='" . $rco["id"] . "' and produit_num='" . $rco["produit_num"] . "'";
																						$pa = mysql_query($sql);
																						if ($rpa=mysql_fetch_array($pa)) {
																							if ($rpa["paiement1"]!=0) {
																								$paiement1 = $rpa["paiement1"];
																								$paiement1_date = $rpa["paiement1_date"];
																							}
																							if ($rpa["paiement2"]!=0) {
																								$paiement2 = $rpa["paiement2"];
																								$paiement2_date = $rpa["paiement2_date"];
																							}
																							if ($rpa["paiement3"]!=0) {
																								$paiement3 = $rpa["paiement3"];
																								$paiement3_date = $rpa["paiement3_date"];																								
																							}
																						}
																						echo '<form name="paiement_fournisseur_' . $rtt["id"] . '_' . $rtt["produit_num"] . '" method="POST" action="' . $_SERVER["PHP_SELF"] . '">
																						<input type="hidden" name="tab" value="tab_1_6">
																						<input type="hidden" name="client_num" value="' . $client_num . '">
																						<input type="hidden" name="id" value="' .crypte($rco["id"]) . '">
																						<input type="hidden" name="produit" value="' . crypte($rco["produit_num"]) . '">
																						<input type="hidden" name="paiementfournisseur" value="ok">
																						<input type="text" name="fournisseur_paiement1" class="form-control inline input-small" Placeholder="Paiement 1" value="' . $paiement1 . '"> <input type="date" name="fournisseur_paiement1_date" value="' . $paiement1_date . '" class="form-control inline input-medium"><br><input type="text" name="fournisseur_paiement2" class="form-control inline input-small" Placeholder="Paiement 2" value="' . $paiement2 . '">  <input type="date" name="fournisseur_paiement2_date" value="' . $paiement2_date . '" class="form-control inline input-medium"><br><input type="text" name="fournisseur_paiement3" class="form-control inline input-small" Placeholder="Paiement 3" value="' . $paiement3 . '"> <input type="date" name="fournisseur_paiement3_date" value="' . $paiement3_date . '" class="form-control inline input-medium"> <button type="submit" class="btn inline green">Ok</button>
																						</form>';
																				}
																				echo '	</td>
																						<td id="fournisseur_date_' . $rco["id"] . '">' . $date_fournisseur . '</td>
																						<td>';
																				if ($checked!="")
																						echo '<i class="fa fa-check-square-o"></i>';		
																				echo '</td>
																						<td><a href="client.php?client_num=' . $client_num . '&fournisseur=ok&id=' . crypte($rco["id"]) . '&produit=' . crypte($rco["produit_num"]) . '&tab=tab_1_6"  class="btn btn-outline btn-circle dark btn-sm black"> Commande Fournisseur</a>';
																				if ($checked!="") {
																					echo ' <a href="#" onClick="window.open(\'/clients/fournisseur.php?id=' . crypte($rco["id"]) . '&produit=' . crypte($rco["produit_num"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-print"></i> Bon de commande</a>';
																				}
																				echo '		
																					</tr>';
																			}
																		?>
																		</tbody>
																	</table>
																</div>
															</div>
															<? if ($fournisseur=="ok") {
																	// On recherche si il y a une robe à commander
																	$sql = "select * from commandes c, commandes_produits cp, md_produits p, marques m where c.id=cp.id and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and c.id='" . decrypte($id) . "' and cp.produit_num='" . decrypte($produit) . "'";
																	$mm = mysql_query($sql);
																	if ($rmm = mysql_fetch_array($mm)) {
																		// On regarde si il y a déjà une commande forunisseur
																		$sql = "select * from commandes_fournisseurs where id='" . decrypte($id) . "' and produit_num='" . decrypte($produit) . "'";
																		$ff = mysql_query($sql);
																		if ($rff = mysql_fetch_array($ff)) {
																			$livraison = $rff["livraison"];
																			$reference = $rff["reference"];
																			$remarques = $rff["remarques"];
																			$poitrine = $rff["poitrine"];
																			$sous_poitrine = $rff["sous_poitrine"];
																			$taille = $rff["taille"];
																			$hanche1 = $rff["hanche1"];
																			$hanche2 = $rff["hanche2"];
																			$biceps = $rff["biceps"];
																			$carrure_avant = $rff["carrure_avant"];
																			$carrure_dos = $rff["carrure_dos"];
																			$longueur_dos = $rff["longueur_dos"];
																			$taille_sol = $rff["taille_sol"];
																			$taille_choisie = $rff["taille_choisie"];
																			$montant = $rff["commande_montant"];
																			$commande_date = $rff["commande_fournisseur_date"];
																		} else {
																			$livraison = "";
																			$reference = "";
																			$remarques = "";
																			$poitrine = $rcl["poitrine"];
																			$sous_poitrine = $rcl["sous_poitrine"];
																			$taille = $rcl["taille"];
																			$hanche1 = $rcl["hanche1"];
																			$hanche2 = $rcl["hanche2"];
																			$biceps = $rcl["biceps"];
																			$carrure_avant = $rcl["carrure_avant"];
																			$carrure_dos = $rcl["carrure_dos"];
																			$longueur_dos = $rcl["longueur_dos"];
																			$taille_sol = $rcl["taille_sol"];
																			$taille_choisie = $rcl["taille_choisie"];
																			$montant = $rcl["commande_montant"];
																			$commande_date = Date("Y-m-d");
																			$sql = "select * from prixachats where prixachat_num='" . $rmm["prixachat_num"] . "'";
																			$pp = mysql_query($sql);
																			if ($rpp=mysql_fetch_array($pp)) {
																				$montant = $rpp["prixachat_montant"];
																				if ($montant!=0) {
																					// On calcul le montant avec la TVA
																					$montant = number_format($montant*1.20,2,".","");
																				}
																			} else {
																				$montant = 0;
																			}
																		}
															?>
															<hr><center><h4>Commande Fournisseur de la commande :  <? echo $rmm["commande_num"] ?></h4></center><hr>
															<div class="row">
																<form name="forunisseur" action="<? echo $_SERVER["PHP_SELF"] ?>" method="POST">
																<input type="hidden" name="tab" value="tab_1_6">
																<input type="hidden" name="client_num" value="<? echo $client_num ?>">
																<input type="hidden" name="id" value="<? echo $id ?>">
																<input type="hidden" name="produit" value="<? echo $produit ?>">
																<input type="hidden" name="marque" value="<? echo $rmm["marque_num"] ?>">
																<input type="hidden" name="cdefournisseur" value="ok">
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p>
																		<strong><? if ($rcl["client_genre"]==0) echo 'Nom de la future mariée'; else echo 'Nom du futur marié'; ?></strong> : <? echo $rcl["client_prenom"] . ' ' . $rcl["client_nom"] ?><br>
																		<strong>Tel : </strong> <? echo $rcl["client_tel"] ?><br>
																		<strong>Mail : </strong> <? echo $rcl["client_mail"] ?><br>
																		<strong>Date de mariage : </strong> <? echo format_date($rcl["client_date_mariage"],11,1) ?><br>
																		<strong>Livraison avant : </strong> <input type="text" name="livraison" placeholder="JJ/MM/AAAA" value="<? echo $livraison ?>">
																	</p>
																</div>
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p><strong>Fournisseur</strong><br>
																	<? echo $rmm["marque_raison_social"] ?><br>
																	<? echo $rmm["marque_adr1"] ?><br>
																	<? if ($rmm["marque_adr2"]!="") echo $rmm["marque_adr2"] . "<br>"; ?>
																	<? echo $rmm["marque_cp"] ?> <? echo $rmm["marque_ville"] ?><br>
																	<? echo $rmm["marque_tel"] ?>
																	</p>
																</div>
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p><strong>Nom du magasin : </strong><? echo 'Olympe ' . $u->mShowroomInfo["showroom_ville"] ?><br>
																	<strong>Date de commande : </strong><input type="date" name="fournisseur_commande_date" value="<? echo $commande_date ?>"><br>
																	<strong>Référence : </strong><input type="text" name="fournisseur_commande_ref" value="<? echo $reference ?>"></p>
																</div>
																<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
																	<hr><p><strong>Référence modèle</strong> : <? echo $rmm["produit_nom"] ?></p><hr>
																</div>
																<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
																	<p><strong>Remarques</strong> :<br> 
																	<textarea class="form-control" name="fournisseur_remarque" rows="3"><? echo $remarques ?></textarea>
																	</p><hr>
																</div>
																<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
																	<p><strong>Mesure Cliente</strong></p>
																	<table class="table table-bordered table-striped table-condensed flip-content">
																	<tr>
																		<td><strong>Tour de poitrine</strong></td>
																		<td align="center"><input type="text" name="fournisseur_poitrine" class="input-xsmall" value="<? echo $poitrine ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de sous poitrine</strong></td>
																		<td align="center"><input type="text" name="fournisseur_sous_poitrine" class="input-xsmall" value="<? echo $sous_poitrine ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de taille</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille" class="input-xsmall" value="<? echo $taille ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de petite hanche</strong></td>
																		<td align="center"><input type="text" name="fournisseur_hanche1" class="input-xsmall" value="<? echo $hanche1 ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de grande hanche</strong></td>
																		<td align="center"><input type="text" name="fournisseur_hanche2" class="input-xsmall" value="<? echo $hanche2 ?>" ></td>
																	</tr>
																	<tr>
																		<td><strong>Tour de biceps</strong></td>
																		<td align="center"><input type="text" name="fournisseur_biceps" class="input-xsmall" value="<? echo $biceps ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Carrure Avant</strong></td>
																		<td align="center"><input type="text" name="fournisseur_carrure_avant" class="input-xsmall" value="<? echo $carrure_avant ?>" ></td>
																	</tr>
																	<tr>
																		<td><strong>Carrure dos</strong></td>
																		<td align="center"><input type="text" name="fournisseur_carrure_dos" class="input-xsmall" value="<? echo $carrure_dos ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Longueur dos</strong></td>
																		<td align="center"><input type="text" name="fournisseur_longueur_dos" class="input-xsmall" value="<? echo $longueur_dos ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Hauteur taille-sol avec talons</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille_sol" class="input-xsmall" value="<? echo $taille_sol ?>" ></td>
																	</tr> 
																	<tr class="danger">
																		<td><strong>Taille choisie</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille_choisie" class="input-xsmall" value="<? echo $taille_choisie ?>" ></td>
																	</tr> 
																	<tr>
																		<td colspan="2"></td>
																	</tr>
																	<tr class="success">
																			<td class="text-right">Montant de la commande :</td>
																			<td class="text-center"><input type="text" name="fournisseur_montant" class="input-xsmall" value="<? echo $montant ?>" > € TTC</td>
																		</tr>
																	</table>
																</div>
																<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-center">
																	<button type="submit" class="btn blue">Enregistrer</button>
																</div>
																</form>
															</div>
															<? 
																	}
																} ?>
														</div>
														<!-- END PERSONAL INFO TAB -->
														
														<!-- END PRIVACY SETTINGS TAB -->
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- END PROFILE CONTENT -->
							</div>
						</div>
						<!-- END PAGE BASE CONTENT -->
					</div>
				</div>
				<!-- END PAGE BASE CONTENT -->
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>