<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Comptabilite - Olympe Mariage";
$desc_page = "Comptabilite - Olympe Mariage";

	if (!isset($mois))
		$mois=0;
	if (!isset($annee))
		$annee=0;
	
	$jour = Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	
	if (!isset($etat))
		$etat=1;
	
	if (!isset($periode))
		$periode = 3;

	if ($date_deb!="") {
		$periode=0;
		if ($date_fin=="") {
			$date_fin = Date("Y-m-d");
		}
	}
	
	if (!isset($showroom)) {
		if ($u->mShowroom==0)
			$showroom=1;
		else
			$showroom = $u->mShowroom;
	}
	
	function get_lundi_dimanche_from_week($week,$year,$format="Y-m-d") {
		$firstDayInYear=date("N",mktime(0,0,0,1,1,$year));
		if ($firstDayInYear<5)
			$shift=-($firstDayInYear-1)*86400;
		else
			$shift=(8-$firstDayInYear)*86400;
		if ($week>1) 
			$weekInSeconds=($week-1)*604800; 
		else 
			$weekInSeconds=0;
		
		$timestamp=mktime(0,0,0,1,1,$year)+$weekInSeconds+$shift;
		$timestamp_dimanche=mktime(0,0,0,1,7,$year)+$weekInSeconds+$shift;

		return array(date($format,$timestamp),date($format,$timestamp_dimanche));

	}
	$titre = "";
	if ($periode!=0) {
		switch ($periode) {
			case 1:
				$date_deb = date("Y-m-d", strtotime("-7 days"));
				$date_fin = date("Y-m-d");
				$titre = "7 derniers jours";
			break;
			
			case 2:
				$semaine = Date("W");
				$annee = Date("Y");
				$semaine--;
				if ($semaine==0) {
					$semaine = 52;
					$annee--;
				}
				$debut_fin_semaine = get_lundi_dimanche_from_week($semaine, $annee);
				$date_deb = $debut_fin_semaine[0];
				$date_fin = $debut_fin_semaine[1];
				$titre = "La semaine dernière";
			break;
			
			case 3:
				$date_deb = date("Y-m-d", strtotime("-30 days"));
				$date_fin = date("Y-m-d");
				$titre = "30 derniers jours";
			break;
			
			case 4:
				//on recupere le mois d'avant
				$mois = Date("m")-1;
				$annee = Date("Y");
				if ($mois==0) {
					$mois = 12;
					$annee--;
				}
				$date_deb = $annee . "-" . $mois . "-01";
				$date_fin = $annee . "-" . $mois . "-" . $jour[$mois];
				$titre = "Le mois dernier";
			break;
		}
	}
	$date_deb .= " 00:00:00";
	$date_fin .= " 23:59:59";
?>

