<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Mon Agenda - Olympe Mariage";
$desc_page = "Mon Agenda - Olympe Mariage";

$message_erreur = "";

if (isset($modif_desc)) {
	// On modifie la description du RDV
	$sql = "update calendriers set calendrier_desc=" . safe_sql($calendrier_desc) . " where calendrier_num=" . safe_sql($calendrier_num);
	$base->query($sql);	
}

if (isset($ajouter)) {
	$date_debut = $date_deb . " " . $time_deb;
	$date_fin = $date_fin . " " . $time_fin;
	
	$client_num = 0;
	$rdv_num = 0;
	if (($date_debut>Date("Y-m-d H:i:s")) && ($date_fin>Date("Y-m-d H:i:s"))) {
		if ($theme==1) {
			$client_search = recupValeurEntreBalise($client,"[","]");
			if (count($client_search)>0) {
				$client_num = $client_search[0];
				// On recherche le client 
				$sql = "select * from clients where client_num=" . safe_sql($client_num);
				$rcl = $base->queryRow($sql);
 				if ($rcl) {
					if ($rcl["client_genre"]==0)
						$genre = "Mme";
					else
						$genre = "Mr";

					$client_nom_complet = str_replace("'","\'",$rcl["client_nom"]) . " " . $rcl["client_prenom"];
					
					// On regarde si on a pas déjà un rendez vous 
					$sql = "select * from rendez_vous where client_num=" . safe_sql($client_num) . " and type_num=" . safe_sql($type);
					$rtt = $base->queryRow($sql);
 					if ($rtt) {
						$sql = "delete from rendez_vous where rdv_num=" . safe_sql($rtt["rdv_num"]);
						$base->query($sql);
							
						$sql = "delete from calendriers where rdv_num=" . safe_sql($rtt["rdv_num"]);
						$base->query($sql);
					}
					
					// On insere un Rendez vous
					$date_rdv = $date_debut;
					$sql = "insert into rendez_vous values(0," . safe_sql($client_num) . "," . safe_sql($type) . "," . safe_sql($date_rdv) . ",'',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00'," . safe_sql($u->mNum) . ")";
					$num = $base->insert($sql);
					
					// On ajoute dans le calendrier du user
					switch ($type) {
						case 1: // 1er RDV
							$theme = 1;
							
							$titre = $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(1,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						case 6: // 2er RDV
							$theme = 1;
							
							$titre = "2e RDV " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(1,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						case 8: // 3er RDV
							$theme = 1;
							
							$titre = "3e RDV " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(1,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						case 7: // RDV Accessoire
							$theme = 1;
							
							$titre = "RDV Acc. " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(1,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						case 2: // Date de reception prévu
						break;
						
						case 3: // Date de réception
						break;
						
						case 4: // RDV Retouche
							$theme = 1;
							
							$titre = "Retouche " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(14,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							// Si on est à Montpellier on envoie aussi à la couturière
							if ($rcl["showroom_num"]==1) {
								SendMail("lilietcie34@gmail.com",$titre_mail,$message_mail,$u->mNum,$client_num);
							}
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						case 5: // RDV Remise
							$theme = 1;
							
							$titre = "Remise " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(5,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
								$sql = "select * from paiements_modes p, showrooms_paiements s where p.mode_num=s.mode_num and showroom_num=" . safe_sql($rcl["showroom_num"]) . " order by mode_ordre ASC";
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
						break;
						
						case 9: // RDV Retouche
							$theme = 1;
							
							$titre = "2e RDV Retouche " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(14,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
						
						
						case 10: // RDV Retouche
							$theme = 1;
							
							$titre = "Retouche " . $client_nom_complet;
							$desc = $description;
							
							// On insere en bdd
							$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($titre) . "," . safe_sql($desc) . "," . safe_sql($u->mNum) . "," . safe_sql($rcl["showroom_num"]) . "," . safe_sql($client_num) . "," . safe_sql($num) . ")";
							$base->query($sql);
							
							// On envoi le mail selon le type de RDV
							$template = getEmailTemplate(15,$rcl["client_genre"]);
				    		$titre_mail = $template["titre"];
				    		$message_mail = $template["message"];
							$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
							$message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
							$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
							$message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
										
							// On envoi le mail
							SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
							
							$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . " where rdv_num=" . safe_sql($num);
							$base->query($sql);
							
						break;
					}
					
				}
			}
		} else {
			$sql = "insert into calendriers values(0," . safe_sql($date_debut) . "," . safe_sql($date_fin) . "," . safe_sql($theme) . "," . safe_sql($description) . ",''," . safe_sql($u->mNum) . "," . safe_sql($u->mShowroom) . "," . safe_sql($client_num) . "," . safe_sql($rdv_num) . ")";
			$base->query($sql);
		}
	} else {
		$message_erreur = "Erreur sur les dates de rendez-vous !";
	}
}

if (isset($ajout_client)) {
	// On test si le client n'exite pas
	$sql = "select * from clients where client_mail=" . safe_sql($mail);
	$tt = $base->query($sql);
	$nbr = count($tt);
	if ($nbr==0) {
		$sql = "insert into clients values (0," . safe_sql($genre) . "," . safe_sql($nom) . "," . safe_sql($prenom) . "," . safe_sql($adr1) . "," . safe_sql($adr2) . "," . safe_sql($cp) . "," . safe_sql($ville) . "," . safe_sql($tel) . "," . safe_sql($mail) . "," . safe_sql($date) . "," . safe_sql($lieu) . ",'',''," . safe_sql($u->mShowroom) . "," . safe_sql($u->mNum) . ",'" . Date("Y-m-d H:i:s") . ",'" . Date("Y-m-d H:i:s") . ",'','','','','','','','','','','','',0,0)";
		$base->query($sql);
	} else {
		$message_erreur = "Un client est déjà enregistré avec cette adresse email !";
	}	
}
?>

<?php include TEMPLATE_PATH . 'head.php'; ?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <?php include TEMPLATE_PATH . 'top.php'; ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>Olympe Mariage</h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Mon Agenda</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light portlet-fit bordered calendar">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject font-green sbold uppercase">Mon Agenda</span>
									</div>
								</div>
								<div class="portlet-body">
									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div id="calendar" class="has-toolbar"> </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END PAGE BASE CONTENT -->
                </div>
				<div id="calendarModal" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
								<h4 id="modalTitle" class="modal-title"></h4>
							</div>
							<!--<div id="modalBody" class="modal-body"> </div>-->
							<form name="calendrier_desc" method="POST" action="<?php current_path() ?>">
								<input type="hidden" name="modif_desc" value="ok">
								<input type="hidden" name="calendrier_num" id="calendrier_num" value="">
								<center><textarea name="calendrier_desc" id="calendrier_desc" cols="80" rows="5"></textarea></center>
							<div class="modal-footer">
								<input type="submit" value="Modifier" class="btn red">
								<a href="" id="lien" class="btn blue">Allez sur la Fiche Client</a>
								<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
							</div>
							</form>
						</div>
					</div>
				</div>
				<div class="modal fade" id="confirm-submit">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h4><strong>Confirmation du Rendez-Vous</strong></h4>
							</div>
							<div class="modal-body">
								<strong>Veuillez vérifier les détails de votre rendez-vous ci dessous :</strong>
								<hr>
								<!-- We display the details entered by the user here -->
								<table class="table">
									<tr>
										<th>Catégorie</th>
										<td id="lcategorie"></td>
									</tr>
									<tr>
										<th>Type</th>
										<td id="ltype"></td>
									</tr>
									<tr>
										<th>Client</th>
										<td id="lclient"></td>
									</tr>
									<tr>
										<th>Debut</th>
										<td id="ldebut"></td>
									</tr>
									<tr>
										<th>Fin</th>
										<td id="lfin"></td>
									</tr>
									<tr>
										<th>Description</th>
										<td id="ldescription"></td>
									</tr>
								</table>

							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
								<a href="#" id="submit" class="btn btn-success success" data-dismiss="modal">Enregistrer</a>
							</div>
						</div>
					</div>
				</div>
				<?php					$param = "";
					// ON recherche les events pour remplir le calendrier perso
					$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num and user_num=" . safe_sql($u->mNum) . " and calendrier_datedeb>='2021-08-01 00:00:00' and c.theme_num=4 order by calendrier_datedeb DESC";
					$cc = $base->query($sql);
					$nbr = count($cc);
					$i=0;
					foreach ($cc as $rcc) {
						if ($i>0) {
							$param .= ',';
						}
						
						list($annee_deb, $mois_deb, $jour_deb, $heure_deb, $minute_deb, $seconde_deb) =
							preg_split('/[: -]/', $rcc["calendrier_datedeb"], 6);

						list($annee_fin, $mois_fin, $jour_fin, $heure_fin, $minute_fin, $seconde_fin) =
							preg_split('/[: -]/', $rcc["calendrier_datefin"], 6);
							
						$mois_deb = $mois_deb-1;
						$mois_fin = $mois_fin-1;
						
						$couleur = $rcc["theme_couleur"];
						$link = "";
						if ($rcc["client_num"]!=0) {
							$genre = 0;
							$sql = "select * from clients where client_num=" . safe_sql($rcc["client_num"]);
							$rcl = $base->queryRow($sql);
 							if ($rcl) {
								$link = '/clients/client?client_num=' . crypte($rcc["client_num"]);
								$genre = $rcl["client_genre"];
							}
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num=" . safe_sql($rcc["rdv_num"]);
							$rrr = $base->queryRow($sql);
							if ($rrr) {
								if ($genre==0)
									$couleur = $rrr["type_couleur"];
								else 
									$couleur = $rrr["type_couleur_homme"];	
							}
						}
						
						
						
						$title_modal = $heure_deb . ":" . $minute_deb . " - " . $heure_fin . ":" . $minute_fin;
						$desc_modal = "<p><strong>" . $rcc["calendrier_titre"] . "</strong></p>";
						
						
						$param .= '{
								title: "' . $rcc["calendrier_titre"] . '",
								start: new Date(' . $annee_deb . ', ' . $mois_deb . ', ' . $jour_deb . ' , ' . $heure_deb . ', ' . $minute_deb . '),
								end: new Date(' . $annee_fin . ', ' . $mois_fin . ', ' . $jour_fin . ' , ' . $heure_fin . ', ' . $minute_fin . '),
								backgroundColor: App.getBrandColor("' . $couleur . '"),';
						if ($link!="")
							$param .= ' url:"' . $link . '",';
						$param .= '	allDay: !1
							 }';
						$i++;
					 }
					 if ($i>0)
						$param.= ",";
					 
					// ON recherche les events pour remplir le calendrier du showroom
					$sql = "select * from calendriers c, calendriers_themes ct, clients cl, users u where c.theme_num=ct.theme_num and c.client_num=cl.client_num and cl.user_num=u.user_num and c.showroom_num=" . safe_sql($u->mShowroom) . "  and calendrier_datedeb>='2021-08-01 00:00:00' and c.theme_num!=4 order by calendrier_datedeb DESC";
					$cc = $base->query($sql);
					$nbr = count($cc);
					$i=0;
					foreach ($cc as $rcc) {
						if ($i>0) {
							$param .= ',';
						}
						
						list($annee_deb, $mois_deb, $jour_deb, $heure_deb, $minute_deb, $seconde_deb) =
							preg_split('/[: -]/', $rcc["calendrier_datedeb"], 6);

						list($annee_fin, $mois_fin, $jour_fin, $heure_fin, $minute_fin, $seconde_fin) =
							preg_split('/[: -]/', $rcc["calendrier_datefin"], 6);
							
						$mois_deb = $mois_deb-1;
						$mois_fin = $mois_fin-1;
						
						$couleur = $rcc["theme_couleur"];
						$link = "";
						$type_rdv = 0;
						if ($rcc["client_num"]!=0) {
							$genre = 0;
							$sql = "select * from clients where client_num=" . safe_sql($rcc["client_num"]);
							$rcl = $base->queryRow($sql);
 							if ($rcl) {
								$link = '/clients/client?client_num=' . crypte($rcc["client_num"]);
								$genre = $rcl["client_genre"];
							}
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num=" . safe_sql($rcc["rdv_num"]);
							$rrr = $base->queryRow($sql);
							if ($rrr) {
								$type_rdv = $rrr["type_num"];
								if ($genre==0)
									$couleur = $rrr["type_couleur"];
								else 
									$couleur = $rrr["type_couleur_homme"];	
							}
						}
						$titre_rdv = str_replace('"','\"',$rcc["calendrier_titre"]);
						
						$calendrier_desc = str_replace('"','\'',$rcc["calendrier_desc"]);
						$calendrier_desc = str_replace('\n',' ',$calendrier_desc);
						$calendrier_desc = str_replace('\r',' ',$calendrier_desc);
						$calendrier_desc = str_replace('^p',' ',$calendrier_desc);
						$calendrier_desc = preg_replace("#\n|\t|\r#","",$calendrier_desc);
						
						if ($calendrier_desc!="")
							$titre_rdv .= " / " . $calendrier_desc;
						
						$title_modal = "De " . $heure_deb . "h" . $minute_deb . " à " . $heure_fin . "h" . $minute_fin . " - " . $rcc["calendrier_titre"];
						if (($type_rdv==6) || ($type_rdv==8) || ($type_rdv==4) || ($type_rdv==5))
							$title_modal .= " : " . $rcc["user_prenom"] . " " . $rcc["user_nom"];
						$desc_modal = "<p><strong>" . $rcc["calendrier_titre"] . "</strong></p>";
						if ($calendrier_desc!="")
							$desc_modal .= "<hr>" . $calendrier_desc;
						if ($link!="")
							$desc_modal .= '<br><br><center><a href=\"' . $link . '\" class=\"btn blue\">Allez sur la fiche client</a>';
						
						// On test si le RDV dure plusieurs jours
						$nb_jour = diff_date($rcc["calendrier_datedeb"],$rcc["calendrier_datefin"]);
						//echo "[ Date : " . $rcc["calendrier_datedeb"] . "-" . $nb_jour . "]";
						if ($nb_jour>0)
							$allday = "1";
						else 
							$allday = "!1";
												
						$param .= '{
								title: "' . $titre_rdv  . '",
								start: new Date(' . $annee_deb . ', ' . $mois_deb . ', ' . $jour_deb . ' , ' . $heure_deb . ', ' . $minute_deb . '),
								end: new Date(' . $annee_fin . ', ' . $mois_fin . ', ' . $jour_fin . ' , ' . $heure_fin . ', ' . $minute_fin . '),
								backgroundColor: "' . $couleur . '",
								title_modal : "' . $title_modal . '",
								desc_modal : "' . $desc_modal . '",
								description : "' . $calendrier_desc . '",
								id : "' . $rcc["calendrier_num"] . '",
								link : "/clients/client?client_num=' . crypte($rcc["client_num"]) . '",';
						/*if ($link!="")
							$param .= ' url:"' . $link . '",';*/
						$param .= '	allDay: ' . $allday  . '
							 }';
						$i++;
					 }
?>					
				<?php $link_script = '<script language="JavaScript">
		var AppCalendar = function() {
			return {
				init: function() {
					this.initCalendar()
				},
				initCalendar: function() {
					if (jQuery().fullCalendar) {
						var e = new Date,
							t = e.getDate(),
							a = e.getMonth(),
							n = e.getFullYear(),
							r = {};
						App.isRTL() ? $("#calendar").parents(".portlet").width() <= 720 ? ($("#calendar").addClass("mobile"), r = {
							right: "title, prev, next",
							center: "",
							left: "agendaDay, agendaWeek, month, today"
						}) : ($("#calendar").removeClass("mobile"), r = {
							right: "title",
							center: "",
							left: "agendaDay, agendaWeek, month, today, prev,next"
						}) : $("#calendar").parents(".portlet").width() <= 720 ? ($("#calendar").addClass("mobile"), r = {
							left: "title, prev, next",
							center: "",
							right: "today,month,agendaWeek,agendaDay"
						}) : ($("#calendar").removeClass("mobile"), r = {
							left: "title",
							center: "",
							right: "prev,next,today,month,agendaWeek,agendaDay"
						});
						var l = function(e) {
								var t = {
									title: $.trim(e.text())
								};
								e.data("eventObject", t), e.draggable({
									zIndex: 999,
									revert: !0,
									revertDuration: 0
								})
							},
							o = function(e) {
								e = 0 === e.length ? "Untitled Event" : e;
								var t = $(\'<div class="external-event label label-default">\' + e + "</div>");
								jQuery("#event_box").append(t), l(t)
							};
						$("#external-events div.external-event").each(function() {
							l($(this))
						}), $("#event_add").unbind("click").click(function() {
							var e = $("#event_title").val();
							o(e)
						}), $("#event_box").html(""),  $("#calendar").fullCalendar("destroy"), $("#calendar").fullCalendar({
							header: r,
							defaultView: "agendaWeek",
							slotMinutes: 15,
							editable: 0,
							droppable: 0,
							scrollTime : \'09:00:00\',
							drop: function(e, t) {
								var a = $(this).data("eventObject"),
									n = $.extend({}, a);
								n.start = e, n.allDay = t, n.className = $(this).attr("data-class"), $("#calendar").fullCalendar("renderEvent", n, !0), $("#drop-remove").is(":checked") && $(this).remove()
							},
							events: [' . $param . '],
							eventClick:  function(event, jsEvent, view) {
								$(\'#modalTitle\').html(event.title_modal);
								$(\'#modalBody\').html(event.desc_modal);
								$(\'#calendrier_desc\').html(event.description);
								$(\'#calendrier_num\').val(event.id);
								$(\'#lien\').attr(\'href\',event.link);
								$(\'#eventUrl\').attr(\'href\',event.url);
								$(\'#calendarModal\').modal();
							}
						})
					}
				}
			}
		}();
		jQuery(document).ready(function() {
			AppCalendar.init()
		});
		
		$(function() {
			var availableTagsClient = [';
			$sql = "select * from clients where showroom_num=" . safe_sql($u->mShowroom) . " and client_nom not like'%?%' and client_prenom not like '%?%' order by client_nom ASC, client_prenom ASC";
			$jj = $base->query($sql);
			$nbr = count($jj);
			$i=0;
			foreach ($jj as $rjj) {
				$client_nom = trim($rjj["client_nom"]);
				$client_prenom = trim($rjj["client_prenom"]);
				$client_affiche = $client_nom . " " . $client_prenom;
				$client_affiche = str_replace("\"","'",$client_affiche);
				$link_script .= "\"" . $client_affiche . " [" . $rjj["client_num"] . "]\"";
				$i++;
				if ($i<$nbr)
					$link_script .= ",";

			}
		
		$link_script .= '];
			
			$("#client").autocomplete({
			  source: availableTagsClient
			});
		});
		</script>';
		$link_script .= '<script src="/assets/global/plugins/fullcalendar/lang/fr.js" type="text/javascript"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">';
		?>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
        <?php include TEMPLATE_PATH . 'bottom.php'; ?>
		<script language="Javascript">
			$('#calendrier_desc').keypress(
				 function(event){
					 if(event.keyCode == 13){
						 event.preventDefault();
						 //alert('Un ENTER ne suffit pas.\nCliquez sur le bouton Soumettre.');
					 }
				 }
			 );
			$('#submitBtn').click(function() {
				 /* when the button in the form, display the entered values in the modal */
				 var dd = new Date($('#date_deb').val());
				 var date_debut = dd.toLocaleDateString();
				 
				 var df = new Date($('#date_fin').val());
				 var date_fin = df.toLocaleDateString();
				 
				 $('#lcategorie').text($('#theme option:selected').text());
				 $('#ltype').text($('#type option:selected').text());
				 $('#lclient').text($('#client').val());
				 $('#ldebut').text('Le ' + date_debut + ' à ' + $('#time_deb').val());
				 $('#lfin').text('Le ' + date_fin + ' à ' + $('#time_fin').val());
				 $('#ldescription').text($('#description').val());
			});

			$('#submit').click(function(){
				 /* when the submit button in the modal is clicked, submit the form */
				//alert('submitting');
				$('#formfield').submit();
			});
		</script>	
    </body>

</html>