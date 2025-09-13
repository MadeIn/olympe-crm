<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des tailles - Olympe Mariage";
$desc_page = "Gestion des tailles - Olympe Mariage";

	$nom_table = "tailles";
	$nom_champ = "taille";
	$alert = "Etes vous sûr de vouloir supprimer cet item ? Attention cet action peut avoir des conséquences sur les produits...";

if (isset($decalle))
{
	if ($decalle=="d")
		$new_pos = $pos+1;
	else
		$new_pos = $pos-1;

	// On decalle 
	$sql = "update tailles set taille_pos='" . $pos . "' where taille_pos=" . $new_pos;
	$base->query($sql);

	$sql = "update tailles set taille_pos='" . $new_pos . "' where taille_num=" . $val_num;
	$base->query($sql);
}

if (isset($modif))
{
	$sql = "update tailles set taille_nom='" . $nom . "'";
	$sql .= " where taille_num=" . decrypte($val_num);
	$base->query($sql);
}

if (isset($ajout))
{
	$sql = "insert into tailles values (0,'" . $nom . "','" . $nbr_ligne . "')";
	$base->query($sql);
}

if (isset($suppr))
{
	$sql = "delete from tailles where taille_num=" . decrypte($suppr);
	$base->query($sql);
}

$sql = "select * from tailles order by taille_pos ASC";
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
                            <li class="active">Gestion des tailles</li>
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
											<span class="caption-subject bold uppercase"> Ajouter une taille</span>
										<?php } else { ?>
											<span class="caption-subject bold uppercase"> Modifier une taille</span>
										<?php } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
									<?php if (!isset($modif_num)) { ?>		
									<input type="hidden" name="ajout" value="ok">
									<input type="hidden" name="nbr_ligne" value="<?= $nbr_ligne ?>">
									<input type="hidden" name="mnum" value="<?= $mnum ?>">
									<input type="hidden" name="mpere" value="<?= $mpere ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-black-tie"></i>
													</span>
													<input type="text" name="nom" class="form-control" required></div></td>
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
									<input type="hidden" name="mnum" value="<?= $mnum ?>">
									<input type="hidden" name="mpere" value="<?= $mpere ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<?php 
												$sql = "select * from tailles d where d.taille_num=" . decrypte($modif_num);
												$rcc = $base->queryRow($sql);
												$i=0;
												if ($rcc)
												{
													
											?>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-black-tie"></i>
													</span>
													<input type="text" name="nom" class="form-control" value="<?= $rcc["taille_nom"] ?>" required></div></td>
											</tr>
											<?php												}
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
									<span class="caption-subject bold uppercase"> Liste des tailles</span>
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
													<div class="success"></div> <a href="<?= current_path() . '?modif_num=' . crypte($row["taille_num"]) ?>"><?= $row["taille_nom"] ?></a></td>
												<td align="center">
												<?php 	
													if ($nbr_ligne>1)
													{
														$fleche_haut=0;
														if ($i>0)
														{
															echo '<a href="' . current_path() . '?val_num=' . $row["taille_num"] . '&pos=' . $row["taille_pos"] . '&decalle=m"><i class="fa fa-chevron-up"></i></a>';
															$fleche_haut=1;
														}
														if ($i<($nbr_ligne-1))
														{
															$margin = "";
															if ($fleche_haut==0)
																$margin = "margin-left:28px;";
															echo '<a href="' . current_path() . '?val_num=' . $row["taille_num"] . '&pos=' . $row["taille_pos"] . '&decalle=d"><i class="fa fa-chevron-down"></i></a>';
														}
													}
												?>
												</td>
												 <td>
													<a href="<?= current_path() . '?modif_num=' . crypte($row["taille_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<?= current_path() . '?suppr=' . crypte($row["taille_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
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