<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$alert = "Etes vous sûr de vouloir supprimer ce produit ?";

$rep = $chemins['photos'] . "/produits/";

if (isset($decalle)) {
	if ($decalle=="d")
		$new_pos = $pos+1;
	else
		$new_pos = $pos-1;
		
	// On decalle 
	$sql = "update md_produits_photos set photo_pos=" . sql_safe($pos) . " where photo_pos=" . $new_pos . " and produit_num=" . sql_safe($modif_num);
	$base->query($sql);
	
	$sql = "update md_produits_photos set photo_pos=" . sql_safe($new_pos) . " where photo_num=" . $photo_pos;
	$base->query($sql);
}

if (isset($modif)) {
	$sql_modif = "";
	$desc = str_replace("&lt;","<",$desc);
	$desc = str_replace("&gt;",">",$desc);
	
	$sql = "update md_produits set produit_etat=" . sql_safe($etat) . ",produit_ref=" . sql_safe($ref) . ",produit_nom=" . sql_safe($nom) . ",  produit_desc=" . sql_safe($desc) . ",categorie_num=" . sql_safe($categorie) . ", marque_num=" . sql_safe($marque);
	$sql .= " where produit_num=" . decrypte($val_num);
	$base->query($sql);

	$img_nom = xtTraiter($nom);
		 
	if ($remise=="")
		$remise=0;
	
	$sql = "select * from md_produits where produit_num=" . decrypte($val_num);
	$rpp = $base->queryRow($sql);
	if ($rpp) {
		$pp_num = $rpp["prix_num"];
		$pa_num = $rpp["prixachat_num"];
	}
	
	// On insere le prix
	if ($prix=="")
		$prix=0;
	
	if ($pp_num!=0) {
		// On efface le prix pour le remettre
		$sql = "delete from prix where prix_num=" . sql_safe($pp_num);
		$base->query($sql);
	}
	
	$mht = norm_num_str($prix ?? ''); // '' accepté par NULLIF plus bas

	$sql = "INSERT INTO prix
        (prix_montant, prix_montant_ht, prix_montant_dollars, prix_montant_livres, prix_montant_livres_ht, prix_date)
        VALUES (0, COALESCE(NULLIF(:mht,''), 0), 0, 0, 0, NOW())";

	$prix_num = $base->insert($sql,['mht' => $mht]);
		
		
	// On insere le prix d'achat
	if ($pa_num!=0)	{
		// On efface le prix pour le remettre
		$sql = "delete from prixachats where prixachat_num=" . sql_safe($pa_num);
		$base->query($sql);
	}
	if ($prixachat=="")
		$prixachat=0;

	$paht = norm_num_str($prixachat ?? ''); // '' accepté par NULLIF plus bas

	$sql = "INSERT INTO prixachats
        (prixachat_montant, prixachat_date)
        VALUES (COALESCE(NULLIF(:paht,''), 0), NOW())";

	$prixachat_num = $base->insert($sql,['paht' => $paht]);

	$sql = "update md_produits set prix_num=" . sql_safe($prix_num) . ", prixachat_num=" . sql_safe($prixachat_num) . ", produit_poids=" . intval($poids) . ", tva_num=" . sql_safe($tva) . ", produit_montant_remise=" . sql_safe($remise) . ", produit_remise_type=" . sql_safe($remise_type) . "  where produit_num=" . decrypte($val_num);
	$base->query($sql);
	
	$modif_num = $val_num;
}