<? include( $chemin . "/mod/head.php"); ?>
<script language="JavaScript">
function initDate() {
	document.getElementById("date_deb").value = ""; 
	document.getElementById("date_fin").value = ""; 
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
                                <a href="/home.php">Accueil</a>
                            </li>
                            <li class="active">Statistiques comptables</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-question font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Statistique de commande</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="liste" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Période</th>
												<th>Date Debut</th>
												<th>Date Fin</th>
												<th>Etat</th>
												<? if ($u->mGroupe==0) { ?>
													<th>Showroom</th>
												<? } ?>
												<th>Facture ID</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<select name="periode" class="form-control input-medium" onChange="initDate()">
														<option value="0"<? if ($periode==0) echo " SELECTED" ?>>Personnalisée</option>
														<option value="1"<? if ($periode==1) echo " SELECTED" ?>>Les 7 derniers jours</option>
														<option value="2"<? if ($periode==2) echo " SELECTED" ?>>La semaine dernière</option>
														<option value="3"<? if ($periode==3) echo " SELECTED" ?>>Les 30 derniers jours</option>
														<option value="4"<? if ($periode==4) echo " SELECTED" ?>>Le mois dernier</option>
													</select>
												</td>
												<td><input type="date" name="date_deb" id="date_deb" class="form-control input-medium" value="<? echo $date_deb ?>"></td>
												<td><input type="date" name="date_fin" id="date_fin" class="form-control input-medium" value="<? echo $date_fin ?>"></td>
												<td>
													<select name="etat" class="form-control input-medium">
														<option value="1" <? if ($etat==1) echo " SELECTED"; ?>>Commandes en cours</option>
														<option value="2" <? if ($etat==2) echo " SELECTED"; ?>>Commandes facturées</option>
														<option value="3" <? if ($etat==3) echo " SELECTED"; ?>>Commandes passées</option>
													</select>
												</td>
												<td><input type="text" name="facture" id="facture" class="form-control input-medium" value="<? echo $facture ?>"></td>
												<? if ($u->mGroupe==0) { ?>
													<td>
														<select name="showroom" class="form-control input-medium">
														<?
															$sql = "select * from showrooms order by showroom_nom ASC";
															$tt = mysql_query($sql);
															while ($rtt=mysql_fetch_array($tt)) {
																echo '<option value="' . $rtt["showroom_num"] . '"';
																if ($rtt["showroom_num"]==$showroom) echo " SELECTED";
																echo '>' . $rtt["showroom_nom"] . '</option>';
															}
														?>
														</select>
													</td>
												<? } else {
													echo '<input type="hidden" name="showroom" value="' . $u->mShowroom . '">';											
												} ?>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
										</table>
									</form>
								</div>
							</div>
						</div>
                    
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-pie-chart font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Statistique du <? echo format_date($date_deb,11,1) ?> au <? echo format_date($date_fin,11,1) ?></span>
									</div>
								</div>
								<div class="portlet-body">
									<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>ID</th>
											<th>Client</th>
											<th>N° Commande</th>
											<th>Date Commande</th>
											<th>N° Facture</th>
											<th>Date Facture</th>
											<th>Echeances</th>
											<th>Monant HT</th>
											<th>TVA</th>
											<th>Montant TTC</th>
											<th>Encaissés</th>
											<th>Reste à payer</th>
										</tr>
									</thead>
									<tbody>
										<?
											if ($facture!="") {
												$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and c.showroom_num='" . $showroom . "' and facture_num='" . $facture . "' order by commande_date DESC";
											} else {
												switch ($etat) {
													case 1:
														$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and commande_num!=0 and facture_num=0 order by commande_date DESC";
													break;

													case 2:
														$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and facture_date>='" . $date_deb . "' and facture_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and commande_num!=0 and facture_num!=0 order by facture_date DESC";
													break;

													case 3:
														$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and commande_num!=0 order by commande_date DESC";
													break;
												}
												
													
											}
											$sql_facture = $sql;
											$cc = mysql_query($sql);
											$total_ht = 0;
											$total_tva = 0;
											$total_ttc = 0;
											$total_encaisse = 0;
											$total_reste_a_payer = 0;
											$nbr_commande = 0;
											while ($rcc=mysql_fetch_array($cc)) {
												$nbr_commande++;
												$nbr_echeance = $rcc["paiement_nombre"];
																			
												// On regarde le nombre de paiement effectué
												$sql = "select * from commandes_paiements where id='" . $rcc["id"] . "'";
												$pa = mysql_query($sql);
												$nbr_paiement = mysql_num_rows($pa);
												
												// On calcul la somme déjà payé
												$montant_paye = 0;
												$sql = "select sum(paiement_montant) val from commandes_paiements where id='" . $rcc["id"] . "'";
												$pa = mysql_query($sql);
												if ($rpa=mysql_fetch_array($pa))
													$montant_paye = $rpa["val"];
												
												$reste_a_paye = abs(montantCommandeTTC($rcc["id"]) - $montant_paye);
																							
												$montant_ttc = montantCommandeTTC($rcc["id"]);
												$montant_ht = montantCommandeHT($rcc["id"]);
												$montant_tva = $montant_ttc - $montant_ht;
												
												$total_ht += $montant_ht;
												$total_tva += $montant_tva;
												$total_ttc += $montant_ttc;
												$total_encaisse += $montant_paye;
												$total_reste_a_payer += $reste_a_paye;
												
												echo '<tr>
														<td>' . $rcc["id"] . '</td>
														<td><a href="/clients/client.php?client_num=' . crypte($rcc["client_num"]) . '">' . $rcc["client_nom"] . ' ' . $rcc["client_prenom"] . '</td>
														<td>';
												if ($etat==1)
													echo '<a href="#" onClick="window.open(\'/acompte/index.php?id=' . crypte($rcc["id"]) . '&paiement=1&print=no\',\'_blank\',\'width=1200,height=800,toolbar=no\');">' . $rcc["commande_num"] . '</a>';
												else 
													echo $rcc["commande_num"];
												echo '  </td>
														<td>' . format_date($rcc["commande_date"],11,1) . '</td>';
												if (($etat==2) || ($etat==3)) {
													echo '<td><a href="#" onClick="window.open(\'/facture/index.php?facture=' . crypte($rcc["id"]) . '&paiement=1&print=no\',\'_blank\',\'width=1200,height=800,toolbar=no\');">' . $rcc["facture_num"] . '</td>';
													echo '	<td>' . format_date($rcc["facture_date"],11,1) . '</td>';
												}
												else 
													echo "<td></td><td></td>";
												echo 	'<td class="text-center">' . $nbr_paiement . '/' . $nbr_echeance . '</td>
														 <td>' . number_format($montant_ht,2,"."," ") . ' €</td>
														 <td>' . number_format($montant_tva,2,"."," ") . ' €</td>
														 <td>' . number_format($montant_ttc,2,"."," ") . ' €</td>
														 <td>' . number_format($montant_paye,2,"."," ") . ' €</td>
														 <td>' . number_format($reste_a_paye,2,"."," ") . ' €</td>
													</tr>';
											}
										?>
										<tr>
											<td colspan="13"><hr></td>
										</tr>
										<tr>
											<td><? if ($etat==2) { ?><a href="#" class="btn blue" onClick="window.open('/facture/all.php?date_deb=<? echo $date_deb ?>&date_fin=<? echo $date_fin ?>&showroom=<? echo $showroom ?>','print','toolbars=no,menubar=no,width=1000,height=600');">Imprimer</a><? } ?></td>
											<td><strong>Total commande : <? echo $nbr_commande ?></strong></td>
											<td colspan="4" align="right"><strong>Total</strong> : </td>
											<td><strong><? echo number_format($total_ht,2,"."," ") ?> €</strong></td>
											<td><strong><? echo number_format($total_tva,2,"."," ") ?> €</strong></td>
											<td><strong><? echo number_format($total_ttc,2,"."," ") ?> €</strong></td>
											<td><strong><? echo number_format($total_encaisse,2,"."," ") ?> €</strong></td>
											<td><strong><? echo number_format($total_reste_a_payer,2,"."," ") ?> €</strong></td>
										</tr>
									</tbody>
									</table>
								</div>
							</div>
						</div>
						<? if ($etat==2) { ?>
							<hr>
							<form name="extract" action="extract.php" method="POST">
							<input type="hidden" name="date_debut" value="<? echo $date_deb ?>">
							<input type="hidden" name="date_fin" value="<? echo $date_fin ?>">
							<input type="hidden" name="showroom" value="<? echo $showroom ?>">
							<input type="submit" value="Export comptabilité" class="btn green">
							</form>
						<? } ?>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>