<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Dashboard - Olympe Mariage";
$desc_page = "Dashboard - Olympe Mariage";

if ($u->mGroupe==0) {
	if (!isset($showroom_choix)) {
		$u->mShowroom = 1; // Si on est admin par defaut c'est Montpellier 
	} else 
		$u->mShowroom = $showroom_choix;
	
	$sql = "select * from showrooms where showroom_num='" . $u->mShowroom . "'";
	$ss = mysql_query($sql);
	if ($rss=mysql_fetch_array($ss)) {
		$u->mShowroomInfo = $rss;
	}
}

$mois_deb = 8;
$mois_encours = Date("n");
if ($mois_encours<9) 
	$annee_deb = Date("Y")-1;
else
	$annee_deb = Date("Y");

$objectif_nbr_total = 0;
$objectif_ca_total = 0;
for ($i=0;$i<12;$i++) {
	$mois_deb++;
	if ($mois_deb==13) {
		$mois_deb=1;
		$annee_deb = $annee_deb+1;
	}
	
	// On cherche les objectifs Femme
	$sql = "select * from showrooms_objectifs where showroom_num='" . $u->mShowroom . "' and genre_num=0 and mois='" . $mois_deb . "' and annee='" . $annee_deb . "'";
	$oo = mysql_query($sql);
	if ($roo=mysql_fetch_array($oo)) {
		$objectif[$mois_deb] = $roo["ca"];
		$objectif_nbr[$mois_deb] = $roo["nbr"];
		$objectif_nbr_total += $roo["nbr"];
		$objectif_ca_total += $roo["ca"];		
	} else {
		$objectif[$mois_deb] = 0;
		$objectif_nbr[$mois_deb] = 0;
	}
	
	// On cherche les objectifs Homme
	$sql = "select * from showrooms_objectifs where showroom_num='" . $u->mShowroom . "' and genre_num=1 and mois='" . $mois_deb . "' and annee='" . $annee_deb . "'";
	$oo = mysql_query($sql);
	if ($roo=mysql_fetch_array($oo)) {
		$objectif_homme[$mois_deb] = $roo["ca"];
		$objectif_homme_nbr[$mois_deb] = $roo["nbr"];
	} else {
		$objectif_homme[$mois_deb] = 0;
		$objectif_homme_nbr[$mois_deb] = 0;
	}
}
$u->mShowroomInfo["nbr_annee"] = $objectif_nbr_total;
$u->mShowroomInfo["ca_annee"] = $objectif_ca_total;

$mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

?>

