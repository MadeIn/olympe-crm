<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Gestion des marques - Olympe Mariage";
$desc_page = "Gestion des marques - Olympe Mariage";

$nom_table = "marques";
$nom_champ = "marque";
$alert = "Etes vous sûr de vouloir supprimer cet item ? Attention cet action peut avoir des conséquences sur les produits...";

if (isset($decalle))
{
	if ($decalle=="d")
		$new_pos = $pos+1;
	else
		$new_pos = $pos-1;

	// On decalle 
	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $pos . "' where " . $nom_champ . "_pos=" . $new_pos;
	mysql_query($sql);
	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $new_pos . "' where " . $nom_champ . "_num=" . $val_num;
	mysql_query($sql);
}

if (isset($modif))
{
	$sql_modif = "";
	$editor = str_replace("&lt;","<",$elm1);
	$editor = str_replace("&gt;",">",$elm1);
	
	$sql = "update " . $nom_table . " set " . $nom_champ . "_visible='" . $etat . "', " . $nom_champ . "_nom='" . $nom . "', " . $nom_champ . "_desc='" . $editor . "', " . $nom_champ . "_raison_social='" . $raison_social . "', " . $nom_champ . "_adr1='" . $adr1 . "', " . $nom_champ . "_adr2='" . $adr2 . "', " . $nom_champ . "_cp='" . $cp . "', " . $nom_champ . "_ville='" . $ville . "', " . $nom_champ . "_rcs='" . $rcs . "', " . $nom_champ . "_tva='" . $tva . "', " . $nom_champ . "_tel='" . $tel . "', " . $nom_champ . "_mail='" . $mail . "', " . $nom_champ . "_site='" . $site . "', " . $nom_champ . "_contact='" . $contact . "', " . $nom_champ . "_contact_mail='" . $contact_mail . "', " . $nom_champ . "_contact_tel='" . $contact_tel . "', " . $nom_champ . "_paiement='" . $paiement . "'";
	$sql .= $sql_modif;
	$sql .= " where " . $nom_champ . "_num=" . decrypte($val_num);
	mysql_query($sql);
}

if (isset($ajout))
{
	$editor = str_replace("&lt;","<",$elm1);
	$editor = str_replace("&gt;",">",$elm1);
	
	$sql = "insert into " . $nom_table . " values (0,'" . $nom . "','" . $editor . "','" . $raison_social . "','" . $adr1 . "','" . $adr2 . "','" . $cp . "','" . $ville . "','" . $rcs . "','" . $tva . "','" . $tel . "','" . $mail . "','" . $site . "','" . $contact . "','" . $contact_mail . "','" . $contact_tel . "','" . $paiement . "','" . $etat . "')";
	mysql_query($sql);
}

if (isset($suppr))
{
	$sql = "delete from " . $nom_table . " where " . $nom_champ . "_num=" . decrypte($suppr);
	mysql_query($sql);
}

	$sql = "select * from " . $nom_table . " order by " . $nom_champ . "_nom ASC";
	$cdr = mysql_query($sql);
	$nbr_ligne = mysql_num_rows($cdr);

?>

