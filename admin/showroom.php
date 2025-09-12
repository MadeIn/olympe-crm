<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Gestion des Showrooms - Olympe Mariage";
$desc_page = "Gestion des Showrooms - Olympe Mariage";




if (isset($action)) {
	
	switch ($action) {
		
		case "add" :
			// On insere le showroom
			$sql = "insert into showrooms values(0,'" . $nom . "','" . $adr1 . "','" . $adr2 . "','" . $cp . "','" . $ville . "','" . $acces . "','" . $tel . "','" . $mail . "','" . $rcs . "','" . $raison . "','" . $siret . "','" . $tva . "','" . $ca_annee . "','" . $ca_janvier . "','" . $ca_fevrier . "','" . $ca_mars . "','" . $ca_avril . "','" . $ca_mai . "','" . $ca_juin . "','" . $ca_juillet . "','" . $ca_aout . "','" . $ca_septembre . "','" . $ca_octobre . "','" . $ca_novembre . "','" . $ca_decembre . "','" . $nbr_annee . "','" . $nbr_janvier . "','" . $nbr_fevrier . "','" . $nbr_mars . "','" . $nbr_avril . "','" . $nbr_mai . "','" . $nbr_juin . "','" . $nbr_juillet . "','" . $nbr_aout . "','" . $nbr_septembre . "','" . $nbr_octobre . "','" . $nbr_novembre . "','" . $nbr_decembre . "','" . $banque_nom . "','" . $banque_code_etablissement . "','" . $banque_code_guichet . "','" . $banque_compte . "','" . $banque_cle_rib . "','" . $banque_swift . "','" . $banque_iban . "')";
			mysql_query($sql);
			$num = mysql_insert_id();
			$num = crypte($num);
		break;
		
		case "update" :
			$sql = "update showrooms set showroom_nom='" . $nom . "',showroom_adr1='" . $adr1 . "',showroom_adr2='" . $adr2 . "',showroom_cp='" . $cp . "',showroom_ville='" . $ville . "',showroom_acces='" . $acces . "',showroom_tel='" . $tel . "',showroom_mail='" . $mail . "',showroom_rcs='" . $rcs . "',showroom_raison='" . $raison . "',showroom_siret='" . $siret . "',showroom_tva='" . $tva . "',ca_annee='" . $ca_annee . "',ca_janvier='" . $ca_janvier . "',ca_fevrier='" . $ca_fevrier . "',ca_mars='" . $ca_mars . "',ca_avril='" . $ca_avril . "',ca_mai='" . $ca_mai . "',ca_juin='" . $ca_juin . "',ca_juillet='" . $ca_juillet . "',ca_aout='" . $ca_aout . "',ca_septembre='" . $ca_septembre . "',ca_octobre='" . $ca_octobre . "',ca_novembre='" . $ca_novembre . "',ca_decembre='" . $ca_decembre . "',nbr_annee='" . $nbr_annee . "',nbr_janvier='" . $nbr_janvier . "',nbr_fevrier='" . $nbr_fevrier . "',nbr_mars='" . $nbr_mars . "',nbr_avril='" . $nbr_avril . "',nbr_mai='" . $nbr_mai . "',nbr_juin='" . $nbr_juin . "',nbr_juillet='" . $nbr_juillet . "',nbr_aout='" . $nbr_aout . "',nbr_septembre='" . $nbr_septembre . "',nbr_octobre='" . $nbr_octobre . "',nbr_novembre='" . $nbr_novembre . "',nbr_decembre='" . $nbr_decembre . "', banque_nom='" . $banque_nom . "', banque_code_etablissement='" . $banque_code_etablissement . "',banque_code_guichet='" . $banque_code_guichet . "',banque_compte='" . $banque_compte . "',banque_cle_rib='" . $banque_cle_rib . "',banque_swift='" . $banque_swift . "',banque_iban='" . $banque_iban . "' where showroom_num='" . decrypte($num) . "'";
			mysql_query($sql);	
			
			// On efface les moyes de paiements pour les remettre
			$sql = "delete from showrooms_paiements where showroom_num='" . intval(decrypte($num)) . "'";
			mysql_query($sql);
		break;
	}
	
	// ON insere les moyens de paiements
	foreach ($mode as $val) {
		$sql = "insert into showrooms_paiements values('" . intval(decrypte($num)) . "','" . $val . "')";
		mysql_query($sql);
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

<? include( $chemin . "/mod/head.php"); ?>
<script language="Javascript">
function confirme() {
	if (confirm("Etes vous sur de vouloir supprimer cet item ?"))
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
										<? if (!isset($edit)) { ?>
											<span class="caption-subject bold uppercase"> Ajouter un Showroom</span>
										<? } else { 
											
											$sql = "select * from showrooms where showroom_num='" . decrypte($edit) . "'";
											$tt = mysql_query($sql);
											if ($rtt=mysql_fetch_array($tt)) {
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
										<? } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="<? echo $_SERVER["PHP_SELF"] ?>" method="POST">
									<? if (!isset($edit)) { ?>
										<input type="hidden" name="action" value="add">
									<? } else { ?>
										<input type="hidden" name="action" value="update">
										<input type="hidden" name="num" value="<? echo crypte($num) ?>">
									<? } ?>
										<div class="form-body">
											<div class="form-group">
												<label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-industry"></i>
													</span>
													<input type="text" name="nom" class="form-control" placeholder="Nom" value="<? echo $nom ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr1" class="form-control" placeholder="Adresse"  value="<? echo $adr1 ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Complément d'adresse</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-road"></i>
													</span>
													<input type="text" name="adr2" class="form-control" placeholder="Complément d'adresse"  value="<? echo $adr2 ?>"> </div>
											</div>
											<div class="form-group">
												<label>CP</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													<input type="text" name="cp" class="form-control" placeholder="Code Postal"  value="<? echo $cp ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Ville</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-shield"></i>
													</span>
													<input type="text" name="ville" class="form-control" placeholder="Ville" value="<? echo $ville ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Accès</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-map"></i>
													</span>
													<textarea name="acces" class="form-control" rows="4"><? echo $acces ?></textarea>
													</div>
											</div>
											<div class="form-group">
												<label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<? echo $tel ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Email</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="email" name="mail" class="form-control" placeholder="Email" value="<? echo $mail ?>" required> </div>
											</div>
											<div class="form-group">
												<label>RCS</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="rcs" class="form-control" placeholder="RCS" value="<? echo $rcs ?>" > </div>
											</div>
											<div class="form-group">
												<label>Raison Social</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="raison" class="form-control" placeholder="Raison social" value="<? echo $raison ?>" > </div>
											</div>
											<div class="form-group">
												<label>SIRET</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="siret" class="form-control" placeholder="SIRET" value="<? echo $siret ?>" > </div>
											</div>
											<div class="form-group">
												<label>TVA</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-barcode"></i>
													</span>
													<input type="text" name="tva" class="form-control" placeholder="TVA Intra" value="<? echo $tva ?>" > </div>
											</div>
											<div class="form-group">
												<label>Coordonnées bancaires</label>
												<div class="input-group">
												<span class="input-group-addon">
														<i class="fa fa-bank"></i>
													</span>
													<table class="table">
														<tr>
															<td>Nom :</td><td><input type="text" name="banque_nom" class="form-control" placeholder="Nom de la banque" value="<? echo $banque_nom ?>">
														</tr>
														<tr>
															<td>Code établissement :</td><td><input type="text" name="banque_code_etablissement" class="form-control" placeholder="Code établissement" value="<? echo $banque_code_etablissement ?>">
														</tr>
														<tr>
															<td>Code guichet :</td><td><input type="text" name="banque_code_guichet" class="form-control" placeholder="Code guichet" value="<? echo $banque_code_guichet ?>">
														</tr>
														<tr>
															<td>N° de compte :</td><td><input type="text" name="banque_compte" class="form-control" placeholder="N° de compte" value="<? echo $banque_compte ?>">
														</tr>
														<tr>
															<td>Clé RIB :</td><td><input type="text" name="banque_cle_rib" class="form-control" placeholder="Clé RIB" value="<? echo $banque_cle_rib ?>">
														</tr>
														<tr>
															<td>Code SWIFT :</td><td><input type="text" name="banque_swift" class="form-control" placeholder="Code SWIFT" value="<? echo $banque_swift ?>">
														</tr>
														<tr>
															<td>IBAN :</td><td><input type="text" name="banque_iban" class="form-control" placeholder="IBAN" value="<? echo $banque_iban ?>">
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
													<? 
														$sql = "select * from paiements_modes order by mode_ordre ASC";
														$pp = mysql_query($sql);
														while ($rpp=mysql_fetch_array($pp)) {
															$checked = "";
															$sql = "select * from showrooms_paiements where showroom_num='" . intval(decrypte($edit)) . "' and mode_num='" . $rpp["mode_num"] . "'";
															$tt = mysql_query($sql);
															if ($rtt=mysql_fetch_array($tt)) {
																$checked = " CHECKED";
															}
															echo '<li><input type="checkbox" name="mode[]" value="' . $rpp["mode_num"] . '"' . $checked . '> ' . $rpp["mode_nom"] . '</li>';
														}
													?>
												</ul>
												</div>
											</div>
											<div class="form-group">
												<label>Objectif CA Annuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<input type="text" name="ca_annee" class="form-control" placeholder="Ca Annuel" value="<? echo $ca_annee ?>" size="5"> </div>
											</div>
											<div class="form-group">
												<label>Objectif Nbr mensuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<table class="table">
														<tr>
															<td>Jan.<br><input type="text" name="nbr_janvier" class="form-control" placeholder="01" value="<? echo $nbr_janvier ?>"></td>
															<td>Fev.<br><input type="text" name="nbr_fevrier" class="form-control" placeholder="02" value="<? echo $nbr_fevrier ?>"></td>
															<td>Mars<br><input type="text" name="nbr_mars" class="form-control" placeholder="03" value="<? echo $nbr_mars ?>"></td>
															<td>Avr.<br><input type="text" name="nbr_avril" class="form-control" placeholder="04" value="<? echo $nbr_avril ?>"></td>
															<td>Mai<br><input type="text" name="nbr_mai" class="form-control" placeholder="05" value="<? echo $nbr_mai ?>"></td>
															<td>Juin<br><input type="text" name="nbr_juin" class="form-control" placeholder="06" value="<? echo $nbr_juin ?>"></td>
														</tr>
														<tr>
															<td>Jui.<br><input type="text" name="nbr_juillet" class="form-control" placeholder="07" value="<? echo $nbr_juillet ?>"></td>
															<td>Aout<br><input type="text" name="nbr_aout" class="form-control" placeholder="08" value="<? echo $nbr_aout ?>"></td>
															<td>Sep.<br><input type="text" name="nbr_septembre" class="form-control" placeholder="09" value="<? echo $nbr_septembre ?>"></td>
															<td>Oct.<br><input type="text" name="nbr_octobre" class="form-control" placeholder="10" value="<? echo $nbr_octobre ?>"></td>
															<td>Nov.<br><input type="text" name="nbr_novembre" class="form-control" placeholder="11" value="<? echo $nbr_novembre ?>"></td>
															<td>Dec.<br><input type="text" name="nbr_decembre" class="form-control" placeholder="12" value="<? echo $nbr_decembre ?>"></td>
														</tr>
													</table>
												</div>
											</div>
											<div class="form-group">
												<label>Objectif CA mensuel robes</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-eur"></i>
													</span>
													<table class="table">
														<tr>
															<td>Jan.<br><input type="text" name="ca_janvier" class="form-control" placeholder="01" value="<? echo $ca_janvier ?>"></td>
															<td>Fev.<br><input type="text" name="ca_fevrier" class="form-control" placeholder="02" value="<? echo $ca_fevrier ?>"></td>
															<td>Mars<br><input type="text" name="ca_mars" class="form-control" placeholder="03" value="<? echo $ca_mars ?>"></td>
															<td>Avr.<br><input type="text" name="ca_avril" class="form-control" placeholder="04" value="<? echo $ca_avril ?>"></td>
															<td>Mai<br><input type="text" name="ca_mai" class="form-control" placeholder="05" value="<? echo $ca_mai ?>"></td>
															<td>Juin<br><input type="text" name="ca_juin" class="form-control" placeholder="06" value="<? echo $ca_juin ?>"></td>
														</tr>
														<tr>
															<td>Jui.<br><input type="text" name="ca_juillet" class="form-control" placeholder="07" value="<? echo $ca_juillet ?>"></td>
															<td>Aout<br><input type="text" name="ca_aout" class="form-control" placeholder="08" value="<? echo $ca_aout ?>"></td>
															<td>Sep.<br><input type="text" name="ca_septembre" class="form-control" placeholder="09" value="<? echo $ca_septembre ?>"></td>
															<td>Oct.<br><input type="text" name="ca_octobre" class="form-control" placeholder="10" value="<? echo $ca_octobre ?>"></td>
															<td>Nov.<br><input type="text" name="ca_novembre" class="form-control" placeholder="11" value="<? echo $ca_novembre ?>"></td>
															<td>Dec.<br><input type="text" name="ca_decembre" class="form-control" placeholder="12" value="<? echo $ca_decembre ?>"></td>
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
										<? 
											$sql = "select * from showrooms order by showroom_nom ASC";
											$cc = mysql_query($sql);
											while ($rcc=mysql_fetch_array($cc)) {
												echo '<tr>
													<td class="highlight">
														<div class="success"></div> <a href="' . $_SERVER["PHP_SELF"] . '?edit=' . crypte($rcc["showroom_num"]) . '">' . $rcc["showroom_nom"] . '</a>
													</td>
													<td>
														<a href="' . $_SERVER["PHP_SELF"] . '?edit=' . crypte($rcc["showroom_num"]) . '" class="btn btn-outline btn-circle btn-sm purple">
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
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>