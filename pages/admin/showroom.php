<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des Showrooms - Olympe Mariage";
$desc_page = "Gestion des Showrooms - Olympe Mariage";

if (isset($action)) {
	
	switch ($action) {
		
		case "add" :
			// On insere le showroom
			$sql = "insert into showrooms values(0," . sql_safe($nom) . "," . sql_safe($adr1) . "," . sql_safe($adr2) . "," . sql_safe($cp) . "," . sql_safe($ville) . "," . sql_safe($acces) . "," . sql_safe($tel) . "," . sql_safe($mail) . "," . sql_safe($rcs) . "," . sql_safe($raison) . "," . sql_safe($siret) . "," . sql_safe($tva) . "," . sql_safe($ca_annee) . "," . sql_safe($ca_janvier) . "," . sql_safe($ca_fevrier) . "," . sql_safe($ca_mars) . "," . sql_safe($ca_avril) . "," . sql_safe($ca_mai) . "," . sql_safe($ca_juin) . "," . sql_safe($ca_juillet) . "," . sql_safe($ca_aout) . "," . sql_safe($ca_septembre) . "," . sql_safe($ca_octobre) . "," . sql_safe($ca_novembre) . "," . sql_safe($ca_decembre) . "," . sql_safe($nbr_annee) . "," . sql_safe($nbr_janvier) . "," . sql_safe($nbr_fevrier) . "," . sql_safe($nbr_mars) . "," . sql_safe($nbr_avril) . "," . sql_safe($nbr_mai) . "," . sql_safe($nbr_juin) . "," . sql_safe($nbr_juillet) . "," . sql_safe($nbr_aout) . "," . sql_safe($nbr_septembre) . "," . sql_safe($nbr_octobre) . "," . sql_safe($nbr_novembre) . "," . sql_safe($nbr_decembre) . "," . sql_safe($banque_nom) . "," . sql_safe($banque_code_etablissement) . "," . sql_safe($banque_code_guichet) . "," . sql_safe($banque_compte) . "," . sql_safe($banque_cle_rib) . "," . sql_safe($banque_swift) . "," . sql_safe($banque_iban) . ")";
			$num = $base->insert($sql);
			$num = crypte($num);
		break;
		
		case "update" :
			$sql = "update showrooms set showroom_nom=" . sql_safe($nom) . ",showroom_adr1=" . sql_safe($adr1) . ",showroom_adr2=" . sql_safe($adr2) . ",showroom_cp=" . sql_safe($cp) . ",showroom_ville=" . sql_safe($ville) . ",showroom_acces=" . sql_safe($acces) . ",showroom_tel=" . sql_safe($tel) . ",showroom_mail=" . sql_safe($mail) . ",showroom_rcs=" . sql_safe($rcs) . ",showroom_raison=" . sql_safe($raison) . ",showroom_siret=" . sql_safe($siret) . ",showroom_tva=" . sql_safe($tva) . ",ca_annee=" . sql_safe($ca_annee) . ",ca_janvier=" . sql_safe($ca_janvier) . ",ca_fevrier=" . sql_safe($ca_fevrier) . ",ca_mars=" . sql_safe($ca_mars) . ",ca_avril=" . sql_safe($ca_avril) . ",ca_mai=" . sql_safe($ca_mai) . ",ca_juin=" . sql_safe($ca_juin) . ",ca_juillet=" . sql_safe($ca_juillet) . ",ca_aout=" . sql_safe($ca_aout) . ",ca_septembre=" . sql_safe($ca_septembre) . ",ca_octobre=" . sql_safe($ca_octobre) . ",ca_novembre=" . sql_safe($ca_novembre) . ",ca_decembre=" . sql_safe($ca_decembre) . ",nbr_annee=" . sql_safe($nbr_annee) . ",nbr_janvier=" . sql_safe($nbr_janvier) . ",nbr_fevrier=" . sql_safe($nbr_fevrier) . ",nbr_mars=" . sql_safe($nbr_mars) . ",nbr_avril=" . sql_safe($nbr_avril) . ",nbr_mai=" . sql_safe($nbr_mai) . ",nbr_juin=" . sql_safe($nbr_juin) . ",nbr_juillet=" . sql_safe($nbr_juillet) . ",nbr_aout=" . sql_safe($nbr_aout) . ",nbr_septembre=" . sql_safe($nbr_septembre) . ",nbr_octobre=" . sql_safe($nbr_octobre) . ",nbr_novembre=" . sql_safe($nbr_novembre) . ",nbr_decembre=" . sql_safe($nbr_decembre) . ", banque_nom=" . sql_safe($banque_nom) . ", banque_code_etablissement=" . sql_safe($banque_code_etablissement) . ",banque_code_guichet=" . sql_safe($banque_code_guichet) . ",banque_compte=" . sql_safe($banque_compte) . ",banque_cle_rib=" . sql_safe($banque_cle_rib) . ",banque_swift=" . sql_safe($banque_swift) . ",banque_iban=" . sql_safe($banque_iban) . " where showroom_num=" . sql_safe(decrypte($num)) . "";
			$base->query($sql);	
			
			// On efface les moyes de paiements pour les remettre
			$sql = "delete from showrooms_paiements where showroom_num='" . intval(decrypte($num)) . "'";
			$base->query($sql);

			$edit = $num;
		break;
	}
	
	// ON insere les moyens de paiements
	foreach ($mode as $val) {
		$sql = "insert into showrooms_paiements values('" . intval(decrypte($num)) . "'," . sql_safe($val) . ")";
		$base->insert($sql);
	}
	
	
	$nom = "";
	$adr1 = "";
	$adr2 = "";
	$cp = "";
	$ville = "";
	$tel = "";
	$mail = "";
	$rcs = "";
	$acces = "";
	
	$banque_nom = "";
	$banque_code_etablissement = "";
	$banque_code_guichet = "";
	$banque_compte = "";
	$banque_cle_rib = "";
	$banque_swift = "";
	$banque_iban = "";
	
	$ca_annee=0;
	$ca_janvier=0;
	$ca_fevrier=0;
	$ca_mars=0;
	$ca_avril=0;
	$ca_mai=0;
	$ca_juin=0;
	$ca_juillet=0;
	$ca_aout=0;
	$ca_septembre=0;
	$ca_octobre=0;
	$ca_novembre=0;
	$ca_decembre=0;
	
	$nbr_annee=0;
	$nbr_janvier=0;
	$nbr_fevrier=0;
	$nbr_mars=0;
	$nbr_avril=0;
	$nbr_mai=0;
	$nbr_juin=0;
	$nbr_juillet=0;
	$nbr_aout=0;
	$nbr_septembre=0;
	$nbr_octobre=0;
	$nbr_novembre=0;
	$nbr_decembre=0;

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
                            <li class="active">Gestion des showrooms</li>
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
										<?php if (!isset($edit)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter un Showroom</span>
										<?php } else { 
											
											$sql = "select * from showrooms where showroom_num='" . decrypte($edit) . "'";
											$rtt = $base->queryRow($sql);
 											if ($rtt) {
												$num = $rtt["showroom_num"];
												$nom = $rtt["showroom_nom"];
												$adr1 = $rtt["showroom_adr1"];
												$adr2 = $rtt["showroom_adr2"];
												$cp = $rtt["showroom_cp"];
												$ville = $rtt["showroom_ville"];
												$tel = $rtt["showroom_tel"];
												$mail = $rtt["showroom_mail"];
												$rcs = $rtt["showroom_rcs"];
												$acces = $rtt["showroom_acces"];
												$raison = $rtt["showroom_raison"];
												$tva = $rtt["showroom_tva"];
												$siret = $rtt["showroom_siret"];
												
												$banque_nom = $rtt["banque_nom"];
												$banque_code_etablissement = $rtt["banque_code_etablissement"];
												$banque_code_guichet = $rtt["banque_code_guichet"];
												$banque_compte = $rtt["banque_compte"];
												$banque_cle_rib = $rtt["banque_cle_rib"];
												$banque_swift = $rtt["banque_swift"];
												$banque_iban = $rtt["banque_iban"];
												
												$ca_annee=$rtt["ca_annee"];
												$ca_janvier=$rtt["ca_janvier"];
												$ca_fevrier=$rtt["ca_fevrier"];
												$ca_mars=$rtt["ca_mars"];
												$ca_avril=$rtt["ca_avril"];
												$ca_mai=$rtt["ca_mai"];
												$ca_juin=$rtt["ca_juin"];
												$ca_juillet=$rtt["ca_juillet"];
												$ca_aout=$rtt["ca_aout"];
												$ca_septembre=$rtt["ca_septembre"];
												$ca_octobre=$rtt["ca_octobre"];
												$ca_novembre=$rtt["ca_novembre"];
												$ca_decembre=$rtt["ca_decembre"];
												
												$nbr_annee=$rtt["nbr_annee"];
												$nbr_janvier=$rtt["nbr_janvier"];
												$nbr_fevrier=$rtt["nbr_fevrier"];
												$nbr_mars=$rtt["nbr_mars"];
												$nbr_avril=$rtt["nbr_avril"];
												$nbr_mai=$rtt["nbr_mai"];
												$nbr_juin=$rtt["nbr_juin"];
												$nbr_juillet=$rtt["nbr_juillet"];
												$nbr_aout=$rtt["nbr_aout"];
												$nbr_septembre=$rtt["nbr_septembre"];
												$nbr_octobre=$rtt["nbr_octobre"];
												$nbr_novembre=$rtt["nbr_novembre"];
												$nbr_decembre=$rtt["nbr_decembre"];
											}
											
										?>
											<span class="caption-subject bold uppercase"> Modifier un Showroom</span>
										<?php } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="<?= current_path() ?>" method="POST">
									<?php if (!isset($edit)) { ?>
										<input type="hidden" name="action" value="add">
									<?php } else { ?>
										<input type="hidden" name="action" value="update">
										<input type="hidden" name="num" value="<?= crypte($num) ?>">
									<?php } ?>
										<div class="form-body">
											<div class="form-group">
												<label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-industry"></i>
													</span>
													<input type="text" name="nom" class="form-control" placeholder="Nom" value="<?= ($nom ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr1" class="form-control" placeholder="Adresse"  value="<?= ($adr1 ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Complément d'adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr2" class="form-control" placeholder="Complément d'adresse"  value="<?= ($adr2 ?? '') ?>"> </div>
											</div>
											<div class="form-group">
												<label>CP</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="cp" class="form-control" placeholder="Code Postal"  value="<?= ($cp ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Ville</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="ville" class="form-control" placeholder="Ville" value="<?= ($ville ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Accès</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-map"></i>
													</span>
													<textarea name="acces" class="form-control" rows="4"><?= ($acces ?? '') ?></textarea>
													</div>
											</div>
											<div class="form-group">
												<label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<?= ($tel ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Email</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="email" name="mail" class="form-control" placeholder="Email" value="<?= ($mail ?? '') ?>" required> </div>
											</div>
											<div class="form-group">
												<label>RCS</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="rcs" class="form-control" placeholder="RCS" value="<?= ($rcs ?? '') ?>" > </div>
											</div>
											<div class="form-group">
												<label>Raison Social</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="raison" class="form-control" placeholder="Raison social" value="<?= ($raison ?? '') ?>" > </div>
											</div>
											<div class="form-group">
												<label>SIRET</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="siret" class="form-control" placeholder="SIRET" value="<?= ($siret ?? '') ?>" > </div>
											</div>
											<div class="form-group">
												<label>TVA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="tva" class="form-control" placeholder="TVA Intra" value="<?= ($tva ?? '') ?>" > </div>
											</div>
											<div class="form-group">
												<label>Coordonnées bancaires</label>
												<div class="input-group">
												<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<table class="table">
														<tr>
															<td>Nom :</td><td><input type="text" name="banque_nom" class="form-control" placeholder="Nom de la banque" value="<?= ($banque_nom ?? '')?>">
														</tr>
														<tr>
															<td>Code établissement :</td><td><input type="text" name="banque_code_etablissement" class="form-control" placeholder="Code établissement" value="<?= ($banque_code_etablissement ?? '') ?>">
														</tr>
														<tr>
															<td>Code guichet :</td><td><input type="text" name="banque_code_guichet" class="form-control" placeholder="Code guichet" value="<?= ($banque_code_guichet ?? '') ?>">
														</tr>
														<tr>
															<td>N° de compte :</td><td><input type="text" name="banque_compte" class="form-control" placeholder="N° de compte" value="<?= ($banque_compte ?? '') ?>">
														</tr>
														<tr>
															<td>Clé RIB :</td><td><input type="text" name="banque_cle_rib" class="form-control" placeholder="Clé RIB" value="<?= ($banque_cle_rib ?? '') ?>">
														</tr>
														<tr>
															<td>Code SWIFT :</td><td><input type="text" name="banque_swift" class="form-control" placeholder="Code SWIFT" value="<?= ($banque_swift ?? '') ?>">
														</tr>
														<tr>
															<td>IBAN :</td><td><input type="text" name="banque_iban" class="form-control" placeholder="IBAN" value="<?= ($banque_iban ?? '') ?>">
														</tr>
													</table>
												</div>
											</div>
											<div class="form-group">
												<label>Paiements acceptés</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
												<ul>
													<?php 
														$sql = "select * from paiements_modes order by mode_ordre ASC";
														$pp = $base->query($sql);
														foreach ($pp as $rpp) {
															$checked = "";
															if (isset($edit)) {
																$sql = "select * from showrooms_paiements where showroom_num='" . intval(decrypte($edit)) . "' and mode_num='" . $rpp["mode_num"] . "'";
																$rtt = $base->queryRow($sql);
																if ($rtt) {
																	$checked = " CHECKED";
																}
															}
															echo '<li><input type="checkbox" name="mode[]" value="' . $rpp["mode_num"] . '"' . $checked . '> ' . $rpp["mode_nom"] . '</li>';
														}
													?>
												</ul>
												</div>
											</div>
											<div class="form-group">
												<label>Objectif Nbr Annuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<input type="text" name="nbr_annee" class="form-control" placeholder="Nbr Annuel" value="<?= ($nbr_annee ?? '') ?>" size="5"> </div>
											</div>
											<div class="form-group">
												<label>Objectif Nbr mensuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<table class="table">
														<tr>
															<td>Jan.<br><input type="text" name="nbr_janvier" class="form-control" placeholder="01" value="<?= ($nbr_janvier ?? '') ?>"></td>
															<td>Fev.<br><input type="text" name="nbr_fevrier" class="form-control" placeholder="02" value="<?= ($nbr_fevrier ?? '') ?>"></td>
															<td>Mars<br><input type="text" name="nbr_mars" class="form-control" placeholder="03" value="<?= ($nbr_mars ?? '') ?>"></td>
															<td>Avr.<br><input type="text" name="nbr_avril" class="form-control" placeholder="04" value="<?= ($nbr_avril ?? '') ?>"></td>
															<td>Mai<br><input type="text" name="nbr_mai" class="form-control" placeholder="05" value="<?= ($nbr_mai ?? '') ?>"></td>
															<td>Juin<br><input type="text" name="nbr_juin" class="form-control" placeholder="06" value="<?= ($nbr_juin ?? '') ?>"></td>
														</tr>
														<tr>
															<td>Jui.<br><input type="text" name="nbr_juillet" class="form-control" placeholder="07" value="<?= ($nbr_juillet ?? '') ?>"></td>
															<td>Aout<br><input type="text" name="nbr_aout" class="form-control" placeholder="08" value="<?= ($nbr_aout ?? '') ?>"></td>
															<td>Sep.<br><input type="text" name="nbr_septembre" class="form-control" placeholder="09" value="<?= ($nbr_septembre ?? '') ?>"></td>
															<td>Oct.<br><input type="text" name="nbr_octobre" class="form-control" placeholder="10" value="<?= ($nbr_octobre ?? '') ?>"></td>
															<td>Nov.<br><input type="text" name="nbr_novembre" class="form-control" placeholder="11" value="<?= ($nbr_novembre ?? '') ?>"></td>
															<td>Dec.<br><input type="text" name="nbr_decembre" class="form-control" placeholder="12" value="<?= ($nbr_decembre ?? '') ?>"></td>
														</tr>
													</table>
												</div>
											</div>
											<div class="form-group">
												<label>Objectif CA Annuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<input type="text" name="ca_annee" class="form-control" placeholder="Ca Annuel" value="<?= ($ca_annee ?? '') ?>" size="5"> </div>
											</div>
											<div class="form-group">
												<label>Objectif CA mensuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<table class="table">
														<tr>
															<td>Jan.<br><input type="text" name="ca_janvier" class="form-control" placeholder="01" value="<?= ($ca_janvier ?? '') ?>"></td>
															<td>Fev.<br><input type="text" name="ca_fevrier" class="form-control" placeholder="02" value="<?= ($ca_fevrier ?? '') ?>"></td>
															<td>Mars<br><input type="text" name="ca_mars" class="form-control" placeholder="03" value="<?= ($ca_mars ?? '') ?>"></td>
															<td>Avr.<br><input type="text" name="ca_avril" class="form-control" placeholder="04" value="<?= ($ca_avril ?? '') ?>"></td>
															<td>Mai<br><input type="text" name="ca_mai" class="form-control" placeholder="05" value="<?= ($ca_mai ?? '') ?>"></td>
															<td>Juin<br><input type="text" name="ca_juin" class="form-control" placeholder="06" value="<?= ($ca_juin ?? '') ?>"></td>
														</tr>
														<tr>
															<td>Jui.<br><input type="text" name="ca_juillet" class="form-control" placeholder="07" value="<?= ($ca_juillet ?? '') ?>"></td>
															<td>Aout<br><input type="text" name="ca_aout" class="form-control" placeholder="08" value="<?= ($ca_aout ?? '') ?>"></td>
															<td>Sep.<br><input type="text" name="ca_septembre" class="form-control" placeholder="09" value="<?= ($ca_septembre ?? '') ?>"></td>
															<td>Oct.<br><input type="text" name="ca_octobre" class="form-control" placeholder="10" value="<?= ($ca_octobre ?? '') ?>"></td>
															<td>Nov.<br><input type="text" name="ca_novembre" class="form-control" placeholder="11" value="<?= ($ca_novembre ?? '') ?>"></td>
															<td>Dec.<br><input type="text" name="ca_decembre" class="form-control" placeholder="12" value="<?= ($ca_decembre ?? '') ?>"></td>
														</tr>
													</table>
												</div>
											</div>
											
										</div>
										<div class="form-actions">
											<button type="submit" class="btn blue">Enregistrer</button>
											<button type="reset" class="btn default">Annuler</button>
										</div>
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
									<span class="caption-subject bold uppercase"> Liste des Showrooms</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th><i class="fa fa-industry"></i> Showroom </th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
										<?php 
											$sql = "select * from showrooms order by showroom_nom ASC";
											$cc = $base->query($sql);
											foreach ($cc as $rcc) {
												echo '<tr>
													<td class="highlight">
														<div class="success"></div> <a href="' . current_path() . '?edit=' . crypte($rcc["showroom_num"]) . '">' . $rcc["showroom_nom"] . '</a>
													</td>
													<td>
														<a href="' . current_path() . '?edit=' . crypte($rcc["showroom_num"]) . '" class="btn btn-outline btn-circle btn-sm purple">
															<i class="fa fa-edit"></i> Edit </a>
													</td>
												</tr>';
												
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
		 <?php if (isset($action)) { 
				switch ($action) {
					case 'add':
						echo '<script>$ol.toastSuccess("Showroom mis à jour !");</script>';
					break;
					case 'update':
						echo '<script>$ol.toastSuccess("Showroom ajouté !");</script>';
					break;
				}
		   }
		?>
    </body>

</html>