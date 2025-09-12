<!-- BEGIN HEADER -->
<header class="page-header">
	<nav class="navbar mega-menu" role="navigation">
		<div class="container-fluid">
			<div class="clearfix navbar-fixed-top">
				<!-- Brand and toggle get grouped for better mobile display -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="toggle-icon">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</span>
				</button>
				<!-- End Toggle Button -->
				<!-- BEGIN LOGO -->
				<a id="index" class="page-logo" href="/home.php">
					<img src="/images/logo-olympe.png" alt="Logo Olympe Mariage"> 
				<? if ($u->mGroupe==0) 
						echo ' - <span style="color:#fff;font-size:20px;margin-left:20px;">ADMINISTRATION</span>'; 
					else
						echo ' <span style="color:#fff;font-size:20px;margin-left:20px;">Showroom ' . $u->mShowroomInfo["showroom_ville"] . '</span>';
				?>	
				</a>
				<!-- END LOGO -->
				<!-- BEGIN TOPBAR ACTIONS -->
				<div class="topbar-actions">
					<!-- BEGIN USER PROFILE -->
					<div class="btn-group-img btn-group">
						<button type="button" class="btn btn-sm md-skip dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<span>Bonjour, <? echo $u->mPrenom . " " . $u->mNom ?></span>
							<? if ($u->mPhoto!="") { ?>
								<img src="/photos/users/<? echo $u->mPhoto ?>" alt=""> </button>
							<? } else { ?>
								<img src="/assets/layouts/layout2/img/avatar.png" alt=""> </button>
							<? } ?>
						<ul class="dropdown-menu-v2" role="menu">
							<? if ($u->mGroupe!=0) { ?>
								<li>
									<a href="/calendrier/">
										<i class="icon-calendar"></i> Mon agenda </a>
								</li>
								<li class="divider"> </li>
							<? } ?>
							<li>
								<a href="/email/">
									<i class="icon-envelope"></i> Mes emails envoyés </a>
							</li>
							<li>
								<a href="/deconnect.php">
									<i class="icon-key"></i> Deconnexion </a>
							</li>
						</ul>
					</div>
					<!-- END USER PROFILE -->
				</div>
				<!-- END TOPBAR ACTIONS -->
			</div>
			<!-- BEGIN HEADER MENU -->
			<div class="nav-collapse collapse navbar-collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li class="dropdown dropdown-fw dropdown-fw-disabled <? if ($_SERVER["PHP_SELF"]=="/home.php") { echo "active open selected"; } ?>">
						<a href="/home.php" class="text-uppercase">
							<i class="icon-home"></i> Dashboard </a>
							<ul class="dropdown-menu dropdown-menu-fw">
								<li>
									<a href="/home.php">
										<i class="fa fa-clone"></i> Accueil </a>
								</li>
							</ul>
					</li>
					<? if ($u->mGroupe!=0) { ?>
						<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/calendrier/")) { echo "active open selected"; } ?>">
							<a href="/calendrier/" class="text-uppercase">
								<i class="fa fa-calendar"></i> Mon calendrier </a>
							<ul class="dropdown-menu dropdown-menu-fw">
								<li>
									<a href="/calendrier/rendez-vous.php">
										<i class="fa fa-list-alt"></i> Prendre Rendez vous </a>
								</li>
								<li>
									<a href="/calendrier/">
										<i class="fa fa-list-alt"></i> Consultation </a>
								</li>
								<li>
									<a href="/calendrier/liste.php">
										<i class="fa fa-calendar-plus-o"></i> Gestion des RDV </a>
								</li>
							</ul>
						</li>
					<? } else { ?>
						<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/calendrier/")) { echo "active open selected"; } ?>">
							<a href="/calendrier/showroom.php" class="text-uppercase">
								<i class="fa fa-calendar"></i> RDV / Showroom </a>
							<ul class="dropdown-menu dropdown-menu-fw">
								<li>
									<a href="/calendrier/showroom.php">
										<i class="fa fa-list-alt"></i> Consultation </a>
								</li>
							</ul>
						</li>
					<? } ?>
					
					<li class="dropdown dropdown-fw dropdown-fw-disabled <? if (strstr($_SERVER["PHP_SELF"],"/clients/")) { echo "active open selected"; } ?>">
						<a href="javascript:;" class="text-uppercase">
							<i class="fa fa-group"></i> Clients </a>
						<ul class="dropdown-menu dropdown-menu-fw">
						<? if ($u->mGroupe!=0) { ?>
							<li>
								<a href="/clients/creation.php">
									<i class="fa fa-plus"></i> Création </a>
							</li>
						<? } ?>
							<li>
								<a href="/clients/liste.php">
									<i class="fa fa-list"></i> Consultation </a>
							</li>
							<li>
								<a href="/clients/relance.php">
									<i class="fa fa-envelope"></i> Relance </a>
							</li>
						</ul>
					</li>
					<li class="dropdown dropdown-fw dropdown-fw-disabled <? if (strstr($_SERVER["PHP_SELF"],"/dashboard/")) { echo "active open selected"; } ?>">
						<a href="/dashboard/" class="text-uppercase">
							<i class="fa fa-group"></i> Tableau de bord </a>
					</li>
					<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/produits/")) { echo "active open selected"; } ?>">
						<a href="javascript:;" class="text-uppercase">
							<i class="fa fa-female"></i> Produits </a>
						<ul class="dropdown-menu dropdown-menu-fw">
							<li>
								<a href="/produits/produit.php"> 
									<i class="fa fa-plus"></i> Création </a>
							</li>
							<li>
								<a href="/produits/liste.php"> 
									<i class="fa fa-list"></i> Consultation </a>
							</li>
							<li>
								<a href="/produits/categories.php"> 
									<i class="fa fa-bookmark-o"></i> Catégories </a>
							</li>
							<li>
								<a href="/produits/marques.php"> 
									<i class="fa fa-fire"></i> Marques </a>
							</li>
							<li>
								<a href="/produits/tailles.php"> 
									<i class="fa fa-black-tie"></i> Tailles </a>
							</li>
							<li>
								<a href="/produits/commandes.php"> 
									<i class="fa fa-industry"></i> Commandes Fournisseurs </a>
							</li>
							<li>
								<a href="/produits/paiements-fournisseurs.php"> 
									<i class="fa fa-euro"></i> Paiements Fournisseurs </a>
							</li>
						</ul>
					</li>
					<? if ($u->mCompta==1) { ?>
						<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/compta/")) { echo "active open selected"; } ?>">
							<a href="/comptabilite/index.php" class="text-uppercase">
								<i class="icon-layers"></i> Comptabilité </a>
							<ul class="dropdown-menu dropdown-menu-fw">
								<li>
									<a href="/comptabilite/index.php">
										<i class="fa fa-pie-chart"></i> Tableau de bord </a>
								</li>
							</ul>
						</li>
					<? } ?>
					<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/stats/")) { echo "active open selected"; } ?>">
						<a href="javascript:;" class="text-uppercase">
							<i class="fa fa-area-chart"></i> Statistiques </a>
						<ul class="dropdown-menu dropdown-menu-fw">
							<li>
								<a href="/stats/index.php">
									<i class="fa fa-pie-chart"></i> Tableau de bord </a>
							</li>
							<li>
								<a href="/stats/vente-objectif.php">
									<i class="fa fa-euro"></i>  Ventes / Objectif / RDV / Mois</a>
							</li>
							<li>
								<a href="/stats/rdv_conseillere.php">
									<i class="fa fa-black-tie"></i>  Taux de transformation vendeuse</a>
							</li>
							<li>
								<a href="/stats/categorie.php">
									<i class="fa fa-pie-chart"></i>  Stats par catégorie</a>
							</li>
						<? if ($u->mGroupe==0) { ?>	
							<li>
								<a href="/stats/dashboard.php">
									<i class="fa fa-black-tie"></i>  Dashboard annuel</a>
							</li>
							<li>
								<a href="/stats/email.php">
									<i class="fa fa-envelope"></i> Extraction email </a>
							</li>
							<li>
								<a href="/stats/addresses.php">
									<i class="fa fa-envelope"></i> Addresses clientes </a>
							</li>
						<? } ?>
						</ul>
					</li>
					<? if ($u->mGroupe==0) { // On est administrateur  ?>
					<li class="dropdown dropdown-fw dropdown-fw-disabled  <? if (strstr($_SERVER["PHP_SELF"],"/admin/")) { echo "active open selected"; } ?>">
						<a href="javascript:;" class="text-uppercase">
							<i class="fa fa-cogs"></i> Administration </a>
						<ul class="dropdown-menu dropdown-menu-fw">
							<li>
								<a href="/admin/users.php">
									<i class="fa fa-user"></i> Utilisateurs </a>
							</li>
							<li>
								<a href="/admin/showroom.php">
									<i class="fa fa-industry"></i> Showroom </a>
							</li>
							<? if ($u->mCompta==1) { ?>
							<li>
								<a href="/admin/showroom-objectif.php?genre=0">
									<i class="fa fa-industry"></i> Showroom objectif Femme</a>
							</li>
							<li>
								<a href="/admin/showroom-objectif.php?genre=1">
									<i class="fa fa-industry"></i> Showroom objectif Homme</a>
							</li>
							<? } ?>
						</ul>
					</li>
					<? } ?>
				</ul>
			</div>
			<!-- END HEADER MENU -->
		</div>
		<!--/container-->
	</nav>
</header>
<!-- END HEADER -->