if (isset($ajout)) {
	$desc = str_replace("&lt;","<",$desc);
	$desc = str_replace("&gt;",">",$desc);
	
	$sql = "insert into md_produits values (0," . sql_safe($ref) . "," . sql_safe($nom) . "," . sql_safe($desc) . ",'','" . Date("Y-m-d H:i:s") . "'," . sql_safe($categorie) . "," . sql_safe($marque) . "," . sql_safe($etat) . ",0,0,0,'',0,0)";
	$num = $base->insert($sql);
	
		
	$nom = xtTraiter($nom);

	if ($remise=="")
		$remise=0;
	
	// On insere le prix
	if ($prix=="")
		$prix=0;
		
	$prix = str_replace(",",".",$prix);
	$sql = "insert into prix values(0,''," . sql_safe($prix) . ",'','','','" . Date("Y-m-d H:i:s") . "')";
	$prix_num = $base->insert($sql);
	
	// On insere le prix d'achat
	if ($prixachat=="")
		$prixachat=0;
	$prixachat = str_replace(",",".",$prixachat);
	$sql = "insert into prixachats values(0," . sql_safe($prixachat) . ",'" . Date("Y-m-d H:i:s") . ")";
	$prixachat_num = $base->insert($sql);
	
	$sql = "update md_produits set prix_num=" . sql_safe($prix_num) . ", prixachat_num=" . sql_safe($prixachat_num) . ", produit_poids=" . intval($poids) . ", tva_num=" . sql_safe($tva_num) . ", produit_montant_remise=" . sql_safe($remise) . ", produit_remise_type=" . sql_safe($remise_type) . "  where produit_num=" . sql_safe($num);
	$base->query($sql);
	
	// On ajoute les photos
	$nbr_upload = 3;
	for ($i=1;$i<=$nbr_upload;$i++) {
		$nom_image = "";
		$legende = "";
		$file_upload = "userfile_acc_" . $i;
		$nom_photo = $nom . "-" . $i;
		$leg = "leg_" . $i;
		$legende = $_POST[$leg];
		$nom_image = uploadPhotoPdt($_FILES[$file_upload],$nom_photo,'produits',"1200","800","400");
		if ($nom_image!="")	{
			$sql = "insert into md_produits_photos values(0," . sql_safe($num) . "," . sql_safe($nom_image) . "," . sql_safe($legende) . "," . sql_safe($i) . ")";
			$base->query($sql);
		}
	}	
	$modif_num = crypte($num);
}

if (isset($add_photo)) {
	$nbr_upload = 0;
	// On recupere le nom
	$sql = "select * from md_produits where produit_num=" . decrypte($modif_num) . " LIMIT 0,1";
	$vv = $base->queryRow($sql);
	if ($vv)
		$img_nom = $vv["produit_nom"];
	
	// On recupere la pos+1
	$sql = "select max(photo_pos) val from md_produits_photos where produit_num=" . decrypte($modif_num);
	$ph = $base->queryRow($sql);
	if ($ph)
		$pos = $ph["val"] + 1;
	else
		$pos = 1;
			
	// On ajoute les photos
	$nbr_upload = 1;
	for ($i=1;$i<=$nbr_upload;$i++)	{
		$nom_image = "";
		$file_upload = "userfile_acc_" . $i;
		$nom_photo = $img_nom . "-" . $pos;
		$nom_image = uploadPhotoPdt($_FILES[$file_upload],$nom_photo,'produits',"1200","800","400");
		if ($nom_image!="") {
			$leg = "leg_" . $i;
			$legende = $_POST[$leg];
			$sql = "insert into md_produits_photos values(0," . decrypte($modif_num) . "," . sql_safe($nom_image) . "," . sql_safe($legende) . "," . sql_safe($pos) . ")";
			$base->query($sql);
		}
		$pos++;
	}
}

if (isset($suppr)) {
	$sql = "delete from md_produits where produit_num=" . decrypte($suppr);
	$base->query($sql);
	
	$sql = "delete from md_stocks where produit_num=" . $suppr;
	$base->query($sql);
	
	// On efface les photos
	$sql = "select * from md_produits_photos where produit_num=" .decrypte($suppr);
	$con = $base->query($sql);
	foreach ($con as $rcc){
		$chemin_photo = $rep . "/min/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
		$chemin_photo = $rep . "/norm/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
		$chemin_photo = $rep . "/zoom/" . $rcc["photo_chemin"];
		unlink($chemin_photo);
	}
	$sql = "delete from md_produits_photos where produit_num=" . decrypte($suppr);
	$base->query($sql);
}

