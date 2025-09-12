<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Mon Agenda - Olympe Mariage";
$desc_page = "Mon Agenda - Olympe Mariage";

if (isset($ajouter)) {
	$date_debut = $date_deb . " " . $time_deb;
	$date_fin = $date_fin . " " . $time_fin;
	
	$client_num = 0;
	$rdv_num = 0;
	
	if ($theme==1) {
		$client_search = recupValeurEntreBalise($client,"[","]");
		if (count($client_search)>0) {
			$client_num = $client_search[0];
			// On recherche le client 
			$sql = "select * from clients where client_num='" . $client_num . "'";
			$cl = mysql_query($sql);
			if ($rcl = mysql_fetch_array($cl)) {
				if ($rcl["client_genre"]==0)
					$genre = "Mme";
				else
					$genre = "Mr";

				$client_nom_complet = $rcl["client_nom"] . " " . $rcl["client_prenom"];
				
				// On regarde si on a pas déjà un rendez vous 
				$sql = "select * from rendez_vous where client_num='" . $client_num . "' and type_num='" . $type . "'";
				$tt = mysql_query($sql);
				if ($rtt=mysql_fetch_array($tt)) {
					$sql = "delete from rendez_vous where rdv_num='" . $rtt["rdv_num"] . "'";
					mysql_query($sql);
						
					$sql = "delete from calendriers where rdv_num='" . $rtt["rdv_num"] . "'";
					mysql_query($sql);
				}
				
				// On insere un Rendez vous
				$date_rdv = $date_debut;
				$sql = "insert into rendez_vous values(0,'" . $client_num . "','" . $type . "','" . $date_rdv . "','',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00','" . $u->mNum . "')";
				mysql_query($sql);
				
				$num = mysql_insert_id();
				
				// On ajoute dans le calendrier du user
				switch ($type) {
					case 1: // 1er RDV
						$theme = 1;
						
						$titre = $client_nom_complet;
						$desc = $description;
						 
						// On insere en bdd
						$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . $client_num . "','" . $num . "')";
						mysql_query($sql);
						
						// On envoi le mail selon le type de RDV
						$titre_mail = $mail_type[1]["titre"];
						$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
						$message_mail = $mail_type[1]["message"];
						$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
						$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
						SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
						
						$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
						mysql_query($sql);
						
					break;
					
					case 6: // 2er RDV
						$theme = 1;
						
						$titre = "2e RDV " . $client_nom_complet;
						$desc = $description;
						 
						// On insere en bdd
						$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . $client_num . "','" . $num . "')";
						mysql_query($sql);
						
						// On envoi le mail selon le type de RDV
						$titre_mail = $mail_type[1]["titre"];
						$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
						$message_mail = $mail_type[1]["message"];
						$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
						$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
						SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
						
						$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
						mysql_query($sql);
						
					break;
					
					case 7: // RDV Accessoire
						$theme = 1;
						
						$titre = "RDV Acc. " . $client_nom_complet;
						$desc = $description;
						 
						// On insere en bdd
						$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . $client_num . "','" . $num . "')";
						mysql_query($sql);
						
						// On envoi le mail selon le type de RDV
						$titre_mail = $mail_type[1]["titre"];
						$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
						$message_mail = $mail_type[1]["message"];
						$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
						$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
						SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
						
						$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
						mysql_query($sql);
						
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
						$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . $client_num . "','" . $num . "')";
						mysql_query($sql);
						
						// On envoi le mail selon le type de RDV
						$titre_mail = $mail_type[1]["titre"];
						$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
						$message_mail = $mail_type[1]["message"];
						$message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
						$message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
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
						SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
						
						$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
						mysql_query($sql);
						
					break;
					
					case 5: // RDV Remise
						$theme = 1;
						
						$titre = "Remise " . $client_nom_complet;
						$desc = $description;
						 
						// On insere en bdd
						$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $u->mNum . "','" . $rcl["showroom_num"] . "','" . $client_num . "','" . $num . "')";
						mysql_query($sql);
						
						// On envoi le mail selon le type de RDV
						$titre_mail = $mail_type[5]["titre"];
						$titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
						$message_mail = $mail_type[5]["message"];
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
						}
									
						// On envoi le mail
						SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,decrypte($client_num));
						
						$sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
						mysql_query($sql);
					break;
				}
				
			}
		}
	} else {
		$sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $description . "','','" . $u->mNum . "','" . $u->mShowroom . "','" . $client_num . "','" . $rdv_num . "')";
		mysql_query($sql);
	}
}

$message_erreur = "";

