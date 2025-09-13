<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des utilisateurs - Olympe Mariage";
$desc_page = "Gestion des utilisateurs - Olympe Mariage";

if (isset($action)) {
	
	switch ($action) {
		
		case "add" :
			// On insere le showroom
			$sql = "insert into users values(0,'" . $nom . "','" . $prenom . "','" . $mail . "','" . $mdp . "','" . $mail_mdp . "','" . $tel . "','','" . Date("Y-m-d H:i:s") . "','0000-00-00 00:00:00','" . $groupe . "','" . $showroom . "','" . $acces . "','" . $etat . "')";
			$num = $base->insert($sql);	
			$num = crypte($num);
		break;
		
		case "update" :
			$sql = "update users set user_nom='" . $nom . "',user_prenom='" . $prenom . "',user_email='" . $mail . "',user_mdp='" . $mdp . "',user_email_mdp='" . $mail_mdp . "',user_tel='" . $tel . "',groupe_num='" . $groupe . "',showroom_num='" . $showroom . "',user_etat='" . $etat . "', acces_compta='" . $acces . "' where user_num='" . decrypte($num) . "'";
			$base->query($sql);	
		break;
	}
	
	$img_nom = Slug($nom . "-". $prenom) . "-" . Date("YmdHis");
	// On insere la photo de fond
	$nom_image = uploadPhotoProfil($_FILES['photofileacc'],$img_nom,"200");
	if ($nom_image!="") {
		// Tout est ok alors on insere dans la base
		$sql = "update users set user_photo='" . $nom_image . "' where user_num='" . decrypte($num) . "'";
		$base->query($sql);
	}
	
	$nom 	= "";
	$prenom = "";
	$email 	= "";
	$email_mdp 	= "";
	$mdp 	= "";
	$tel 	= "";
	$photo 	= "";
	$groupe = 1;
	$showroom = 0;
	$acces	 = 1;
	$etat	 = 1;
}
?>
<?php $link_plugin = '<link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />'; ?>
<?php include TEMPLATE_PATH . 'head.php'; ?>
<script language="Javascript">
function confirme() {
	if (confirm("Etes vous sur de vouloir supprimer cet utilisateur ?"))
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
                                <a href="/home">Accueil</a>
                            </li>
                            <li class="active">Gestion des utilisateurs</li>
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
										<?php if (!isset($edit)) {
												$nom 	= "";
												$prenom = "";
												$mail 	= "";
												$mail_mdp 	= "";
												$mdp 	= "";
												$tel 	= "";
												$photo 	= "";
												$groupe = 1;
												$acces = 1;
												$showroom = 0;
												$etat	 = 1;
										?>
											<span class="caption-subject bold uppercase"> Ajouter un Utilisateur</span>
										<?php } else { 
											
											$sql = "select * from users where user_num='" . decrypte($edit) . "'";
											$rtt = $base->queryRow($sql);
 											if ($rtt) {
												$num 	= $rtt["user_num"];
												$nom 	= $rtt["user_nom"];
												$prenom = $rtt["user_prenom"];
												$mail 	= $rtt["user_email"];
												$mail_mdp 	= $rtt["user_email_mdp"];
												$mdp 	= $rtt["user_mdp"];
												$tel 	= $rtt["user_tel"];
												$photo 	= $rtt["user_photo"];
												$groupe = $rtt["groupe_num"];
												$showroom = $rtt["showroom_num"];
												$etat	 = $rtt["user_etat"];
												$acces	 = $rtt["acces_compta"];
											}
											
										?>
											<span class="caption-subject bold uppercase"> Modifier un utilisateur</span>
										<?php } ?>
									</div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data">
									<?php if (!isset($edit)) { ?>
										<input type="hidden" name="action" value="add">
									<?php } else { ?>
										<input type="hidden" name="action" value="update">
										<input type="hidden" name="num" value="<?php echo crypte($num) ?>">
									<?php } ?>
										<div class="form-body">
											<div class="form-group">
												<label>Nom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input type="text" name="nom" class="form-control" placeholder="Nom" value="<?php echo $nom ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Prenom</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													<input type="text" name="prenom" class="form-control" placeholder="Prénom" value="<?php echo $prenom ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Email</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-envelope"></i>
													</span>
													<input type="email" name="mail" class="form-control" placeholder="Email" value="<?php echo $mail ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Mot de passe</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user-secret"></i>
													</span>
													<input type="text" name="mdp" class="form-control" placeholder="Mot de passe" value="<?php echo $mdp ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Mot de passe de connexion email</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user-secret"></i>
													</span>
													<input type="text" name="mail_mdp" class="form-control" placeholder="Mot de passe de connexion email" value="<?php echo $mail_mdp ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Tel</label>
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-mobile-phone"></i>
													</span>
													<input type="text" name="tel" class="form-control" placeholder="Téléphone" value="<?php echo $tel ?>" required> </div>
											</div>
											<div class="form-group">
												<label>Groupe</label>
												<select name="groupe" class="form-control">
													<option value="1" <?php if ($groupe==1) echo " SELECTED" ?>>Utilisateur</option>
													<option value="0" <?php if ($groupe==0) echo " SELECTED" ?>>Administrateur</option>
													<option value="2" <?php if ($groupe==2) echo " SELECTED" ?>>Couturiere</option>
												</select>
											</div>
											<div class="form-group">
												<label>Showroom</label>
												<select name="showroom" class="form-control">
													<option value="0">--------------------</option>
													<?php														$sql = "select * from showrooms";
														$ss = $base->query($sql);
														foreach ($ss as $rss) {
															echo '<option value="' . $rss["showroom_num"] . '"';
															if ($rss["showroom_num"]==$showroom) {
																echo " SELECTED";
															}
															echo '>' . $rss["showroom_nom"] . '</option>';
														}
													?>
												</select>
											</div>
											<div class="form-group">
												<label>Accès à la compta du Showroom</label>
												<select name="acces" class="form-control">
													<option value="1" <?php if ($acces==1) echo " SELECTED" ?>>OUI</option>
													<option value="0" <?php if ($acces==0) echo " SELECTED" ?>>NON</option>
												</select>
											</div>
											<div class="form-group">
												<label>Etat</label>
												<select name="etat" class="form-control">
													<option value="1" <?php if ($etat==1) echo " SELECTED" ?>>Actif</option>
													<option value="0" <?php if ($etat==0) echo " SELECTED" ?>>Banni</option>
												</select>
											</div>
											<div class="form-group">
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
													<?php if ($photo=="") { ?>
														<img src="http://www.placehold.it/200x200/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> </div>
													<?php } else { ?>
														<img src="/photos/users/<?php echo $photo ?>" alt="" /> </div>
													<?php } ?>
													<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;"> </div>
													<div>
														<span class="btn default btn-file">
															<span class="fileinput-new"> Selectionner une image </span>
															<span class="fileinput-exists"> Modifier </span>
															<input type="file" name="photofileacc"> </span>
														<a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Effacer </a>
													</div>
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
									<span class="caption-subject bold uppercase"> Liste des utilisateurs</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th><i class="fa fa-user-plus"></i> Administrateurs </th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
										<?php 
											$sql = "select * from users where groupe_num=0 order by user_nom ASC, user_prenom ASC";
											$cc = $base->query($sql);
											foreach ($cc as $rcc) {
												echo '<tr>
													<td class="highlight">
														<div class="success"></div> <a href="' . current_path() . '?edit=' . crypte($rcc["user_num"]) . '">' . $rcc["user_prenom"] . ' ' . $rcc["user_nom"] . '</a>
													</td>
													<td>
														<a href="' . current_path() . '?edit=' . crypte($rcc["user_num"]) . '" class="btn btn-outline btn-circle btn-sm purple">
															<i class="fa fa-edit"></i> Edit </a>
													</td>
												</tr>';
												
											}
										?>
										</tbody>
									</table>
									<?php										$showroom_select = 0;
									?>
									<table class="table table-striped table-bordered table-advance table-hover">
									<?php 
										$sql = "select * from users u, showrooms s where u.showroom_num=s.showroom_num and groupe_num>0 order by s.showroom_num ASC, user_nom ASC, user_prenom ASC";
										$cc = $base->query($sql);
										$i=0;
										foreach ($cc as $rcc) {
											if ($showroom_select!=$rcc["showroom_num"]) {
												if ($i!=0)
													echo '</tbody>'; 
												echo '<thead>
													<tr>
														<th><i class="fa fa-industry"></i> ' . $rcc["showroom_nom"] . ' </th>
														<th> </th>
													</tr>
												</thead>
												<tbody>';
												$showroom_select = $rcc["showroom_num"];
											}
											echo '<tr>
												<td class="highlight">
													<div class="success"></div> <a href="' . current_path() . '?edit=' . crypte($rcc["user_num"]) . '">' . $rcc["user_prenom"] . ' ' . $rcc["user_nom"] . '</a>
												</td>
												<td>
													<a href="' . current_path() . '?edit=' . crypte($rcc["user_num"]) . '" class="btn btn-outline btn-circle btn-sm purple">
														<i class="fa fa-edit"></i> Edit </a>
												</td>
											</tr>';
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
				<?php $script_supp = '<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
								   <script src="/assets/pages/scripts/profile.min.js" type="text/javascript"></script>'; ?>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>