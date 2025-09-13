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
  
	if (!isset($date_deb))
		$date_deb = Date("Y-m") . "-01";
		
	if (!isset($date_fin))
		$date_fin = Date("Y-m-d");
	
	if (!isset($categorie))
		$categorie = 0;

	if (!isset($produitauto)) {
		$produitauto = "";
		$produit_num = 0;
	} else {
		$tabproduit = recupValeurEntreBalise($produitauto,"[","]");
		$produit_num = $tabproduit[0];
	}
 
	if (!isset($marques))
		$marques = 0;

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
												<th>Produits</th>
												<th>Catégories</th>
												<th>Marques</th>
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
												<td><input id="produitauto" name="produitauto"  class="form-control" value="<?php echo $produitauto ?>"></td>
												<td>
													<select name="categorie" class="form-control">
														<option value="0">------------</option>
														<?php															$sql = "select * from categories";
															$cc = $base->query($sql);
															foreach ($cc as $rcc) {
																echo '<option value="' . $rcc["categorie_num"] . '"';
																if ($rcc["categorie_num"]==$categorie)
																	echo " SELECTED";
																echo ">" . $rcc["categorie_nom"] . "</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select name="marques" class="form-control">
														<option value="0">------------</option>
														<?php															$sql = "select * from marques";
															$cc = $base->query($sql);
															foreach ($cc as $rcc) {
																echo '<option value="' . $rcc["marque_num"] . '"';
																if ($rcc["marque_num"]==$marques)
																	echo " SELECTED";
																echo ">" . $rcc["marque_nom"] . "</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="date" name="date_deb" class="form-control" value="<?php echo $date_deb ?>">
												</td>
												<td>
													<input type="date" name="date_fin" class="form-control" value="<?php echo $date_fin ?>">
												</td>
												<?php if ($u->mGroupe==0) { ?>
													<td>
														<select name="showroom" class="form-control input-medium">
															<option value="-1">Tous</option>
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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php if ($produit_num==0) { ?>
								<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
									<div class="portlet box red">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-black-tie"></i>Statistiques par catégorie </div>
										</div>
										<div class="portlet-body">
											<table class="table table-striped table-bordered table-advance table-hover">
												<thead>
													<tr>
														<th></th>
														<th>Catégorie</th>
														<th>Nbr</th>
														<th>Montant HT</th>
													</tr>
												</thead>
												<tbody>
												<?php												
												  $nbr = 0;
												  $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, categorie_nom FROM commandes c, commandes_produits cd, md_produits p, categories ca WHERE c.id=cd.id and cd.produit_num=p.produit_num and p.categorie_num=ca.categorie_num and c.commande_num!=0";
												  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
												  if ($categorie!=0)
													  $sql .= " and p.categorie_num=" . $categorie;
												  if ($marques!=0)
													  $sql .= " and p.marque_num=" . $marques;
												  $sql .= " and commande_date>='" . $date_deb . " 00:00:00' and commande_date<='" . $date_fin . " 23:59:59'";
												  $sql .= " GROUP BY ca.categorie_num";
												  $sql .= " ORDER BY val DESC ";
												  $re = $base->query($sql);
												  $nbr_total = 0;
												  $montant = 0;
												  foreach ($re as $row)
												  {
													 $nbr++;
													 $nbr_total += $row["val"];
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
													 
													 $montant += $row["total"];
												?>
													<tr>
														<td><?php echo $nbr ?>.</td>
														<td><?php echo $row["categorie_nom"] ?></td>
														<td><?php echo $row["val"] ?></td>
														<td><?php echo number_format($row["total"],2,'.',' ') ?></td>
													</tr>
												<?php } ?>	
												<tr>
													<td colspan="2"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-newspaper-o"></i> Statistiques par marque </div>
										</div>
										<div class="portlet-body">
											<table class="table table-striped table-bordered table-advance table-hover">
												<thead>
													 <tr>
														<th></th>
														<th>Marque</th>
														<th>Nbr</th>
														<th>Montant HT</th>
													</tr>
												</thead>
												<tbody>
												<?php												  $nbr = 0;
												  $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, marque_nom FROM commandes c, commandes_produits cd, md_produits p, marques m WHERE c.id=cd.id and cd.produit_num=p.produit_num and p.marque_num=m.marque_num and commande_num!=0";
												  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
												  if ($categorie!=0)
													  $sql .= " and p.categorie_num=" . $categorie;
												  if ($marques!=0)
													  $sql .= " and p.marque_num=" . $marques;
												  $sql .= " and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
												  $sql .= " GROUP BY m.marque_num";
												  $sql .= " ORDER BY val DESC ";
												  $re = $base->query($sql);
												  $nbr_total = 0;
												  $montant = 0;
												  foreach ($re as $row)
												  {
													 $nbr++;
													 $nbr_total += $row["val"];
													 $montant += $row["total"];
												?>
													<tr>
														<td><?php echo $nbr ?>.</td>
														<td><?php echo $row["marque_nom"] ?></td>
														<td><?php echo $row["val"] ?></td>
														<td><?php echo number_format($row["total"],2,',',' ') ?></td>
													</tr>
												<?php } ?>	
												<tr>
													<td colspan="2"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php } ?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="portlet box green">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-indent"></i> Statistiques par produits / Tailles </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<th></th>
													<th>Produits</th>
													<th>Catégories</th>
													<th>Marques</th>
													<th>Tailles</th>
													<th>Nbr</th>
													<th>Montant HT</th>
												</tr>
											</thead>									 
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, produit_nom, taille_nom, categorie_nom, marque_nom FROM commandes c, commandes_produits cd, md_produits p, categories ca, marques m, tailles t WHERE c.id=cd.id and cd.produit_num=p.produit_num and p.categorie_num=ca.categorie_num and p.marque_num=m.marque_num and cd.taille_num=t.taille_num and commande_num!=0";
											  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=0)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=0)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produit_num!=0)
												  $sql .= " and cd.produit_num=" . $produit_num;
											  $sql .= " and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " GROUP BY cd.produit_num,cd.taille_num";
											  $sql .= " ORDER BY p.produit_nom ASC, val DESC ";
											  $re = $base->query($sql);
											    $nbr_total = 0;
												  $montant = 0;			  
											  foreach ($re as $row)
											  {
												 $nbr++;
												  $nbr_total += $row["val"];
												  $montant += $row["total"];
											?>
												<tr>
													<td><?php echo $nbr ?>.</td>
													<td><?php echo $row["produit_nom"] ?></td>
													<td><?php echo $row["categorie_nom"] ?></td>
													<td><?php echo $row["marque_nom"] ?></td>
													<td><?php echo $row["taille_nom"] ?></td>
													<td><?php echo $row["val"] ?></td>
													<td><?php echo number_format($row["total"],2,',',' ') ?></td>
												</tr>
											<?php } ?>	
											<tr>
													<td colspan="5"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="portlet box yellow">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-credit-card"></i> Statistiques par produits / Tailles / Commandes </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<th>Id</th>
													<th>Date</th>
													<th>Produits</th>
													<th>Catégories</th>
													<th>Marques</th>
													<th>Tailles</th>
													<th>Nbr</th>
													<th>Montant HT</th>
												</tr>
											</thead>
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "SELECT * FROM commandes c, commandes_produits cd, md_produits p, categories ca, marques m, tailles t WHERE c.id=cd.id and cd.produit_num=p.produit_num and p.categorie_num=ca.categorie_num and p.marque_num=m.marque_num and cd.taille_num=t.taille_num and commande_num!=0";
											  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=0)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=0)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produit_num!=0)
												  $sql .= " and cd.produit_num=" . $produit_num;
											  $sql .= " and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " ORDER BY p.produit_nom ASC";
											  $re = $base->query($sql);
											   $nbr_total = 0;
												  $montant = 0;			  
											  foreach ($re as $row)
											  {
												 $nbr++;
												 $nbr_total += $row["qte"];
												  $montant += ($row["qte"]*$row["montant_ht"]);
											?>
												<tr>
													<td><?php echo $row["commande_num"] ?></td>
													<td><?php echo format_date($row["commande_date"],11,1) ?></td>
													<td><?php echo $row["produit_nom"] ?></td>
													<td><?php echo $row["categorie_nom"] ?></td>
													<td><?php echo $row["marque_nom"] ?></td>
													<td><?php echo $row["taille_nom"] ?></td>
													<td><?php echo $row["qte"] ?></td>
													<td><?php echo number_format($row["qte"]*$row["montant_ht"],2,',',' ') ?></td>
												</tr>
											<?php } ?>	
											<tr>
													<td colspan="6"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="portlet box blue">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-recycle"></i> Statistiques par produits </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<tr>
													<th></th>
													<th>Produits</th>
													<th>Nbr</th>
													<th>Montant HT</th>
												</tr>
												</tr>
											</thead>
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, produit_nom FROM commandes c, commandes_produits cd, md_produits p WHERE c.id=cd.id and cd.produit_num=p.produit_num and commande_num!=0";
											  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=0)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=0)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produit_num!=0)
												  $sql .= " and cd.produit_num=" . $produit_num;
											  $sql .= " and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " GROUP BY p.produit_num";
											  $sql .= " ORDER BY val DESC ";
											  $re = $base->query($sql);
											  $nbr_total = 0;
												  $montant = 0;				  
											  foreach ($re as $row)
											  {
												 $nbr++;
												  $nbr_total += $row["val"];
												  $montant += $row["total"];
											?>
												<tr>
													<td><?php echo $nbr ?>.</td>
													<td><?php echo $row["produit_nom"] ?></td>
													<td><?php echo $row["val"] ?></td>
													<td><?php echo number_format($row["total"],2,',',' ') ?></td>
												</tr>
											<?php } ?>	
											<tr>
													<td colspan="2"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-navicon"></i> Statistiques par Tailles </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												 <tr>
													<th></th>
													<th>Tailles</th>
													<th>Nbr</th>
													<th>Montant HT</th>
												</tr>
											</thead>
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, taille_nom FROM commandes c, commandes_produits cd, md_produits p, tailles t WHERE c.id=cd.id and cd.produit_num=p.produit_num and cd.taille_num=t.taille_num and commande_num!=0";
											  if ($showroom!=-1)
													$sql .= " and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=0)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=0)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produit_num!=0)
												  $sql .= " and cd.produit_num=" . $produit_num;
											  $sql .= " and commande_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " GROUP BY cd.taille_num";
											  $sql .= " ORDER BY val DESC ";
											  $re = $base->query($sql);
												$nbr_total = 0;
												  $montant = 0;				  
											  foreach ($re as $row)
											  {
												 $nbr++;
												   $nbr_total += $row["val"];
												  $montant += $row["total"];
											?>
												<tr>
													<td><?php echo $nbr ?>.</td>
													<td><?php echo $row["taille_nom"] ?></td>
													<td><?php echo $row["val"] ?></td>
													<td><?php echo number_format($row["total"],2,',',' ') ?></td>
												</tr>
											<?php } ?>
											<tr>
													<td colspan="2"></td>
													<td><?php echo $nbr_total ?></td>
													<td><?php echo number_format($montant,2,'.',' ') ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-navicon"></i> Statistiques par Villes </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												 <tr>
													<th></th>
													<th>Villes</th>
													<th>Nbr</th>
												</tr>
											</thead>
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "select client_ville, count(client_num) val from clients where client_datecreation>='" . $date_deb . "' and client_datecreation<='" . $date_fin . "'";
											  if ($showroom!=-1)
												$sql .= " and showroom_num='" . $showroom . "'";
											  $sql .= " group by client_ville order by val DESC";
											  $re = $base->query($sql);
											  $nbr_total = 0;
											  foreach ($re as $row)
											  {
												 $nbr++;
											?>
												<tr>
													<td><?php echo $nbr ?>.</td>
													<td><?php echo $row["client_ville"] ?></td>
													<td><?php echo $row["val"] ?></td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
				<?php					$link_script .= '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
				<script>
				  $(function() {
					var availableTagsPdts = [';
				
					$sql = "select * from md_produits order by produit_nom ASC";
					$jj = $base->query($sql);
					$nbr = count($jj);
					$i=0;
					foreach ($jj as $rjj) {
						$produit_nom = trim($rjj["produit_nom"]);
						$link_script .= "\"" . $produit_nom . " [" . $rjj["produit_num"] . "]\"";
						$i++;
						if ($i<$nbr)
							$link_script .= ",";

					}
				
					$link_script .= '];
					
					$("#produitauto").autocomplete({
					  source: availableTagsPdts
					});
				  });
				  </script>';
				?>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>