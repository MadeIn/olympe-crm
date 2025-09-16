<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";

if (!isset($tab)) {
	if ($u->mGroupe!=0)
		$tab="tab_1_1";
	else
		$tab="tab_1_6";
}

if (isset($modifier)) { // On modifie les infos 
	$sql = "update clients set 
			client_genre=" . sql_safe($genre) . ",
			client_nom=" . sql_safe($nom) . ",
			client_prenom=" . sql_safe($prenom) . ",
			client_adr1=" . sql_safe($adr1) . ",
			client_adr2=" . sql_safe($adr2) . ",
			client_cp=" . sql_safe($cp) . ",
			client_ville=" . sql_safe($ville) . ",
			client_tel=" . sql_safe($tel) . ",
			client_mail=" . sql_safe($mail) . ",
			client_date_mariage=" . sql_safe($date) . ",
			client_lieu_mariage=" . sql_safe($lieu) . ",
			client_remarque=" . sql_safe($remarques) . ",
			connaissance_num=" . sql_safe($connaissance) . ",
			client_datemodification=" . sql_safe(Date("Y-m-d H:i:s")) . ",
			poitrine=" . sql_safe($poitrine) . ",
			sous_poitrine=" . sql_safe($sous_poitrine) . ",
			taille=" . sql_safe($taille) . ",
			hanche1=" . sql_safe($hanche1) . ",
			hanche2=" . sql_safe($hanche2) . ",
			carrure_avant=" . sql_safe($carrure_avant) . ",
			carrure_dos=" . sql_safe($carrure_dos) . ",
			biceps=" . sql_safe($biceps) . ",
			taille_sol=" . sql_safe($taille_sol) . ",
			longueur_dos=" . sql_safe($longueur_dos) . ",
			pointure=" . sql_safe($pointure) . ",
			tour_taille=" . sql_safe($tour_taille) . ",
			interet=" . sql_safe($interet) . ", 
			user_num=" . sql_safe($user_suivi) . ", 
			couturiere_num=" . sql_safe($couturiere) . ", 
			showroom_num=" . sql_safe($showroom_modif) . " 
		where client_num=" . decrypte($client_num);
	$base->query($sql);
}

$sql = "select * from clients where client_num=" . decrypte($client_num);
$rcl = $base->queryRow($sql);
if (!$rcl) {
	header("location:/home");
}

if ($rcl["client_genre"]==0)
	$genre = "Mme";
else
	$genre = "Mr";

$client_nom_complet = str_replace("'","\'",$rcl["client_nom"]) . " " . $rcl["client_prenom"];