<? include( $chemin . "/mod/head.php"); ?>

    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <? include( $chemin . "/mod/top.php"); ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
					<? if ($u->mGroupe!=0) { ?>
						<div class="breadcrumbs">
							<h1>Olympe Mariage - <? echo htmlentities(format_date(Date("Y-m-d"),13,1)) ?></h1>
							<ol class="breadcrumb">
								<li>
									<a href="#">Accueil</a>
								</li>
								<li class="active">Dashboard</li>
							</ol>
						</div>
					<? } else { ?>
						<form name="choix" action="<? echo $_SERVER["PHP_SELF"] ?>" method="POST">
						<div class="breadcrumbs">
							<h1>Olympe Mariage <select name="showroom_choix" onChange="this.form.submit()" class="form-inline">
							<?
								$sql = "select * from showrooms order by showroom_num ASC";
								$sh = mysql_query($sql);
								while ($rsh=mysql_fetch_array($sh)) {
									echo '<option value="' . $rsh["showroom_num"] . '"';
									if ($rsh["showroom_num"]==$u->mShowroom)
										echo " SELECTED";
									echo '>' . $rsh["showroom_ville"] . '</option>';
								}
							?>
							</select>
							- <? echo htmlentities(format_date(Date("Y-m-d"),13,1)) ?></h1>
							<ol class="breadcrumb">
								<li>
									<a href="#">Accueil</a>
								</li>
								<li class="active">Dashboard</li>
							</ol>
						</div>
						</form>
					<? } ?>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
							<?
								$date_debut = Date("Y-m") . "-01 00:00:00";
								$date_fin = Date("Y-m") . "-" . $mois_jour[Date("n")] . " 23:59:59";
								
								$sql = "select * from commandes c where commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "'";
								$ca_mois_ht = 0;
								$nbr_commande_mois = 0;
								$co = mysql_query($sql);
								while ($rco=mysql_fetch_array($co)) {
									$ca_mois += montantCommandeHT($rco["id"]);
									$nbr_commande_mois++;
								}
								$ca_mois = number_format($ca_mois,2);
							?>
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-green-sharp">
                                            <span data-counter="counterup" data-value="<? echo $ca_mois ?>">0</span>
                                            <small class="font-green-sharp">€</small>
                                        </h3>
                                        <small>CA HT du mois</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-red-haze">
                                            <span data-counter="counterup" data-value="<? echo $nbr_commande_mois ?>">0</span>
                                        </h3>
                                        <small>Nombre de commande du mois</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-basket"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?
							$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "'";
							$ca_mois_robe = 0;
							$nbr_mois_robe = 0;
							$co = mysql_query($sql);
							$commande_encours=0;
							while ($rco=mysql_fetch_array($co)) {
								$prix_produit = $rco["montant_ht"];
								if ($rco["montant_ht_remise"]!=0)
									$prix_produit = $rco["montant_ht_remise"];
								
								$prix_produit = $prix_produit*$rco["qte"];
								
								switch ($rco["commande_produit_remise_type"]) {
									case 1: // Remise en %
										$prix_produit = $prix_produit*(1-($rco["commande_produit_remise"]/100));
									break;
								
									case 2: // Remise en euro
										$prix_produit = $prix_produit - $rco["commande_produit_remise"];
									break;
								}
								if ($rco["categorie_num"]==11)
									$nbr_mois_robe += $rco["qte"];
								
								/*if ($commande_encours!=$rco["id"]) {
									$nbr_mois_robe += $rco["qte"];
									$commande_encours = $rco["id"];
								}*/									
								$ca_mois_robe += $prix_produit;								
							}
							if ($objectif[Date("n")]>0)
								$objectif_ca = ($ca_mois_robe / $objectif[Date("n")]);
							else
								$objectif_ca = 0;
							if ($objectif_ca>1) $objectif_ca=1;
							$objectif_ca = number_format($objectif_ca*100,0);
							
							if ($objectif_nbr[Date("n")]>0)
								$objectif_nbr_robe = ($nbr_mois_robe / $objectif_nbr[Date("n")]);
							else
								$objectif_nbr_robe = 0;
							if ($objectif_nbr_robe>1) $objectif_nbr_robe=1;
							$objectif_nbr_robe = number_format($objectif_nbr_robe*100,0);
							
							$ca_mois_robe = number_format($ca_mois_robe,2);
						?>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-blue-sharp">
                                            <span data-counter="counterup" data-value="<? echo $ca_mois_robe ?>">0</span>
											<small class="font-blue-sharp">€</small>
                                        </h3>
                                        <small>CA HT Vente de robes du mois</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-basket-loaded"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: <? echo $objectif_ca ?>%;" class="progress-bar progress-bar-success blue-sharp">
                                            <span class="sr-only"><? echo $objectif_ca ?>% objectif</span>
                                        </span>
                                    </div>
                                    <div class="status">
                                        <div class="status-title"> objectif </div>
                                        <div class="status-number"> <? echo $objectif_ca ?>% </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-purple-soft">
                                            <span data-counter="counterup" data-value="<? echo $nbr_mois_robe ?>">0</span>
                                        </h3>
                                        <small>Nbr de robes vendues du mois</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-user-female"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: <? echo $objectif_nbr_robe ?>%;" class="progress-bar progress-bar-success purple-soft">
                                            <span class="sr-only"><? echo $objectif_nbr_robe ?>% objectif</span>
                                        </span>
                                    </div>
                                    <div class="status">
                                        <div class="status-title"> objectif </div>
                                        <div class="status-number"> <? echo $objectif_nbr_robe ?>% </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-xs-12 col-sm-12">
							<div id="calendar" class="has-toolbar"> </div>
							<hr>
							<? if ($u->mCompta==1) { ?>
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="dashboard-stat2 bordered">
										<?
											// On calcul l'année en cours
											$mois_deb = 8;
											$mois_encours = Date("n");
											if ($mois_encours<9) 
												$annee_deb = Date("Y")-1;
											else
												$annee_deb = Date("Y");
											
											$annee_fin = $annee_deb+1;
											
											$date_debut_annee = $annee_deb . "-09-01 00:00:00";
											$date_fin_annee = $annee_fin . "-08-31 23:59:59";
											
											$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut_annee . "' and commande_date<='" . $date_fin_annee . "' and c.showroom_num='" . $u->mShowroom . "'";
											$ca_annee_robe = 0;
											$nbr_annne_robe = 0;
											$co = mysql_query($sql);
											$commande_encours = 0;
											while ($rco=mysql_fetch_array($co)) {
												$prix_produit = $rco["montant_ht"];
												if ($rco["montant_ht_remise"]!=0)
													$prix_produit = $rco["montant_ht_remise"];
												
												$prix_produit = $prix_produit*$rco["qte"];
												
												switch ($rco["commande_produit_remise_type"]) {
													case 1: // Remise en %
														$prix_produit = $prix_produit*(1-($rco["commande_produit_remise"]/100));
													break;
												
													case 2: // Remise en euro
														$prix_produit = $prix_produit - $rco["commande_produit_remise"];
													break;
												}
												if ($commande_encours!=$rco["id"]) {
													$nbr_annee_robe += $rco["qte"];
													$commande_encours = $rco["id"];
												}
												$ca_annee_robe += $prix_produit;												
											}
											
											if ($u->mShowroomInfo["ca_annee"]>0)
												$objectif_ca_annee = ($ca_annee_robe / $u->mShowroomInfo["ca_annee"]);
											else 
												$objectif_ca_annee = 0;
											if ($objectif_ca_annee>1) $objectif_ca_annee=1;
											$objectif_ca_annee = number_format($objectif_ca_annee*100,0);
											
											$ca_annee_robe = number_format($ca_annee_robe,2);
										?>
											<div class="display">
												<div class="number">
													<h3 class="font-green-sharp">
														<span data-counter="counterup" data-value="<? echo $ca_annee_robe ?>">0</span>
														<small class="font-green-sharp">€</small>
													</h3>
													<small>CA HT Robes de l'année </small>
												</div> <div class="number">
													<h3 class="font-red-soft">
														 <span style="margin-left:30px;margin-right:30px;">/</span> <span data-counter="counterup" data-value="<? echo number_format($u->mShowroomInfo["ca_annee"],2) ?>">0</span>
														<small class="font-red-soft">€</small>
													</h3>
													 <span style="margin-left:50px;margin-right:30px;"></span><small>Objectif annuel</small>
												</div>
												<div class="icon">
													<i class="icon-credit-card"></i>
												</div>
											</div>
											<div class="progress-info">
												<div class="progress">
													<span style="width: <? echo $objectif_ca_annee ?>%;" class="progress-bar progress-bar-success blue-sharp">
														<span class="sr-only"><? echo $objectif_ca_annee ?>% objectif</span>
													</span>
												</div>
												<div class="status">
													<div class="status-title"> objectif </div>
													<div class="status-number"> <? echo $objectif_ca_annee ?>% </div>
												</div>
											</div>
										</div>
									</div>
									<?
										$sql = "select count(rdv_num) val from rendez_vous r, clients c where c.client_num=r.client_num and rdv_date>='" . $date_debut_annee . "' and rdv_date<='" . $date_fin_annee . "' and r.type_num=1 and c.showroom_num='" . $u->mShowroom . "'  and client_genre=0";	
										$rr = mysql_query($sql);
										$nbr_rdv = 0;
										$transformation = 0;
										if ($rrr=mysql_fetch_array($rr)) {
											$nbr_rdv = $rrr["val"];
											
											$sql = "select count(rdv_num) val from rendez_vous r, clients c where c.client_num=r.client_num and rdv_date>='" . Date("Y-m-d H:i:s") . "' and rdv_date<='" . $date_fin_annee . "' and r.type_num=1 and c.showroom_num='" . $u->mShowroom . "'  and client_genre=0";
											$dd = mysql_query($sql);
											$nbr_rdv_a_venir = 0;
											if ($rdd=mysql_fetch_array($dd)) {
												$nbr_rdv_a_venir = $rdd["val"];
											}
											
											if ($nbr_rdv>0) 
												$transformation = ($nbr_annee_robe / $nbr_rdv)*100;
											else
												$transformation = 0;
											$transformation = number_format($transformation,0);
										}
									?>
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="dashboard-stat2 bordered">
											<div class="display">
												<div class="number">
													<h3 class="font-red-haze">
														<span data-counter="counterup" data-value="<? echo $nbr_rdv ?>">0</span>
													</h3>
													<small>Rendez-vous sur l'année dont <font color="red"><? echo $nbr_rdv_a_venir ?></font> à venir</small>
												</div>
												<div class="icon">
													<i class="icon-user-female"></i>
												</div>
											</div>
											<div class="progress-info">
												<div class="progress">
													<span style="width: <? echo $transformation ?>%;" class="progress-bar progress-bar-success blue-sharp">
														<span class="sr-only"><? echo $transformation ?>% taux de transformation</span>
													</span>
												</div>
												<div class="status">
													<div class="status-title"> taux de transformation </div>
													<div class="status-number"> <? echo $transformation ?>% </div>
												</div>
											</div>
										</div>
									</div>
									
									<?
										$sql = "select * from commandes c where commande_num!=0 and commande_date>='" . $date_debut_annee . "' and commande_date<='" . $date_fin_annee . "' and c.showroom_num='" . $u->mShowroom . "'";
										$ca_annee = 0;
										$nbr_commande_mois = 0;
										$co = mysql_query($sql);
										while ($rco=mysql_fetch_array($co)) {
											$ca_annee += montantCommandeHT($rco["id"]);
											$nbr_commande_mois++;
										}
										$ca_annee = number_format($ca_annee,2);
									?>
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="dashboard-stat2 bordered">
											<div class="display">
												<div class="number">
													<h3 class="font-red-haze">
														<span data-counter="counterup" data-value="<? echo $ca_annee ?>">0</span>
														<small class="font-red-sharp">€</small>
													</h3>
													<small>CA HT Annuel</small>
												</div>
												<div class="icon">
													<i class="icon-credit-card"></i>
												</div>
											</div>
										</div>
									</div>
									<?
										$sql = "select * from commandes c where commande_num!=0 and commande_date>='" . $date_debut_annee . "' and commande_date<='" . $date_fin_annee . "' and c.showroom_num='" . $u->mShowroom . "'";
										$co = mysql_query($sql);
										$montant_commande = 0;
										while ($rco=mysql_fetch_array($co)) {
											$montant_commande += montantCommandeTTC($rco["id"]);
										}	
										$montant_commande = number_format($montant_commande,2);
										$sql = "select sum(paiement_montant) val from commandes c, commandes_paiements cp where c.id=cp.id and commande_num!=0 and commande_date>='" . $date_debut_annee . "' and commande_date<='" . $date_fin_annee . "' and c.showroom_num='" . $u->mShowroom . "'";
										$pa = mysql_query($sql);
										$encaissement = 0;
										if ($rpa=mysql_fetch_array($pa)) {
											$encaissement = number_format($rpa["val"],2);
										}
										
										if ($montant_commande>0)
											$taux_dette = (1-($encaissement / $montant_commande))*100;
										else
											$taux_dette = 0;
										$taux_dette = number_format($taux_dette,0);
										
									?>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="dashboard-stat2 bordered">
											<div class="display">
												<div class="number">
													<h3 class="font-red-sharp">
														<span data-counter="counterup" data-value="<? echo $montant_commande ?>">0</span>
														<small class="font-red-sharp">€</small>
													</h3>
													<small>CA TTC</small>
												</div>  <div class="number">
													<h3 class="font-purple-soft">
														 <span style="margin-left:30px;margin-right:30px;">/</span> <span data-counter="counterup" data-value="<? echo $encaissement ?>">0</span>
														<small class="font-purple-soft">€</small>
													</h3>
													 <span style="margin-left:50px;margin-right:30px;"></span><small>Encaissement</small>
												</div>
												<div class="icon">
													<i class="icon-basket-loaded"></i>
												</div>
											</div>
											<div class="progress-info">
												<div class="progress">
													<span style="width: <? echo $taux_dette ?>%;" class="progress-bar progress-bar-success purple-soft">
														<span class="sr-only"><? echo $taux_dette ?>% paiement</span>
													</span>
												</div>
												<div class="status">
													<div class="status-title"> paiement </div>
													<div class="status-number"> <? echo $taux_dette ?>% </div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<? } ?>
                        </div>
                        <div class="col-lg-6 col-xs-12 col-sm-12">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption ">
                                        <span class="caption-subject font-dark bold uppercase">C.A. / Objectifs  - Vente de robes</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="dashboard_amchart_3" class="CSSAnimationChart"></div>
                                </div>
                            </div>
							<div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption ">
                                        <span class="caption-subject font-dark bold uppercase">Nbr / Objectifs  - Vente de robes</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="dashboard_amchart_2" class="CSSAnimationChart"></div>
                                </div>
                            </div>
                        </div>
						<? if ($u->mCompta==1) { ?>
							 <div class="col-lg-6 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">C.A. + Nbr / Objectifs  - Vente de robes sur l'année Femme</span>
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
											<?
												$mois_deb = 8;
												$mois_encours = Date("n");
												if ($mois_encours<9) 
													$annee_deb = Date("Y")-1;
												else
													$annee_deb = Date("Y");
												
												$nbr_total = 0;
												$ca_total = 0;
												$objectif_nbr_total = 0;
												$objectif_ca_total = 0;
												for ($i=0;$i<12;$i++) {
													$mois_deb++;
													if ($mois_deb==13) {
														$mois_deb=1;
														$annee_deb = $annee_deb+1;
													}
													$abscisse_mois = $mois_deb . "/" . $annee_deb;
													
													$date_debut = $annee_deb . "-" . $mois_deb . "-01 00:00:00";
													$date_fin = $annee_deb . "-" . $mois_deb . "-" . $mois_jour[$mois_deb] . " 23:59:59";
													
													$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "'";
													$ca_mois = 0;
													$nbr_mois = 0;
													$co = mysql_query($sql);
													while ($rco=mysql_fetch_array($co)) {
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
														if ($ca_mois<$objectif[$mois_deb])
															$etat = '<i class="fa fa-frown-o font-red font-lg"></i>';
														else
															$etat = '<i class="fa fa-smile-o font-green-jungle font-lg"></i>';
													} 
													
													$nbr_total += $nbr_mois;
													$ca_total += $ca_mois;
													$objectif_nbr_total += $objectif_nbr[$mois_deb];
													$objectif_ca_total += $objectif[$mois_deb];
													
													$ca_mois = number_format($ca_mois,2,"."," ");
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $nbr_mois . '</td>
															<td>' . $ca_mois . ' €</td>
															<td>' . $objectif_nbr[$mois_deb] . '</td>
															<td>' . number_format($objectif[$mois_deb],2,"."," ") . ' €</td>
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
											<span class="caption-subject font-dark bold uppercase">Stats 1e & 2e RDV sur l'année Femme</span>
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
											<?
												$mois_deb = 8;
												$mois_encours = Date("n");
												if ($mois_encours<9) 
													$annee_deb = Date("Y")-1;
												else
													$annee_deb = Date("Y");
												
												$total_1 = 0;
												$total_2 = 0;
												for ($i=0;$i<12;$i++) {
													$mois_deb++;
													if ($mois_deb==13) {
														$mois_deb=1;
														$annee_deb = $annee_deb+1;
													}
													$abscisse_mois = $mois_deb . "/" . $annee_deb;
													
													$date_debut = $annee_deb . "-" . $mois_deb . "-01 00:00:00";
													$date_fin = $annee_deb . "-" . $mois_deb . "-" . $mois_jour[$mois_deb] . " 23:59:59";
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "' and r.type_num=1 and client_genre=0";
													$co = mysql_query($sql);
													$nbr_premier = mysql_num_rows($co);
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "' and r.type_num=6 and client_genre=0";
													$co = mysql_query($sql);
													$nbr_deuxieme = mysql_num_rows($co);
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
											<span class="caption-subject font-dark bold uppercase">C.A. + Nbr / Objectifs  - Vente de costumes sur l'année Homme</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<thead>
												<tr>
													<th>Mois</th>
													<th>CA</th>
													<th>Objectif CA</th>
													<th class="text-center">Etat</th>
												</tr>
											</thead>
											<tbody>
											<?
												$mois_deb = 8;
												$mois_encours = Date("n");
												if ($mois_encours<9) 
													$annee_deb = Date("Y")-1;
												else
													$annee_deb = Date("Y");
												
												$nbr_total = 0;
												$ca_total = 0;
												$objectif_nbr_total = 0;
												$objectif_ca_total = 0;
												for ($i=0;$i<12;$i++) {
													$mois_deb++;
													if ($mois_deb==13) {
														$mois_deb=1;
														$annee_deb = $annee_deb+1;
													}
													$abscisse_mois = $mois_deb . "/" . $annee_deb;
													
													$date_debut = $annee_deb . "-" . $mois_deb . "-01 00:00:00";
													$date_fin = $annee_deb . "-" . $mois_deb . "-" . $mois_jour[$mois_deb] . " 23:59:59";
													
													$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (29) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "'";
													//echo $sql;
													$ca_mois = 0;
													$nbr_mois = 0;
													$co = mysql_query($sql);
													while ($rco=mysql_fetch_array($co)) {
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
														if ($ca_mois<$objectif_homme[$mois_deb])
															$etat = '<i class="fa fa-frown-o font-red font-lg"></i>';
														else
															$etat = '<i class="fa fa-smile-o font-green-jungle font-lg"></i>';
													} 
													
													$nbr_total += $nbr_mois;
													$ca_total += $ca_mois;
													$objectif_nbr_total += $objectif_homme_nbr[$mois_deb];
													$objectif_ca_total += $objectif_homme[$mois_deb];
													
													$ca_mois = number_format($ca_mois,2,"."," ");
													echo '</tr>
															<td>' . $abscisse_mois . '</td>
															<td>' . $ca_mois . ' €</td>
															<td>' . number_format($objectif_homme[$mois_deb],2,"."," ") . ' €</td>
															<td class="text-center">' . $etat . '</td>
														</tr>';
												}
												echo '</tr>
															<td><strong>Total</strong></td>
															<td><strong>' . number_format($ca_total,2,"."," ") . ' €</strong></td>
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
											<span class="caption-subject font-dark bold uppercase">Stats 1e & 2e RDV sur l'année Homme</span>
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
											<?
												$mois_deb = 8;
												$mois_encours = Date("n");
												if ($mois_encours<9) 
													$annee_deb = Date("Y")-1;
												else
													$annee_deb = Date("Y");
												
												$total_1 = 0;
												$total_2 = 0;
												for ($i=0;$i<12;$i++) {
													$mois_deb++;
													if ($mois_deb==13) {
														$mois_deb=1;
														$annee_deb = $annee_deb+1;
													}
													$abscisse_mois = $mois_deb . "/" . $annee_deb;
													
													$date_debut = $annee_deb . "-" . $mois_deb . "-01 00:00:00";
													$date_fin = $annee_deb . "-" . $mois_deb . "-" . $mois_jour[$mois_deb] . " 23:59:59";
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "' and r.type_num=1 and client_genre=1";
													$co = mysql_query($sql);
													$nbr_premier = mysql_num_rows($co);
													
													$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "' and r.type_num=6 and client_genre=1";
													$co = mysql_query($sql);
													$nbr_deuxieme = mysql_num_rows($co);
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
						</div>
						<? } ?>
                    <!-- END PAGE BASE CONTENT -->
                </div>
				<? $link_script = '<script src="/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>'; ?>
				<?
					$param = "";
					// ON recherche les events pour remplir le calendrier perso
					$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num and user_num='" . $u->mNum . "' and c.theme_num=4 and calendrier_datedeb>='" . Date("Y-m-d 00:00:00") . "' and calendrier_datedeb<='" . Date("Y-m-d 23:59:59") . "' order by calendrier_datedeb DESC";
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
						$link = "";
						if ($rcc["client_num"]!=0) {
							$genre = 0;
							$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
							$cl = mysql_query($sql);
							if ($rcl=mysql_fetch_array($cl)) {
								$link = '/clients/client.php?client_num=' . crypte($rcc["client_num"]);
								$genre = $rcl["client_genre"];
							}
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num='" . $rcc["rdv_num"] . "'";
							$rr = mysql_query($sql);
							if ($rrr=mysql_fetch_array($rr)) {
								if ($genre==0)
									$couleur = $rrr["type_couleur"];
								else 
									$couleur = $rrr["type_couleur_homme"];	
							}
						}
						
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
					$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num and showroom_num='" . $u->mShowroom . "' and calendrier_datedeb>='" . Date("Y-m-d 00:00:00") . "' and calendrier_datedeb<='" . Date("Y-m-d 23:59:59") . "' and c.theme_num!=4 order by calendrier_datedeb DESC";
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
						$link = "";
						if ($rcc["client_num"]!=0) {
							$genre = 0;
							$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
							$cl = mysql_query($sql);
							if ($rcl=mysql_fetch_array($cl)) {
								$link = '/clients/client.php?client_num=' . crypte($rcc["client_num"]);
								$genre = $rcl["client_genre"];
							}
							$sql = "select * from rendez_vous r, rdv_types t where r.type_num=t.type_num and rdv_num='" . $rcc["rdv_num"] . "'";
							$rr = mysql_query($sql);
							if ($rrr=mysql_fetch_array($rr)) {
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
						
						if ($rcc["calendrier_desc"]!="")
							$titre_rdv .= " / " . $calendrier_desc;
					
						$param .= '{
								title: "' . $titre_rdv . '",
								start: new Date(' . $annee_deb . ', ' . $mois_deb . ', ' . $jour_deb . ' , ' . $heure_deb . ', ' . $minute_deb . '),
								end: new Date(' . $annee_fin . ', ' . $mois_fin . ', ' . $jour_fin . ' , ' . $heure_fin . ', ' . $minute_fin . '),
								backgroundColor: "' . $couleur . '",';
						if ($link!="")
							$param .= ' url:"' . $link . '",';
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
							left: "title",
							center: "",
							right: "today"
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
							defaultView: "agendaDay",
							slotMinutes: 15,
							editable: 0,
							droppable: 0,
							scrollTime : \'09:00:00\',
							drop: function(e, t) {
								var a = $(this).data("eventObject"),
									n = $.extend({}, a);
								n.start = e, n.allDay = t, n.className = $(this).attr("data-class"), $("#calendar").fullCalendar("renderEvent", n, !0), $("#drop-remove").is(":checked") && $(this).remove()
							},
							events: [' . $param . ']
						})
					}
				}
			}
		}();
		jQuery(document).ready(function() {
			AppCalendar.init()
		});
		
		</script>';
		$link_script .= '<script src="/assets/global/plugins/fullcalendar/lang/fr.js" type="text/javascript"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">';
				
		// On calcul l'année en cours
		$mois_deb = 8;
		$mois_encours = Date("n");
		if ($mois_encours<9) 
			$annee_deb = Date("Y")-1;
		else
			$annee_deb = Date("Y");
		
		$link_script .= '<script language="Javascript">
                AmCharts.makeChart("dashboard_amchart_3", {
                    type:"serial", addClassNames:!0, theme:"light", path:"../assets/global/plugins/amcharts/ammap/images/", autoMargins:!1, marginLeft:55, marginRight:8, marginTop:10, marginBottom:26, balloon: {
                        adjustBorderColor: !1, horizontalPadding: 10, verticalPadding: 8, color: "#ffffff"
                    }
                    , dataProvider:[';
					
		for ($i=0;$i<12;$i++) {
			$mois_deb++;
			if ($mois_deb==13) {
				$mois_deb=1;
				$annee_deb = $annee_deb+1;
			}
			$abscisse_mois = $mois_deb . "/" . $annee_deb;
			
			$date_debut = $annee_deb . "-" . $mois_deb . "-01 00:00:00";
			$date_fin = $annee_deb . "-" . $mois_deb . "-" . $mois_jour[$mois_deb] . " 23:59:59";
			
			$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $u->mShowroom . "'";
			//echo $sql;
			$ca_mois = 0;
			$nbr_mois = 0;
			$co = mysql_query($sql);
			while ($rco=mysql_fetch_array($co)) {
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
			$ca_mois = number_format($ca_mois,2,".","");
			$link_script .= '{mois:"' . $abscisse_mois . '",income:' . $ca_mois . ',expenses:' . $objectif[$mois_deb] . '}';
			$link_script_nbr .= '{mois:"' . $abscisse_mois . '",income:' . $nbr_mois . ',expenses:' . $objectif_nbr[$mois_deb] . '}';
			if ($i<11) {
				$link_script .= ',';
				$link_script_nbr .= ',';
			}
		}			
					
             $link_script .= '], valueAxes:[ {
                        axisAlpha: 0, position: "left"
                    }
                    ], startDuration:1, graphs:[ {
                        alphaField: "alpha", balloonText: "<span style=\'font-size:12px;\'>[[title]] [[category]]:<br><span style=\'font-size:20px;\'>[[value]]</span> [[additional]]</span>", fillAlphas: 1, title: "CA", type: "column", valueField: "income", dashLengthField: "dashLengthColumn"
                    }
                    , {
                        id: "graph2", balloonText: "<span style=\'font-size:12px;\'>[[title]] [[category]]:<br><span style=\'font-size:20px;\'>[[value]]</span> [[additional]]</span>", bullet: "round", lineThickness: 3, bulletSize: 7, bulletBorderAlpha: 1, bulletColor: "#FFFFFF", useLineColorForBulletBorder: !0, bulletBorderThickness: 3, fillAlphas: 0, lineAlpha: 1, title: "Objectif", valueField: "expenses"
                    }
                    ], categoryField:"mois", categoryAxis: {
                        gridPosition: "start", axisAlpha: 0, tickLength: 0
                    }
                    , "export": {
                        enabled: !0
                    }
                }
                )';
		$link_script .= '
			AmCharts.makeChart("dashboard_amchart_2", {
                    type:"serial", addClassNames:!0, theme:"light", path:"../assets/global/plugins/amcharts/ammap/images/", autoMargins:!1, marginLeft:55, marginRight:8, marginTop:10, marginBottom:26, balloon: {
                        adjustBorderColor: !1, horizontalPadding: 10, verticalPadding: 8, color: "#ffffff"
                    }
                    , dataProvider:[';
					
		$link_script .= $link_script_nbr;
					
             $link_script .= '], valueAxes:[ {
                        axisAlpha: 0, position: "left"
                    }
                    ], startDuration:1, graphs:[ {
                        alphaField: "alpha", balloonText: "<span style=\'font-size:12px;\'>[[title]] [[category]]:<br><span style=\'font-size:20px;\'>[[value]]</span> [[additional]]</span>", fillAlphas: 1, title: "Nbr", type: "column", valueField: "income", dashLengthField: "dashLengthColumn"
                    }
                    , {
                        id: "graph2", balloonText: "<span style=\'font-size:12px;\'>[[title]] [[category]]:<br><span style=\'font-size:20px;\'>[[value]]</span> [[additional]]</span>", bullet: "round", lineThickness: 3, bulletSize: 7, bulletBorderAlpha: 1, bulletColor: "#FFFFFF", useLineColorForBulletBorder: !0, bulletBorderThickness: 3, fillAlphas: 0, lineAlpha: 1, title: "Objectif", valueField: "expenses"
                    }
                    ], categoryField:"mois", categoryAxis: {
                        gridPosition: "start", axisAlpha: 0, tickLength: 0
                    }
                    , "export": {
                        enabled: !0
                    }
                }
                )';
        $link_script .= '</script>';
		?>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>