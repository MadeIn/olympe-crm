<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Extraction email - Olympe Mariage";
$desc_page = "Extraction email - Olympe Mariage";
  
  $mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
  $mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  
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
                            <li class="active">Extraction Addresses</li>
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
											<span class="caption-subject bold uppercase"> Recherche addresses</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<?= form_action_same() ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Date debut</th>
												<th>Date fin</th>
												<th>Etat</th>
												<th>Showroom</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<input type="date" name="date_debut" value="<?= $date_debut ?>">
												</td>
												<td>
													<input type="date" name="date_fin" value="<?= $date_fin ?>">
												</td>
												<td>
													<select name="etat" class="form-control input-medium">
														<option value="0"<?php if ($etat==0) echo " SELECTED"; ?>>Tous les clients</option>
														<option value="3"<?php if ($etat==3) echo " SELECTED"; ?>>Les clientes qui ont commandés des robes</option>
														<option value="1"<?php if ($etat==1) echo " SELECTED"; ?>>Les clients qui n'ont pas commandés</option>
														<option value="2"<?php if ($etat==2) echo " SELECTED"; ?>>Les clients qui ont commandés</option>
													</select>
												</td>
												<td>
													<select name="showroom" class="form-control input-medium">
														<option value="0">Tous</option>
													<?php														$sql = "select * from showrooms order by showroom_nom ASC";
														$tt = $base->query($sql);
														foreach ($tt as $rtt) {
															echo '<option value="' . $rtt["showroom_num"] . '"';
															if ($rtt["showroom_num"]==$showroom) echo " SELECTED";
															echo '>' . $rtt["showroom_nom"] . '</option>';
														}
													?>
													</select>
												</td>
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
						<table class="table">
						<?php							$sql = "select * from clients where client_num=client_num";
							if ($date_debut!="")
								$sql .= " and client_datecreation>='" . $date_debut . " 00:00:00'";
							if ($date_fin!="")
								$sql .= " and client_datecreation<='" . $date_fin . " 23:59:59'";
							if ($showroom!=0)
								$sql .= " and showroom_num='" . $showroom . "'";
							$cc = $base->query($sql);
							$nbr_email = 0;
							foreach ($cc as $rcc) {
								$test=1;
								if ($etat>0) { 
									// On test si la cliente a déjà commandé
									if ($etat!=3) {
										$sql = "select * from commandes where client_num='" . $rcc["client_num"] . "' and commande_num!=0";
										$tt = $base->query($sql);
										$nbr_commande = count($tt);
										if ($etat==1) {
											if ($nbr_commande>0)
												$test=0;
										} else if ($etat==2) {
											if ($nbr_commande==0)
												$test=0;
										}
									} else {
										$sql = "select * from commandes c, commandes_produits cp, md_produits p where c.id=cp.id and cp.produit_num=p.produit_num and categorie_num=11 and client_num='" . $rcc["client_num"] . "' and commande_num!=0";
										$tt = $base->query($sql);
										$nbr_commande = count($tt);
										if ($nbr_commande==0)
											$test=0;
									}
								}
								
								if ($test==1) {
									echo '<tr>
											<td>' . $rcc["client_nom"] . ' ' . $rcc["client_prenom"] . '</td>
											<td>' . $rcc["client_adr1"];
									if ($rcc["client_adr2"]!="") echo $rcc["client_adr2"];
									echo '	</td>	
											<td>' . $rcc["client_cp"] . '</td>
											<td>' . $rcc["client_ville"] . '</td>
											<td>' . $rcc["client_tel"] . '</td>
											<td>' . $rcc["client_mail"] . '</td>
										</tr>';	
									$nbr_email++;
								}
							}
						?>
						</table>
						<hr>
						<p>Nombre d'adresse : <strong><?= $nbr_email ?></strong></p>
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