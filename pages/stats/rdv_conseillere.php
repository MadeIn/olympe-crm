<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
	$titre_page = "Taux de transformation - Olympe Mariage";
	$desc_page = "Taux de transformation - Olympe Mariage";

if ($u->mGroupe==0) {
	if (!isset($showroom_choix)) {
		$u->mShowroom = 1; // Si on est admin par defaut c'est Montpellier 
	} else 
		$u->mShowroom = $showroom_choix;

	$sql = "select * from showrooms where showroom_num='" . $u->mShowroom . "'";
	$rss = $base->queryRow($sql);
if ($rss) {
		$u->mShowroomInfo = $rss;
	}
}
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
	
	include TEMPLATE_PATH . 'head.php'; ?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <?php include TEMPLATE_PATH . 'top.php'; ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                   <form name="choix" action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
					<div class="breadcrumbs">
					<?php if ($u->mGroupe!=0) { ?>
						<div class="breadcrumbs">
							<h1>Olympe Mariage - <?= htmlentities(format_date(Date("Y-m-d"),13,1)) ?></h1>
							<ol class="breadcrumb">
								<li>
									<a href="#">Accueil</a>
								</li>
								<li class="active">Dashboard</li>
							</ol>
						</div>
					<?php } else { ?>
						<h1>Olympe Mariage <select name="showroom_choix" onChange="this.form.submit()" class="form-inline">
						<?php							$sql = "select * from showrooms order by showroom_num ASC";
							$sh = $base->query($sql);
							foreach ($sh as $rsh) {
								echo '<option value="' . $rsh["showroom_num"] . '"';
								if ($rsh["showroom_num"]==$u->mShowroom)
									echo " SELECTED";
								echo '>' . $rsh["showroom_ville"] . '</option>';
							}
						?>
						</select></h1>
						<ol class="breadcrumb">
							<li>
								<a href="#">Accueil</a>
							</li>
							<li class="active">Transformation vendeuse</li>
						</ol>
					<?php } ?>
					</div>
					</form>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-black-tie"></i>Taux de transformation par vendeuse</div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<th>Vendeuse</th>
													<th>Nbr de Rendez-vous</th>
													<th>Nbr de commande</th>
													<th>Taux de transformation</th>
												</tr>
											</thead>
											<tbody>
											<?php												$nbr_total_rdv = 0;
												$nbr_total_commande = 0;
												
												$sql = "select * from users where showroom_num='" . $u->mShowroom . "' and user_num not in (3,5,14) order by user_nom ASC, user_prenom ASC";
												$cc = $base->query($sql);
												foreach ($cc as $rcc) {
													$sql = "select count(rdv_num) val from rendez_vous r, clients c where c.client_num=r.client_num and rdv_date>='" . $date_debut_annee . "' and rdv_date<='" . Date("Y-m-d H:i:s") . "' and r.type_num=1 and c.showroom_num='" . $u->mShowroom . "' and client_genre=0 and c.user_num='" . $rcc["user_num"] . "'";	
													$rrr = $base->queryRow($sql);
													$nbr_rdv = 0;
													$transformation = 0;
													$nbr_commande = 0;
													if ($rrr) {
														$nbr_rdv = $rrr["val"];																				
														
														// Commande réalisé par la conseillere
														$sql = "select * from commandes c, commandes_produits cp, md_produits p, clients cl where c.id=cp.id and cp.produit_num=p.produit_num and c.client_num=cl.client_num and categorie_num IN (11,25,27) and commande_num!=0 and commande_date>='" . $date_debut_annee . "' and commande_date<='" . $date_fin_annee . "' and c.showroom_num='" . $u->mShowroom . "' and cl.user_num='" . $rcc["user_num"] . "'";
														$nbr_annne_robe = 0;
														$co = $base->query($sql);
														$commande_encours = 0;
														foreach ($co as $rco) {
															if ($commande_encours!=$rco["id"]) {
																$nbr_commande += $rco["qte"];
																$commande_encours = $rco["id"];
															}
														}
														if ($nbr_rdv>0) 
															$transformation = ($nbr_commande / $nbr_rdv)*100;
														else
															$transformation = 0;
														$transformation = number_format($transformation,0);
														$nbr_total_rdv += $nbr_rdv;
														$nbr_total_rdv_avenir += $nbr_rdv_avenir;
														$nbr_total_commande += $nbr_commande;
													}
											?>
												<tr>
													<td><?= $rcc["user_nom"] . " " . $rcc["user_prenom"] ?></td>
													<td><?= $nbr_rdv ?></td>
													<td><?= $nbr_commande ?></td>
													<td><?= $transformation ?>%</td>
												</tr>
											<?php } 
												if ($nbr_total_rdv>0) 
													$total_transformation = ($nbr_total_commande / $nbr_total_rdv)*100;
												else
													$total_transformation = 0;
												$total_transformation = number_format($total_transformation,0);
											?>	
											<tr>
												<td><strong>Total</strong></td>
												<td><strong><?= $nbr_total_rdv ?></strong></td>
												<td><strong><?= $nbr_total_commande ?></strong></td>
												<td><strong><?= $total_transformation ?>%</strong></td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>