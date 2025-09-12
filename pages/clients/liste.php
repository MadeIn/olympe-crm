<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Rechercher un client - Olympe Mariage";
$desc_page = "Rechercher un client - Olympe Mariage";
?>

<?php include TEMPLATE_PATH . 'head.php'; ?>

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
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Rechercher un client</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-settings font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Rechercher un client</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="liste" method="POST" action="<?php echo $PHP_SELF ?>" enctype="multipart/form-data">
									<input type="hidden" name="recherche" value="ok">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label>Nom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-user"></i>
												</span>
												<input type="text" name="nom" class="form-control" placeholder="Nom"> 
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>Email</label>
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-envelope"></i>
											</span>
											<input type="email" name="email" class="form-control" placeholder="Email"> 
										</div>
									</div>
									<?php if ($u->mShowroom==0) { ?>
										<div class="form-group">
											<label>Showroom</label>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-industry"></i>
												</span>
												<select name="showroom" class="form-control">
													<?
														$sql = "select * from showrooms order by showroom_num ASC";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt)) {
															echo '<option value="' . $rtt["showroom_num"] . '">' . $rtt["showroom_nom"] . '</option>';
														}
													?>
												</select>
											</div>
										</div>
										<?php } else {
											echo '<input type="hidden" name="showroom" value="' . $u->mShowroom . '">';											
										} ?>
									<div class="form-actions">
										<button type="submit" class="btn blue">Rechercher</button>
									</div>
									</form>
								</div>
							</div>
						</div>
						<?php if (isset($recherche)) { 
								echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption font-red-sunglo">
											<i class="icon-settings font-red-sunglo"></i>
												<span class="caption-subject bold uppercase"> Liste des clients</span>
										</div>
									</div>';
								$sql = "select * from clients where client_num=client_num";
								//if ($u->mGroupe!=0)
								$sql .= " and showroom_num='" . $showroom . "'";
								if ($nom!="")
									$sql .= " and client_nom like '%" . $nom . "%'";
								if ($email!="")
									$sql .= " and client_mail='" . $email . "'";
								$sql .= " order by client_nom ASC, client_prenom ASC";
								$cc = mysql_query($sql);
								$nb = mysql_num_rows($cc);
								if ($nb>0) {
									echo '<div class="table-scrollable">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Nom</th>
												<th>Email</th>
												<th>Tel</th>
												<th>Date mariage</th>
												<th>Lieu</th>
												<th></th>
											</tr>
										</thead>
										<tbody>';
									while ($rcc=mysql_fetch_array($cc)) {
										$sql = "select * from rendez_vous where client_num='" . $rcc["client_num"] . "' and type_num=5";
										$tt = mysql_query($sql);
										$nbr = mysql_num_rows($tt);
										if ($nbr>0)
											$tab = "&tab=tab_1_4";
										else
											$tab = "";
										echo '<tr>
												<td>' . $rcc["client_nom"] . ' ' . $rcc["client_prenom"] . '</td>
												<td>' . $rcc["client_mail"] . '</td>
												<td>' . $rcc["client_tel"] . '</td>
												<td>' . format_date($rcc["client_date_mariage"],11,1) . '</td>
												<td>' . $rcc["client_lieu_mariage"] . '</td>
												<td><a href="client.php?client_num=' . crypte($rcc["client_num"]) . $tab . '"  class="btn btn-outline btn-circle btn-sm purple">
															<i class="fa fa-edit"></i> Edit </a></td>';
									}
									echo '	</tbody>
										</table>
									</div>';
								} else {
									echo '<div class="row">';
									echo '<p><b><i>Aucun client ne correspond à votre recherche !</i></b></p>';
									echo '</div>';
								}
 						
									
								echo '</div>
								</div>';
						 } ?>
                    </div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>