<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$nom_table = "produits";
$nom_champ = "produit";
$alert = "Etes vous sûr de vouloir supprimer ce produit ?";

$base = $_SERVER['DOCUMENT_ROOT'];
$rep =  $base . "/photos/" . $nom_table . "/";

if (isset($decalle)) {
	if ($decalle=="d")
		$new_pos = $pos+1;
	else
		$new_pos = $pos-1;
		
	// On decalle 
	$sql = "update md_" . $nom_table . "_photos set photo_pos='" . $pos . "' where photo_pos=" . $new_pos . " and " . $nom_champ ."_num='" . $modif_num . "'";
	mysql_query($sql);
	
	$sql = "update md_" . $nom_table . "_photos set photo_pos='" . $new_pos . "' where photo_num=" . $photo_pos;
	mysql_query($sql);
}

if (isset($modif)) {
	$sql_modif = "";
	$desc = str_replace("&lt;","<",$desc);
	$desc = str_replace("&gt;",">",$desc);
	
	$sql = "update md_" . $nom_table . " set " . $nom_champ . "_etat='" . $etat . "'," . $nom_champ . "_ref='" . $ref . "'," . $nom_champ . "_nom='" . $nom . "',  " . $nom_champ . "_desc='" . $desc . "',categorie_num='" . $categorie . "', marque_num='" . $marque . "'";
	$sql .= " where " . $nom_champ . "_num=" . decrypte($val_num);
	mysql_query($sql);

	$img_nom = xtTraiter($nom);
		 
	if ($remise=="")
		$remise=0;
	
	$sql = "select * from md_produits where produit_num='" . decrypte($val_num) . "'";
	$pp = mysql_query($sql);
	if ($rpp = mysql_fetch_array($pp)) {
		$pp_num = $rpp["prix_num"];
		$pa_num = $rpp["prixachat_num"];
	}
	
	// On insere le prix
	if ($prix=="")
		$prix=0;
	
	if ($pp_num!=0) {
		// On efface le prix pour le remettre
		$sql = "delete from prix where prix_num='" . $pp_num . "'";
		mysql_query($sql);
	}
	$prix = str_replace(",",".",$prix);
	$sql = "insert into prix values(0,'','" . $prix . "','','','','" . Date("Y-m-d H:i:s") . "')";
	mysql_query($sql);
	
	$sql = "select max(prix_num) val from prix";
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$prix_num = $rt["val"];
	
		
	// On insere le prix d'achat
	if ($pa_num!=0)	{
		// On efface le prix pour le remettre
		$sql = "delete from prixachats where prixachat_num='" . $pa_num . "'";
		mysql_query($sql);
	}
	if ($prixachat=="")
		$prixachat=0;
	$prixachat = str_replace(",",".",$prixachat);
	$sql = "insert into prixachats values(0,'" . $prixachat . "','" . Date("Y-m-d H:i:s") . "')";
	mysql_query($sql);
	
	$sql = "select max(prixachat_num) val from prixachats";
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$prixachat_num = $rt["val"];
			
	$sql = "update md_produits set prix_num='" . $prix_num . "', prixachat_num='" . $prixachat_num . "', produit_poids='" . intval($poids) . "', tva_num='" . $tva . "', produit_montant_remise='" . $remise . "', produit_remise_type='" . $remise_type . "'  where produit_num='" . decrypte($val_num) . "'";
	mysql_query($sql);
	
	$modif_num = $val_num;
}