if (isset($ajout_client)) {
	// On test si le client n'exite pas
	$sql = "select * from clients where client_mail='" . $mail . "'";
	$tt = mysql_query($sql);
	$nbr = mysql_num_rows($tt);
	if ($nbr==0) {
		$sql = "insert into clients values (0,'" . $genre . "','" . $nom . "','" . $prenom . "','" . $adr1 . "','" . $adr2 . "','" . $cp . "','" . $ville . "','" . $tel . "','" . $mail . "','" . $date . "','" . $lieu . "','','','" . $u->mShowroom . "','" . $u->mNum . "','" . Date("Y-m-d H:i:s") . "','" . Date("Y-m-d H:i:s") . "','','','','','','','','',0)";
		mysql_query($sql);
	} else {
		$message_erreur = "Un client est déjà enregistré avec cette adresse email !";
	}	
}
?>

<? include( $chemin . "/mod/head.php"); ?>
<script language="Javascript">
function displayReponse(sText, place) {
	var info = document.getElementById(place);
	info.innerHTML = sText;
}

function addWidget() {	
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
	
	type = document.getElementById("theme").options[document.getElementById("theme").selectedIndex].value;
	link = "display.php?type="+ type + "&mode=1";
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					displayReponse(oXmlHttp.responseText, "select_client");
					if (type!=1) {
						displayReponse("", "select_acompte");
					}
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "select_client");
				}
		}
	};
	oXmlHttp.send(null);
}

function addAcompte() {	
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
	
	type = document.getElementById("type").options[document.getElementById("type").selectedIndex].value;
	nom = document.getElementById("client").value;
	if (type==5) {
		link = "display.php?type=" + type + "&client=" + nom + "&mode=2";
		oXmlHttp.open("get",link, true);
		oXmlHttp.onreadystatechange = function () {
			if (oXmlHttp.readyState == 4) {
					if (oXmlHttp.status == 200) {
						//alert('OK : ' + oXmlHttp.responseText);
						displayReponse(oXmlHttp.responseText, "select_acompte");
					}
					else {
						//alert('Erreur : ' + oXmlHttp.statusText);
						displayReponse("Erreur : " + oXmlHttp.statusText, "select_acompte");
					}
			}
		};
		oXmlHttp.send(null);
	}
}

function changeDateFin() {
	date = document.getElementById("date_deb").value;
	document.getElementById("date_fin").value = date;
}