<? include( $chemin . "/mod/head.php"); ?>
<script language="Javascript">
function confirme() {
	if (confirm("<? echo $alert ?>"))
		return true;
	else 
		return false;
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
                            <li class="active">Gestion des marques</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-settings font-red-sunglo"></i>
										<? if (!isset($modif_num)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter une marque</span>
										<? } else { ?>
											<span class="caption-subject bold uppercase"> Modifier une marque</span>
										<? } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
									<? if (!isset($modif_num)) { ?>		
									 <input type="hidden" name="ajout" value="ok">
								 	 <input type="hidden" name="nbr_ligne" value="<? echo $nbr_ligne ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-fire"></i>
													</span>
													<input type="text" name="nom" class="form-control" required></div></td>
											</tr>
											 <tr>
												<td><label>Etat</label>
												<div class="input-group">
												
													<input type="radio" name="etat" value="1" checked> Visible &nbsp;
													<input type="radio" name="etat" value="0"> Invisible
												</div>
												</td>
											</tr>
											<tr>
												<td><hr></td>
											</tr>
											<tr>
												<td><label>Raison Social</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-industry"></i>
													</span>
													<input type="text" name="raison_social" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr1" class="form-control"></div></td>
											</tr>
											<tr>
												<td>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr2" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Code Postal</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="cp" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Ville</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="ville" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>RCS</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="rcs" class="form-control" ></div></td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="tva" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="mail" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Site Web</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-globe"></i>
													</span>
													<input type="text" name="site" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Contact Nom & Prenom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input type="text" name="contact" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Contact mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="contact_mail" class="form-control"></div></td>
											</tr>
											<tr>
												<td><label>Contact Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="contact_tel" class="form-control"></div></td>
											</tr>				
											<tr>
												<td><label>Methode de paiement (Ex : 60/40 ou 100)</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-euro"></i>
													</span>
													<input type="text" name="paiement" class="form-control"></div></td>
											</tr>															
											<tr>
												<td><input type="submit" value="Ajouter" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									<? } else { ?>
									<input type="hidden" name="modif" value="ok">
									<input type="hidden" name="nbr_ligne" value="<? echo $nbr_ligne ?>">
									<input type="hidden" name="val_num" value="<? echo $modif_num ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										 <tbody>
										<? 
											$sql = "select * from " . $nom_table . " d where d." . $nom_champ . "_num=" . decrypte($modif_num);
											$cc = mysql_query($sql);
											$i=0;
											if ($rcc=mysql_fetch_array($cc))
											{
												$etat = $rcc[$nom_champ . "_visible"];
										?>
										<tr>
											<td><label>Nom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-fire"></i>
												</span>
												<input type="text" name="nom" class="form-control" value="<? echo $rcc[$nom_champ . "_nom"] ?>" required></div></td>
										</tr>
										<tr>
												<td><hr></td>
											</tr>
											<tr>
												<td><label>Raison Social</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-industry"></i>
													</span>
													<input type="text" name="raison_social" class="form-control" value="<? echo $rcc[$nom_champ . "_raison_social"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr1" class="form-control" value="<? echo $rcc[$nom_champ . "_adr1"] ?>"></div></td>
											</tr>
											<tr>
												<td>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr2" class="form-control" value="<? echo $rcc[$nom_champ . "_adr2"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Code Postal</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="cp" class="form-control" value="<? echo $rcc[$nom_champ . "_cp"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Ville</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="ville" class="form-control" value="<? echo $rcc[$nom_champ . "_ville"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>RCS</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="rcs" class="form-control" value="<? echo $rcc[$nom_champ . "_rcs"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="tva" class="form-control" value="<? echo $rcc[$nom_champ . "_tva"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control" value="<? echo $rcc[$nom_champ . "_tel"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="mail" class="form-control" value="<? echo $rcc[$nom_champ . "_mail"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Site Web</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-globe"></i>
													</span>
													<input type="text" name="site" class="form-control" value="<? echo $rcc[$nom_champ . "_site"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact Nom & Prenom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input type="text" name="contact" class="form-control" value="<? echo $rcc[$nom_champ . "_contact"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="contact_mail" class="form-control" value="<? echo $rcc[$nom_champ . "_contact_mail"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="contact_tel" class="form-control" value="<? echo $rcc[$nom_champ . "_contact_tel"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Methode de paiement (Ex : 60/40 ou 100)</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-euro"></i>
													</span>
													<input type="text" name="paiement" class="form-control" value="<? echo $rcc[$nom_champ . "_paiement"] ?>"></div></td>
											</tr>
										 <tr>
											<td><label>Etat</label>
											<div class="input-group">
												<input type="radio" name="etat" value="1" <? if ($etat==1) echo " checked"; ?>> Visible &nbsp;
												<input type="radio" name="etat" value="0" <? if ($etat==0) echo " checked"; ?>> Invisible
											</div>
											</td>
										</tr>
										<?
											}
										?>
										<tr>
											<td><input type="submit" value="Modifier" class="btn blue"></td>
										</tr>
									</tbody>
									</table>
									<? } ?>
									</form>
								</div>
							</div>
							<!-- END SAMPLE FORM PORTLET-->
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="portlet light bordered">
							<div class="portlet-title">
								<div class="caption font-blue-sunglo">
									<i class="icon-settings font-blue-sunglo"></i>
									<span class="caption-subject bold uppercase"> Liste des marques</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
									<table class="table table-striped table-bordered table-advance table-hover">
										  <tbody>
											<?
												$i=0;
												while ($row=mysql_fetch_array($cdr)) {
											?>
											<tr>
												<td class="highlight">
													<div class="success"></div> <a href="<? echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["marque_num"]) ?>"><? echo $row[$nom_champ . "_nom"] ?></a></td>
												 <td>
													<a href="<? echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["marque_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<? echo $_SERVER["PHP_SELF"] . '?suppr=' . crypte($row["marque_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
														<i class="fa fa-trash-o"></i> Suppr </a>-->
												</td>
											</tr>
											<?
												$i++;
												}
											?>
										</tbody>
									</table>
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