if (isset($ajout)) {
	$desc = str_replace("&lt;","<",$desc);
	$desc = str_replace("&gt;",">",$desc);
	
	$sql = "insert into md_" . $nom_table . " values (0,'" . $ref . "','" . $nom . "','" . $desc . "','','" . Date("Y-m-d H:i:s") . "','" . $categorie . "','" . $marque . "','" . $etat . "',0,0,0,'',0,0)";
	echo $sql;
	mysql_query($sql);
	// On recupere le num categ
	$sql = "select max(" . $nom_champ . "_num) val from md_" . $nom_table;
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$num = $rt["val"];
		
	$nom = xtTraiter($nom);

	if ($remise=="")
		$remise=0;
	
	// On insere le prix
	if ($prix=="")
		$prix=0;
		
	$prix = str_replace(",",".",$prix);
	$sql = "insert into prix values(0,'','" . $prix . "','','','','" . Date("Y-m-d H:i:s") . "')";
	mysql_query($sql);
	
	$sql = "select max(prix_num) val from prix";
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$prix_num = $rt["val"];
		
	// On insere le prix d'achat
	if ($prixachat=="")
		$prixachat=0;
	$prixachat = str_replace(",",".",$prixachat);
	$sql = "insert into prixachats values(0,'" . $prixachat . "','" . Date("Y-m-d H:i:s") . "')";
	mysql_query($sql);
	
	$sql = "select max(prixachat_num) val from prixachats";
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$prixachat_num = $rt["val"];
			
	$sql = "update md_produits set prix_num='" . $prix_num . "', prixachat_num='" . $prixachat_num . "', produit_poids='" . intval($poids) . "', tva_num='" . $tva_num . "', produit_montant_remise='" . $remise . "', produit_remise_type='" . $remise_type . "'  where produit_num='" . $num . "'";
	mysql_query($sql);
	
	// On ajoute les photos
	$nbr_upload = 3;
	for ($i=1;$i<=$nbr_upload;$i++) {
		$nom_image = "";
		$legende = "";
		$file_upload = "userfile_acc_" . $i;
		$nom_photo = $nom . "-" . $i;
		$leg = "leg_" . $i;
		$legende = $_POST[$leg];
		$nom_image = uploadPhotoPdt($_FILES[$file_upload],$nom_photo,$nom_table,"1200","800","400");
		if ($nom_image!="")	{
			$sql = "insert into md_" . $nom_table . "_photos values(0,'" . $num . "','" . $nom_image . "','" . $legende . "','" . $i . "')";
			mysql_query($sql);
		}
	}	
	$modif_num = crypte($num);
}

if (isset($add_photo)) {
	$nbr_upload = 0;
	// On recupere le nom
	$sql = "select * from md_" . $nom_table . " where " . $nom_champ . "_num='" . decrypte($modif_num) . "' LIMIT 0,1";
	$vv = DbSelect($sql);
	if ($vv["nbr"]==1)
		$img_nom = $vv["result"][0][$nom_champ . "_nom"];
	
	// On recupere la pos+1
	$sql = "select max(photo_pos) val from md_" . $nom_table . "_photos where " . $nom_champ . "_num='" . decrypte($modif_num) . "'";
	$ph = DbSelect($sql);
	if ($ph["nbr"]==1)
		$pos = $ph["result"][0]["val"] + 1;
	else
		$pos = 1;
			
	// On ajoute les photos
	$nbr_upload = 1;
	for ($i=1;$i<=$nbr_upload;$i++)	{
		$nom_image = "";
		$file_upload = "userfile_acc_" . $i;
		$nom_photo = $img_nom . "-" . $pos;
		$nom_image = uploadPhotoPdt($_FILES[$file_upload],$nom_photo,$nom_table,"1200","800","400");
		if ($nom_image!="") {
			$leg = "leg_" . $i;
			$legende = $_POST[$leg];
			$sql = "insert into md_" . $nom_table . "_photos values(0,'" . decrypte($modif_num) . "','" . $nom_image . "','" . $legende . "','" . $pos . "')";
			mysql_query($sql);
		}
		$pos++;
	}
}

if (isset($suppr)) {
	$sql = "delete from md_" . $nom_table . " where " . $nom_champ . "_num=" . decrypte($suppr);
	mysql_query($sql);
	
	$sql = "delete from md_stocks where " . $nom_champ . "_num=" . $suppr;
	mysql_query($sql);
	
	// On efface les photos
	$sql = "select * from md_" . $nom_table . "_photos where " . $nom_champ . "_num='" .decrypte($suppr) . "'";
	$con = DbSelect($sql);
	foreach ($con["result"] as $rcc){
		$chemin_photo = $rep . "/min/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
		$chemin_photo = $rep . "/norm/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
		$chemin_photo = $rep . "/zoom/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
	}
	$sql = "delete from md_" . $nom_table . "_photos where " . $nom_champ . "_num=" . decrypte($suppr);
	mysql_query($sql);
}