if (isset($modif_stock)) {
	// On efface les anciens stocks pour les reinserer
	$sql = "delete from stocks where produit_num=" . decrypte($modif_num) . " and taille_num=" . $taille_num . " and showroom_num=" . sql_safe($u->mShowroom);
	$base->query($sql);
	
	if (($st_virtuel!="") && ($st_reel!="")) {
		if (($st_virtuel>0) && ($st_reel>0))
			$date_reappro = "0000-00-00";
		$sql = "insert into stocks values(" . decrypte($modif_num) . "," . sql_safe($taille_num) . "," . sql_safe($st_virtuel) . "," . sql_safe($st_reel) . ",'10'," . sql_safe($date_reappro) . "," . sql_safe($u->mShowroom) . ")";
		$sql_stock = $sql;
		$base->query($sql);
	}
}

if (isset($suppr_photo)) {
	// On efface la photo
	$sql = "select * from md_produits_photos where photo_num=" . decrypte($suppr_photo);
	$cc = $base->queryRow($sql);
	$chemin_photo = $rep . "/min/" . $cc["photo_chemin"];
	unlink($chemin_photo);
	$chemin_photo = $rep . "/norm/" . $cc["photo_chemin"];
	unlink($chemin_photo);
	$chemin_photo = $rep . "/zoom/" . $cc["photo_chemin"];
	unlink($chemin_photo);
	
	// On efface dans la base
	$sql = "delete from md_produits_photos where photo_num=" . decrypte($suppr_photo);
	$base->query($sql);
	
	// On remet les photos dans l'ordre
	$sql = "select * from md_produits_photos where produit_num=" . decrypte($modif_num) . " order by photo_pos ASC";
	$cc = $base->query($sql);
	$pos=1;
	foreach ($cc as $res) {
		$sql = "update md_produits_photos set photo_pos=" . sql_safe($pos) . " where photo_num=" . sql_safe($res["photo_num"]);
		$base->query($sql);
		$pos++;
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

<?php include TEMPLATE_PATH . 'head.php'; ?>
<script language="Javascript">
async function confirme() {
    return await $ol.confirmDialog("<?= $alert ?>");
}
</script>
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
							<?php if (!isset($modif_num)) { ?>
								<li class="active">Ajouter un produit</li>
							<?php } else { ?>
								<li class="active">Modifier un produit</li>
							<?php } ?>
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
										<?php if (!isset($modif_num)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter un produit</span>
										<?php } else { ?>
											<span class="caption-subject bold uppercase"> Modifier un produit</span>
										<?php } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
									<?php if (!isset($modif_num)) { ?>		
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
													<?php														
														$sql = "select * from categories order by categorie_nom ASC";
														$cc = $base->query($sql);
														foreach ($cc as $rcc)	{
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
													<?php														
														$sql = "select * from marques order by marque_nom ASC";
														$cc = $base->query($sql);
														foreach ($cc as $rcc)	{
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
													<?php 
														$sql = "select * from tva";
														$tt = $base->query($sql);
														foreach ($tt as $rtt)
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
													<input type="text" name="remise" value="<?= $remise ?>" class="form-control" placeholder="Remise en %"> 
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
														<input type="radio" name="etat" value="1" <?php if ($etat==1) echo " checked"; ?>> Visible &nbsp;
														<input type="radio" name="etat" value="0" <?php if ($etat==0) echo " checked"; ?>> Invisible
													</div>
												</td>
											</tr>
											<tr>
												<td><input type="submit" value="Ajouter" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									<?php } else { ?>
									<input type="hidden" name="modif" value="ok">
									<input type="hidden" name="nbr_ligne" value="<?= $nbr_ligne ?>">
									<input type="hidden" name="val_num" value="<?= $modif_num ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										 <tbody>
										<?php 
											 $sql = "select * from md_produits d where d.produit_num=" . decrypte($modif_num);
											$rcc = $base->queryRow($sql);
											$i=0;
											if ($rcc) {
												$etat = $rcc["produit_etat"];
												$ref = $rcc["produit_ref"];
												$nom = $rcc["produit_nom"];
												$desc = $rcc["produit_desc"];
												$categorie_num = $rcc["categorie_num"];
												$marque_num = $rcc["marque_num"];
												
												$poids = $rcc["produit_poids"];
												$tva = $rcc["tva_num"];								
												$remise = $rcc["produit_montant_remise"];
												$remise_type = $rcc["produit_remise_type"];
												
												$sql = "select * from prix where prix_num=" . sql_safe($rcc["prix_num"]);
												$rpp = $base->queryRow($sql);
												if ($rpp)
													$prix = $rpp["prix_montant_ht"];
												else
													$prix = 0;
													
												$sql = "select * from prixachats where prixachat_num=" . sql_safe($rcc["prixachat_num"]);
												$rpp = $base->queryRow($sql);
												if ($rpp)
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
													<input type="text" name="ref" class="form-control" value="<?= $ref ?>" required></div></td>
											</tr>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
													</span>
													<input type="text" name="nom" class="form-control" value="<?= $nom ?>" required></div></td>
											</tr>
											<tr>
												<td><label>Description</label>
												<div class="input-group">
													<textarea class="wysihtml5 form-control" rows="4" name="desc"><?= $desc ?></textarea>
												</div></td>
											</tr>
											<tr>
												<td><label>Catégories</label>
													<div class="input-group">
													<select name="categorie">
														<option value="0">-----------------</option>
													<?php														
														$sql = "select * from categories order by categorie_nom ASC";
														$cm = $base->query($sql);
														foreach ($cm as $rcm)	{
															echo "<option value=\"" . $rcm["categorie_num"] . "\"";
															if ($rcm["categorie_num"]==($categorie_num ?? 0))
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
													<?php														
														$sql = "select * from marques order by marque_nom ASC";
														$cm = $base->query($sql);
														foreach ($cm as $rcm)	{
															echo "<option value=\"" . $rcm["marque_num"] . "\"";
															if ($rcm["marque_num"]==($marque_num ?? 0))
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
													<input type="text" name="poids" class="form-control" value="<?= ($poids ?? '') ?>" placeholder="Poids (g)">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix € (HT)</label>
												<div class="input-group">
													<input type="text" name="prix" class="form-control" value="<?= ($prix ?? '') ?>" placeholder="Prix de vente € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Prix d'achat € (HT)</label>
												<div class="input-group">
													<input type="text" name="prixachat" class="form-control" value="<?= ($prixachat ?? '') ?>"  placeholder="Prix d'achat € HT">
												</div>
												</td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<select name="tva">
													<?php 
														$sql = "select * from tva";
														$tt = $base->query($sql);
														foreach ($tt as $rtt)	{
															echo '<option value="' . $rtt["tva_num"] . '"';
															if ($rtt["tva_num"]==($tva ?? 0))
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
													<input type="text" name="remise" value="<?= ($remise ?? '') ?>" class="m-wrap small">
													<?php $remise_type = $remise_type ?? 0; ?>
													<select name="remise_type">
														<option value="0"<?php if ($remise_type==0) echo " SELECTED"; ?>>--</option>
														<option value="1"<?php if ($remise_type==1) echo " SELECTED"; ?>>%</option>
													</select>
												</div>
												</td>
											</tr>
											<tr>
												<td><label>Etat</label>
													<div class="input-group">
														<php $etat = $etat ?? 0; ?>
														<input type="radio" name="etat" value="1" <?php if ($etat==1) echo " checked"; ?>> Visible &nbsp;
														<input type="radio" name="etat" value="0" <?php if ($etat==0) echo " checked"; ?>> Invisible
													</div>
												</td>
											</tr>
											<tr>
												<td><input type="submit" value="Modifier" class="btn blue"></td>
											</tr>
											</tbody>
											</table>
											</form>
										<?php											}
										} ?>
								</div>
							</div>
							<!-- END SAMPLE FORM PORTLET-->
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<?php if (!isset($modif_num)) { ?>	
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
							<?php } else { ?>
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
											<form name="ajouter_photo" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
											<input type="hidden" name="add_photo" value="ok">
											<input type="hidden" name="modif_num" value="<?= $modif_num ?>">
											<input type="hidden" name="nbr_ligne" value="<?= $nbr_ligne ?>">
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
										<?php											
											$sql = "select * from md_produits_photos p where produit_num=" . decrypte($modif_num) . " order by photo_pos ASC";
											$res = $base->query($sql);
											$nbr_ligne = count($res);
											$i=0;
											if ($nbr_ligne>0) 
											{
												foreach ($res as $rcc)
												{
													$couv = "";
													if ($i==0)
														$couv = " (c)";
											?>
												<tr>
													<td><?= $rcc["photo_pos"] . $couv ?></td>
													<td class="span2"><img src="/photos/produits/norm/<?= $rcc["photo_chemin"] ?>" height="30" /><br /><?= $rcc["photo_legende"] ?></td>
													<td> 
													<?php 	
														if ($nbr_ligne>1)
														{
															$fleche_haut=0;
															if ($i>0)
															{
																echo '<a href="' . current_path() . '?photo_pos=' . $rcc["photo_num"] . '&pos=' . $rcc["photo_pos"] . '&modif_num=' . $modif_num . '&decalle=m"><i class="fa fa-chevron-up"></i></a>';
																$fleche_haut=1;
															}
															if ($i<($nbr_ligne-1))
															{
																echo '<a href="' . current_path() . '?photo_pos=' . $rcc["photo_num"] . '&pos=' . $rcc["photo_pos"] . '&modif_num=' . $modif_num . '&decalle=d"><i class="fa fa-chevron-down"></i></a>';
															}
														}
													?>
													</td>
													<td><a href="<?= current_path() ?>?suppr_photo=<?= crypte($rcc["photo_num"]) ?>&modif_num=<?= $modif_num ?>" onClick="return confirme();" class="btn red mini">Supprimer</a></td>
												</tr>
											<?php													
													$i++;
												}
											}
										?>
									
										</tbody>
									</table>
									</div>
								</div>
								<?php if ($u->mGroupe==1) { // Si on est un showroom, on gere les stocks ?>
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
										<?php											
											$sql = "select * from tailles t, categories_tailles c where t.taille_num=c.taille_num and c.categorie_num=" . $categorie_num;
											$ss = $base->query($sql);
											foreach ($ss as $st) {
												$sql = "select * from stocks where taille_num=" . $st["taille_num"] . " and produit_num=" . decrypte($modif_num) . " and showroom_num=" . sql_safe($u->mShowroom);
												$rcc = $base->queryRow($sql);
												if ($rcc) {
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
											<form name="modification_stock_<?= $st["taille_num"] ?>" action="<?= current_path() ?>" method="POST">
											<tr>
												<input type="hidden" name="modif_num" value="<?= $modif_num ?>">
												<input type="hidden" name="taille_num" value="<?= $st["taille_num"] ?>">
												<input type="hidden" name="modif_stock" value="ok">
												<td align="right" id="a11gnoir"><?= $st["taille_nom"] ?></td>
												<td align=center><input type=text name=st_virtuel value="<?= $stock_virtuel ?>" class="form-control"></td>
												<td align=center><input type=text name=st_reel value="<?= $stock_reel ?>" class="form-control"></td>
												<td align=center><input type=date name=date_reappro value="<?= $reappro ?>"  class="form-control"></td>
												<td align="center"><input type="submit" name="stock" value="OK" class="btn black mini"></td>
											</tr>
											</form>
											<?php } ?>
										</tbody>
									</table>
									</div>
								</div>
								<?php } ?>
							<?php } ?>
						</div>
                    </div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php 
				$script_supp = '<script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
				<script src="/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
				<script src="/assets/pages/scripts/form-validation.min.js" type="text/javascript"></script>';
				include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>