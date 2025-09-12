<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Gestion des clients - Olympe Mariage";
$desc_page = "Gestion des clients - Olympe Mariage";

$message_erreur = "";

if (isset($ajout)) {
	if ($showroom==0)
		$showroom = $u->mShowroom;
	// On test si le client n'exite pas
	$sql = "select * from clients where client_mail='" . $mail . "'";
	$tt = mysql_query($sql);
	$nbr = mysql_num_rows($tt);
	if ($nbr==0) {
		$sql = "insert into clients values (0,'" . $genre . "','" . $nom . "','" . $prenom . "','" . $adr1 . "','" . $adr2 . "','" . $cp . "','" . $ville . "','" . $tel . "','" . $mail . "','" . $date . "','" . $lieu . "','" . $remarques . "','" . $connaissance . "','" . $showroom . "','" . $u->mNum . "','" . Date("Y-m-d H:i:s") . "','" . Date("Y-m-d H:i:s") . "','" . $poitrine . "','" . $sous_poitrine . "','" . $taille . "','" . $hanche1 . "','" . $hanche2 . "','" . $carrure_avant . "','" . $carrure_dos . "','" . $longueur_dos . "','" . $biceps . "','" . $taille_sol . "','" . $pointure . "','" . $tour_taille . "','" . $interet . "',0)";
		mysql_query($sql);
	} else {
		$message_erreur = "Un client est déjà enregistré avec cette adresse email !";
	}	
}
?>
<? include( $chemin . "/mod/head.php"); ?>
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
                            <li class="active">Ajouter un client</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<? if ($message_erreur!="") { ?>
								<h3 class="font-red-thunderbird"><strong><i class="fa fa-warning"></i> <? echo $message_erreur ?></strong></h3>
							<? } ?>
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-settings font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Ajouter un client</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<? echo $PHP_SELF ?>" enctype="multipart/form-data">
									<input type="hidden" name="ajout" value="ok">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label>Genre</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-intersex"></i>
												</span>
												<select name="genre" class="form-control">
													<option value="0">Femme</option>
													<option value="1">Homme</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label>Nom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-user"></i>
												</span>
												<input type="text" name="nom" class="form-control" placeholder="Nom" value="<? echo $nom ?>" required> </div>
										</div>
										<div class="form-group">
											<label>Prenom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-user"></i>
												</span>
												<input type="text" name="prenom" class="form-control" placeholder="Prénom" value="<? echo $prenom ?>" required> </div>
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
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label>Mensuration</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-odnoklassniki-square"></i>
												</span>
												<table>
													<tr>
														<td>Tour Taille<br><input type="text" name="taille" class="form-control"></td>
														<td>Poitrine<br><input type="text" name="poitrine" class="form-control"></td>
														<td>Ss poitrine<br><input type="text" name="sous_poitrine" class="form-control"></td>
														<td>Lg Dos<br><input type="text" name="longueur_dos" class="form-control"></td>
														<td>Biceps<br><input type="text" name="biceps" class="form-control"></td>
														<td>Taille-sol avec talons<br><input type="text" name="biceps" class="form-control"></td>
													</tr>
													<tr>
														<td>Hanche 1<br><input type="text" name="hanche1" class="form-control"></td>
														<td>Hanche 2<br><input type="text" name="hanche2" class="form-control"></td>
														<td>Carrure Av<br><input type="text" name="carrure_avant" class="form-control"></td>
														<td>Carrure Dos<br><input type="text" name="carrure_dos" class="form-control"></td>
														<td>Pointure<br><input type="text" name="pointure" class="form-control"></td>
														<td>Taille<br><input type="text" name="tour_taille" class="form-control"></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="form-group">
											<label>Date du mariage</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar-check-o"></i>
												</span>
												<input type="date" name="date" class="form-control" placeholder="Date du mariage"> </div>
										</div>
										<div class="form-group">
											<label>Lieu de mariage</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-black-tie"></i>
												</span>
												<input type="text" name="lieu" class="form-control" placeholder="Lieu du mariage"> </div>
										</div>
										<div class="form-group">
											<label>Remarques</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-book"></i>
												</span>
												<textarea class="form-control" rows="4" name="remarques"></textarea> </div>
										</div>
										<div class="form-group">
											<label>Comment avez vous connu Olympe ?</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-meh-o"></i>
												</span>
												<select name="connaissance" class="form-control">
													<option value="0">----------------</option>
													<option value="1">Publicité</option>
													<option value="2">Sur Internet</option>
													<option value="3">Bouche à oreille</option>
													<option value="4">Autres</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label>Client intéressé</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-meh-o"></i>
												</span>
												<select name="interet" class="form-control">
													<option value="0">----------------</option>
													<option value="1">Bof</option>
													<option value="2">Intéressé</option>
													<option value="3">Très intéressé</option>
													<option value="4">Non</option>
												</select>
											</div>
										</div>
										<? if ($u->mShowroom==0) { ?>
											<div class="form-group">
											<label>Showroom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-industry"></i>
												</span>
												<select name="showroom" class="form-control">
													<?
														$sql = "select * from showrooms order by showroom_nom ASC";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt)) {
															echo '<option value="' . $rtt["showroom_num"] . '">' . $rtt["showroom_nom"] . '</option>';
														}
													?>
												</select>
											</div>
										</div>
										<? } else {
											echo '<input type="hidden" name="showroom" value="0">';											
										} ?>
										<div class="form-actions">
											<button type="submit" class="btn blue">Enregistrer</button>
											<button type="reset" class="btn default">Annuler</button>
										</div>										
									</div>
									</form>
									<div style="clear:both;"></div>
								</div>
							</div>
							<!-- END SAMPLE FORM PORTLET-->
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