function changeHeureFin() {
	date = document.getElementById("time_deb").value;
	document.getElementById("time_fin").value = date;
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
										<div class="col-md-3 col-sm-12">
											<div class="portlet light bordered">
												<? if ($message_erreur!="") { ?>
													<h3 class="font-red-thunderbird"><strong><i class="fa fa-warning"></i> <? echo $message_erreur ?></strong></h3>
												<? } ?>
												<div class="portlet-title tabbable-line">
													<ul class="nav nav-tabs">
														<li class="active">
															<a href="#tab_1_1" data-toggle="tab">Prise de RDV</a>
														</li>
														<li>
															<a href="#tab_1_2" data-toggle="tab">Ajouter un client</a>
														</li>
													</ul>
												</div>
												<div class="portlet-body">
													<div class="tab-content">
														<!-- CHANGE AVATAR TAB -->
														<div class="tab-pane active" id="tab_1_1">
															<!-- BEGIN DRAGGABLE EVENTS PORTLET-->
															<h3 class="event-form-title margin-bottom-20"><i class="fa fa-plus"></i> Ajouter un Rendez-vous</h3>
															<div id="external-events">
																<form name="ajouter" method="POST" id="formfield" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
																<input type="hidden" name="ajouter" value="ok">
																<div class="form-group">
																	<label>Catégorie</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-meh-o"></i>
																		</span>
																		<select name="theme" id="theme" class="form-control" onChange="addWidget();">
																		<?
																			$sql = "select * from calendriers_themes order by theme_pos ASC";
																			$tt = mysql_query($sql);
																			while ($rtt=mysql_fetch_array($tt)) {
																				echo '<option value="' . $rtt["theme_num"] . '"';
																				if ($rtt["theme_num"]==1)
																					echo " SELECTED";
																				echo '>' . $rtt["theme_nom"] . '</option>';
																			}
																		?>
																		</select>
																	</div>
																</div>
																<div id="select_client">
																	<div class="form-group">
																		<label>Type</label>
																		<div class="input-group">
																			<span class="input-group-addon">
																				<i class="fa fa-share-alt"></i>
																			</span>
																			<select name="type" id="type" class="form-control">
																			<?
																				$sql = "select * from rdv_types where type_num NOT IN (2,3) order by type_pos ASC";
																				$tt = mysql_query($sql);
																				while ($rtt=mysql_fetch_array($tt)) {
																					echo '<option value="' . $rtt["type_num"] . '"';
																					if ($rtt["type_num"]==1)
																						echo " SELECTED";
																					echo '>' . utf8_encode($rtt["type_nom"]) . '</option>';
																				}
																			?>
																			</select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label>Client</label>
																		<div class="input-group">
																			<span class="input-group-addon">
																				<i class="fa fa-search"></i>
																			</span>
																			<input type="text" name="client" id="client" class="form-control" placeholder="Nom du client" onChange="addAcompte();">
																		</div>
																	</div>
																</div>
																<div class="form-group" id="select_acompte">
																</div>
																<div class="form-group">
																	<label>Debut</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-calendar-check-o"></i>
																		</span>
																		<input type="date" name="date_deb" id="date_deb" class="form-control" placeholder="Date du mariage" value="<? echo Date("Y-m-d") ?>" onChange="changeDateFin();">
																		<input type="time" name="time_deb" id="time_deb" class="form-control" placeholder="heure du mariage" value="<? echo Date("H") ?>:00" onChange="changeHeureFin();">
																	</div>
																</div>
																<div class="form-group">
																	<label>Fin</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-calendar-check-o"></i>
																		</span>
																		<input type="date" name="date_fin" id="date_fin" class="form-control" placeholder="Date du mariage" value="<? echo Date("Y-m-d") ?>">
																		<input type="time" name="time_fin" id="time_fin" class="form-control" placeholder="heure du mariage" value="<? echo intval(Date("H")+1) ?>:00">
																	</div>
																</div>
																<div class="form-group">
																	<label>Description</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-pencil-square-o"></i>
																		</span>
																		<input type="text" id="description" name="description" class="form-control">
																	</div>
																</div>
																<div class="form-actions">
																	<button type="button" id="submitBtn" data-toggle="modal" data-target="#confirm-submit" class="btn blue">Enregistrer</button>
																</div>										
																</form>
															</div>
														</div>
														<div class="tab-pane" id="tab_1_2">
															<h3 class="event-form-title margin-bottom-20"><i class="fa fa-plus"></i> Ajouter un client</h3>
															<div id="external-events">
																<form name="ajouter_client" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
																<input type="hidden" name="ajout_client" value="ok">
																<div class="form-group">
																	<label>Genre</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-intersex"></i>
																		</span>
																		<select name="genre" class="form-control">
																			<option value="0">Femme</option>
																			<option value="1">Homme</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label>Nom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="nom" class="form-control" placeholder="Nom" value="<? echo $nom ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Prenom</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-user"></i>
																		</span>
																		<input type="text" name="prenom" class="form-control" placeholder="Prénom" value="<? echo $prenom ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Tel</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-mobile-phone"></i>
																		</span>
																		<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<? echo $tel ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Email</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-envelope"></i>
																		</span>
																		<input type="email" name="mail" class="form-control" placeholder="Email" value="<? echo $mail ?>" required> </div>
																</div>
																<div class="form-group">
																	<label>Adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr1" class="form-control" placeholder="Adresse"  value="<? echo $adr1 ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Complément d'adresse</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-road"></i>
																		</span>
																		<input type="text" name="adr2" class="form-control" placeholder="Complément d'adresse"  value="<? echo $adr2 ?>"> </div>
																</div>
																<div class="form-group">
																	<label>CP</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-search"></i>
																		</span>
																		<input type="text" name="cp" class="form-control" placeholder="Code Postal"  value="<? echo $cp ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Ville</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-shield"></i>
																		</span>
																		<input type="text" name="ville" class="form-control" placeholder="Ville" value="<? echo $ville ?>"> </div>
																</div>
																<div class="form-group">
																	<label>Date du mariage</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-calendar-check-o"></i>
																		</span>
																		<input type="date" name="date" class="form-control" placeholder="Date du mariage"> </div>
																</div>
																<div class="form-group">
																	<label>Lieu de mariage</label>
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-black-tie"></i>
																		</span>
																		<input type="text" name="lieu" class="form-control" placeholder="Lieu du mariage"> </div>
																</div>
																<div class="form-actions">
																	<button type="submit" class="btn blue">Enregistrer</button>
																</div>		
																</form>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- END DRAGGABLE EVENTS PORTLET-->
										</div>
										<div class="col-md-9 col-sm-12">
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
							<div id="modalBody" class="modal-body"> </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
							</div>
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
				<script language="Javascript">
				</script>
				<?
					$param = "";
					// ON recherche les events pour remplir le calendrier perso
					$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num and user_num='" . $u->mNum . "' and c.theme_num=4 order by calendrier_datedeb DESC";
					$cc = mysql_query($sql);
					$nbr = mysql_num_rows($cc);
					$i=0;
					while ($rcc=mysql_fetch_array($cc)) {
						if ($i>0) {
							$param .= ',';
						}
						
						list(
							$annee_deb,
							$mois_deb,
							$jour_deb,
							$heure_deb,
							$minute_deb,
							$seconde_deb ) = split('[: -]',$rcc["calendrier_datedeb"],6);
						
						list(
							$annee_fin,
							$mois_fin,
							$jour_fin,
							$heure_fin,
							$minute_fin,
							$seconde_fin ) = split('[: -]',$rcc["calendrier_datefin"],6);
							
						$mois_deb = $mois_deb-1;
						$mois_fin = $mois_fin-1;
						
						$couleur = $rcc["theme_couleur"];
						if ($rcc["client_num"]!=0) {
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num='" . $rcc["rdv_num"] . "'";
							$rr = mysql_query($sql);
							if ($rrr=mysql_fetch_array($rr)) {
								$couleur = $rrr["type_couleur"];								
							}
						}
						$link = "";
						if ($rcc["client_num"]!=0) {
							$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
							$cl = mysql_query($sql);
							if ($rcl=mysql_fetch_array($cl)) {
								$link = '/clients/client.php?client_num=' . crypte($rcc["client_num"]);
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
					$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num and showroom_num='" . $u->mShowroom . "' and c.theme_num!=4 order by calendrier_datedeb DESC";
					$cc = mysql_query($sql);
					$nbr = mysql_num_rows($cc);
					$i=0;
					while ($rcc=mysql_fetch_array($cc)) {
						if ($i>0) {
							$param .= ',';
						}
						
						list(
							$annee_deb,
							$mois_deb,
							$jour_deb,
							$heure_deb,
							$minute_deb,
							$seconde_deb ) = split('[: -]',$rcc["calendrier_datedeb"],6);
						
						list(
							$annee_fin,
							$mois_fin,
							$jour_fin,
							$heure_fin,
							$minute_fin,
							$seconde_fin ) = split('[: -]',$rcc["calendrier_datefin"],6);
							
						$mois_deb = $mois_deb-1;
						$mois_fin = $mois_fin-1;
						
						$couleur = $rcc["theme_couleur"];
						if ($rcc["client_num"]!=0) {
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num='" . $rcc["rdv_num"] . "'";
							$rr = mysql_query($sql);
							if ($rrr=mysql_fetch_array($rr)) {
								$couleur = $rrr["type_couleur"];								
							}
						}
						$link = "";
						if ($rcc["client_num"]!=0) {
							$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
							$cl = mysql_query($sql);
							if ($rcl=mysql_fetch_array($cl)) {
								$link = '/clients/client.php?client_num=' . crypte($rcc["client_num"]);
							}
						}
						$titre_rdv = $rcc["calendrier_titre"];
						if ($rcc["calendrier_desc"]!="")
							$titre_rdv .= " / " . $rcc["calendrier_desc"];
						
						$title_modal = "De" . $heure_deb . "h" . $minute_deb . " à " . $heure_fin . "h" . $minute_fin;
						$desc_modal = "<p><strong>" . $rcc["calendrier_titre"] . "</strong></p>";
						if ($rcc["calendrier_desc"]!="")
							$desc_modal .= "<hr>" . $rcc["calendrier_desc"];
						if ($link!="")
							$desc_modal .= '<br><br><center><a href=\"' . $link . '\" class=\"btn blue\">Allez sur la fiche client</a>';
						
						$param .= '{
								title: "' . $titre_rdv  . '",
								start: new Date(' . $annee_deb . ', ' . $mois_deb . ', ' . $jour_deb . ' , ' . $heure_deb . ', ' . $minute_deb . '),
								end: new Date(' . $annee_fin . ', ' . $mois_fin . ', ' . $jour_fin . ' , ' . $heure_fin . ', ' . $minute_fin . '),
								backgroundColor: "' . $couleur . '",
								title_modal : "' . $title_modal . '",
								desc_modal : "' . $desc_modal . '",';
						/*if ($link!="")
							$param .= ' url:"' . $link . '",';*/
						$param .= '	allDay: !1
							 }';
						$i++;
					 }
?>					
				<? $link_script = '<script language="JavaScript">
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
			$sql = "select * from clients where showroom_num='" . $u->mShowroom . "' and client_nom not like'%?%' and client_prenom not like '%?%' order by client_nom ASC, client_prenom ASC";
			$jj = mysql_query($sql);
			$nbr = mysql_num_rows($jj);
			$i=0;
			while ($rjj=mysql_fetch_array($jj)) {
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
		<script src="/assets/global/plugins/fullcalendar/gcal.js" type="text/javascript"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">';
		?>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <?  include( $chemin . "/mod/bottom.php"); ?>
<script language="Javascript">
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
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#calendar').fullCalendar({
					googleCalendarApiKey: 'AIzaSyCzLtWNhp8437cIxMtRwS3b4BWI72CB4tg',
					events: {
						googleCalendarId: '5acqe03qbt1396cdgfc9gfud8s@group.calendar.google.com'
					}
				});
			});

			</script>
    </body>

</html>