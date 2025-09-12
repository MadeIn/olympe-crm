<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Commandes Fournisseurs - Olympe Mariage";
$desc_page = "Commandes Fournisseurs - Olympe Mariage";
  
  $mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
  $mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  
  if (!isset($showroom)) {
		if ($u->mShowroom==0)
			$showroom=1;
		else
			$showroom = $u->mShowroom;
	}
  
  if (!isset($mois))
	$mois = 0;

  if (!isset($categorie))
	$categorie = -1;

 if (!isset($produitauto)) {
	$produitauto = "";
	$produit_num = 0;
 } else {
	 $tabproduit = recupValeurEntreBalise($produitauto,"[","]");
	 $produit_num = $tabproduit[0];
 }
 
 if (!isset($marques))
	$marques = -1;

  if (!isset($annee))
	$annee = 0;

  if (($mois!=0) && ($annee!=0)) {
	  $date_deb = $annee . "-" . $mois . "-1";
	  $date_fin = $annee . "-" . $mois . "-" . $mois_jour[intval($mois)];
  } else if (($mois==0) && ($annee>0)) {
	  $date_deb = $annee . "-01-01";
	  $date_fin = $annee . "-12-31";
  } else {
	 // On calcul l'année en cours
	$mois_deb = 8;
	$mois_encours = Date("n");
	if ($mois_encours<9) 
		$annee_deb = Date("Y")-1;
	else
		$annee_deb = Date("Y");
	
	$annee_fin = $annee_deb+1;
	
	$date_deb = $annee_deb . "-09-01";
	$date_fin = $annee_fin . "-08-31";
  }
  
  $date_deb .= " 00:00:00";
  $date_fin .= " 23:59:59";
?>

<? include( $chemin . "/mod/head.php"); ?>
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
                            <li class="active">Commandes Fournisseurs</li>
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
											<span class="caption-subject bold uppercase"> Recherche</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<? echo $PHP_SELF ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Produits</th>
												<th>Catégories</th>
												<th>Marques</th>
												<th>Date</th>
												<? if ($u->mGroupe==0) { ?>
													<th>Showroom</th>
												<? } ?>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input id="produitauto" name="produitauto"  class="form-control" value="<? echo $produitauto ?>"></td>
												<td>
													<select name="categorie" class="form-control">
														<option value="-1">------------</option>
														<?
															$sql = "select * from categories";
															$cc = mysql_query($sql);
															while ($rcc=mysql_fetch_array($cc)) {
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
														<option value="-1">------------</option>
														<?
															$sql = "select * from marques";
															$cc = mysql_query($sql);
															while ($rcc=mysql_fetch_array($cc)) {
																echo '<option value="' . $rcc["marque_num"] . '"';
																if ($rcc["marque_num"]==$marques)
																	echo " SELECTED";
																echo ">" . $rcc["marque_nom"] . "</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select name="mois" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<option value="0">--</option>
														<? 
														for ($i=1;$i<13;$i++) {
															echo "<option value=\"" . sprintf($i,"%02d") . "\"";
															if (sprintf($i,"%02d")==$mois)
																echo " SELECTED";
															echo ">" . $mois_nom[$i] . "</option>\n";
														}
														?>		
													</select>
													<select name="annee" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<option value="0">----</option>
														<? 
														for ($i=Date("Y");$i>2015;$i--) {
															echo "<option value=\"" .$i . "\"";
															if ($i==$annee)
																echo " SELECTED";
															echo ">" . $i . "</option>\n";
														}
														?>		
													</select>
												</td>
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
												<? } ?>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									</form>
								</div>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-black-tie"></i>Commandes Forunisseurs </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<th>Date</th>
													<th>Fournisseur</th>
													<th>Catégorie</th>
													<th>Produit</th>
													<th>Montant TTC</th>
													<th>Paiement</th>
													<th>Reste à Payer</th>
													<th>Client</th>
													<th>Commande</th>
													<th>Commande Date</th>
												</tr>
											</thead>
											<tbody>
											<?
											  $nbr = 0;
											  $sql = "select * from commandes c, commandes_fournisseurs f, md_produits p, marques m, categories ca, clients cl where c.id=f.id and f.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=ca.categorie_num and c.client_num=cl.client_num and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=-1)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=-1)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produitauto!="")
												  $sql .= " and produit_nom like '%" . $produitauto . "%'";
											  $sql .= " and commande_fournisseur_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " ORDER BY commande_fournisseur_date DESC ";
											  $re = mysql_query($sql);
											  $montant_total_ttc = 0;
											  $paiement_total = 0;
											  $reste_total = 0;
											  while ($row=mysql_fetch_array($re)) {
												$nbr++;
												$paiement = 0;
												$reste = 0;
												 // On regarde les paiements
												 $sql = "select * from commandes_fournisseurs_paiements where id='" . $row["id"] . "' and produit_num='" . $row["produit_num"] . "'";
												 $pa = mysql_query($sql);
												 if ($rpa = mysql_fetch_array($pa)) {
													 $paiement = $rpa["paiement1"] + $rpa["paiement2"] + $rpa["paiement3"];
												 }
												 $reste = $row["commande_montant"] - $paiement;
												 $montant_total_ttc += $row["commande_montant"];
												 $paiement_total += $paiement;
												 $reste_total += $reste;
											?>
												<tr>
													<td><? echo format_date($row["commande_fournisseur_date"],11,1) ?></td>
													<td><? echo $row["marque_nom"] ?></td>
													<td><? echo $row["categorie_nom"] ?></td>
													<td><? echo $row["produit_nom"] ?></td>
													<td><? echo number_format($row["commande_montant"],2,'.',' ') ?></td>
													<td><? echo number_format($paiement,2,'.',' ') ?></td>
													<td><? echo number_format($reste,2,'.',' ') ?></td>
													<td><a href="/clients/client.php?client_num=<? echo crypte($row["client_num"]) ?>&tab=tab_1_6"><? echo $row["client_nom"] . " " . $row["client_prenom"] ?></a></td>
													<td><? echo $row["commande_num"] ?></td>
													<td><? echo format_date($row["commande_date"],11,1) ?></td>
												</tr>
											<? } ?>	
											</tbody>
											<tr>
												<td><b>Total</b></td>
												<td><? echo $nbr ?></td>
												<td colspan="2"></td>
												<td><? echo number_format($montant_total_ttc,2,'.',' ') ?> €</td>
												<td><? echo number_format($paiement_total,2,'.',' ') ?> €</td>
												<td><? echo number_format($reste_total,2,'.',' ') ?> €</td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>