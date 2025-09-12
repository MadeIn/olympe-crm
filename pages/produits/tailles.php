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
	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $pos . "' where " . $nom_champ . "_pos=" . $new_pos;
	$base->query($sql);

	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $new_pos . "' where " . $nom_champ . "_num=" . $val_num;
	$base->query($sql);
}

if (isset($modif))
{
	$sql_modif = "";
	$editor = str_replace("&lt;","<",$elm1);
	$editor = str_replace("&gt;",">",$elm1);

	$sql = "update " . $nom_table . " set " . $nom_champ . "_nom='" . $nom . "'";
	$sql .= $sql_modif;
	$sql .= " where " . $nom_champ . "_num=" . decrypte($val_num);
	$base->query($sql);
}

if (isset($ajout))
{
	$sql = "insert into " . $nom_table . " values (0,'" . $nom . "','" . $nbr_ligne . "')";
	$base->query($sql);
}

if (isset($suppr))
{
	$sql = "delete from " . $nom_table . " where " . $nom_champ . "_num=" . decrypte($suppr);
	$base->query($sql);
}

$sql = "select * from " . $nom_table . " order by " . $nom_champ . "_pos ASC";
$cdr = $base->query($sql);
$nbr_ligne = count($cdr);

?>

<?php include TEMPLATE_PATH . 'head.php'; ?>
<script language="Javascript">
function confirme() {
	if (confirm("<?php echo $alert ?>"))
		return true;
	else 
		return false;
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
                                <a href="/home.php">Accueil</a>
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
									<form name="ajouter" method="POST" action="<?php echo $PHP_SELF ?>" enctype="multipart/form-data">
									<?php if (!isset($modif_num)) { ?>		
									<input type="hidden" name="ajout" value="ok">
									<input type="hidden" name="nbr_ligne" value="<?php echo $nbr_ligne ?>">
									<input type="hidden" name="mnum" value="<?php echo $mnum ?>">
									<input type="hidden" name="mpere" value="<?php echo $mpere ?>">
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
									<input type="hidden" name="nbr_ligne" value="<?php echo $nbr_ligne ?>">
									<input type="hidden" name="val_num" value="<?php echo $modif_num ?>">
									<input type="hidden" name="mnum" value="<?php echo $mnum ?>">
									<input type="hidden" name="mpere" value="<?php echo $mpere ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										<tbody>
											<?php 
												$sql = "select * from " . $nom_table . " d where d." . $nom_champ . "_num=" . decrypte($modif_num);
												$cc = $base->query($sql);
												$i=0;
												if ($rcc=mysql_fetch_array($cc))
												{
													
											?>
											<tr>
												<td><label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-black-tie"></i>
													</span>
													<input type="text" name="nom" class="form-control" value="<?php echo $rcc[$nom_champ . "_nom"] ?>" required></div></td>
											</tr>
											<?
												}
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
											<?
												$i=0;
												foreach ($cdr as $row) {
											?>
											<tr>
												<td class="highlight">
													<div class="success"></div> <a href="<?php echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["taille_num"]) ?>"><?php echo $row[$nom_champ . "_nom"] ?></a></td>
												<td align="center">
												<?php 	
													if ($nbr_ligne>1)
													{
														$fleche_haut=0;
														if ($i>0)
														{
															echo "<a href=\"" . $PHP_SELF . "?val_num=" . $row[$nom_champ . "_num"] . "&pos=" . $row[$nom_champ . "_pos"] . "&decalle=m\"><i class=\"fa fa-chevron-up\"></i></a>";
															$fleche_haut=1;
														}
														if ($i<($nbr_ligne-1))
														{
															$margin = "";
															if ($fleche_haut==0)
																$margin = "margin-left:28px;";
															echo "<a href=\"" . $PHP_SELF . "?val_num=" . $row[$nom_champ . "_num"] . "&pos=" . $row[$nom_champ . "_pos"] . "&decalle=d\"><i class=\"fa fa-chevron-down\"></i></a>";
														}
													}
												?>
												</td>
												 <td>
													<a href="<?php echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["taille_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<?php echo $_SERVER["PHP_SELF"] . '?suppr=' . crypte($row["taille_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
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
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>