echo "[" . $modif_stock . "][" . $taille_num . "]";
if (isset($modif_stock)) {
	// On efface les anciens stocks pour les reinserer
	$sql = "delete from stocks where produit_num=" . decrypte($modif_num) . " and taille_num=" . $taille_num . " and showroom_num='" . $u->mShowroom . "'";
	echo $sql;
	mysql_query($sql);
	
	if (($st_virtuel!="") && ($st_reel!="")) {
		if (($st_virtuel>0) && ($st_reel>0))
			$date_reappro = "0000-00-00";
		$sql = "insert into stocks values('" . decrypte($modif_num) . "','" . $taille_num . "','" . $st_virtuel . "','" . $st_reel . "','10','" . $date_reappro . "','" . $u->mShowroom . "')";
		$sql_stock = $sql;
		mysql_query($sql);
	}
}

if (isset($suppr_photo)) {
	// On efface la photo
	$sql = "select * from md_" . $nom_table . "_photos where photo_num='" . decrypte($suppr_photo) . "'";
	$cc = DbSelect($sql);
	$chemin_photo = $rep . "/min/" . $cc["result"][0]["photo_chemin"];
	unlink($chemin_photo);
	$chemin_photo = $rep . "/norm/" . $cc["result"][0]["photo_chemin"];
	unlink($chemin_photo);
	$chemin_photo = $rep . "/zoom/" . $cc["result"][0]["photo_chemin"];
	unlink($chemin_photo);
	
	// On efface dans la base
	$sql = "delete from md_" . $nom_table . "_photos where photo_num='" . decrypte($suppr_photo) . "'";
	mysql_query($sql);
	
	// On remet les photos dans l'ordre
	$sql = "select * from md_" . $nom_table . "_photos where " . $nom_champ . "_num='" . decrypte($modif_num) . "' order by photo_pos ASC";
	$cc = DbSelect($sql);
	$pos=1;
	if ($cc["nbr"]>0) {
		foreach ($cc["result"] as $res) {
			$sql = "update md_" . $nom_table . "_photos set photo_pos='" . $pos . "' where photo_num='" . $res["photo_num"] . "'";
			mysql_query($sql);
			$pos++;
		}
	}
}

