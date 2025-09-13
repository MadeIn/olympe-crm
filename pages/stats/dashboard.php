<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Dashboard - Olympe Mariage";
$desc_page = "Dashboard - Olympe Mariage";
  
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
                            <li class="active">Dashboard</li>
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
											<span class="caption-subject bold uppercase"> Dashboard</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<?= form_action_same() ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Date Debut</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
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
							<div class="col-lg-12 col-xs-12 col-sm-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption ">
											<span class="caption-subject font-dark bold uppercase">Dashboard  - Vente / RDV par année comptable</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<?php												$nbr_annee = Date("Y") - $annee_deb;
												$mois_encours = Date("n");
												if ($mois_encours>=9) 
													$nbr_annee++;
												
												for ($j=0;$j<$nbr_annee;$j++) {
													$annee_debut = $annee_deb + $j;
													echo '<thead>
														<tr class="success">
															<th>' . $annee_debut . '/' . ($annee_debut+1) . '</th>';
													$mois_debut = 8;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
														}
														echo '<th class="text-center">' . $mois_nom[$mois_debut] . '</th>';
													}
													echo '	<th class="text-center">Total<th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="bold">Nombre de Robe</td>';
													$mois_debut = 8;
													$annee_select = $annee_debut;
													$total = 0;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
														$nbr_mois = 0;
														$co = $base->query($sql);
														foreach ($co as $rco) {
															$nbr_mois += $rco["qte"];
														}
														echo '<td class="text-center">' . $nbr_mois . '</td>';
														$total += $nbr_mois;
													}
													echo '<td class="text-center">' . $total . '</td>';
													echo '</tr>';
													echo '<tr>
															<td class="bold">CA Robe</td>';
													$total = 0;
													$mois_debut = 8;
													$annee_select = $annee_debut;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (11,25,27)  and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
														$ca_mois = 0;
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
														$total += $ca_mois;
														$ca_mois = number_format($ca_mois,2,"."," ");
														echo '<td class="text-center">' . $ca_mois . '€</td>';
													}
													$total = number_format($total,2,"."," ");
													echo '<td class="text-center">' . $total . '€</td>';
													echo '</tr>';
													echo '<tr>
															<td class="bold">RDV Femme</td>';
													$total = 0;
													$mois_debut = 8;
													$annee_select = $annee_debut;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=1 and client_genre=0";
														$co = $base->query($sql);
														$nbr_premier = count($co);
														echo '<td class="text-center">' . $nbr_premier . '</td>';
														$total += $nbr_premier;
													}
													echo '<td class="text-center">' . $total . '</td>';
													echo '</tr>
													<tr>
															<td class="bold">Nombre de Costume</td>';
													$total = 0;
													$mois_debut = 8;
													$annee_select = $annee_debut;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (29) and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
														$nbr_mois = 0;
														$co = $base->query($sql);
														foreach ($co as $rco) {
															$nbr_mois += $rco["qte"];
														}
														echo '<td class="text-center">' . $nbr_mois . '</td>';
														$total += $nbr_mois;
													}
													echo '<td class="text-center">' . $total . '</td>';
													echo '</tr>';
													echo '<tr>
															<td class="bold">CA Homme</td>';
													$total = 0;
													$mois_debut = 8;
													$annee_select = $annee_debut;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num IN (29)  and commande_num!=0 and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
														$ca_mois = 0;
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
														$total += $ca_mois;
														$ca_mois = number_format($ca_mois,2,"."," ");
														echo '<td class="text-center">' . $ca_mois . '€</td>';
													}
													$total = number_format($total,2,"."," ");
													echo '<td class="text-center">' . $total . '€</td>';
													echo '</tr>';
													echo '<tr>
															<td class="bold">RDV Homme</td>';
													$total = 0;
													$mois_debut = 8;
													$annee_select = $annee_debut;
													for ($i=0;$i<12;$i++) {
														$mois_debut++;
														if ($mois_debut==13) {
															$mois_debut=1;
															$annee_select = $annee_select+1;
														}
														$date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
														$date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
														$sql = "select * from rendez_vous r, clients c where r.client_num=c.client_num and rdv_date>='" . $date_debut . "' and rdv_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and r.type_num=1 and client_genre=1";
														$co = $base->query($sql);
														$nbr_premier = count($co);
														echo '<td class="text-center">' . $nbr_premier . '</td>';
														$total += $nbr_panier;
													}
													echo '<td class="text-center">' . $total . '</td>';
													echo '</tr>';
													echo '</tbody>';
												}
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