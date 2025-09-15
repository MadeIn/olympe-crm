<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des categories - Olympe Mariage";
$desc_page = "Gestion des categories - Olympe Mariage";

$nom_table = "categories";
$nom_champ = "categorie";
$alert = "Etes vous sûr de vouloir supprimer cet item ? Attention cet action peut avoir des conséquences sur les produits...";

if (isset($decalle))
{
	if ($decalle=="d")
		$new_pos = $pos+1;
	else
		$new_pos = $pos-1;

	// On decalle 
	$sql = "update categories set categorie_pos=" . safe_sql($pos) . " where categorie_pos=" . $new_pos;
	$base->query($sql);
	$sql = "update categories set categorie_pos=" . safe_sql($new_pos) . " where categorie_num=" . $val_num;
	$base->query($sql);
}

if (isset($modif))
{
	$sql_modif = "";
	$sql = "update categories set categorie_visible=" . safe_sql($etat) . ", categorie_nom=" . safe_sql($nom);
	$sql .= $sql_modif;
	$sql .= " where categorie_num=" . decrypte($val_num);
	$base->query($sql);

	// On insere les tailles
	$sql = "delete from categories_tailles where categorie_num=" . decrypte($val_num);
	$base->query($sql);
	
	if (isset($taille))
	{
		foreach ($taille as $choix)
		{
			$sql = "insert into categories_tailles values(" . decrypte($val_num) . "," . $choix . ")";
			$base->query($sql);
		}
	}
	else
	{
		$sql = "insert into categories_tailles values(" . decrypte($val_num) . ",0)";
		$base->query($sql);
	}
	$modif_num=$val_num;
}



if (isset($ajout))
{
	$sql = "insert into categories values (0," . safe_sql($nom) . "," . safe_sql($etat) . ")";
	$num = $base->insert($sql);

	// On insere les tailles
	if (isset($taille))
	{
		foreach ($taille as $choix)
		{
			$sql = "insert into categories_tailles values(" . $num . "," . $choix . ")";
			$base->query($sql);
		}
	}
	else
	{
		$sql = "insert into categories_tailles values(" . $num . ",0)";
		$base->query($sql);
	}
	
}

if (isset($suppr)) {
	$sql = "delete from categories where categorie_num=" . decrypte($suppr);
	$base->query($sql);
}

$sql = "select * from categories order by categorie_nom ASC";
$cdr = $base->query($sql);
$nbr_ligne = count($cdr);

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
                            <li class="active">Gestion des categories</li>
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
											<span class="caption-subject bold uppercase"> Ajouter une catégorie</span>
										<?php } else { ?>
											<span class="caption-subject bold uppercase"> Modifier une catégorie</span>
										<?php } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
									<?php if (!isset($modif_num)) { ?>		
									 <input type="hidden" name="ajout" value="ok">
								 	 <input type="hidden" name="nbr_ligne" value="<?= $nbr_ligne ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<tr height="35">
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bookmark-o"></i>
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
												<td><label>Taille(s)</label>
													<div class="input-group">
													<?php														
														$sql = "select * from tailles where taille_num>0 order by taille_pos ASC";
														$cc = $base->query($sql);
														foreach ($cc as $rcc)
															echo "<input type=\"checkbox\" value=\"" . $rcc["taille_num"] . "\" name=\"taille[]\">" . $rcc["taille_nom"] . "<br>";
													?>
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
											$sql = "select * from categories d where d.categorie_num=" . decrypte($modif_num);
											$rcc = $base->queryRow($sql);
											$i=0;
											if ($rcc)
											{
												$etat = $rcc[$nom_champ . "_visible"];
										?>
										<tr>
											<td><label>Nom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-bookmark-o"></i>
												</span>
												<input type="text" name="nom" class="form-control" value="<?= $rcc[$nom_champ . "_nom"] ?>" required></div></td>
										</tr>
										 <tr>
											<td><label>Etat</label>
											<div class="input-group">
												<input type="radio" name="etat" value="1" <?php if ($etat==1) echo " checked"; ?>> Visible &nbsp;
												<input type="radio" name="etat" value="0" <?php if ($etat==0) echo " checked"; ?>> Invisible
											</div>
											</td>
										</tr>
										<tr height="35">
											<td><label>Taille(s)</label>
												<div class="input-group">
												<?php													
													$sql = "select * from tailles where taille_num>=0 order by taille_pos ASC";
													$cc = $base->query($sql);
													foreach ($cc as $rcc) {
														$sql = "select * from categories_tailles where taille_num=" . $rcc["taille_num"] . " and categorie_num=" . decrypte($modif_num);
														$test = $base->query($sql);
														$nbr_res = count($test);
														echo ' <input type="checkbox" value="' . $rcc["taille_num"] . '" name="taille[]"';
														if ($nbr_res>0)
															echo " CHECKED";
														echo '>' . $rcc["taille_nom"] . '<br>';
													}
														
												?>
												</div>
											</td>
										</tr>
										<?php											}
										?>
										<tr>
											<td><input type="submit" value="Modifier" class="btn blue"></td>
										</tr>
									</tbody>
									</table>
									<?php } ?>
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
									<span class="caption-subject bold uppercase"> Liste des catégories</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
									<table class="table table-striped table-bordered table-advance table-hover">
										  <tbody>
											<?php												
												$i=0;
												foreach ($cdr as $row) {
											?>
											<tr>
												<td class="highlight">
													<div class="success"></div> <a href="<?= current_path() . '?modif_num=' . crypte($row["categorie_num"]) ?>"><?= $row[$nom_champ . "_nom"] ?></a></td>
												 <td>
													<a href="<?= current_path() . '?modif_num=' . crypte($row["categorie_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<?= current_path() . '?suppr=' . crypte($row["categorie_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
														<i class="fa fa-trash-o"></i> Suppr </a>-->
												</td>
											</tr>
											<?php												
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
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>