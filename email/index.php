<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Dashboard - Olympe Mariage";
$desc_page = "Dashboard - Olympe Mariage";
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
                                <a href="#">Accueil</a>
                            </li>
                            <li class="active">Mes emails envoyés</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="panel-group accordion" id="accordion1">
						<?
							$sql = "select * from mails m, clients c where m.client_num=c.client_num and m.user_num='" . $u->mNum . "' order by mail_date DESC LIMIT 0,50";
							$cc = mysql_query($sql);
							$i=1;
							while ($rcc=mysql_fetch_array($cc)) {
								$date = format_date($rcc["mail_date"],7,1);
								if ($date==Date("Y-m-d")) {
									$affiche_date = format_date($rcc["mail_date"],7,1);
								} else {
									$affiche_date = format_date($rcc["mail_date"],10,1);
								}
								
								echo '<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="row panel-title">
											<a class="accordion-toggle" style="line-height:32px" data-toggle="collapse" data-parent="#accordion1" href="#collapse_' . $i . '"> 
												<span class="col-md-1 col-sm-1 col-xs-1">' . $affiche_date . '</span>
												<span class="col-md-3 col-sm-3 col-xs-3"><strong>' . $rcc["client_mail"] . '</strong></span>
												<span class="col-md-3 col-sm-3 col-xs-3">' . $rcc["client_prenom"] . ' ' . $rcc["client_nom"] . '</span>
												<span class="col-md-5 col-sm-5 col-xs-5"><strong>' . $rcc["mail_titre"] . '</strong></span>
											</a>
										</h4>
									</div>
									<div id="collapse_' . $i . '" class="panel-collapse collapse">
										<div class="panel-body text-center" style="height:350px; overflow-y:auto;">
											' . $rcc["mail_message"] . '
										</div>
									</div>
								</div>';
								$i++;
							}
						?>
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