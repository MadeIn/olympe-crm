<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Dashboard - Olympe Mariage";
$desc_page = "Dashboard - Olympe Mariage";

$jour_mois = array("0","31","29","31","30","31","30","31","31","30","31","30","31");

if (!isset($mois))
	$mois = "00";
if (!isset($annee))
	$annee = "00";
if (!isset($type))
	$type = "0";

if (isset($calendrier_suppr)) {
	$sql = "delete from calendriers where calendrier_num='" . decrypte($calendrier_suppr) . "'";
	$base->query($sql);
}
?>

<?php include TEMPLATE_PATH . 'head.php'; ?>
<script language="JavaScript">
function confirme() {
	if (confirm("Etes vous sur de vouloir supprimer cette date ?"))
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
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Gestion des RDV</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<!-- BEGIN DRAGGABLE EVENTS PORTLET-->
							
									<form name="recherche" method="POST" action="<?php echo $PHP_SELF ?>" enctype="multipart/form-data">
									<input type="hidden" name="recherche" value="ok">
									<div class="form-inline">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar-check-o"></i>
											</span>
											<select name="mois" class="form-control">
												<option value="00"<?php if ($mois=="00") echo " SELECTED"; ?>>Mois</option>
												<option value="01"<?php if ($mois=="01") echo " SELECTED"; ?>>Janvier</option>
												<option value="02"<?php if ($mois=="02") echo " SELECTED"; ?>>Fevrier</option>
												<option value="03"<?php if ($mois=="03") echo " SELECTED"; ?>>Mars</option>
												<option value="04"<?php if ($mois=="04") echo " SELECTED"; ?>>Avril</option>
												<option value="05"<?php if ($mois=="05") echo " SELECTED"; ?>>Mai</option>
												<option value="06"<?php if ($mois=="06") echo " SELECTED"; ?>>Juin</option>
												<option value="07"<?php if ($mois=="07") echo " SELECTED"; ?>>Juillet</option>
												<option value="08"<?php if ($mois=="08") echo " SELECTED"; ?>>Aout</option>
												<option value="09"<?php if ($mois=="09") echo " SELECTED"; ?>>Septembre</option>
												<option value="10"<?php if ($mois=="10") echo " SELECTED"; ?>>Octobre</option>
												<option value="11"<?php if ($mois=="11") echo " SELECTED"; ?>>Novembre</option>
												<option value="12"<?php if ($mois=="12") echo " SELECTED"; ?>>Décembre</option>
											</select>
											
											<span class="input-group-addon">
												<i class="fa fa-calendar-check-o"></i>
											</span>
											<select name="annee" class="form-control">
											<option value="00">Année</option>
											<?
												for ($i=Date("Y");$i<=Date("Y")+3;$i++) {
													echo '<option value="' . $i . '"';
													if ($i==$annee)
														echo " SELECTED";
													echo '>' . $i . '</option>';
												}
											?>
											</select>
											
											<span class="input-group-addon">
												<i class="fa fa-meh-o"></i>
											</span>
											<select name="type" class="form-control">
											<option value="0">Catégorie</option>
											<?
												$sql = "select * from calendriers_themes order by theme_pos ASC";
												$tt = $base->query($sql);
												foreach ($tt as $rtt) {
													echo '<option value="' . $rtt["theme_num"] . '"';
													if ($rtt["theme_num"]==$type)
														echo " SELECTED";
													echo '>' . $rtt["theme_nom"] . '</option>';
												}
											?>
											</select>
											<span class="input-group-addon"><button type="submit" class="btn blue btn-xs">Recherche</button></span>
											
										</div>
									</div>
									</form>
								<!-- END DRAGGABLE EVENTS PORTLET-->
							</div>
						</div>
						<hr>
						<table class="table table-bordered">
						<thead>
							<tr>
								<th> Début </th>
								<th> Fin </th>
								<th> Titre </th>
								<th> Theme </th>
								<th> Client </th>
								<th> </th>
							</tr>
						</thead>
						<tbody>
							<?
								$jour = "0000-00-00";
								$sql = "select * from calendriers c, calendriers_themes ct where c.theme_num=ct.theme_num ";
								if ($type!=0) {
									$sql .= " and c.theme_num='" . $type . "'";
									if ($type!=1)
										$sql .= " and user_num='" . $u->mNum . "'";
									else
										$sql .= " and showroom_num='" . $u->mShowroom . "'";
								}
								else 
									$sql .= " and user_num='" . $u->mNum . "'";
								if ($mois!="00") {
									if ($mois!=Date("n"))
										$date_deb = $annee . "-" . $mois . "-01 00:00:00";
									else
										$date_deb = $annee . "-" . $mois . "-" . Date("d") . " 00:00:00";
									$date_fin = $annee . "-" . $mois . "-" . $jour_mois[intval($mois)] . " 00:00:00";
									$sql .= " and calendrier_datedeb>='" . $date_deb . "' and calendrier_datedeb<='" . $date_fin . "' order by calendrier_datedeb ASC";
								} else {
									$sql .= " and calendrier_datedeb>='" . Date("Y-m-d") . " 00:00:00' order by calendrier_datedeb ASC LIMIT 0,50";
								}
								$cc = $base->query($sql);
								foreach ($cc as $rcc) {
									list(
										$annee_deb,
										$mois_deb,
										$jour_deb,
										$heure_deb,
										$minute_deb,
										$seconde_deb ) = split('[: -]',$rcc["calendrier_datedeb"],6);
									
									list(
										$annee_fin,
										$mois_fin,
										$jour_fin,
										$heure_fin,
										$minute_fin,
										$seconde_fin ) = split('[: -]',$rcc["calendrier_datefin"],6);
									
									$date_test = $annee_deb . "-" . $mois_deb . "-" . $jour_deb;
									if ($jour!=$date_test) {
										echo '<tr class="danger"><td colspan="6" align="center"><strong>' . format_date($date_test,0,1) . '</strong></td></tr>';
										$jour = $date_test;
									}
									echo '<tr>
										<td>' . $heure_deb . ":" . $minute_deb . '</td>
										<td>' . $heure_fin . ":" . $minute_fin . '</td>
										<td>' . $rcc["calendrier_titre"] . '</td>';
									
									$nom = $rcc["theme_nom"];
									$couleur = $rcc["theme_couleur"];

									echo '<td bgcolor="' . $couleur . '"><nobr><strong><font color="#fff">' . $nom . '</font></strong></nobr></td>';
									echo '<td><nobr>';
									if ($rcc["client_num"]!=0) {
										$sql = "select * from clients where client_num='" . $rcc["client_num"] . "'";
										$rcl = $base->queryRow($sql);
 if ($rcl) {
											echo '<a href="/clients/client.php?client_num=' . crypte($rcc["client_num"]) . '">' . $rcl["client_prenom"] . ' ' . $rcl["client_nom"] . '</a>';
										}
									}
									echo '</nobr></td>';
									echo '<td><a href="' . $_SERVER["PHP_SELF"] . '?calendrier_suppr=' . crypte($rcc["calendrier_num"]) . '&mois=' . $mois . '&annee=' . $annee . '&type=' . $type . '" onClick="return confirme()" class="btn btn-outline btn-circle dark btn-sm red"><i class="fa fa-trash"></i> Suppr</a></td>';
									echo '</tr>';
								}
							?>
						</tbody>
						</table>
                    </div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>