$poids = "";
$prix = "";
$prixachat = "";
$tva = "";
$remise = "";
$remise_type = 0;
$etat = 1;
$link_plugin = '<link href="/assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />';
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
							<? if (!isset($modif_num)) { ?>
								<li class="active">Ajouter un produit</li>
							<? } else { ?>
								<li class="active">Modifier un produit</li>
							<? } ?>
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
											<span class="caption-subject bold uppercase"> Ajouter un produit</span>
										<? } else { ?>
											<span class="caption-subject bold uppercase"> Modifier un produit</span>
										<? } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
									<? if (!isset($modif_num)) { ?>		
									 <input type="hidden" name="ajout" value="ok">
									 <input type="hidden" name="nbr_ligne" value="0">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<tr>
												<td><label>Ref</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
													</span>
													<input type="text" name="ref" class="form-control" required></div></td>
											</tr>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
													</span>
													<input type="text" name="nom" class="form-control" required></div></td>
											</tr>
											<tr>
												<td><label>Description</label>
												<div class="input-group">
													<textarea class="wysihtml5 form-control" rows="4" name="desc"></textarea></div></td>
											</tr>
											<tr>
												<td><label>Catégories</label>
													<div class="input-group">
													 <select name="categorie">
														<option value="0">-----------------</option>
													<?
														$sql = "select * from categories order by categorie_nom ASC";
														$cc = mysql_query($sql);
														while ($rcc=mysql_fetch_array($cc))	{
															echo "<option value=\"" . $rcc["categorie_num"] . "\">" . $rcc["categorie_nom"] . "</option>\n";
														}
													?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td><label>Marques</label>
													<div class="input-group">
													 <select name="marque">
														<option value="0">-----------------</option>
													<?
														$sql = "select * from marques order by marque_nom ASC";
														$cc = mysql_query($sql);
														while ($rcc=mysql_fetch_array($cc))	{
															echo "<option value=\"" . $rcc["marque_num"] . "\">" . $rcc["marque_nom"] . "</option>\n";
														}
													?>
													</select>
													</div>
												</td>
											</tr>
											<tr>
												<td><label>Poids (g)</label>
												<div class="input-group">
													<input type="text" name="poids" class="form-control" placeholder="Poids (g)">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix € (HT)</label>
												<div class="input-group">
													<input type="text" name="prix" class="form-control" placeholder="Prix de vente € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix d'achat € (HT)</label>
												<div class="input-group">
													<input type="text" name="prixachat" class="form-control" placeholder="Prix d'achat € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<select name="tva">
													<? 
														$sql = "select * from tva";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt))
														{
															echo '<option value="' . $rtt["tva_num"] . '"';
															if ($rtt["tva_num"]==$tva)
																echo " SELECTED";
															echo '>' . $rtt["tva_taux"] . '%</option>';
														}
													?>
													</select>
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Remise</label>
												<div class="input-group">
													<input type="text" name="remise" value="<? echo $remise ?>" class="form-control" placeholder="Remise en %"> 
													<select name="remise_type">
														<option value="0" SELECTED>--</option>
														<option value="1">%</option>
													</select>
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Etat</label>
													<div class="input-group">
														<input type="radio" name="etat" value="1" <? if ($etat==1) echo " checked"; ?>> Visible &nbsp;
														<input type="radio" name="etat" value="0" <? if ($etat==0) echo " checked"; ?>> Invisible
													</div>
												</td>
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
											 $sql = "select * from md_" . $nom_table . " d where d." . $nom_champ . "_num=" . decrypte($modif_num);
											$cc = mysql_query($sql);
											$i=0;
											if ($rcc=mysql_fetch_array($cc)) {
												$etat = $rcc[$nom_champ . "_etat"];
												$ref = $rcc["produit_ref"];
												$nom = $rcc["produit_nom"];
												$desc = $rcc["produit_desc"];
												$categorie_num = $rcc["categorie_num"];
												$marque_num = $rcc["marque_num"];
												
												$poids = $rcc[$nom_champ . "_poids"];
												$tva = $rcc["tva_num"];								
												$remise = $rcc["produit_montant_remise"];
												$remise_type = $rcc["produit_remise_type"];
												
												$sql = "select * from prix where prix_num='" . $rcc["prix_num"] . "'";
												$pp = mysql_query($sql);
												if ($rpp = mysql_fetch_array($pp))
													$prix = $rpp["prix_montant_ht"];
												else
													$prix = 0;
													
												$sql = "select * from prixachats where prixachat_num='" . $rcc["prixachat_num"] . "'";
												$pp = mysql_query($sql);
												if ($rpp = mysql_fetch_array($pp))
													$prixachat = $rpp["prixachat_montant"];
												else
													$prixachat = 0;								
										?>
											<tr>
												<td><label>Ref</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
													</span>
													<input type="text" name="ref" class="form-control" value="<? echo $ref ?>" required></div></td>
											</tr>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
													</span>
													<input type="text" name="nom" class="form-control" value="<? echo $nom ?>" required></div></td>
											</tr>
											<tr>
												<td><label>Description</label>
												<div class="input-group">
													<textarea class="wysihtml5 form-control" rows="4" name="desc"><? echo $desc ?></textarea>
												</div></td>
											</tr>
											<tr>
												<td><label>Catégories</label>
													<div class="input-group">
													<select name="categorie">
														<option value="0">-----------------</option>
													<?
														$sql = "select * from categories order by categorie_nom ASC";
														$cm = mysql_query($sql);
														while ($rcm=mysql_fetch_array($cm))	{
															echo "<option value=\"" . $rcm["categorie_num"] . "\"";
															if ($rcm["categorie_num"]==$categorie_num)
																echo " SELECTED";
															echo ">" . $rcm["categorie_nom"] . "</option>\n";
														}
													?>
													</select>
													</div>
												</td>
											</tr>
											<tr>
												<td><label>Marques</label>
													<div class="input-group">
													<select name="marque">
														<option value="0">-----------------</option>
													<?
														$sql = "select * from marques order by marque_nom ASC";
														$cm = mysql_query($sql);
														while ($rcm=mysql_fetch_array($cm))	{
															echo "<option value=\"" . $rcm["marque_num"] . "\"";
															if ($rcm["marque_num"]==$marque_num)
																echo " SELECTED";
															echo ">" . $rcm["marque_nom"] . "</option>\n";
														}
													?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td><label>Poids (g)</label>
												<div class="input-group">
													<input type="text" name="poids" class="form-control" value="<? echo $poids ?>" placeholder="Poids (g)">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix € (HT)</label>
												<div class="input-group">
													<input type="text" name="prix" class="form-control" value="<? echo $prix ?>" placeholder="Prix de vente € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix d'achat € (HT)</label>
												<div class="input-group">
													<input type="text" name="prixachat" class="form-control" value="<? echo $prixachat ?>"  placeholder="Prix d'achat € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<select name="tva">
													<? 
														$sql = "select * from tva";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt))	{
															echo '<option value="' . $rtt["tva_num"] . '"';
															if ($rtt["tva_num"]==$tva)
																echo " SELECTED";
															echo '>' . $rtt["tva_taux"] . '%</option>';
														}
													?>
													</select>
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Remise</label>
												<div class="input-group">
													<input type="text" name="remise" value="<? echo $remise ?>" class="m-wrap small">
													<select name="remise_type">
														<option value="0"<? if ($remise_type==0) echo " SELECTED"; ?>>--</option>
														<option value="1"<? if ($remise_type==1) echo " SELECTED"; ?>>%</option>
													</select>
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Etat</label>
													<div class="input-group">
														<input type="radio" name="etat" value="1" <? if ($etat==1) echo " checked"; ?>> Visible &nbsp;
														<input type="radio" name="etat" value="0" <? if ($etat==0) echo " checked"; ?>> Invisible
													</div>
												</td>
											</tr>
											<tr>
												<td><input type="submit" value="Modifier" class="btn blue"></td>
											</tr>
											</tbody>
											</table>
											</form>
										<?
											}
										} ?>
								</div>
							</div>
							<!-- END SAMPLE FORM PORTLET-->
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<? if (!isset($modif_num)) { ?>	
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-blue-sunglo">
										<i class="fa fa-picture-o"></i>
										<span class="caption-subject bold uppercase"> Photos du produit</span>
									</div>
								</div>
								<div class="portlet-body">
									<div class="control-group">
										<label class="control-label"><b>Photo 1 (Couverture) : </b></label>
										<div class="controls">
											<input name="userfile_acc_1" type="file" size=50 style="margin:5px 0;" /><br />
											<hr size="1" width="99%">
											<label class="control-label"><b>Photo 2 : </b></label>
											<input name="userfile_acc_2" type="file" size=50 style="margin:5px 0;" /><br />
											<hr size="1" width="99%">
											<label class="control-label"><b>Photo 3 : </b></label>
											<input name="userfile_acc_3" type="file" size=50 style="margin:5px 0;" /><br />
										</div>
									</div>
								</div>
								</form>
							</div>
							<? } else { ?>
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-blue-sunglo">
											<i class="fa fa-picture-o"></i>
											<span class="caption-subject bold uppercase">Ajouter une photo</span>
										</div>
									</div>
									<div class="portlet-body">
										<div class="control-group">
											<label class="control-label"><b>Photo : </b></label>
											<form name="ajouter_photo" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
											<input type="hidden" name="add_photo" value="ok">
											<input type="hidden" name="modif_num" value="<? echo $modif_num ?>">
											<input type="hidden" name="nbr_ligne" value="<? echo $nbr_ligne ?>">
											<div class="controls">
												<input name="userfile_acc_1" type="file" size=50 style="margin:5px 0;" /><br />
												<textarea id="leg_1" name="leg_1" style="height:50px;width:90%;"></textarea><br />
												<button type="submit" class="btn red">Envoyer</button>
											</div>
											</form>
										</div>
									</div>
								</div>
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-blue-sunglo">
											<i class="fa fa-file-photo-o"></i>
											<span class="caption-subject bold uppercase">Liste des photos</span>
										</div>
									</div>
									<div class="portlet-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Photo</th>
												<th>Source</th>
												<th> </th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
										<?
											$sql = "select * from md_" . $nom_table . "_photos p where " . $nom_champ . "_num='" . decrypte($modif_num) . "' order by photo_pos ASC";
											$res = DbSelect($sql);
											$nbr_ligne = $res["nbr"];
											$i=0;
											if ($nbr_ligne>0) 
											{
												foreach ($res["result"] as $rcc)
												{
													$couv = "";
													if ($i==0)
														$couv = " (c)";
											?>
												<tr>
													<td><? echo $rcc["photo_pos"] . $couv ?></td>
													<td class="span2"><img src="/photos/produits/norm/<? echo $rcc["photo_chemin"] ?>" height="30" /><br /><? echo $rcc["photo_legende"] ?></td>
													<td> 
													<? 	
														if ($nbr_ligne>1)
														{
															$fleche_haut=0;
															if ($i>0)
															{
																echo "<a href=\"" . $PHP_SELF . "?photo_pos=" . $rcc["photo_num"] . "&pos=" . $rcc["photo_pos"] . "&modif_num=" . $modif_num . "&decalle=m\"><i class=\"fa fa-chevron-up\"></i></a>";
																$fleche_haut=1;
															}
															if ($i<($nbr_ligne-1))
															{
																echo "<a href=\"" . $PHP_SELF . "?photo_pos=" . $rcc["photo_num"] . "&pos=" . $rcc["photo_pos"] . "&modif_num=" . $modif_num . "&decalle=d\"><i class=\"fa fa-chevron-down\"></i></a>";
															}
														}
													?>
													</td>
													<td><a href="<? echo $PHP_SELF ?>?suppr_photo=<? echo crypte($rcc["photo_num"]) ?>&modif_num=<? echo $modif_num ?>" onClick="return confirme();" class="btn red mini">Supprimer</a></td>
												</tr>
											<?
													$i++;
												}
											}
										?>
									
										</tbody>
									</table>
									</div>
								</div>
								<? if ($u->mGroupe==1) { // Si on est un showroom, on gere les stocks ?>
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-blue-sunglo">
											<i class="fa fa fa-tasks"></i>
											<span class="caption-subject bold uppercase">Gestion des stocks</span>
										</div>
									</div>
									<div class="portlet-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Taille</th>
												<th>St Virtuel</th>
												<th>St réel</th>
												<th>Date réappro</th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
										<?
											$sql = "select * from tailles t, categories_tailles c where t.taille_num=c.taille_num and c.categorie_num=" . $categorie_num;
											$ss = mysql_query($sql);
											while ($st = mysql_fetch_array($ss)) {
												$sql = "select * from stocks where taille_num=" . $st["taille_num"] . " and produit_num=" . decrypte($modif_num) . " and showroom_num='" . $u->mShowroom . "'";
												$cc = mysql_query($sql);
												if ($rcc=mysql_fetch_array($cc)) {
													$stock_virtuel = $rcc["stock_virtuel"];
													$stock_reel = $rcc["stock_reel"];
													$stock_limite = $rcc["stock_limite"];
													$reappro = $rcc["stock_reappro"];
												}
												else {
													$stock_virtuel = "";
													$stock_reel = "";
													$stock_limite = "";
													$reappro = "";
												}
											?>
											<form name="modification_stock_<? echo $st["taille_num"] ?>" action="<? echo $_SERVER["PHP_SELF"] ?>" method="POST">
											<tr>
												<input type="hidden" name="modif_num" value="<? echo $modif_num ?>">
												<input type="hidden" name="taille_num" value="<? echo $st["taille_num"] ?>">
												<input type="hidden" name="modif_stock" value="ok">
												<td align="right" id="a11gnoir"><? echo $st["taille_nom"] ?></td>
												<td align=center><input type=text name=st_virtuel value="<? echo $stock_virtuel ?>" class="form-control"></td>
												<td align=center><input type=text name=st_reel value="<? echo $stock_reel ?>" class="form-control"></td>
												<td align=center><input type=date name=date_reappro value="<? echo $reappro ?>"  class="form-control"></td>
												<td align="center"><input type="submit" name="stock" value="OK" class="btn black mini"></td>
											</tr>
											</form>
											<? } ?>
										</tbody>
									</table>
									</div>
								</div>
								<? } ?>
							<? } ?>
						</div>
                    </div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? 
				$script_supp = '       <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
				<script src="/assets/pages/scripts/form-validation.min.js" type="text/javascript"></script>';
				include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>