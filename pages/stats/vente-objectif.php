<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Statistiques - Olympe Mariage";
$desc_page = "Statistiques - Olympe Mariage";
  
	$mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
	$mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  
	if (!isset($showroom)) {
		if ($u->mShowroom==0)
			$showroom=1;
		else
			$showroom = $u->mShowroom;
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
                                <a href="/home">Accueil</a>
                            </li>
                            <li class="active">Statistiques</li>
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
											<span class="caption-subject bold uppercase"> Statistiques</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<?= form_action_same() ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Date Debut</th>
												<th>Date Fin</th>
												<?php if ($u->mGroupe==0) { ?>
													<th>Showroom</th>
												<?php } ?>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<select name="mois_deb" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<?php 
														for ($i=1;$i<13;$i++) {
															echo "<option value=\"" . sprintf($i,"%02d") . "\"";
															if (sprintf($i,"%02d")==$mois_deb)
																echo " SELECTED";
															echo ">" . $mois_nom[$i] . "</option>\n";
														}
														?>		
													</select>
													<select name="annee_deb" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<?php 
														for ($i=Date("Y")+1;$i>2015;$i--) {
															echo "<option value=\"" .$i . "\"";
															if ($i==$annee_deb)
																echo " SELECTED";
															echo ">" . $i . "</option>\n";
														}
														?>		
													</select>
												</td>
												<td>
													<select name="mois_fin" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<?php 
														for ($i=1;$i<13;$i++) {
															echo "<option value=\"" . sprintf($i,"%02d") . "\"";
															if (sprintf($i,"%02d")==$mois_fin)
																echo " SELECTED";
															echo ">" . $mois_nom[$i] . "</option>\n";
														}
														?>		
													</select>
													<select name="annee_fin" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<?php 
														for ($i=Date("Y")+1;$i>2015;$i--) {
															echo "<option value=\"" .$i . "\"";
															if ($i==$annee_fin)
																echo " SELECTED";
															echo ">" . $i . "</option>\n";
														}
														?>		
													</select>
												</td>
												<?php if ($u->mGroupe==0) { ?>
													<td>
														<select name="showroom" class="form-control input-medium">
														<?php															$sql = "select * from showrooms order by showroom_nom ASC";
															$tt = $base->query($sql);
															foreach ($tt as $rtt) {
																echo '<option value="' . $rtt["showroom_num"] . '"';
																if ($rtt["showroom_num"]==$showroom) echo " SELECTED";
																echo '>' . $rtt["showroom_nom"] . '</option>';
															}
														?>
														</select>
													</td>
												<?php } ?>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									</form>
								</div>
							</div>
						</div>
                    	<?php if (isset($recherche)) { ?>
							<div class="col-lg-6 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">C.A. + Nbr / Objectifs  - Vente de robes sur l'année</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<thead>
												<tr>
													<th>Mois</th>
													<th>Nbr</th>
													<th>CA</th>
													<th>Objectif Nbr</th>
													<th>Objectif CA</th>
													<th class="text-center">Etat</th>
												</tr>
											</thead>
											<tbody>
											<?php												$date_deb = $annee_deb . "-" . $mois_deb . "-01";
												$date_fin = $annee_fin . "-" . $mois_fin . "-01";
												
												$sql = "select * from showrooms_objectifs where showroom_num='" . $showroom . "' and genre_num=0 and date>='" . $date_deb . "' and date<='" . $date_fin . "' order by date ASC";
												$cc = $base->query($sql);
												
												$nbr_total = 0;
												$ca_total = 0;
												$objectif_nbr_total = 0;
												$objectif_ca_total = 0;
												foreach ($cc as $rcc) {
													
													$objectif_ca = $rcc["ca"];
													$objectif_nbr = $rcc["nbr"];
													
													$abscisse_mois = $rcc["mois"] . "/" . $rcc["annee"];
													
													$date_debut = $rcc["annee"] . "-" . $rcc["mois"] . "-01 00:00:00";
													$date_fin = $rcc["annee"] . "-" . $rcc["mois"] . "-" . $mois_jour[$rcc["mois"]] . " 23:59:59";
													
													$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
													$ca_mois = 0;
													$nbr_mois = 0;
													$co = $base->query($sql);
													foreach ($co as $rco) {
														$prix_produit = $rco["montant_ht"];
														if ($rco["montant_ht_remise"]!=0)
															$prix_produit = $rco["montant_ht_remise"];
														
														$prix_produit = $prix_produit*$rco["qte"];
														if ($rco["categorie_num"]==11)
															$nbr_mois += $rco["qte"];
														switch ($rco["commande_produit_remise_type"]) {
															case 1: // Remise en %
																$prix_produit = $prix_produit*(1-($rco["commande_produit_remise"]/100));
															break;
														
															case 2: // Remise en euro
																$prix_produit = $prix_produit - $rco["commande_produit_remise"];
															break;
														}
														$ca_mois += $prix_produit;
													}
													$etat = "";
													if ($ca_mois!=0) {
														if ($ca_mois<$objectif_ca)
															$etat = '<i class="fa fa-frown-o font-red font-lg"></i>';
														else
															$etat = '<i class="fa fa-smile-o font-green-jungle font-lg"></i>';
													} 
													
													$nbr_total += $nbr_mois;
													$ca_total += $ca_mois;
													$objectif_nbr_total += $objectif_nbr;
													$objectif_ca_total += $objectif_ca;
													
													$ca_mois = number_format($ca_mois,2,"."," ");
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $nbr_mois . '</td>
															<td>' . $ca_mois . ' €</td>
															<td>' . $objectif_nbr . '</td>
															<td>' . number_format($objectif_ca,2,"."," ") . ' €</td>
															<td class="text-center">' . $etat . '</td>
														</tr>';
												}
												echo '</tr>
															<td><strong>Total</strong></td>
															<td><strong>' . $nbr_total . '</strong></td>
															<td><strong>' . number_format($ca_total,2,"."," ") . ' €</strong></td>
															<td><strong>' . $objectif_nbr_total . '</strong></td>
															<td><strong>' . number_format($objectif_ca_total,2,"."," ") . ' €</strong></td>
															<td class="text-center"></td>
														</tr>';
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">Stats 1e & 2e RDV sur l'année</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<thead>
												<tr>
													<th>Mois</th>
													<th>Nbr 1e RDV</th>
													<th>Nbr 2e RDV</th>
												</tr>
											</thead>
											<tbody>
											<?php												$date_deb = $annee_deb . "-" . $mois_deb . "-01";
												$date_fin = $annee_fin . "-" . $mois_fin . "-01";
												
												$sql = "select * from showrooms_objectifs where showroom_num='" . $showroom . "' and genre_num=0 and date>='" . $date_deb . "' and date<='" . $date_fin . "' order by date ASC";
												$cc = $base->query($sql);
												
												$total_1 = 0;
												$total_2 = 0;
												foreach ($cc as $rcc) {
													
													$abscisse_mois = $rcc["mois"] . "/" . $rcc["annee"];
													
													$date_debut = $rcc["annee"] . "-" . $rcc["mois"] . "-01 00:00:00";
													$date_fin = $rcc["annee"] . "-" . $rcc["mois"] . "-" . $mois_jour[$rcc["mois"]] . " 23:59:59";
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=1 and client_genre=0";
													$co = $base->query($sql);
													$nbr_premier = count($co);
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=6 and client_genre=0";
													$co = $base->query($sql);
													$nbr_deuxieme = count($co);
													$total_1 += $nbr_premier;
													$total_2 += $nbr_deuxieme;
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $nbr_premier . '</td>
															<td>' . $nbr_deuxieme . '</td>
														</tr>';
												}
												echo '</tr>
														<td><strong>Total</strong></td>
														<td><strong>' . $total_1 . '</strong></td>
														<td><strong>' . $total_2 . '</strong></td>
													</tr>';
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">C.A. + Nbr / Objectifs  - Vente de costumes sur l'année</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<thead>
												<tr>
													<th>Mois</th>
													<th>Nbr</th>
													<th>CA</th>
													<th>Objectif Nbr</th>
													<th>Objectif CA</th>
													<th class="text-center">Etat</th>
												</tr>
											</thead>
											<tbody>
											<?php												$date_deb = $annee_deb . "-" . $mois_deb . "-01";
												$date_fin = $annee_fin . "-" . $mois_fin . "-01";
												
												$sql = "select * from showrooms_objectifs where showroom_num='" . $showroom . "' and genre_num=1 and date>='" . $date_deb . "' and date<='" . $date_fin . "' order by date ASC";
												$cc = $base->query($sql);
												
												$nbr_total = 0;
												$ca_total = 0;
												$objectif_nbr_total = 0;
												$objectif_ca_total = 0;
												foreach ($cc as $rcc) {
													
													$objectif_ca = $rcc["ca"];
													$objectif_nbr = $rcc["nbr"];
													
													$abscisse_mois = $rcc["mois"] . "/" . $rcc["annee"];
													
													$date_debut = $rcc["annee"] . "-" . $rcc["mois"] . "-01 00:00:00";
													$date_fin = $rcc["annee"] . "-" . $rcc["mois"] . "-" . $mois_jour[$rcc["mois"]] . " 23:59:59";
													
													$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (29) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
													$ca_mois = 0;
													$nbr_mois = 0;
													$co = $base->query($sql);
													foreach ($co as $rco) {
														$prix_produit = $rco["montant_ht"];
														if ($rco["montant_ht_remise"]!=0)
															$prix_produit = $rco["montant_ht_remise"];
														
														$prix_produit = $prix_produit*$rco["qte"];
														if ($rco["categorie_num"]==29)
															$nbr_mois += $rco["qte"];
														switch ($rco["commande_produit_remise_type"]) {
															case 1: // Remise en %
																$prix_produit = $prix_produit*(1-($rco["commande_produit_remise"]/100));
															break;
														
															case 2: // Remise en euro
																$prix_produit = $prix_produit - $rco["commande_produit_remise"];
															break;
														}
														$ca_mois += $prix_produit;
													}
													$etat = "";
													if ($ca_mois!=0) {
														if ($ca_mois<$objectif_ca)
															$etat = '<i class="fa fa-frown-o font-red font-lg"></i>';
														else
															$etat = '<i class="fa fa-smile-o font-green-jungle font-lg"></i>';
													} 
													
													$nbr_total += $nbr_mois;
													$ca_total += $ca_mois;
													$objectif_nbr_total += $objectif_nbr;
													$objectif_ca_total += $objectif_ca;
													
													$ca_mois = number_format($ca_mois,2,"."," ");
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $nbr_mois . '</td>
															<td>' . $ca_mois . ' €</td>
															<td>' . $objectif_nbr . '</td>
															<td>' . number_format($objectif_ca,2,"."," ") . ' €</td>
															<td class="text-center">' . $etat . '</td>
														</tr>';
												}
												echo '</tr>
															<td><strong>Total</strong></td>
															<td><strong>' . $nbr_total . '</strong></td>
															<td><strong>' . number_format($ca_total,2,"."," ") . ' €</strong></td>
															<td><strong>' . $objectif_nbr_total . '</strong></td>
															<td><strong>' . number_format($objectif_ca_total,2,"."," ") . ' €</strong></td>
															<td class="text-center"></td>
														</tr>';
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">Stats 1e & 2e RDV Homme sur l'année</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<thead>
												<tr>
													<th>Mois</th>
													<th>Nbr 1e RDV</th>
													<th>Nbr 2e RDV</th>
												</tr>
											</thead>
											<tbody>
											<?php												$date_deb = $annee_deb . "-" . $mois_deb . "-01";
												$date_fin = $annee_fin . "-" . $mois_fin . "-01";
												
												$sql = "select * from showrooms_objectifs where showroom_num='" . $showroom . "' and genre_num=1 and date>='" . $date_deb . "' and date<='" . $date_fin . "' order by date ASC";
												$cc = $base->query($sql);
												
												$total_1 = 0;
												$total_2 = 0;
												foreach ($cc as $rcc) {
													
													$abscisse_mois = $rcc["mois"] . "/" . $rcc["annee"];
													
													$date_debut = $rcc["annee"] . "-" . $rcc["mois"] . "-01 00:00:00";
													$date_fin = $rcc["annee"] . "-" . $rcc["mois"] . "-" . $mois_jour[$rcc["mois"]] . " 23:59:59";
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=1 and client_genre=1";
													$co = $base->query($sql);
													$nbr_premier = count($co);
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=6 and client_genre=1";
													$co = $base->query($sql);
													$nbr_deuxieme = count($co);
													$total_1 += $nbr_premier;
													$total_2 += $nbr_deuxieme;
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $nbr_premier . '</td>
															<td>' . $nbr_deuxieme . '</td>
														</tr>';
												}
												echo '</tr>
														<td><strong>Total</strong></td>
														<td><strong>' . $total_1 . '</strong></td>
														<td><strong>' . $total_2 . '</strong></td>
													</tr>';
											?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>