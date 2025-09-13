<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
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
	$sql = "update marques set marque_pos='" . $pos . "' where marque_pos=" . $new_pos;
	$base->query($sql);
	$sql = "update marques set marque_pos='" . $new_pos . "' where marque_num=" . $val_num;
	$base->query($sql);
}

if (isset($modif))
{
	$sql = "update marques set marque_visible='" . $etat . "', marque_nom='" . $nom . "', marque_raison_social='" . $raison_social . "', marque_adr1='" . $adr1 . "', marque_adr2='" . $adr2 . "', marque_cp='" . $cp . "', marque_ville='" . $ville . "', marque_rcs='" . $rcs . "', marque_tva='" . $tva . "', marque_tel='" . $tel . "', marque_mail='" . $mail . "', marque_site='" . $site . "', marque_contact='" . $contact . "', marque_contact_mail='" . $contact_mail . "', marque_contact_tel='" . $contact_tel . "', marque_paiement='" . $paiement . "'";
	$sql .= $sql_modif;
	$sql .= " where marque_num=" . decrypte($val_num);
	$base->query($sql);
}

if (isset($ajout))
{
	$sql = "insert into marques values (0,'" . $nom . "','','" . $raison_social . "','" . $adr1 . "','" . $adr2 . "','" . $cp . "','" . $ville . "','" . $rcs . "','" . $tva . "','" . $tel . "','" . $mail . "','" . $site . "','" . $contact . "','" . $contact_mail . "','" . $contact_tel . "','" . $paiement . "','" . $etat . "')";
	$base->query($sql);
}

if (isset($suppr))
{
	$sql = "delete from marques where marque_num=" . decrypte($suppr);
	$base->query($sql);
}

	$sql = "select * from marques order by marque_nom ASC";
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
										<?php if (!isset($modif_num)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter une marque</span>
										<?php } else { ?>
											<span class="caption-subject bold uppercase"> Modifier une marque</span>
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
									<?php } else { ?>
									<input type="hidden" name="modif" value="ok">
									<input type="hidden" name="nbr_ligne" value="<?= $nbr_ligne ?>">
									<input type="hidden" name="val_num" value="<?= $modif_num ?>">
									<table class="table table-striped table-bordered table-advance table-hover">
										 <tbody>
										<?php 
											$sql = "select * from marques d where d.marque_num=" . decrypte($modif_num);
											$rcc = $base->queryRow($sql);
											$i=0;
											if ($rcc)
											{
												$etat = $rcc["marque_visible"];
										?>
										<tr>
											<td><label>Nom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-fire"></i>
												</span>
												<input type="text" name="nom" class="form-control" value="<?= $rcc["marque_nom"] ?>" required></div></td>
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
													<input type="text" name="raison_social" class="form-control" value="<?= $rcc["marque_raison_social"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr1" class="form-control" value="<?= $rcc["marque_adr1"] ?>"></div></td>
											</tr>
											<tr>
												<td>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr2" class="form-control" value="<?= $rcc["marque_adr2"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Code Postal</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="cp" class="form-control" value="<?= $rcc["marque_cp"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Ville</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="ville" class="form-control" value="<?= $rcc["marque_ville"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>RCS</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="rcs" class="form-control" value="<?= $rcc["marque_rcs"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>TVA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<input type="text" name="tva" class="form-control" value="<?= $rcc["marque_tva"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control" value="<?= $rcc["marque_tel"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="mail" class="form-control" value="<?= $rcc["marque_mail"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Site Web</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-globe"></i>
													</span>
													<input type="text" name="site" class="form-control" value="<?= $rcc["marque_site"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact Nom & Prenom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input type="text" name="contact" class="form-control" value="<?= $rcc["marque_contact"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact mail</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="text" name="contact_mail" class="form-control" value="<?= $rcc["marque_contact_mail"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Contact Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="contact_tel" class="form-control" value="<?= $rcc["marque_contact_tel"] ?>"></div></td>
											</tr>
											<tr>
												<td><label>Methode de paiement (Ex : 60/40 ou 100)</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-euro"></i>
													</span>
													<input type="text" name="paiement" class="form-control" value="<?= $rcc["marque_paiement"] ?>"></div></td>
											</tr>
										 <tr>
											<td><label>Etat</label>
											<div class="input-group">
												<input type="radio" name="etat" value="1" <?php if ($etat==1) echo " checked"; ?>> Visible &nbsp;
												<input type="radio" name="etat" value="0" <?php if ($etat==0) echo " checked"; ?>> Invisible
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
									<span class="caption-subject bold uppercase"> Liste des marques</span>
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
													<div class="success"></div> <a href="<?= current_path() . '?modif_num=' . crypte($row["marque_num"]) ?>"><?= $row["marque_nom"] ?></a></td>
												 <td>
													<a href="<?= current_path() . '?modif_num=' . crypte($row["marque_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a> 
													<!--<a href="<?= current_path() . '?suppr=' . crypte($row["marque_num"]) ?>" class="btn btn-outline btn-circle dark btn-sm black" onClick="return confirme()">
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