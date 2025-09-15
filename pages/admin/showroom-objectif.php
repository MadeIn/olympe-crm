<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des objectifs Showrooms - Olympe Mariage";
$desc_page = "Gestion des objectifs Showrooms - Olympe Mariage";

$date_mois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");

if (isset($objectif)) {
	// On efface avant de reinserer
	$sql = "delete from showrooms_objectifs where showroom_num='" . $showroom_choix . "' and mois='" . $mois . "' and genre_num='" . $genre . "' and annee='" . $annee . "'";
	$base->query($sql);
	
	$date = $annee . "-" . $mois . "-01";
	$sql = "insert into showrooms_objectifs values(" . safe_sql($showroom_choix) . "," . safe_sql($genre) . "," . safe_sql($date) . "," . safe_sql($mois) . "," . safe_sql($annee) . "," . safe_sql($ca) . "," . safe_sql($nbr) . ")";
	$base->query($sql);
}

?>

<?php include TEMPLATE_PATH . 'head.php'; ?>
<script language="Javascript">
async function confirme() {
    return await $ol.confirmDialog("Êtes-vous sûr de vouloir supprimer cet item ?");
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
                            <li class="active">Gestion des objectif des showrooms</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-settings font-red-sunglo"></i>
											<span class="caption-subject bold uppercase">Choisissez un showroom</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="<?= current_path() ?>" method="POST">
									<input type="hidden" name="genre" value="<?= $genre ?>">
									<div class="form-group">
										<label>Showroom</label>
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-industry"></i>
											</span>
											<select name="showroom_choix" class="form-control">
											<?php 
												$sql = "select * from showrooms";
												$cc = $base->query($sql);
												foreach ($cc as $rcc) {
													echo '<option value="' . $rcc["showroom_num"] . '"';
													if ($rcc["showroom_num"]==$showroom_choix)
														echo ' SELECTED';
													echo '>' . $rcc["showroom_nom"] . '</option>';
												}
											?>
											</select>
										</div>
									</div>
									<div class="form-actions">
										<button type="submit" class="btn blue">Sélectionner</button>
									</div>
									</form>
								</div>
							</div>
							<?php if (isset($showroom_choix)) { ?>
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-red-sunglo">
											<i class="icon-settings font-red-sunglo"></i>
												<span class="caption-subject bold uppercase">Ajouter un objectif</span>
										</div>
									</div>
									<div class="portlet-body form">
										<form role="form" action="<?= current_path() ?>" method="POST">
										<input type="hidden" name="showroom_choix" value="<?= $showroom_choix ?>">
										<input type="hidden" name="genre" value="<?= $genre ?>">
										<input type="hidden" name="objectif" value="ok">
										<div class="form-group">
											<label>Date</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-industry"></i>
												</span>
												<select name="mois" class="form-control">
													<option value="1">Janvier</option>
													<option value="2">Février</option>
													<option value="3">Mars</option>
													<option value="4">Avril</option>
													<option value="5">Mai</option>
													<option value="6">Juin</option>
													<option value="7">Juillet</option>
													<option value="8">Aout</option>
													<option value="9">Septembre</option>
													<option value="10">Octobre</option>
													<option value="11">Novembre</option>
													<option value="12">Decembre</option>
												</select>
												<select name="annee" class="form-control">
												<?php for ($i=Date("Y")+2;$i>2016;$i--) {
														echo '<option value="' . $i . '"';
														if ($i==$annee)
															echo ' SELECTED';
														echo '>' . $i . '</option>';
													}
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
												<label>CA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="ca" class="form-control" placeholder="Chiffre d'affaire"  value="" required> </div>
											</div>
											<div class="form-group">
												<label>Nbr</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="nbr" class="form-control" placeholder="Nbr de vente" value="" required> </div>
											</div>
										<div class="form-actions">
											<button type="submit" class="btn blue">Ajouter</button>
										</div>
										</form>
									</div>
								</div>
								<!-- END SAMPLE FORM PORTLET-->
							<?php } ?>
						</div>
						<?php if (isset($showroom_choix)) { ?>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-blue-sunglo">
											<i class="icon-settings font-blue-sunglo"></i>
											<span class="caption-subject bold uppercase"> Liste des objectifs</span>
										</div>
									</div>
									<div class="portlet-body">
										<div class="table-scrollable">
											<table class="table table-striped table-bordered table-advance table-hover">
												<thead>
													<tr>
														<th class="bold"><i class="fa fa-industry"></i> &nbsp;Mois  </th>
														<th class="text-center bold"> CA </th>
														<th class="text-center bold"> Nbr </th>
													</tr>
												</thead>
												<tbody>
												<?php 
													$sql = "select * from showrooms_objectifs where showroom_num='" . $showroom_choix . "' and genre_num='" . $genre . "' order by annee DESC, mois DESC";
													$cc = $base->query($sql);
													foreach ($cc as $rcc) {
														echo '<tr>
															<td class="highlight">
																<div class="success"></div> &nbsp;&nbsp;' . $date_mois[$rcc["mois"]] . ' ' . $rcc["annee"] . '
															</td>
															<td class="text-center">' . safe_number_format($rcc["ca"],2) . ' €</td>
															<td class="text-center">' . $rcc["nbr"] . '</td>
														</tr>';
														
													}
												?>
												</tbody>
											</table>
										</div>
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