if (isset($rdv_num)) {
	$num = decrypte($rdv_num);
	if ($num!=0) { // On efface l'ancien RDV pour le modifier
		$sql = "delete from rendez_vous where rdv_num=" . sql_safe($num);
		$base->query($sql);
		
		$sql = "delete from calendriers where rdv_num=" . sql_safe($num);
		$base->query($sql);
	}
	// On insere un Rendez vous
	$date_rdv = $date . " " . $time;
	$sql = "insert into rendez_vous values(0," . decrypte($client_num) . "," . sql_safe($type_num) . "," . sql_safe($date_rdv) . "," . sql_safe($remarque) . ",0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00'," . sql_safe($u->mNum) . ")";
	$num = $base->insert($sql);
	
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
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(1,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
			
		break;
		
		case 6: // 2e RDV
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+60 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "2e RDV " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(1,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
			
		break;
		
		case 8: // 3eme RDV
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+60 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "3e RDV " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(1,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
			
		break;
		
		case 7: // RDV Accessoires
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+30 minutes",$dateTimestamp));
			$theme = 1;
			
			$titre = "RDV Acc. " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(1,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
			
		break;
		
		case 2: // Date de reception prévu
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(2,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE]",format_date($date,0,1),$message_mail);
			if ($remarque!="")
				$remarque = " de " . $remarque;
			$message_mail = str_replace("[REMARQUE]",$remarque,$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
		
		case 3: // Date de réception
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(3,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
		
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
		
		case 4: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "Retouche " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(14,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			// Si on est à Montpellier on envoie aussi à la couturière
			if ($rcl["showroom_num"]==1) {
				SendMail("lilietcie34@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			if ($rcl["showroom_num"]==2) {
				SendMail("margotla1982@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
		
		case 5: // RDV Remise
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "Remise " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(5,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			if ($dernier_acompte>0) {
				$sql = "select * from paiements_modes p, showrooms_paiements s where p.mode_num=s.mode_num and showroom_num=" . sql_safe($rcl["showroom_num"]) . " order by mode_ordre ASC";
				$pa = $base->query($sql);
				$nbr_paiement = count($pa);
				$moyen_paiement = "";
				$nbr_mode = 0;
				foreach ($pa as $rpa) {
					$moyen_paiement .= $rpa["mode_nom"];
					$nbr_mode++;
					if ($nbr_mode<$nbr_paiement) {
						if ($nbr_mode==(intval($nbr_paiement)-1))
							$moyen_paiement .=  " ou ";
						else
							$moyen_paiement .= ", ";
					}
				}
				$message_acompte = '<p>Ce rendez-vous s\'accompagne du paiement d\'un acompte de ' . $dernier_acompte . '&euro;, que vous pouvez régler par ' . $moyen_paiement . '.</p>';
				$message_mail = str_replace("[ACOMPTE_VALEUR]",$message_acompte,$message_mail);
			} else {
				$message_mail = str_replace("[ACOMPTE_VALEUR]","",$message_mail);
			}
						
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
		
		case 9: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "2e RDV Retouche " . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(14,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
			$adresse = $u->mShowroomInfo["showroom_adr1"];
			if ($u->mShowroomInfo["showroom_adr2"]!="")
				$adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
			$adresse = $adresse;
			$message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
			$message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
			$message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			
			// Si on est à Montpellier on envoie aussi à la couturière
			if ($rcl["showroom_num"]==1) {
				SendMail("lilietcie34@gmail.com",$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
			}
			
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
		
		case 10: // RDV Retouche
			$date_deb = $date_rdv;
			$dateTimestamp = strtotime($date_deb);
			$date_fin = date('Y-m-d H:i:s', strtotime("+1 hour",$dateTimestamp));
			$theme = 1;
			
			$titre = "RDV Retouche Marseille" . $client_nom_complet;
			$desc = "";
			 
			// On insere en bdd
			$sql = "insert into calendriers values(0," . sql_safe($date_deb) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . "," . decrypte($client_num). "," . sql_safe($num) . ")";
			$base->query($sql);
			
			// On envoi le mail selon le type de RDV
			$template = getEmailTemplate(15,$rcl["client_genre"]);
			$titre_mail = $template["titre"];
			$message_mail = $template["message"];
			$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
			$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
			$message_mail = str_replace("[DATE_HEURE]",format_date($date_deb,2,1),$message_mail);
			
			// On envoi le mail
			SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
						
			$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num);
			$base->query($sql);
		break;
	}
}

if (isset($selection_devis)) {
	// ON cherche le numero de devis 
	$devis_deb = Date("Y") * 10000;
	$sql = "select max(devis_num) val from commandes where devis_num>" . sql_safe($devis_deb);
	$rdd = $base->queryRow($sql);
if ($rdd) {
		if ($rdd["val"]>0)
			$devis_num = $rdd["val"]+1;
		else
			$devis_num = $devis_deb + 1 ;
	} else {
		$devis_num = $devis_deb + 1 ;
	}
	
	// On insere le devis
	$sql = "insert into commandes values(0," . decrypte($client_num). "," . sql_safe($devis_num) . "," . sql_safe(Date("Y-m-d H:i:s")) . ",'0','0000-00-00 00:00:00','0','0000-00-00 00:00:00','0','0','0','0','0','1'," . sql_safe($u->mNum) . "," . sql_safe($u->mShowroom) . ")";
	$id = $base->insert($sql);
	
	// ON insere les produits contenu dans la sélection
	$sql = "select * from selections_produits where selection_num=" . decrypte($selection_devis);
	$dd = $base->query($sql);
	
	$montant_total_ht = 0;
	$montant_total_tva = 0;
	$montant_total_ttc = 0;
	
	foreach ($dd as $rdd) {
		$prixProduit = RecupPrix($rdd["produit_num"]);
		$sql = "insert into commandes_produits values (" . sql_safe($id) . "," . sql_safe($rdd["produit_num"]) . ",'-1',1," . sql_safe($prixProduit["montant_ht"]) . "," . sql_safe($prixProduit["montant_tva"]) . "," . sql_safe($prixProduit["montant_ttc"]) . "," . sql_safe($prixProduit["montant_remise"]) . "," . sql_safe($prixProduit["montant_remise_type"]) . "," . sql_safe($prixProduit["montant_ht_remise"]) . "," . sql_safe($prixProduit["montant_tva_remise"]) . "," . sql_safe($prixProduit["montant_ttc_remise"]) . ",'0','0')";
		$base->query($sql);
		
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
	$sql = "update commandes set commande_ht=" . sql_safe($montant_total_ht) . ", commande_tva=" . sql_safe($montant_total_tva) . ", commande_ttc=" . sql_safe($montant_total_ttc) . " where id=" . sql_safe($id);
	$base->query($sql);
}

if (isset($selection_envoi)) {
	// On envoi le mail à la cliente avec sa sélection
	
	$template = getEmailTemplate(7,$rcl["client_genre"]);
	$titre_mail = $template["titre"];
	$message_mail = $template["message"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
	$message_mail = str_replace("[SELECTION_NUM]",$selection_envoi,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "update selections set selection_envoye=1, selection_envoye_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where selection_num=" . decrypte($selection_envoi);
	$base->query($sql);
}

if (isset($commande_passage)) {
	// On test si toutes les tailles sont renseignées
	$sql = "select * from commandes_produits where id='" . decrypte($commande_passage) . "' and taille_num='-1'";
	$tt = $base->query($sql);
	$nbr = count($tt);
	if ($nbr==0) { // On passe la commande
		// On recupere le numero de devis pour le mettre dans commande
		$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id=" . decrypte($commande_passage);
		$rco = $base->queryRow($sql);
 		if ($rco) {
			$commande_num = $rco["devis_num"];
			
			// ON regarde si il y a une date de commande pour la modifier ou pas
			if ($rco["commande_date"]!="0000-00-00 00:00:00")
				$commande_modif_date = $rco["commande_date"];
			else
				$commande_modif_date = Date("Y-m-d H:i:s");
			
			// On modifie la commande
			$sql = "update commandes set commande_num=" . sql_safe($commande_num) . ", commande_date=" . sql_safe($commande_modif_date) . " where id=" . decrypte($commande_passage);
			$base->query($sql);
			
			$commande_modif = $commande_passage;
			
			// On regarde si un paiement comptant pour directement inséré le suivi paiement
			$commande = montantCommande($rco["id"]);
			if ($rco["paiement_nombre"]==1) {
				$echeance = explode("/",$rde["paiement_modele"]);
				if ($commande["remise"]==0) { 
					$montant_a_payer = safe_number_format($commande["commande_ttc"],2,".","");
				} else { 
					$montant_a_payer = safe_number_format($commande["commande_remise_ttc"],2,".","");
				}
				$sql = "delete from commandes_paiements where id=" . decrypte($commande_passage);
				$base->query($sql);
				
				// On insere le paiement
				$sql = "insert into commandes_paiements values(" . decrypte($commande_passage) . ",'1'," . sql_safe(Date("Y-m-d H:i:s")) . "," . sql_safe($montant_a_payer) . ",'1','',0,'0000-00-00 00:00:00')";
				$base->query($sql);
				
				if ($rco["facture_num"]==0) {
					// ON genere le numero de facture
					$facture_deb = Date("Y") * 100000 + Date("n") * 1000;
					$sql = "select max(facture_num) val from commandes where facture_num>" . sql_safe($facture_deb) . " and showroom_num=" . sql_safe($rco["showroom_num"]);
					$rdd = $base->queryRow($sql);
					if ($rdd) {
						if ($rdd["val"]>0)
							$facture_num = $rdd["val"]+1;
						else
							$facture_num = $facture_deb + 1 ;
					} else {
						$facture_num = $facture_deb + 1 ;
					}
					
					$sql = "update commandes set facture_num=" . sql_safe($facture_num) . ",facture_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where id=" . decrypte($commande_passage);
					$base->query($sql);
					
					// On decroit les stocks
					$sql = "select * from commandes where id=" . decrypte($commande_passage);
					$rcc = $base->queryRow($sql);
					if ($rcc) {
						$showroom_num = $rcc["showroom_num"];
						// On recupere les produits de la commande pour les enlever du stock
						$sql = "select * from commandes_produits where id=" . decrypte($commande_passage);
						$co = $base->query($sql);
						foreach ($co as $rco) {
							$sql = "select * from stocks where produit_num=" . sql_safe($rco["produit_num"]) . " and taille_num=" . sql_safe($rco["taille_num"]) . " and showroom_num=" . sql_safe($showroom_num);
							$rss = $base->queryRow($sql);
							if ($rss) {
								// On update les stocks
								$stock_virtuel = $rss["stock_virtuel"] - $rco["qte"];
								$stock_reel = $rss["stock_reel"] - $rco["qte"];
								
								$sql = "update stocks set stock_virtuel=" . sql_safe($stock_virtuel) . ", stock_reel=" . sql_safe($stock_reel) . " where produit_num=" . sql_safe($rco["produit_num"]) . " and taille_num=" . sql_safe($rco["taille_num"]) . " and showroom_num=" . sql_safe($showroom_num);
								$base->query($sql);
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
	if (($modif ?? '')=="ok") {
		$sql = "update commandes_paiements set paiement_montant=" . sql_safe($montant) . ", mode_num=" . sql_safe($mode) . ", cheque_num=" . sql_safe($num) . " where id=" . decrypte($commande_modif) . " and paiement_num=" . sql_safe($echeance);
		$base->query($sql);
	} else {
		if ($nbr_echeance>1) {
			
			if ($montant<=$reste_a_payer) {
				$sql = "delete from commandes_paiements where id=" . decrypte($commande_modif) . " and paiement_num=" . sql_safe($echeance);
				$base->query($sql);
				
				// On insere le paiement
				$sql = "insert into commandes_paiements values(" . decrypte($commande_modif) . "," . sql_safe($echeance) . "," . sql_safe($date) . "," . sql_safe($montant) . "," . sql_safe($mode) . "," . sql_safe($num) . ",0,'0000-00-00 00:00:00')";
				$base->query($sql);
				
				if ($echeance==$nbr_echeance) { // Le paiement est terminé, on génére la facture
					// On regarde si il n'y a pas déjà un numero de facture 
					$sql = "select * from commandes where id=" . decrypte($commande_modif);
					$rco = $base->queryRow($sql);
 					if ($rco) {
						if ($rco["facture_num"]==0) {
							// ON cherche le numero de facture
							$facture_deb = Date("Y") * 100000 + Date("n") * 1000;
							$sql = "select max(facture_num) val from commandes where facture_num>" . sql_safe($facture_deb) . " and showroom_num=" . sql_safe($rco["showroom_num"]);
							$rdd = $base->queryRow($sql);
							if ($rdd) {
								if ($rdd["val"]>0)
									$facture_num = $rdd["val"]+1;
								else
									$facture_num = $facture_deb + 1 ;
							} else {
								$facture_num = $facture_deb + 1 ;
							}
							
							$sql = "update commandes set facture_num=" . sql_safe($facture_num) . ",facture_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where id=" . decrypte($commande_modif);
							$base->query($sql);
							
							// On decroit les stocks
							$sql = "select * from commandes where id=" . decrypte($commande_modif);
							$rcc = $base->queryRow($sql);
							if ($rcc) {
								$showroom_num = $rcc["showroom_num"];
								// On recupere les produits de la commande pour les enlever du stock
								$sql = "select * from commandes_produits where id=" . decrypte($commande_modif);
								$co = $base->query($sql);
								foreach ($co as $rco) {
									$sql = "select * from stocks where produit_num=" . sql_safe($rco["produit_num"]) . " and taille_num=" . sql_safe($rco["taille_num"]) . " and showroom_num=" . sql_safe($showroom_num);
									$rss = $base->queryRow($sql);
									if ($rss) {
										// On update les stocks
										$stock_virtuel = $rss["stock_virtuel"] - $rco["qte"];
										$stock_reel = $rss["stock_reel"] - $rco["qte"];
										
										$sql = "update stocks set stock_virtuel=" . sql_safe($stock_virtuel) . ", stock_reel=" . sql_safe($stock_reel) . " where produit_num=" . sql_safe($rco["produit_num"]) . " and taille_num=" . sql_safe($rco["taille_num"]) . " and showroom_num=" . sql_safe($showroom_num);
										$base->query($sql);
									}
								}							
							}
							
							// Si on est dans le showroom de Montpellier on decroit les stock du WEBSHOP
							if ($showroom_num===1) {
								majStockWeb($commande_passage);
							}
						}
					}
				}
			} else {
				$message_erreur_paiement = "Attention le montant de l'acompte est supérieur au reste à régler !";
			}
		} else { // La facture a déjà été réglé c'est juste une modif du mode de paiement comptant
			$sql = "update commandes_paiements set mode_num=" . sql_safe($mode) . ", cheque_num=" . sql_safe($num) . " where id=" . decrypte($commande_modif) . " and paiement_num=" . sql_safe($echeance);
			$base->query($sql);
		}
	}
}

if (isset($suppr_rdv_num)) {
	$sql = "delete from rendez_vous where rdv_num=" . decrypte($suppr_rdv_num);
	$base->query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from calendriers where rdv_num=" . decrypte($suppr_rdv_num);
	$base->query($sql);
}

if (isset($selection_suppr)) {
	$sql = "delete from selections where selection_num=" . decrypte($selection_suppr);
	$base->query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from selections_produits where selection_num=" . decrypte($selection_suppr);
	$base->query($sql);
}

if (isset($devis_suppr)) {
	$sql = "delete from commandes where id=" . decrypte($devis_suppr);
	$base->query($sql);
	
	// On efface dans le calendrier du user
	$sql = "delete from commandes_produits where id=" . decrypte($devis_suppr);
	$base->query($sql);
}

if (isset($commande_suppr)) {
	$sql = "update commandes set commande_num='0' where id=" . decrypte($commande_suppr);
	$base->query($sql);
	
	$devis_modif = $commande_suppr;
	$tab = "tab_1_3";
}

if (isset($paiement_suppr)) {
	$sql = "delete from commandes_paiements where id=" . decrypte($paiement_suppr) . " and paiement_num=" . sql_safe($echeance);
	$base->query($sql);
	$commande_modif = $paiement_suppr;
}

if (isset($devis)) {
	// ON cherche le numero de devis 
	$devis_deb = Date("Y") * 10000;
	$sql = "select max(devis_num) val from commandes where devis_num>" . sql_safe($devis_deb);
	$rdd = $base->queryRow($sql);
	if ($rdd) {
		if ($rdd["val"]>0)
			$devis_num = $rdd["val"]+1;
		else
			$devis_num = $devis_deb + 1 ;
	} else {
		$devis_num = $devis_deb + 1 ;
	}
	
	// On créé un devis
	$sql = "insert into commandes values(0," . decrypte($client_num). "," . sql_safe($devis_num) . "," . sql_safe(Date("Y-m-d H:i:s")) . ",'0','0000-00-00 00:00:00','0','0000-00-00 00:00:00','0','0','0','0','0','3'," . sql_safe($u->mNum) . "," . sql_safe($u->mShowroom) . ")";
	$base->query($sql);
}

if (isset($devis_envoi)) { // ON envoie le devis par mail
	// On test si toutes les tailles sont renseignées
	$sql = "select * from commandes_produits where id=" . decrypte($devis_envoi) . " and taille_num='-1'";
	$tt = $base->query($sql);
	$nbr = count($tt);
	if ($nbr==0) { // On passe la commande
		// On envoi le mail avec le devis
		$template = getEmailTemplate(8,$rcl["client_genre"]);
		$titre_mail = $template["titre"];
		$message_mail = $template["message"];
		$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
		$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
		$message_mail = str_replace("[DEVIS_NUM]",$devis_envoi,$message_mail);
		
		$sql = "select * from commandes co, paiements p where co.paiement_num=p.paiement_num and id=" . decrypte($devis_envoi);
		$rde = $base->queryRow($sql);
		if ($rde) {
			$commande = montantCommande($rde["id"]);
			if ($rde["paiement_nombre"]>1) {
				$echeance = explode("/",$rde["paiement_modele"]);
				if ($commande["remise"]==0) { 
					$montant_a_payer = safe_number_format($commande["commande_ttc"],2,".","");
					$acompte = safe_number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				} else { 
					$montant_a_payer = safe_number_format($commande["commande_remise_ttc"],2,".","");
					$acompte = safe_number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				}
				$message_acompte = "accompagné du paiement du premier acompte de " . $echeance[0] . "% (" . $acompte . " &euro;)";
				$message_mail = str_replace("[ACOMPTE_VALEUR]",$message_acompte,$message_mail);
						
				$message_suite_acompte = "</p>Pour information, nous vous demanderons ensuite les écheances de paiement suivantes : ";
								
				$echeance_desc = explode("/",$rde["paiement_description"]);
				for ($i=1;$i<$rde["paiement_nombre"];$i++) {
					$acompte_val = safe_number_format(($montant_a_payer*($echeance[$i]/100)),2,"."," ");
					$message_suite_acompte .= $echeance[$i] .'% ' . $echeance_desc[$i] . ' ('. $acompte_val . '&euro;)';
					if ($i<($rde["paiement_nombre"]-1))
						$message_suite_acompte .= " et ";
				}
				$message_suite_acompte .= ".</p>";
				$message_mail = str_replace("[ACOMPTE_SUITE]",$message_suite_acompte,$message_mail);
			}
		}
		
		// ON regarde si il y a une robe et si elle est sur mesure
		$sql = "select * from commandes_produits where taille_num='35' and id=" . decrypte($devis_envoi);
		$rtt = $base->queryRow($sql);
 		if ($rtt) {
			$message_retouche = "";
		} else {
			$message_retouche = "";
		}
		
		$message_mail = str_replace("[RETOUCHE]",$message_retouche,$message_mail);
		
		// On envoi le mail
		SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));

		$sql = "delete from commandes_mails where id=" . decrypte($devis_envoi);
		$base->query($sql);
		
		$sql = "insert into commandes_mails values(" . decrypte($devis_envoi) . ",'1'," . sql_safe(Date("Y-m-d H:i:s")) . ",0,'0000-00-00 00:00:00')";
		$base->query($sql);
	} else {
		$message_erreur_devis = "Vous devez renseigner toutes les tailles avant d'envoyer le devis !";
		$devis_modif = $devis_envoi;
		$tab = "tab_1_3";
	}
}

if (isset($facture_envoi)) { // ON envoie le devis par mail
	// On envoi le mail avec le devis
	$template = getEmailTemplate(9,$rcl["client_genre"]);
	$titre_mail = $template["titre"];
	$message_mail = $template["message"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
	$message_mail = str_replace("[FACTURE_NUM]",$facture_envoi,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "select * from commandes_mails where id=" . decrypte($facture_envoi);
	$rtt = $base->queryRow($sql);
 	if ($rtt) {
		$sql = "update commandes_mails set facture_mail=1, facture_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where id=" . decrypte($facture_envoi);
		$base->query($sql);
	} else {
		$sql = "insert into commandes_mails values(" . decrypte($facture_envoi) . ",0,'0000-00-00 00:00:00','1'," . sql_safe(Date("Y-m-d H:i:s")) . ")";
		$base->query($sql);
	}
}

if (isset($acompte_envoi)) { // ON envoie le devis par mail
	// On envoi le mail avec le devis
	$template = getEmailTemplate(10,$rcl["client_genre"]);
	$titre_mail = $template["titre"];
	$message_mail = $template["message"];
	$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
	$titre_mail = str_replace("[PAIEMENT_NUM]",$paiement,$titre_mail);
	$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
	$message_mail = str_replace("[COMMANDE_NUM]",$acompte_envoi,$message_mail);
	$message_mail = str_replace("[PAIEMENT_NUM]",$paiement_echeance,$message_mail);
	
	// On envoi le mail
	SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
	
	$sql = "update commandes_paiements set paiement_mail=1, paiement_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where id=" . decrypte($acompte_envoi) . " and paiement_num=" . sql_safe($paiement_echeance);
	$base->query($sql);
	
	$commande_modif = $acompte_envoi;
}

if (isset($selection)) {
	// On créé une sélection
	$sql = "insert into selections values(0," . sql_safe(Date("Y-m-d H:i:s")) . ",'0','0000-00-00'," . decrypte($client_num). "," . sql_safe($u->mNum) . "," . sql_safe($rcl["showroom_num"]) . ")";
	$base->query($sql);
}

if (isset($cdefournisseur)) {
	// On efface la commande fournisseur en cours
	$sql = "delete from commandes_fournisseurs where id=" . decrypte($id) . " and produit_num=" . decrypte($produit);
	$base->query($sql);
	
	$sql = "insert into commandes_fournisseurs values(" . decrypte($id) . "," . decrypte($produit) . "," . sql_safe($marque) . "," . sql_safe($livraison) . "," . sql_safe($fournisseur_commande_ref) . "," . sql_safe($fournisseur_remarque) . "," . sql_safe($fournisseur_poitrine) . "," . sql_safe($fournisseur_sous_poitrine) . "," . sql_safe($fournisseur_taille) . "," . sql_safe($fournisseur_hanche1) . "," . sql_safe($fournisseur_hanche2) . "," . sql_safe($fournisseur_biceps) . "," . sql_safe($fournisseur_carrure_avant) . "," . sql_safe($fournisseur_carrure_dos) . "," . sql_safe($fournisseur_longueur_dos) . "," . sql_safe($fournisseur_taille_sol) . "," . sql_safe($fournisseur_taille_choisie) . "," . sql_safe($fournisseur_montant) . "," . sql_safe($fournisseur_commande_date) . ",0,1," . sql_safe(Date("Y-m-d H:i:s")) . ")";
	$base->query($sql);
}

if (isset($paiementfournisseur)) {
	// On efface le paiement pour le réinsérer
	$sql = "delete from commandes_fournisseurs_paiements where id=" . decrypte($id) . " and produit_num=" . decrypte($produit);
	$base->query($sql);
	
	$sql = "insert into commandes_fournisseurs_paiements values(" . decrypte($id) . "," . decrypte($produit) . "," . sql_safe($fournisseur_paiement1) . "," . sql_safe($fournisseur_paiement2) . "," . sql_safe($fournisseur_paiement3) . "," . sql_safe($fournisseur_paiement1_date) . "," . sql_safe($fournisseur_paiement2_date) . "," . sql_safe($fournisseur_paiement3_date) . ")";
	$base->query($sql);
}

$titre_page = "Client " . $rcl["client_nom"] . " " . $rcl["client_prenom"] . " - Olympe Mariage";
$desc_page = "Client " . $rcl["client_nom"] . " " . $rcl["client_prenom"] . " - Olympe Mariage";

$link_plugin = '<link href="/assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />';
echo '<style>.mt-element-overlay .mt-overlay-1 { height:110!important; } </style>';
include TEMPLATE_PATH . 'head.php'; 
?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <?php include TEMPLATE_PATH . 'top.php'; ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1><?= $rcl["client_nom"] . " " . $rcl["client_prenom"] ?></h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Client <?= $rcl["client_nom"] . " " . $rcl["client_prenom"] ?></li>
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
											<div class="profile-usertitle-name"> <?= $rcl["client_nom"] . " " . $rcl["client_prenom"] ?> </div>
											<div class="profile-usertitle-job"> <?php if ($rcl["client_genre"]==0) echo "Femme"; else echo "Homme"; ?> </div>
										</div>
										<!-- SIDEBAR MENU -->
										<div class="profile-usermenu">
											<ul class="nav">
												<li>
													<a href="mailto:<?= $rcl["client_mail"] ?>">
														<i class="fa fa-envelope"></i> <?= $rcl["client_mail"] ?> </a>
												</li>
												<li>
													<a href="tel:<?= $rcl["client_tel"] ?>">
														<i class="fa fa-phone"></i> <?= $rcl["client_tel"] ?> </a>
												</li>
												<li>
													<a href="#">
														<i class="fa fa-heart"></i> <?= format_date($rcl["client_date_mariage"],11,1) ?> </a>
												</li>
												<li>
													<a href="#">
														<i class="fa fa-map-marker"></i> <?= $rcl["client_lieu_mariage"]  ?> </a>
												</li>
												<?php 
													$sql = "select * from users where user_num=" . sql_safe($rcl["user_num"]);
													$rtt = $base->queryRow($sql);
 													if ($rtt) {
														echo '<li>
															<a href="#">
																<i class="fa fa-eye"></i> Suivi par : ' . $rtt["user_prenom"] . ' ' . $rtt["user_nom"]  . '</a>
														</li>';
													}
												?>
												<?php 
													$sql = "select * from users where user_num=" . sql_safe($rcl["couturiere_num"]);
													$rtt = $base->queryRow($sql);
 													if ($rtt) {
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
									<?php											
											$sql = "select * from selections where client_num='" . decrypte($client_num). "' order by selection_date DESC";
											$ss = $base->query($sql);
											$nbr_selection = count($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num). "' and devis_num>0 and commande_num=0 and facture_num=0 order by commande_date DESC";
											$ss = $base->query($sql);
											$nbr_devis = count($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num). "' and devis_num>0 and commande_num>0 order by commande_date DESC";
											$ss = $base->query($sql);
											$nbr_commande = count($ss);
											
											$sql = "select * from commandes where client_num='" . decrypte($client_num). "' and devis_num>0 and commande_num>0 order by commande_date DESC";
											$ss = $base->query($sql);
											$commande_ttc = 0;
											foreach ($ss as $rss) 
												$commande_ttc += montantCommandeTTC($rss["id"]);
									?>
									<div class="portlet light bordered">
										<!-- STAT -->
										<div class="row list-separated profile-stat">
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <?= $nbr_selection ?> </div>
												<div class="uppercase profile-stat-text"> Sél. </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <?= $nbr_devis ?> </div>
												<div class="uppercase profile-stat-text"> Devis </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <?= $nbr_commande ?> </div>
												<div class="uppercase profile-stat-text"> Com. </div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<div class="uppercase profile-stat-title"> <?= $commande_ttc ?>€ </div>
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
													<?php if ($u->mGroupe!=0) { ?>
														<li<?php if ($tab=="tab_1_1") echo ' class="active"'?>>
															<a href="#tab_1_1" data-toggle="tab">Prise de RDV</a>
														</li>
														<li<?php if ($tab=="tab_1_2") echo ' class="active"'?>>
															<a href="#tab_1_2" data-toggle="tab">Selection</a>
														</li>
														<li<?php if ($tab=="tab_1_3") echo ' class="active"'?>>
															<a href="#tab_1_3" data-toggle="tab">Devis</a>
														</li>
														<li<?php if ($tab=="tab_1_4") echo ' class="active"'?>>
															<a href="#tab_1_4" data-toggle="tab">Commande</a>
														</li>
													<?php } ?>
														<li<?php if ($tab=="tab_1_6") echo ' class="active"'?>>
															<a href="#tab_1_6" data-toggle="tab">Modifier</a>
														</li>
													</ul>
												</div>
												<div class="portlet-body">
													<div class="tab-content">
														<?php if ($u->mGroupe!=0) { ?>
														<div class="tab-pane<?php if ($tab=="tab_1_1") echo " active"?>" id="tab_1_1">
														<?php 
															$sql = "select * from rdv_types order by type_pos ASC";
															$tt = $base->query($sql);
															foreach ($tt as $rtt) { 
																// On test si on a déjà rentré dans la base le RDV
																$sql = "select * from rendez_vous where client_num='" . decrypte($client_num). "' and type_num=" . sql_safe($rtt["type_num"]);
																$rcc = $base->queryRow($sql);
																$etat=0;
																$num=0;
																$remarque = "";
																$mail = 0;
																$mail_relance = 0;
																$date = "";
																$heure = "";
																if ($rcc) {
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
																<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
																<input type="hidden" name="tab" value="tab_1_1">
																<input type="hidden" name="client_num" value="<?= $client_num ?>">
																<input type="hidden" name="type_num" value="<?= $rtt["type_num"] ?>">
																<input type="hidden" name="rdv_num" value="<?= crypte($num) ?>">
																<table class="table table-hover table-advance table-striped">
																	<thead>
																		<tr>
																			<th class="font-blue-steel"><strong><?= $rtt["type_nom"] ?></strong></th>
																			<th> </th>
																			<th> </th>
																			<th> </th>
																		</tr>
																	</thead>
																	<tbody>
																	<tr>
																		<td><input type="date" name="date" class="form-inline" placeholder="" value="<?= $date ?>">
																			<input type="time" name="time" class="form-inline" placeholder="" value="<?= $heure ?>">
																		</td>
																		<td>
																			<?php 
																				if ($rtt["type_num"]==2) {
																					echo 'Atelier de <input type="text" name="remarque" value="' . $remarque . '" class="form-inline">';
																					echo '<input type="hidden" name="dernier_acompte" value="0">';
																				} else {
																					echo '<input type="hidden" name="remarque" value="">';
																					if ($rtt["type_num"]==5) {
																						// On recherche les commandes en cours non facturée
																						$sql = "select * from commandes where client_num='" . decrypte($client_num). "' and devis_num!=0 and commande_num!=0 and facture_num=0 order by commande_date DESC";
																						$co = $base->query($sql);
																						$nbr_commande = count($co);
																						if ($nbr_commande>0) {
																							echo '<select name="dernier_acompte" class="form-control">';
																							foreach ($co as $rco) {
																								$dernier_acompte = safe_number_format(resteAPayerCommande($rco["id"]),2,"."," ");
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
																			<?php if ($etat==0) { ?>
																				<input type="submit" value="Ok" class="btn btn-outline btn-circle btn-sm purple">
																			<?php } else { ?>
																				<input type="submit" value="Modifier" class="btn btn-outline btn-circle btn-sm purple"> 
																				<a href="client?client_num=<?= crypte($rcc["client_num"]) ?>&suppr_rdv_num=<?= crypte($num) ?>"  class="btn btn-outline btn-circle dark btn-sm black" data-confirm="confirme_annulation_rdv"> Annuler</a>
																			<?php } ?>
																		</td>
																		<td>
																			<?php 
																				if (($mail==1) && ($mail_date!="0000-00-00 00:00:00")) { echo '<small><strong>Mail envoyé le : </strong>' . format_date($mail_date,2,1) . '</small>';}
																				if (($mail_relance==1) && ($mail_relance_date!="0000-00-00 00:00:00")) { echo '<br><small><strong>Mail relance envoyé le : </strong>' . format_date($mail_date,2,1) . '</small>';}
																			?>
																		</td>
																	</tr>
																	</tbody>
																</table>
																</form>
														<?php } ?>
														</div>
														<!-- END CHANGE AVATAR TAB -->
														<!-- CHANGE PASSWORD TAB -->
														<div class="tab-pane<?php if ($tab=="tab_1_2") echo " active"?>" id="tab_1_2">
															<h4><i class="fa fa-plus"></i> Liste des sélections</h4>
															<?php																
																$sql = "select * from selections where client_num='" . decrypte($client_num). "' order by selection_date DESC";
																$ss = $base->query($sql);
																$nbr_selection = count($ss);
																if ($nbr_selection>0) {
																	echo '<table class="table table-bordered table-striped">
																			<thead>
																				<th>Date</th>
																				<th>Sélection</th>
																				<th></th>
																			</thead>
																			<tbody>';
																	foreach ($ss as $rss) {
																		echo '<tr>
																				<td>' . format_date($rss["selection_date"],11,1) . '</td>
																				<td id="select_' . $rss["selection_num"] . '">
																					<div class="mt-element-card mt-element-overlay">';
																		// On affiche les produits sélectionnés
																		$sql = "select * from selections_produits s, md_produits p where s.produit_num=p.produit_num and selection_num=" . sql_safe($rss["selection_num"]);
																		$pp = $base->query($sql);
																		$nbr_pp = count($pp);
																		if ($nbr_pp>0) {
																			foreach ($pp as $rpp) {
																				$sql = "select * from md_produits_photos where produit_num=" . sql_safe($rpp["produit_num"]) . " and photo_pos=1";
																				$rph = $base->queryRow($sql);
																				if ($rph) {
																					$image_pdt = "/photos/produits/min/" . $rph["photo_chemin"];
																				} else 
																					$image_pdt = "https://placehold.co/50x50?text=No+image";
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
																					<a href="' . current_path() . '?client_num=' . $client_num . '&selection_ajout=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-plus"></i> Ajouter</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&selection_envoi=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&selection_devis=' . crypte($rss["selection_num"]) . '&tab=tab_1_3" data-confirm="confirmeDevis" class="btn btn-outline btn-circle dark btn-sm purple"><i class="fa fa-euro"></i> Devis</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&selection_suppr=' . crypte($rss["selection_num"]) . '&tab=tab_1_2" data-confirm="confirme" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>';
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
															<?php if (!isset($selection_ajout)) { ?>
																<center><a href="<?= current_path() ?>?client_num=<?= $client_num ?>&selection=ok&tab=tab_1_2" class="btn btn-lg red"> <i class="fa fa-plus"></i> Créer une sélection</a></center>
															<?php } else { ?>
																<h4><i class="fa fa-plus"></i> Ajouter des produits à la sélection</h4>
																<div class="row">
																	<div class="col-md-4">
																		<form name="rechercher" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
																			<input type="hidden" name="recherche_produit" value="ok">
																			<input type="hidden" name="client_num" value="<?= $client_num ?>">
																			<input type="hidden" name="selection_ajout" value="<?= $selection_ajout ?>">
																			<input type="hidden" name="tab" value="tab_1_2">
																			<table class="table table-striped table-bordered table-advance table-hover">
																				<tbody>
																					<tr>
																						<td><label>Nom</label>
																						<div class="input-group">
																							<span class="input-group-addon">
																								<i class="fa fa-list"></i>
																							</span>
																							<input type="text" name="nom" class="form-control" value="<?= ($nom ?? '') ?>"></div></td>
																					</tr>
																					<tr>
																						<td><label>Categorie</label>
																						<div class="input-group">
																							<select name="categorie">
																							<option value="0">-----------------</option>
																							<?php 
																							$sql = "select * from categories order by categorie_nom ASC";
																							$cc = $base->query($sql);
																							foreach ($cc as $rcc)
																							{
																								echo "<option value=\"" . $rcc["categorie_num"] . "\"";
																								if (($categorie ?? 0)==$rcc["categorie_num"])
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
																							<?php 
																							$sql = "select * from marques order by marque_nom ASC";
																							$cc = $base->query($sql);
																							foreach ($cc as $rcc)
																							{
																								echo "<option value=\"" . $rcc["marque_num"] . "\"";
																								if (($marque ?? 0)==$rcc["marque_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["marque_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><input type="submit" value="Rechercher" class="btn blue"> <a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_2" class="btn red">Annuler</a></td>
																					</tr>
																				</tbody>
																			</table>									
																		</form>
																	</div>
																	<div class="col-md-8">
																		<div class="mt-element-card mt-element-overlay">
																			<div class="row">
																			<?php if (isset($recherche_produit)) { 
																					$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and produit_etat='1'";
																					if ($categorie!=0)
																						$sql .= " and p.categorie_num=" . sql_safe($categorie);
																					if ($marque!=0)
																						$sql .= " and p.marque_num=" . sql_safe($marque);
																					if ($nom!="") {
																						$nom = str_replace("'","\'",$nom);
																						$sql .= " and produit_nom like '%" . $nom . "%'";
																					}
																					$sql .= " order by categorie_nom ASC, produit_nom ASC";
																					$cc = $base->query($sql);
																					$nbr_produit = count($cc);
																					if ($nbr_produit>0) {
																						foreach ($cc as $rcc) { 
																							$sql = "select * from md_produits_photos where produit_num=" . sql_safe($rcc["produit_num"]) . " and photo_pos=1";
																							$rpp = $base->queryRow($sql);
 																							if ($rpp) {
																								$image_pdt = "/photos/produits/min/" . $rpp["photo_chemin"];
																							} else 
																								$image_pdt = "https://placehold.co/200x200";
																							//echo '<div class="col-md-3"><a href=""><figure><figcaption>' . $rcc["produit_nom"] . '</figcaption><img src="' . $image_pdt . '" class="img-responsive"></figure></div>';
																							echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
																								<div class="mt-card-item">
																									<div class="mt-card-avatar mt-overlay-1">
																										<figure style="height:100px;overflow:hidden;position:relative;line-height:100px;">
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
															<?php } ?>
														</div>
														<!-- END CHANGE PASSWORD TAB -->
														<!-- PRIVACY SETTINGS TAB -->
														<div class="tab-pane<?php if ($tab=="tab_1_3") echo " active"?>" id="tab_1_3">
															<h4><i class="fa fa-plus"></i> Liste des devis en cours</h4>
															<?php																
																$sql = "select * from commandes where devis_num!=0 and commande_num=0 and facture_num=0 and client_num='" . decrypte($client_num). "' order by devis_date DESC";
																$ss = $base->query($sql);
																$nbr_devis = count($ss);
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
																	foreach ($ss as $rss) {
																		$sql = "select * from commandes_produits where id=" . sql_safe($rss["id"]);
																		$pp = $base->query($sql);
																		$nbr_produit = count($pp);
																		
																		echo '<tr>
																				<td>' . $rss["devis_num"] . '</td>
																				<td>' . format_date($rss["devis_date"],11,1) . '</td>
																				<td>' . $nbr_produit . '</td>
																				<td>' . safe_number_format(montantCommandeTTC($rss["id"]),2) . ' €</td>
																				<td class="text-center"> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&devis_modif=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-plus"></i> Modifier</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&devis_consulte=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm green"><i class="fa fa-book"></i> Consulter</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&devis_envoi=' . crypte($rss["id"]) . '&tab=tab_1_3" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer</a> 
																					<a href="#" onClick="window.open(\'/devis/index?devis=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Imprimer</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&devis_suppr=' . crypte($rss["id"]) . '&tab=tab_1_3" data-confirm="confirmeSupprDevis" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>
																				</td>
																				<td>';
																				$sql = "select * from commandes_mails where id=" . sql_safe($rss["id"]) . " and devis_mail=1";
																				$rdm = $base->queryRow($sql);
																				if ($rdm) {
																					echo '<small><strong>Devis envoyé le : </strong>' . format_date($rdm["devis_mail_date"],2,1) . '</small>';
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
															<?php if ((!isset($devis_ajout)) && (!isset($devis_modif)) && (!isset($devis_consulte)))  { ?>
																<center><a href="<?= current_path() ?>?client_num=<?= $client_num ?>&devis=ok&tab=tab_1_3" class="btn btn-lg red"> <i class="fa fa-plus"></i> Créer un devis</a></center>
															<?php } ?>
															
															<?php if (isset($devis_consulte)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id=" . decrypte($devis_consulte);
																	$rcc = $base->queryRow($sql);
																	if ($rcc) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Devis n° : <?= $rcc["devis_num"] ?></h4>
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
															<?php 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id=" . decrypte($devis_consulte);
																		$pp = $base->query($sql);
																		foreach ($pp as $rpp) {
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
																				if (safe_number_format($prix_total_ttc,2)<=0)
																					echo "OFFERT";
																				else
																					echo safe_number_format($prix_total_ttc,2) . ' €';
																				echo '</td>
																			</tr>';
																		} ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total HT</strong></td>
																			<td><?= safe_number_format($commande["commande_ht"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>TVA (20%)</strong></td>
																			<td><?= safe_number_format($commande["commande_tva"],2,"."," ") ?> €</td>
																		</tr>
																		<?php if ($commande["remise"]==0) { 
																				$montant_a_payer = safe_number_format($commande["commande_ttc"],2,".","");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><?= safe_number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<?php } else { 
																				$montant_a_payer = safe_number_format($commande["commande_remise_ttc"],2,".","");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><?= safe_number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="6" align="right"><strong>Remise</strong></td>
																			<td><?= $commande["remise"] ?></td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>Total à payer</strong></td>
																			<td><?= safe_number_format($commande["commande_remise_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<?php } ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Méthode de paiement</strong></td>
																			<td><?= $rcc["paiement_titre"] ?></td>
																		</tr>
																		<?php																			
																				if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				$echeance = explode("/",$rcc["paiement_modele"]);
																				$acompte_num = 1;
																				foreach ($echeance as $val) {
																					$acompte_val = safe_number_format(($montant_a_payer*($val/100)),2,"."," ");
																					echo '<tr>
																							<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																							<td>' . $acompte_val . ' €</td>
																						</tr>';
																					$acompte_num++;
																				}
																			}
																		?>																		
																		<tr><td colspan="7" align="right"><a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_3" class="btn red">Fermer</a></td></tr>
																	</tbody>
																</table>
															<?php 	}
																} ?>
															
															<?php if (isset($devis_modif)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id=" . decrypte($devis_modif);
																	$rcc = $base->queryRow($sql);
																	if ($rcc) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Devis n° : <?= $rcc["devis_num"] ?> 
																<?php if (($message_erreur_devis ?? '') !="") 
																	echo ' - <i class="fa fa-warning"></i> <font class="font-red-thunderbird"><strong>' . $message_erreur_devis . '</strong></font>'; ?>
																</h4>
																<table class="table table-bordered table-striped">
																	<thead>
																		<th colspan="2">Produit</th>
																		<th>Taille</th>
																		<th>Prix Unitaire</th>
																		<th><center>Qte</center></th>
																		<th>Montant</th>
																		<th>Remise</th>
																	</thead>
																	<tbody id="devis_<?= $rcc["id"] ?>">
																	<?php 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id=" . decrypte($devis_modif);
																		$pp = $base->query($sql);
																		foreach ($pp as $rpp) {
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
																			$sql = "select * from stocks where taille_num=" . $rpp["taille_num"] . " and produit_num=" . $rpp["produit_num"] . " and showroom_num=" . sql_safe($u->mShowroom);
																			$rss = $base->queryRow($sql);
																			if ($rss) {
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
																			$ss = $base->query($sql);
																			foreach ($ss as $st) {
																				echo '<option value="' . $st["taille_num"] . '"';
																				if ($st["taille_num"]==$rpp["taille_num"])
																					echo " SELECTED";
																				echo '>' . $st["taille_nom"] . '</option>';
																			}
																			echo '</select></td>
																				<td>' . safe_number_format($rpp["montant_ttc"],2,"."," ") . ' €</td>
																				<td align="center"><select name="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" id="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" onChange="modifQte(' . decrypte($devis_modif) . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"] . ');">';
																				for ($i=0;$i<=$stock;$i++) {
																					echo '<option value="' . $i . '"';
																					if ($i==$rpp["qte"])
																						echo " SELECTED";
																					echo '>' . $i . '</option>';
																				}
																			echo '</select></td>
																				<td>';
																					if (safe_number_format($prix_total_ttc,2)<=0)
																						echo "OFFERT";
																					else
																						echo safe_number_format($prix_total_ttc,2,"."," ") . ' €';
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
																			<td colspan="2"><?= safe_number_format($commande["commande_ht"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>TVA (20%)</strong></td>
																			<td colspan="2"><?= safe_number_format($commande["commande_tva"],2,"."," ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>Total TTC</strong></td>
																			<td colspan="2"><?= safe_number_format($commande["commande_ttc"],2,"."," ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="5" align="right"><strong>Remise</strong></td>
																			<td colspan="2"><input type="text" name="remise_montant" id="remise_montant" value="<?= $commande["commande_remise"] ?>" class="form-inline input-xsmall"> 
																				<select name="remise_type" id="remise_type" class="form-inline input-xsmall" onChange="remiseCommande(<?= $rcc["id"]?>)">
																					<option value="0">--</option>
																					<option value="1"<?php if ($commande["commande_remise_type"]==1) echo " SELECTED"; ?>>%</option>
																					<option value="2"<?php if ($commande["commande_remise_type"]==2) echo " SELECTED"; ?>>€</option>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td colspan="5" align="right"><strong>Total à payer</strong></td>
																			<td colspan="2"><?php 
																				if ($commande["commande_remise_type"]!=0) {
																					echo safe_number_format($commande["commande_remise_ttc"],2,"."," ");
																					$montant_a_payer = safe_number_format($commande["commande_remise_ttc"],2,".","");
																				}
																				else {
																					echo safe_number_format($commande["commande_ttc"],2,"."," ");
																					$montant_a_payer = safe_number_format($commande["commande_ttc"],2,".","");
																				}
																				?> €
																			</td>
																		</tr>	
																		<tr>
																			<td colspan="5" align="right"><strong>Méthode de paiement</strong></td>
																			<td colspan="2">
																				<select name="paiement_<?= $rcc["id"] ?>" id="paiement_<?= $rcc["id"] ?>" onChange="modifPaiement(<?= $rcc["id"] ?>)">
																				<?php																					
																					$sql = "select * from paiements order by paiement_pos ASC";
																					$pp = $base->query($sql);
																					foreach ($pp as $rpp) {
																						echo '<option value="' . $rpp["paiement_num"] . '"';
																						if ($rpp["paiement_num"]==$rcc["paiement_num"])
																							echo " SELECTED";
																						echo '>' . $rpp["paiement_titre"] . '</option>';
																					}
																				?>
																				</select>
																			</td>
																		</tr>
																		<?php if ($rcc["commande_date"]!="0000-00-00 00:00:00") {// On met la modification
																				$date_bdc_commande = substr($rcc["commande_date"],0,10);

																		?>
																		<tr>
																			<td colspan="5" align="right"><strong>Date de commande</strong></td>
																			<td colspan="2">
																				<input type="date" name="date_bdc" id="date_bdc" value="<?= $date_bdc_commande ?>" onChange="modifDateCommande(<?= $rcc["id"]?>)">
																			</td>
																		</tr>
																		<?php } ?>
																		<?php																			
																			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				$echeance = explode("/",$rcc["paiement_modele"]);
																				$acompte_num = 1;
																				foreach ($echeance as $val) {
																					$acompte_val = safe_number_format(($montant_a_payer*($val/100)),2,"."," ");
																					echo '<tr>
																							<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																							<td>' . $acompte_val . ' €</td>
																						</tr>';
																					$acompte_num++;
																				}
																			}
																		?>
																		<tr><td colspan="7" align="right"><a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_3" class="btn red">Fermer</a> <a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_4&commande_passage=<?= crypte($rcc["id"]) ?>" class="btn blue" data-confirm="confirme_commande" data-id="<?= $rcc["id"] ?>">Passer la commande</a></td></tr>
																	</tbody>
																</table>
															<?php 	} ?>
																<h4><i class="fa fa-plus"></i> Ajouter des produits au devis</h4>
																<div class="row">
																	<div class="col-md-4">
																		<form name="rechercher" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
																			<input type="hidden" name="recherche_produit" value="ok">
																			<input type="hidden" name="client_num" value="<?= $client_num ?>">
																			<input type="hidden" name="devis_modif" value="<?= $devis_modif ?>">
																			<input type="hidden" name="tab" value="tab_1_3">
																			<table class="table table-striped table-bordered table-advance table-hover">
																				<tbody>
																					<tr>
																						<td><label>Nom</label>
																						<div class="input-group">
																							<span class="input-group-addon">
																								<i class="fa fa-list"></i>
																							</span>
																							<input type="text" name="nom" class="form-control" value="<?= ($nom ?? '') ?>"></div></td>
																					</tr>
																					<tr>
																						<td><label>Categorie</label>
																						<div class="input-group">
																							<select name="categorie">
																							<option value="0">-----------------</option>
																							<?php 
																							$sql = "select * from categories order by categorie_nom ASC";
																							$cc = $base->query($sql);
																							foreach ($cc as $rcc)
																							{
																								echo "<option value=\"" . $rcc["categorie_num"] . "\"";
																								if (($categorie ?? 0)==$rcc["categorie_num"])
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
																							<?php 
																							$sql = "select * from marques order by marque_nom ASC";
																							$cc = $base->query($sql);
																							foreach ($cc as $rcc)
																							{
																								echo "<option value=\"" . $rcc["marque_num"] . "\"";
																								if (($marque ?? 0) ==$rcc["marque_num"])
																									echo " SELECTED";
																								echo ">" . $rcc["marque_nom"] . "</option>\n";
																							}
																						?>		
																							</select>
																						</div>
																						</td>
																					</tr>
																					<tr>
																						<td><input type="submit" value="Rechercher" class="btn blue"> 
																						<a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_3" class="btn red">Annuler</a></td>
																					</tr>
																				</tbody>
																			</table>									
																		</form>
																	</div>
																	<div class="col-md-8">
																		<div class="mt-element-card mt-element-overlay">
																			<div class="row">
																			<?php if (isset($recherche_produit)) { 
																					$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and produit_etat='1'";
																					if ($categorie!=0)
																						$sql .= " and p.categorie_num=" . sql_safe($categorie);
																					if ($marque!=0)
																						$sql .= " and p.marque_num=" . sql_safe($marque);
																					if ($nom!="") {
																						$nom = str_replace("'","\'",$nom);
																						$sql .= " and produit_nom like '%" . $nom . "%'";
																					}
																					$sql .= " order by categorie_nom ASC, produit_nom ASC";
																					$cc = $base->query($sql);
																					$nbr_produit = count($cc);
																					if ($nbr_produit>0) {
																						foreach ($cc as $rcc) { 
																							$sql = "select * from md_produits_photos where produit_num=" . sql_safe($rcc["produit_num"]) . " and photo_pos=1";
																							$rpp = $base->queryRow($sql);
 																							if ($rpp) {
																								$image_pdt = "/photos/produits/min/" . $rpp["photo_chemin"];
																							} else 
																								$image_pdt = "https://placehold.co/200x200?text=No+image";
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
															<?php } ?>
														</div>
														<!-- CHANGE COMMANDE TAB -->
														<div class="tab-pane<?php if ($tab=="tab_1_4") echo " active"?>" id="tab_1_4">
															<h4><i class="fa fa-plus"></i> Liste des commandes</h4>
															<?php																
																$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and devis_num!=0 and commande_num!=0 and client_num='" . decrypte($client_num). "' order by commande_date DESC";
																$ss = $base->query($sql);
																$nbr_commande = count($ss);
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
																	foreach ($ss as $rss) {
																		$nbr_echeance = $rss["paiement_nombre"];
																		
																		// On regarde le nombre de paiement effectué
																		$sql = "select * from commandes_paiements where id=" . sql_safe($rss["id"]);
																		$pa = $base->query($sql);
																		$nbr_paiement = count($pa);
																		
																		// On calcul la somme déjà payé
																		$montant_paye = 0;
																		$sql = "select sum(paiement_montant) val from commandes_paiements where id=" . sql_safe($rss["id"]);
																		$rpa = $base->queryRow($sql); 
																		if ($rpa)																			
																			$montant_paye = $rpa["val"];
																		
																		$reste_a_paye = safe_number_format(abs(montantCommandeTTC($rss["id"]) - $montant_paye),2,"."," ");
																																				
																		$sql = "select * from commandes_produits where id=" . sql_safe($rss["id"]);
																		$pp = $base->query($sql);
																		$nbr_produit = count($pp);
																		
																		$facture_num = "-";
																		if ($rss["facture_num"]!="")
																			$facture_num = $rss["facture_num"];
																		
																		echo '<tr>
																				<td>' . $rss["commande_num"] . '</td>
																				<td>' . format_date($rss["commande_date"],11,1) . '</td>
																				<td class="text-center">' . $nbr_produit . '</td>
																				<td>' . safe_number_format((float) montantCommandeTTC($rss["id"]), 2, ".", " ") . ' €</td>
																				<td class="text-center">' . $nbr_paiement . '/' . $nbr_echeance . '</td>
																				<td>' . safe_number_format((float) $montant_paye,2) . ' €</td>
																				<td>' . $reste_a_paye . ' €</td>
																				<td align="center">' . $facture_num . '</td>
																				<td> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&commande_modif=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm black"><i class="fa fa-euro"></i> Paiements</a> 
																					<a href="' . current_path() . '?client_num=' . $client_num . '&commande_consulte=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm green"><i class="fa fa-book"></i> Consulter</a>';
																		if ($rss["facture_num"]!=0) {
																			// ON regarde si la facture a été envoyé par mail
																			$sql = "select * from commandes_mails where id=" . sql_safe($rss["id"]) . " and facture_mail=1";
																			$rff = $base->queryRow($sql);
																			$envoye = "";
																			if ($rff) {
																				$envoye = " le " . format_date($rff["facture_mail_date"],11,1);
																			}
																			
																			echo '<a href="#" onClick="window.open(\'/facture/index?facture=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Facture</a> ';
																			echo '<a href="' . current_path() . '?client_num=' . $client_num . '&facture_envoi=' . crypte($rss["id"]) . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer' . $envoye . '</a> ';
																			echo '<a href="#" onClick="window.open(\'/bon-de-reception/index?facture=' . crypte($rss["id"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm"><i class="fa fa-print"></i> Bon de reception</a> ';
																		}
																		if ($rss["facture_num"]=="0")
																			echo '		<a href="' . current_path() . '?client_num=' . $client_num . '&commande_suppr=' . crypte($rss["id"]) . '&tab=tab_1_4" data-confirm="confirmeSupprCommande" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Modifier</a>
																				</td>
																			</tr>';
																	}
																	echo '	</tbody>
																		</table>';
																} else {
																	echo '<p><i>Aucune commande en cours</i></p>';
																}
															?>															
															<?php if (isset($commande_consulte)) { 
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id=" . decrypte($commande_consulte);
																	$rcc = $base->queryRow($sql);
																	if ($rcc) {
																		$commande = montantCommande($rcc["id"]);
															?>
																<h4><i class="fa fa-plus"></i> Commande n° : <?= $rcc["commande_num"] ?></h4>
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
																	<?php 																
																		$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id=" . decrypte($commande_consulte);
																		$pp = $base->query($sql);
																		foreach ($pp as $rpp) {
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
																				<td>' . safe_number_format((float) $rpp["montant_ttc"],2,".", " ") . ' €' . '</td>
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
																				if (safe_number_format((float) $prix_total_ttc,2)<=0)
																					echo "OFFERT";
																				else
																					echo safe_number_format((float) $prix_total_ttc,2,".", " ") . ' €';
																				echo '</td>
																			</tr>';
																		} ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total HT</strong></td>
																			<td><?= safe_number_format($commande["commande_ht"],2,".", " ") ?> €</td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>TVA (20%)</strong></td>
																			<td><?= safe_number_format($commande["commande_tva"],2,".", " ") ?> €</td>
																		</tr>
																		<?php if ($commande["remise"]==0) { 
																				$montant_a_payer = safe_number_format($commande["commande_ttc"],2,".", "");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><?= safe_number_format($commande["commande_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<?php } else { 
																				$montant_a_payer = safe_number_format($commande["commande_remise_ttc"],2,".", "");
																		?>
																		<tr>
																			<td colspan="6" align="right"><strong>Total TTC</strong></td>
																			<td><?= safe_number_format($commande["commande_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<tr>
																			<td colspan="6" align="right"><strong>Remise</strong></td>
																			<td><?= $commande["remise"] ?></td>
																		</tr>
																		<tr>
																			<td colspan="6" align="right"><strong>Total à payer</strong></td>
																			<td><?= safe_number_format($commande["commande_remise_ttc"],2,".", " ") ?> €</td>
																		</tr>	
																		<?php } ?>
																		<tr>
																			<td colspan="6" align="right"><strong>Méthode de paiement</strong></td>
																			<td><?= $rcc["paiement_titre"] ?></td>
																		</tr>
																		<?php																			
																			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
																				// On regarde si il y a déjà eu des paiments
																				$sql = "select * from commandes_paiements where id=" . sql_safe($rcc["id"]);
																				$pa = $base->query($sql);
																				$nbr_paiement = count($pa);
																				if ($nbr_paiement==0) {
																					$echeance = explode("/",$rcc["paiement_modele"]);
																					$acompte_num = 1;
																					foreach ($echeance as $val) {
																						$acompte_val = safe_number_format(($montant_a_payer*($val/100)),2,"."," ");
																						echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
																								<td>' . $acompte_val . ' €</td>
																							</tr>';
																						$acompte_num++;
																					}
																				} else {
																					$acompte_num = 0;
																					$montant_paye = 0;
																					foreach ($pa as $rpa) {
																						$acompte_num++;
																						echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . '</strong></td>
																								<td>' . safe_number_format($rpa["paiement_montant"],2,"."," ") . ' €</td>
																							</tr>';
																						$montant_paye += safe_number_format($rpa["paiement_montant"],2,".","");
																					}
																					$reste_a_payer = $montant_a_payer - $montant_paye;
																					if ($acompte_num<$rcc["paiement_nombre"]) {
																						$echeance_restante = $rcc["paiement_nombre"]-$acompte_num;
																						$reste_acompte_a_payer = $reste_a_payer/$echeance_restante;
																						for ($zz=$acompte_num+1;$zz<=$rcc["paiement_nombre"];$zz++) {
																							echo '<tr>
																								<td colspan="6" align="right"><strong>Acompte ' . $zz . '</strong></td>
																								<td>' . safe_number_format($reste_acompte_a_payer,2,"."," ") . ' €</td>
																							</tr>';
																						}
																					}
																				}
																			}
																		?>
																		<tr>
																			<td colspan="7" align="right"><a href="/commandes/index?cde=<?= crypte($rcc["commande_num"]) ?>" class="btn blue" target="_blank">Imprimer</a> <a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_4" class="btn red">Fermer</a></td>
																		</tr>
																	</tbody>
																</table>
															<?php		}
																} ?>
															<?php if (isset($commande_modif)) { 
																	// On recupere le nombre d'écheance
																	$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id=" . decrypte($commande_modif);
																	$rpa = $base->queryRow($sql);
																	if ($rpa) {
																		$nbr_echeance = $rpa["paiement_nombre"];
																		
																		// On regarde le nombre de paiement effectué
																		$sql = "select * from commandes_paiements where id=" . sql_safe($rpa["id"]);
																		$pa = $base->query($sql);
																		$nbr_paiement = count($pa);
																		
																		// On calcul la somme déjà payé
																		$montant_paye = 0;
																		$sql = "select sum(paiement_montant) val from commandes_paiements where id=" . sql_safe($rpa["id"]);
																		$rpp = $base->queryRow($sql); if ($rpp)																			$montant_paye = $rpp["val"];
																		
																		$reste_a_paye = safe_number_format(abs(montantCommandeTTC($rpa["id"]) - $montant_paye),2,".","");
															?>
																<h4><i class="fa fa-plus"></i> Paiement commande : <?= $rpa["commande_num"] ?> 
																<?php 
																if (($message_erreur_paiement ?? '') !== '') 
																	echo ' - <i class="fa fa-warning"></i> <font class="font-red-thunderbird"><strong>' . $message_erreur_paiement . '</strong></font>'; 
																?></h4>
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
																<?php																	
																	$echeance=1;
																	$sql = "select * from commandes_paiements c, paiements_modes m where c.mode_num=m.mode_num and id=" . decrypte($commande_modif);
																	$pp = $base->query($sql);
																	foreach ($pp as $rpp) {
																		echo '<form name="paiement_' . $echeance . '" action="' . form_action_same() . '" method="POST">
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
																		$mm = $base->query($sql);
																		foreach ($mm as $rmm) {
																			echo '<option value="' . $rmm["mode_num"] . '"';
																			if ($rmm["mode_num"]==$rpp["mode_num"])
																				echo ' SELECTED';
																			echo '>' . $rmm["mode_nom"] . '</option>';
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
																			echo '		<a href="#" onClick="window.open(\'/acompte/index?id=' . $commande_modif . '&paiement=' . $rpp["paiement_num"] . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm yellow"><i class="fa fa-print"></i> Facture</a> 
																			<a href="' . current_path() . '?client_num=' . $client_num . '&acompte_envoi=' . crypte($rpp["id"]) . '&paiement_echeance=' . $rpp["paiement_num"] . '&tab=tab_1_4" class="btn btn-outline btn-circle dark btn-sm blue"><i class="fa fa-envelope"></i> Envoyer ' . $envoye . '</a> 
																						<a href="' . current_path() . '?client_num=' . $client_num . '&paiement_suppr=' . crypte($rpp["id"]) . '&echeance=' . $echeance . '&tab=tab_1_4" data-confirm="confirmeSupprPaiement" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a>';
																		}																					
																		echo '		</td>
																			  </tr>
																			  </form>';
																		$echeance++;
																	}
																	// On complete les echeances
																	for ($e=$echeance;$e<=$nbr_echeance;$e++) {
																		echo '<form name="paiement_' . $e . '" action="' . form_action_same() . '" method="POST">
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
																		$mm = $base->query($sql);
																		foreach ($mm as $rmm) {
																			echo '<option value="' . $rmm["mode_num"] . '"';
																			echo '>' . $rmm["mode_nom"] . '</option>';
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
																	<tr><td colspan="6" align="right"><a href="<?= current_path() ?>?client_num=<?= $client_num ?>&tab=tab_1_4" class="btn red">Fermer</a></td></tr>
																	</tbody>
																</table>
															<?php 
																}
															} ?>
														</div>
														<!-- END CHANGE PASSWORD TAB -->
														<?php } ?>
														<!-- PERSONAL INFO TAB -->
														<div class="tab-pane<?php if ($tab=="tab_1_6") echo " active"?>" id="tab_1_6">
															<p> Modifier les informations personnelles du client </p>
															<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
															<input type="hidden" name="modifier" value="ok">
															<input type="hidden" name="tab" value="tab_1_6">
															<input type="hidden" name="client_num" value="<?= $client_num ?>">
															<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
																<div class="form-group">
																	<label>Genre</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-intersex"></i>
																		</span>
																		<select name="genre" class="form-control">
																			<option value="0"<?php if ($rcl["client_genre"]==0) echo " SELECTED"; ?>>Femme</option>
																			<option value="1"<?php if ($rcl["client_genre"]==1) echo " SELECTED"; ?>>Homme</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Nom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="nom" class="form-control" placeholder="Nom" value="<?= $rcl["client_nom"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Prenom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="prenom" class="form-control" placeholder="Prénom" value="<?= $rcl["client_prenom"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr1" class="form-control" placeholder="Adresse"  value="<?= $rcl["client_adr1"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Complément d'adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr2" class="form-control" placeholder="Complément d'adresse"  value="<?= $rcl["client_adr2"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>CP</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-search"></i>
																		</span>
																		<input type="text" name="cp" class="form-control" placeholder="Code Postal"  value="<?= $rcl["client_cp"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Ville</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-shield"></i>
																		</span>
																		<input type="text" name="ville" class="form-control" placeholder="Ville" value="<?= $rcl["client_ville"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Tel</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-mobile-phone"></i>
																		</span>
																		<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<?= $rcl["client_tel"] ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Email</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-envelope"></i>
																		</span>
																		<input type="email" name="mail" class="form-control" placeholder="Email" value="<?= $rcl["client_mail"] ?>" required> </div>
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
																				<td style="font-size:13px;">Tour Taille<br><input type="text" name="taille" class="form-control" value="<?= $rcl["taille"] ?>"></td>
																				<td style="font-size:13px;">Poitrine<br><input type="text" name="poitrine" class="form-control" value="<?= $rcl["poitrine"] ?>"></td>
																				<td style="font-size:13px;">Ss poitrine<br><input type="text" name="sous_poitrine" class="form-control" value="<?= $rcl["sous_poitrine"] ?>"></td>
																				<td style="font-size:13px;">Lg Dos<br><input type="text" name="longueur_dos" class="form-control" value="<?= $rcl["longueur_dos"] ?>"></td>
																				<td style="font-size:13px;">Biceps<br><input type="text" name="biceps" class="form-control" value="<?= $rcl["biceps"] ?>"></td>
																				<td style="font-size:13px;">Taille-sol talons<br><input type="text" name="taille_sol" class="form-control" value="<?= $rcl["taille_sol"] ?>"></td>
																			</tr>
																			<tr>
																				<td style="font-size:13px;">Hanche 1<br><input type="text" name="hanche1" class="form-control" value="<?= $rcl["hanche1"] ?>"></td>
																				<td style="font-size:13px;">Hanche 2<br><input type="text" name="hanche2" class="form-control" value="<?= $rcl["hanche2"] ?>"></td>
																				<td style="font-size:13px;">Carrure Av<br><input type="text" name="carrure_avant" class="form-control" value="<?= $rcl["carrure_avant"] ?>"></td>
																				<td style="font-size:13px;">Carrure Dos<br><input type="text" name="carrure_dos" class="form-control" value="<?= $rcl["carrure_dos"] ?>"></td>
																				<td style="font-size:13px;">Pointure<br><input type="text" name="pointure" class="form-control" value="<?= $rcl["pointure"] ?>"></td>
																				<td style="font-size:13px;">Taille<br><input type="text" name="tour_taille" class="form-control" value="<?= $rcl["tour_taille"] ?>"></td>
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
																		<input type="date" name="date" class="form-control" placeholder="Date du mariage" value="<?= $rcl["client_date_mariage"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Lieu de mariage</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-black-tie"></i>
																		</span>
																		<input type="text" name="lieu" class="form-control" placeholder="Lieu du mariage"  value="<?= $rcl["client_lieu_mariage"] ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Remarques</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-book"></i>
																		</span>
																		<textarea class="form-control" rows="4" name="remarques"><?= $rcl["client_remarque"] ?></textarea> </div>
																</div>
																<div class="form-group">
																	<label>Comment avez vous connu Olympe ?</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-meh-o"></i>
																		</span>
																		<select name="connaissance" class="form-control">
																			<option value="0">----------------</option>
																			<option value="1"<?php if ($rcl["connaissance_num"]==1) echo " SELECTED";?>>Publicité</option>
																			<option value="2"<?php if ($rcl["connaissance_num"]==2) echo " SELECTED";?>>Sur Internet</option>
																			<option value="3"<?php if ($rcl["connaissance_num"]==3) echo " SELECTED";?>>Bouche à oreille</option>
																			<option value="4"<?php if ($rcl["connaissance_num"]==4) echo " SELECTED";?>>Autres</option>
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
																			<option value="1"<?php if ($rcl["interet"]==1) echo " SELECTED";?>>Bof</option>
																			<option value="2"<?php if ($rcl["interet"]==2) echo " SELECTED";?>>Intéressé</option>
																			<option value="3"<?php if ($rcl["interet"]==3) echo " SELECTED";?>>Très intéressé</option>
																			<option value="4"<?php if ($rcl["interet"]==4) echo " SELECTED";?>>Non</option>
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
																			<?php																				
																				$sql = "select * from users where showroom_num=" . sql_safe($rcl["showroom_num"]) . " and user_etat=1";
																				$uu = $base->query($sql);
																				foreach ($uu as $ruu) {
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
																			<?php																				
																				$sql = "select * from users where showroom_num=" . sql_safe($rcl["showroom_num"]) . " and user_etat=1";
																				$uu = $base->query($sql);
																				foreach ($uu as $ruu) {
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
																			<?php																				
																				$sql = "select * from showrooms";
																				$uu = $base->query($sql);
																				foreach ($uu as $ruu) {
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
																		<?php																			
																			$sql = "select * from commandes c, commandes_produits cd where c.id=cd.id and commande_num>0 and client_num='" . decrypte($client_num). "' order by commande_date ASC, c.id ASC";
																			$co = $base->query($sql);
																			foreach ($co as $rco) {
																				$checked = "";
																				$date_fournisseur = "";
																				$montant = 0;
																				$sql = "select * from commandes_fournisseurs where id=" . sql_safe($rco["id"]) . " and produit_num=" . sql_safe($rco["produit_num"]);
																				$rtt = $base->queryRow($sql);
 																				if ($rtt) {
																					$checked = " CHECKED";
																					$date_fournisseur = format_date($rtt["commande_fournisseur_date"],11,1);
																					$montant = safe_number_format($rtt["commande_montant"],2,"."," ");
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
																						$sql = "select * from commandes_fournisseurs_paiements where id=" . sql_safe($rco["id"]) . " and produit_num=" . sql_safe($rco["produit_num"]);
																						$rpa = $base->queryRow($sql);
																						if ($rpa) {
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
																						echo '<form name="paiement_fournisseur_' . $rtt["id"] . '_' . $rtt["produit_num"] . '" method="POST" action="' . form_action_same() . '">
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
																						<td><a href="client?client_num=' . $client_num . '&fournisseur=ok&id=' . crypte($rco["id"]) . '&produit=' . crypte($rco["produit_num"]) . '&tab=tab_1_6"  class="btn btn-outline btn-circle dark btn-sm black"> Commande Fournisseur</a>';
																				if ($checked!="") {
																					echo ' <a href="#" onClick="window.open(\'/clients/fournisseur?id=' . crypte($rco["id"]) . '&produit=' . crypte($rco["produit_num"]) . '&print=auto\',\'_blank\',\'width=1200,height=800,toolbar=no\');" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-print"></i> Bon de commande</a>';
																				}
																				echo '		
																					</tr>';
																			}
																		?>
																		</tbody>
																	</table>
																</div>
															</div>
															<?php if (isset($fournisseur) && $fournisseur === "ok") {
																	// On recherche si il y a une robe à commander
																	$sql = "select * from commandes c, commandes_produits cp, md_produits p, marques m where c.id=cp.id and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and c.id=" . decrypte($id) . " and cp.produit_num=" . decrypte($produit);
																	$rmm = $base->queryRow($sql);
																	if ($rmm) {
																		// On regarde si il y a déjà une commande forunisseur
																		$sql = "select * from commandes_fournisseurs where id=" . decrypte($id) . " and produit_num=" . decrypte($produit);
																		$rff = $base->queryRow($sql);
																		if ($rff) {
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
																			$sql = "select * from prixachats where prixachat_num=" . sql_safe($rmm["prixachat_num"]);
																			$rpp = $base->queryRow($sql);
 																			if ($rpp) {
																				$montant = $rpp["prixachat_montant"];
																				if ($montant!=0) {
																					// On calcul le montant avec la TVA
																					$montant = safe_number_format($montant*1.20,2,".","");
																				}
																			} else {
																				$montant = 0;
																			}
																		}
															?>
															<hr><center><h4>Commande Fournisseur de la commande :  <?= $rmm["commande_num"] ?></h4></center><hr>
															<div class="row">
																<form name="forunisseur" action="<?= current_path() ?>" method="POST">
																<input type="hidden" name="tab" value="tab_1_6">
																<input type="hidden" name="client_num" value="<?= $client_num ?>">
																<input type="hidden" name="id" value="<?= $id ?>">
																<input type="hidden" name="produit" value="<?= $produit ?>">
																<input type="hidden" name="marque" value="<?= $rmm["marque_num"] ?>">
																<input type="hidden" name="cdefournisseur" value="ok">
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p>
																		<strong><?php if ($rcl["client_genre"]==0) echo 'Nom de la future mariée'; else echo 'Nom du futur marié'; ?></strong> : <?= $rcl["client_prenom"] . ' ' . $rcl["client_nom"] ?><br>
																		<strong>Tel : </strong> <?= $rcl["client_tel"] ?><br>
																		<strong>Mail : </strong> <?= $rcl["client_mail"] ?><br>
																		<strong>Date de mariage : </strong> <?= format_date($rcl["client_date_mariage"],11,1) ?><br>
																		<strong>Livraison avant : </strong> <input type="text" name="livraison" placeholder="JJ/MM/AAAA" value="<?= $livraison ?>">
																	</p>
																</div>
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p><strong>Fournisseur</strong><br>
																	<?= $rmm["marque_raison_social"] ?><br>
																	<?= $rmm["marque_adr1"] ?><br>
																	<?php if ($rmm["marque_adr2"]!="") echo $rmm["marque_adr2"] . "<br>"; ?>
																	<?= $rmm["marque_cp"] ?> <?= $rmm["marque_ville"] ?><br>
																	<?= $rmm["marque_tel"] ?>
																	</p>
																</div>
																<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																	<p><strong>Nom du magasin : </strong><?= 'Olympe ' . $u->mShowroomInfo["showroom_ville"] ?><br>
																	<strong>Date de commande : </strong><input type="date" name="fournisseur_commande_date" value="<?= $commande_date ?>"><br>
																	<strong>Référence : </strong><input type="text" name="fournisseur_commande_ref" value="<?= $reference ?>"></p>
																</div>
																<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
																	<hr><p><strong>Référence modèle</strong> : <?= $rmm["produit_nom"] ?></p><hr>
																</div>
																<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
																	<p><strong>Remarques</strong> :<br> 
																	<textarea class="form-control" name="fournisseur_remarque" rows="3"><?= $remarques ?></textarea>
																	</p><hr>
																</div>
																<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
																	<p><strong>Mesure Cliente</strong></p>
																	<table class="table table-bordered table-striped table-condensed flip-content">
																	<tr>
																		<td><strong>Tour de poitrine</strong></td>
																		<td align="center"><input type="text" name="fournisseur_poitrine" class="input-xsmall" value="<?= $poitrine ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de sous poitrine</strong></td>
																		<td align="center"><input type="text" name="fournisseur_sous_poitrine" class="input-xsmall" value="<?= $sous_poitrine ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de taille</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille" class="input-xsmall" value="<?= $taille ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de petite hanche</strong></td>
																		<td align="center"><input type="text" name="fournisseur_hanche1" class="input-xsmall" value="<?= $hanche1 ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Tour de grande hanche</strong></td>
																		<td align="center"><input type="text" name="fournisseur_hanche2" class="input-xsmall" value="<?= $hanche2 ?>" ></td>
																	</tr>
																	<tr>
																		<td><strong>Tour de biceps</strong></td>
																		<td align="center"><input type="text" name="fournisseur_biceps" class="input-xsmall" value="<?= $biceps ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Carrure Avant</strong></td>
																		<td align="center"><input type="text" name="fournisseur_carrure_avant" class="input-xsmall" value="<?= $carrure_avant ?>" ></td>
																	</tr>
																	<tr>
																		<td><strong>Carrure dos</strong></td>
																		<td align="center"><input type="text" name="fournisseur_carrure_dos" class="input-xsmall" value="<?= $carrure_dos ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Longueur dos</strong></td>
																		<td align="center"><input type="text" name="fournisseur_longueur_dos" class="input-xsmall" value="<?= $longueur_dos ?>" ></td>
																	</tr> 
																	<tr>
																		<td><strong>Hauteur taille-sol avec talons</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille_sol" class="input-xsmall" value="<?= $taille_sol ?>" ></td>
																	</tr> 
																	<tr class="danger">
																		<td><strong>Taille choisie</strong></td>
																		<td align="center"><input type="text" name="fournisseur_taille_choisie" class="input-xsmall" value="<?= $taille_choisie ?>" ></td>
																	</tr> 
																	<tr>
																		<td colspan="2"></td>
																	</tr>
																	<tr class="success">
																			<td class="text-right">Montant de la commande :</td>
																			<td class="text-center"><input type="text" name="fournisseur_montant" class="input-xsmall" value="<?= $montant ?>" > € TTC</td>
																		</tr>
																	</table>
																</div>
																<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-center">
																	<button type="submit" class="btn blue">Enregistrer</button>
																</div>
																</form>
															</div>
															<?php 
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
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
<script>
// Confirmations (plus concis)
async function confirme_annulation_rdv() { return await $ol.ask("Êtes-vous sûr de vouloir annuler ce rendez-vous ?"); }
async function confirme()                { return await $ol.ask("Êtes-vous sûr de vouloir supprimer cette sélection ?"); }
async function confirmeDevis()           { return await $ol.ask("Transformer cette sélection en devis ?"); }
async function confirmeSupprDevis()      { return await $ol.ask("Supprimer ce devis ?"); }
async function confirmeSupprCommande()   { return await $ol.ask("Modifier cette commande ? Elle repassera en devis."); }
async function confirmeSupprPaiement()   { return await $ol.ask("Supprimer ce paiement ?"); }
async function confirme_commande(id) {
  const p = document.getElementById("paiement_" + id);
  const txt = p ? p.options[p.selectedIndex].text : '';
  return await $ol.ask("Passer le devis en commande avec le mode de paiement : " + txt + " ?");
}

document.addEventListener('click', async function(e) {
    const link = e.target.closest('[data-confirm]');
    if (!link) return;
    
    e.preventDefault();
    
    let confirmed = false;
    switch(link.dataset.confirm) {
        case 'confirme_annulation_rdv':
            confirmed = await confirme_annulation_rdv();
            break;
        case 'confirme':
            confirmed = await confirme();
            break;
		case 'confirmeDevis':
            confirmed = await confirmeDevis();
            break;
		case 'confirmeSupprDevis':
            confirmed = await confirmeSupprDevis();
            break;
		case 'confirmeSupprCommande':
            confirmed = await confirmeSupprCommande();
            break;
		case 'confirmeSupprPaiement':
            confirmed = await confirmeSupprPaiement();
            break;
		case 'confirme_commande':
			const id = e.target.closest('[data-id]');
            confirmed = await confirme_commande(id);
            break;
        
    }
    
    if (confirmed) {
        window.location.href = link.href;
    }
});


// API calls
async function addWidget(selection, pdt, mode) {
  const placeId = "select_" + selection;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode: 'addWidget', selection, pdt, mode });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function addPdtDevis(devis, pdt, mode) {
  const placeId = "devis_" + devis;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode: 'addPdtDevis', devis, pdt, mode });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function modifTaille(devis, pdt, tailleKey) {
  const selId = 'taille_' + pdt + '_' + tailleKey;
  const sel = document.getElementById(selId);
  if (!sel) return;
  const taille_new = sel.value;
  try {
    await $ol.apiPost('client', { mode:'modifTaille', devis, pdt, taille: tailleKey, taille_new });
  } catch(e) { /* toast optionnel */ }
}

async function remiseCommande(devis) {
  const montant = document.getElementById("remise_montant")?.value || '';
  const type    = document.getElementById("remise_type")?.value || '';
  const placeId = "devis_" + devis;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode:'remiseCommande', devis, remise_montant: montant, remise_type: type });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function remiseProduit(devis, produit, tailleKey) {
  const m = document.getElementById("remise_produit_" + produit)?.value || '';
  const t = document.getElementById("remise_type_produit_" + produit)?.value || '';
  const taille = document.getElementById("taille_" + produit + "_" + tailleKey)?.value || '';
  const placeId = "devis_" + devis;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode:'remiseProduit', devis, produit, taille, remise_montant:m, remise_type:t });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function modifPaiement(devis) {
  const sel = document.getElementById('paiement_' + devis);
  const paiement = sel ? sel.value : '';
  const placeId = "devis_" + devis;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode:'modifPaiement', devis, paiement });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function modifDateCommande(devis) {
  const date = document.getElementById("date_bdc")?.value || '';
  try {
    await $ol.apiPost('client', { mode:'modifDateCommande', devis, date_commande: date });
  } catch(e) { /* noop */ }
}

async function modifQte(devis, pdt, tailleKey) {
  const qSelId = 'qte_' + pdt + '_' + tailleKey;
  const tSelId = 'taille_' + pdt + '_' + tailleKey;
  const qte_new = document.getElementById(qSelId)?.value || '';
  const taille  = document.getElementById(tSelId)?.value || '';
  const placeId = "devis_" + devis;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode:'modifQte', devis, pdt, taille, qte_new });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}

async function commandeFournisseur(id) {
  const ck = document.getElementById('fournisseur_' + id);
  const val = ck && ck.checked ? '1' : '0';
  const placeId = "fournisseur_date_" + id;
  try {
    $ol.loading(true);
    const data = await $ol.apiPost('client', { mode:'commandeFournisseur', id, val });
    if (data.ok && data.html) displayReponse(data.html, data.place || placeId);
    else displayReponse("Erreur: " + (data.error || 'inconnue'), placeId);
  } catch(e) {
    displayReponse("Erreur réseau", placeId);
  } finally { $ol.loading(false); }
}
</script>
    </body>
</html>