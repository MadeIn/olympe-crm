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
                                <a href="/home.php">Accueil</a>
                            </li>
                            <li class="active">Statistiques / catégorie</li>
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
											<span class="caption-subject bold uppercase"> Statistiques / catégorie</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<?php echo $PHP_SELF ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th colspan=3>Date Debut</th>
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
														<?
															$sql = "select * from showrooms order by showroom_nom ASC";
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
											<span class="caption-subject font-dark bold uppercase">Statistiques  - Nbr de Vente & CA par catégorie par année comptable</span>
										</div>
									</div>
									<div class="portlet-body">
									   <table class="table bordered">
											<?
												$nbr_annee = Date("Y") - $annee_deb;
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
													<tbody>';
                                                    // On va chercher toutes les categories
                                                    $sql = "select * from categories";
                                                    $cc = $base->query($sql);
                                                    foreach ($cc as $rcc) {
														echo '<tr>
															<td class="bold">' . $rcc["categorie_nom"] . '</td>';
                                                    
                                                        $mois_debut = 8;
                                                        $annee_select = $annee_debut;
                                                        $total_nbr_commande = 0;
                                                        $total_ca_commande = 0;
                                                        for ($i=0;$i<12;$i++) {
                                                            $mois_debut++;
                                                            if ($mois_debut==13) {
                                                                $mois_debut=1;
                                                                $annee_select = $annee_select+1;
                                                            }
                                                            $date_debut = $annee_select . "-" . $mois_debut . "-01 00:00:00";
                                                            $date_fin = $annee_select . "-" . $mois_debut . "-" . $mois_jour[$mois_debut] . " 23:59:59";
                                                            $sql = "SELECT sum(qte) val, sum(montant_ht*qte) total, categorie_nom FROM commandes c, commandes_produits cd, md_produits p, categories ca WHERE c.id=cd.id and cd.produit_num=p.produit_num and p.categorie_num=ca.categorie_num and c.commande_num!=0 and c.showroom_num='" . $showroom . "'";
                                                            $sql .= " and p.categorie_num=" . $rcc["categorie_num"];
                                                            $sql .= " and commande_date>='" . $date_debut . "' and commande_date<='" . $date_fin . "'";
                                                            $re = $base->query($sql);
                                                            $nbr_commande = 0;
                                                            $ca = 0;
                                                            if ($rre=mysql_fetch_array($re)) {
                                                                $nbr_commande = $rre["val"];
                                                                $ca = $rre["total"];
                                                                $total_nbr_commande += $nbr_commande;
                                                                $total_ca_commande += $ca;
                                                            }
                                                            echo '<td class="text-center">' . $nbr_commande . '<br>' . number_format($ca,2,"."," ") . '€</td>';
                                                            $total += $nbr_premier;
                                                        }
                                                        echo '<td class="text-center"><b>' . $total_nbr_commande . '<br>' . number_format($total_ca_commande,2,"."," ") . '€</b></td>';
                                                        echo '</tr>';
                                                    }
												}
											?>
											</tbody>
										</table
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