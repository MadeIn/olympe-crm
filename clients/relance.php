<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Extraction email - Olympe Mariage";
$desc_page = "Extraction email - Olympe Mariage";
  
  $mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
  $mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  
  if (!isset($genre))
	  $genre=-1;
  
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
                            <li class="active">Relance Cliente</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-question font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Recherche </span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<? echo $PHP_SELF ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Date debut</th>
												<th>Date fin</th>
												<th>Genre</th>
												<th>Interet</th>
												<th>Showroom</th>
												<th>Suivi par</th>
												<th>Affichage</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<input type="date" name="date_debut" value="<? echo $date_debut ?>">
												</td>
												<td>
													<input type="date" name="date_fin" value="<? echo $date_fin ?>">
												</td>
												<td>
													<select name="genre" class="form-control">
														<option value="-1">Tous</option>
														<option value="0"<? if ($genre==0) echo " SELECTED";?>>Femme</option>
														<option value="1"<? if ($genre==1) echo " SELECTED";?>>Homme</option>
													</select>
												</td>
												<td>
													<select name="interet" class="form-control">
														<option value="0">----------------</option>
														<option value="1"<? if ($interet==1) echo " SELECTED";?>>Bof</option>
														<option value="2"<? if ($interet==2) echo " SELECTED";?>>Intéressé</option>
														<option value="3"<? if ($interet==3) echo " SELECTED";?>>Très intéressé</option>
														<option value="3"<? if ($interet==4) echo " SELECTED";?>>Non</option>
													</select>
												</td>
												<td>
													<select name="showroom" class="form-control input-medium">
														<option value="0">Tous</option>
													<?
														$sql = "select * from showrooms order by showroom_nom ASC";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt)) {
															echo '<option value="' . $rtt["showroom_num"] . '"';
															if ($rtt["showroom_num"]==$showroom) echo " SELECTED";
															echo '>' . $rtt["showroom_nom"] . '</option>';
														}
													?>
													</select>
												</td>
												<td>
													<select name="user_select" class="form-control input-medium">
														<option value="0">Tous</option>
													<?
														$sql = "select * from users order by user_nom ASC";
														$tt = mysql_query($sql);
														while ($rtt=mysql_fetch_array($tt)) {
															echo '<option value="' . $rtt["user_num"] . '"';
															if ($rtt["user_num"]==$user_select) echo " SELECTED";
															echo '>' . $rtt["user_prenom"] . ' ' . $rtt["user_nom"] . '</option>';
														}
													?>
													</select>
												</td>
												<td>
													<select name="affichage" class="form-control">
														<option value="0"<? if ($affichage==0) echo " SELECTED";?>>Tableau client</option>
														<option value="1"<? if ($affichage==1) echo " SELECTED";?>>Email</option>
													</select>
												</td>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									</form>
								</div>
							</div>
						</div>
                    	<? if (isset($recherche)) { ?>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<?
							$sql = "select * from clients c, rendez_vous r where c.client_num=r.client_num and r.type_num=1 ";
							if ($date_debut!="")
								$sql .= " and rdv_date>='" . $date_debut . " 00:00:00'";
							if ($date_fin!="")
								$sql .= " and rdv_date<='" . $date_fin . " 23:59:59'";
							if ($showroom!=0)
								$sql .= " and showroom_num='" . $showroom . "'";
							if ($interet!=0)
								$sql .= " and interet='" . $interet . "'";
							if ($user_select!=0)
								$sql .= " and c.user_num='" . $user_select . "'";
							if ($genre!=-1)
								$sql .= " and client_genre='" . $genre . "'";
					
							$cc = mysql_query($sql);
							$nbr_email = 0;
							if ($affichage==0) {
								echo '<table class="table table-bordered table-bordered table-hover">
										<thead>
											<tr>
												<th>Client</th>
												<th>Tel</th>
												<th>Mail</th>
												<th>Date Mariage</th>
												<th>Lieu</th>
												<th>Remarques</th>
												<th>Interêt</th>
											</tr>
										</thead>
										<tbody>';
							}
							while ($rcc=mysql_fetch_array($cc)) {
								$test=1;
								
								// On test si la cliente a déjà commandé
								$sql = "select * from commandes where client_num='" . $rcc["client_num"] . "' and commande_num!=0";
								$tt = mysql_query($sql);
								$nbr_commande = mysql_num_rows($tt);
								if ($nbr_commande>0)
									$test=0;
								if ($test==1) { 								
									if ($affichage==1) {
										echo $rcc["client_mail"] . "<br>";
										$nbr_email++;
									} else {
										echo '<tr>
												<td><a href="/clients/client.php?client_num=' . crypte($rcc["client_num"]) . '">' . $rcc["client_prenom"] . ' ' . $rcc["client_nom"] . '</td>
												<td>' . $rcc["client_tel"] . '</td>
												<td>' . $rcc["client_mail"] . '</td>
												<td>' . format_date($rcc["client_date_mariage"],11,1) . '</td>
												<td>' . $rcc["client_lieu_mariage"] . '</td>
												<td>' . $rcc["client_remarque"] . '</td>
												<td>';
										 if ($rcc["interet"]==1) echo "Bof";
										 if ($rcc["interet"]==2) echo "Intéressé";
										 if ($rcc["interet"]==3) echo "Très intéressé";
										 if ($rcc["interet"]==4) echo "Non";
										echo '	</td>
											  </tr>';
										$nbr_email++;
									}
								}
							}
							if ($affichage==0)
								echo '	</tbody>
									</table>';
						?>						
						
						<hr>
						<p>Nombre d'email : <strong><? echo $nbr_email ?></strong></p>
						</div>
						<? } ?>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>