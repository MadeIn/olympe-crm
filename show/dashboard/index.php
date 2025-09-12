<? include( $_SERVER['DOCUMENT_ROOT'] . "/show/inc/param.inc"); 
$titre_page = "Statistiques - Olympe Mariage";
$desc_page = "Statistiques - Olympe Mariage";
  
  $mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
  $mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  if (($mois!=0) && ($annee!=0)) {
	  $date_deb = $annee . "-" . $mois . "-1";
	  $date_fin = $annee . "-" . $mois . "-" . $mois_jour[intval($mois)];
  } else if (($mois==0) && ($annee>0)) {
	  $date_deb = $annee . "-01-01";
	  $date_fin = $annee . "-12-31";
  } else {
	  $date_deb = "2017-01-01";
	  $date_fin = Date("Y") . "-12-31";
  }
  
  if (!isset($type))
	  $type=1;
  
  if (!isset($genre))
	  $genre=0;
  
  if ($genre==0)
	  $categorie_select = "11,25,27";
  else
	  $categorie_select = "29";
  
  $date_deb .= " 00:00:00";
  $date_fin .= " 23:59:59";
?>

<? include( $chemin . "/show/mod/head.php"); ?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <? include( $chemin . "/show/mod/top.php"); ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>Olympe Mariage</h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="/show/index.php">Accueil</a>
                            </li>
                            <li class="active">Tableau de bord</li>
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
											<span class="caption-subject bold uppercase"> Tableau de bord par date de mariage</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<? echo $PHP_SELF ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<tr>
												<td>
													<select name="user" class="form-control inline input-xsmall">
														<option value="0">----</option>
														<? 
															$sql =" select * from users where showroom_num='" . $u->mShowroom . "'";
															$cc = mysql_query($sql);
															while ($rcc=mysql_fetch_array($cc)) {
																echo '<option value="' . $rcc["user_num"] . '"';
																if ($user==$rcc["user_num"])
																	echo " SELECTED";
																echo '>' . $rcc["user_prenom"] . '</option>';
															}
														?>		
													</select>
													<select name="genre"class="form-control inline input-large">
														<option value="0"<? if ($genre==0) echo " SELECTED"; ?>>Femme</option>
														<option value="1"<? if ($genre==1) echo " SELECTED"; ?>>Homme</option>
													</select>
													<select name="type" class="form-control inline input-large">
														<option value="1"<? if ($type==1) echo " SELECTED"; ?>>Commande</option>
														<option value="2"<? if ($type==2) echo " SELECTED"; ?>>Devis</option>
													</select>
													<select name="mois" class="form-control inline input-large">
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
													<select name="annee" class="form-control inline input-xsmall">
														<option value="0">----</option>
														<? 
														for ($i=Date("Y")+2;$i>2015;$i--) {
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
												<? } else { ?>
												<input type="hidden" name="showroom" value="<? echo $u->mShowroom ?>">
												<? } ?>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									</form>
								</div>
							</div>
						</div>
                    	<? if (isset($recherche)) { ?>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							
								<div class="portlet-body">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th><strong>Date Mariage</strong></th>
												<th><strong>Cliente</strong></th>
												<th><strong>Creatrice</strong></th>
												<th><strong>Modèle</strong></th>
												<th><strong>Accessoires</strong></th>
												<th><strong>Livraison</strong></th>
												<th><strong>Robe reçue</strong></th>
												<th><strong>RDV Retouche</strong></th>
												<th><strong>RDV Remise</strong></th>
												<th><strong>Remarques</strong></th>
											</tr>
										</thead>
										<tbody>
										<?
										
										  $nbr = 0;
										  $sql = "select * from clients c, commandes co where c.client_num=co.client_num";
										  if ($type==1)
											 $sql .= "  and commande_num>0";
										  else 
											  $sql .= "  and devis_num>0 and commande_num=0";
										  $sql .= " and client_date_mariage>='" . $date_deb . "' and client_date_mariage<='" . $date_fin . "' and c.showroom_num='" . $showroom . "'";
										  if ($user!=0)
											  $sql .= " and c.user_num='" . $user . "'";
										  $sql .= " ORDER BY client_date_mariage DESC ";
										  $re = mysql_query($sql);
										  $nbr_total = 0;
										  $montant = 0;
										  while ($row=mysql_fetch_array($re))
										  {
											 $nbr++;
											 if ($type==1)
												$sql = "select * from commandes c, commandes_produits cp, md_produits p, marques m where c.id=cp.id and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and categorie_num IN (" . $categorie_select . ") and commande_date>='" . $row["commande_date"] . "' and c.commande_num>0 and c.client_num='" . $row["client_num"] . "'";
											 else
												$sql = "select * from commandes c, commandes_produits cp, md_produits p, marques m where c.id=cp.id and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and categorie_num IN (" . $categorie_select . ") and devis_date>='" . $row["devis_date"] . "' and c.devis_num>0 and c.commande_num=0 and c.client_num='" . $row["client_num"] . "'";
											 $cc = mysql_query($sql);
											 $produits = array();
											 $accessoires = array();
											 $commande_robe = mysql_num_rows($cc);
											 if ($commande_robe>0) {
												 while ($rcc=mysql_fetch_array($cc)) {
													 $commande_num = $rcc["id"];
													 $createur = $rcc["marque_nom"];
													 array_push($produits,$rcc["produit_nom"]);
												 }
												 
												 // On test si on a commande au fournisseur
												 $sql = "select * from commandes_fournisseurs where id='" . $row["id"] . "'";
												 $cf = mysql_query($sql);
												 $nbr_cde_fournisseur = mysql_num_rows($cf);
												 if ($nbr_cde_fournisseur>0)
													 $cde_fournisseur = "X";
												 else
													 $cde_fournisseur = "";
												 
												 $montant_ht = montantCommandeHT($commande_num);
												 $montant_ttc = montantCommandeTTC($commande_num);
												 
												 $sql = "select * from commandes c, commandes_produits cp, md_produits p, marques m where c.id=cp.id and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and categorie_num NOT IN (" . $categorie_select . ") and commande_date>='" . $row["commande_date"] . "' and commande_num>0 and c.client_num='" . $row["client_num"] . "'";
												 $cc = mysql_query($sql);
												 while ($rcc=mysql_fetch_array($cc)) {
													 array_push($accessoires,$rcc["produit_nom"]);
												 }
												 
												 $date_livraison = "";
												 $date_reception = "";
												 $date_retouche = "";
												 $date_remise = "";
												 
												 $sql = "select * from rendez_vous where client_num='" . $row["client_num"] . "' and type_num=2";
												 $rr = mysql_query($sql);
												 if ($rrv=mysql_fetch_array($rr)) {
													 $date_livraison = format_date($rrv["rdv_date"],11,1);
												 }
												 $sql = "select * from rendez_vous where client_num='" . $row["client_num"] . "' and type_num=3";
												 $rr = mysql_query($sql);
												 if ($rrv=mysql_fetch_array($rr)) {
													 $date_reception = format_date($rrv["rdv_date"],11,1);
												 }
												 $sql = "select * from rendez_vous where client_num='" . $row["client_num"] . "' and type_num=4";
												 $rr = mysql_query($sql);
												 if ($rrv=mysql_fetch_array($rr)) {
													 $date_retouche = format_date($rrv["rdv_date"],11,1);
												 }
												 $sql = "select * from rendez_vous where client_num='" . $row["client_num"] . "' and type_num=5";
												 $rr = mysql_query($sql);
												 if ($rrv=mysql_fetch_array($rr)) {
													 $date_remise = format_date($rrv["rdv_date"],11,1);
												 }
												 $acompte1 = "";
												 $acompte2 = "";
												 $acompte3 = "";
												 
												 $sql = "select * from commandes_paiements where id='" . $commande_num . "' and paiement_num='1'";
												 $pp = mysql_query($sql);
												 if ($rpp=mysql_fetch_array($pp)) {
													 $acompte1 = number_format($rpp["paiement_montant"],2,'.',' ') . "€";
												 }
												 $sql = "select * from commandes_paiements where id='" . $commande_num . "' and paiement_num='2'";
												 $pp = mysql_query($sql);
												 if ($rpp=mysql_fetch_array($pp)) {
													 $acompte2 = number_format($rpp["paiement_montant"],2,'.',' ') . "€";
												 }
												 $sql = "select * from commandes_paiements where id='" . $commande_num . "' and paiement_num='3'";
												 $pp = mysql_query($sql);
												 if ($rpp=mysql_fetch_array($pp)) {
													 $acompte3 = number_format($rpp["paiement_montant"],2,'.',' ') . "€";
												 }
												 
												 
											?>
												<tr>
													<td><small><? echo format_date($row["client_date_mariage"],11,1) ?></small></td>
													<td><small><? echo $row["client_prenom"] . " " . $row["client_nom"] ?></small></td>
													<td><small><? echo $createur ?></small></td>
													<td><small><? echo implode(",",$produits) ?></small></td>
													<td><small><? echo implode(",",$accessoires) ?></small></td>
													<td><small><? echo $date_livraison ?></small></td>
													<td><small><? echo $date_reception  ?></small></td>
													<td><small><? echo $date_retouche  ?></small></td>
													<td><small><? echo $date_remise ?></small></td>
													<td><small><? echo $row["client_remarque"] ?></small></td>
												</tr>
											<? 
											 }
											} ?>	
										</tbody>
									</table>
								</div>
						</div>
					<? } ?>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/show/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/show/mod/bottom.php"); ?>
    </body>

</html>