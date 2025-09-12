<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Page Introuvable - Olympe Mariage";
$desc_page = "Page Introuvable - Olympe Mariage";
$link_plugin = '<link href="/assets/pages/css/error.min.css" rel="stylesheet" type="text/css" />';
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
                            <li class="active">Page introuvable</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <!-- END PAGE SIDEBAR -->
						<div class="page-content-col">
							<!-- BEGIN PAGE BASE CONTENT -->
							<div class="row">
								<div class="col-md-12 page-404">
									<div class="number font-green"> 404 </div>
									<div class="details">
										<h3>Oops! Vous êtes perdu.</h3>
										<p> La page que vous cherchez est introuvable ou a été déplacé.
											<br/>
											<a href="/home.php"> Retournez à l'accueil </a> ou essayer une autre URL. </p>
									</div>
								</div>
							</div>
							<!-- END PAGE BASE CONTENT -->
						</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>