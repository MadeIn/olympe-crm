<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
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
	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $pos . "' where " . $nom_champ . "_pos=" . $new_pos;
	mysql_query($sql);
	$sql = "update " . $nom_table . " set " . $nom_champ . "_pos='" . $new_pos . "' where " . $nom_champ . "_num=" . $val_num;
	mysql_query($sql);
}

if (isset($modif))
{
	$sql_modif = "";
	$sql = "update " . $nom_table . " set " . $nom_champ . "_visible='" . $etat . "', " . $nom_champ . "_nom='" . $nom . "'";
	$sql .= $sql_modif;
	$sql .= " where " . $nom_champ . "_num=" . decrypte($val_num);
	mysql_query($sql);

	// On insere les tailles
	$sql = "delete from categories_tailles where categorie_num=" . decrypte($val_num);
	mysql_query($sql);
	
	if (isset($taille))
	{
		foreach ($taille as $choix)
		{
			$sql = "insert into categories_tailles values(" . decrypte($val_num) . "," . $choix . ")";
			mysql_query($sql);
		}
	}
	else
	{
		$sql = "insert into categories_tailles values(" . decrypte($val_num) . ",0)";
		mysql_query($sql);
	}
	$modif_num=$val_num;
}



if (isset($ajout))
{
	$sql = "insert into " . $nom_table . " values (0,'" . $nom . "','" . $etat . "')";
	mysql_query($sql);

	// On recupere le num categ
	$sql = "select max(" . $nom_champ . "_num) val from " . $nom_table;
	$test = mysql_query($sql);
	if ($rt = mysql_fetch_array($test))
		$num = $rt["val"];

	// On insere les tailles
	if (isset($taille))
	{
		foreach ($taille as $choix)
		{
			$sql = "insert into categories_tailles values(" . $num . "," . $choix . ")";
			mysql_query($sql);
		}
	}
	else
	{
		$sql = "insert into categories_tailles values(" . $num . ",0)";
		mysql_query($sql);
	}
	
}

if (isset($suppr)) {
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
										<? if (!isset($modif_num)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter une catégorie</span>
										<? } else { ?>
											<span class="caption-subject bold uppercase"> Modifier une catégorie</span>
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
													<?
														$sql = "select * from tailles where taille_num>0 order by taille_pos ASC";
														$cc = mysql_query($sql);
														while ($rcc=mysql_fetch_array($cc))
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
													<i class="fa fa-bookmark-o"></i>
												</span>
												<input type="text" name="nom" class="form-control" value="<? echo $rcc[$nom_champ . "_nom"] ?>" required></div></td>
										</tr>
										 <tr>
											<td><label>Etat</label>
											<div class="input-group">
												<input type="radio" name="etat" value="1" <? if ($etat==1) echo " checked"; ?>> Visible &nbsp;
												<input type="radio" name="etat" value="0" <? if ($etat==0) echo " checked"; ?>> Invisible
											</div>
											</td>
										</tr>
										<tr height="35">
											<td><label>Taille(s)</label>
												<div class="input-group">
												<?
													$sql = "select * from tailles where taille_num>=0 order by taille_pos ASC";
													$cc = mysql_query($sql);
													while ($rcc=mysql_fetch_array($cc)) {
														$sql = "select * from categories_tailles where taille_num=" . $rcc["taille_num"] . " and categorie_num=" . decrypte($modif_num);
														$test = mysql_query($sql);
														$nbr_res = mysql_num_rows($test);
														echo " <input type=\"checkbox\" value=\"" . $rcc["taille_num"] . "\" name=\"taille[]\"";
														if ($nbr_res>0)
															echo " CHECKED";
														echo ">" . $rcc["taille_nom"] . "<br>";
													}
														
												?>
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
									<span class="caption-subject bold uppercase"> Liste des catégories</span>
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
													<div class="success"></div> <a href="<? echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["categorie_num"]) ?>"><? echo $row[$nom_champ . "_nom"] ?></a></td>
												 <td>
													<a href="<? echo $_SERVER["PHP_SELF"] . '?modif_num=' . crypte($row["categorie_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<? echo $_SERVER["PHP_SELF"] . '?suppr=' . crypte($row["categorie_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
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