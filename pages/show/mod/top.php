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
				<a id="index" class="page-logo" href="/show/index.php">
					<img src="/assets/images/logo-olympe.png" alt="Logo Olympe Mariage"> 
				<?php if ($u->mGroupe==0) 
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
							<span>Bonjour, <?php echo $u->mPrenom . " " . $u->mNom ?></span>
							<?php if ($u->mPhoto!="") { ?>
								<img src="/photos/users/<?php echo $u->mPhoto ?>" alt=""> </button>
							<?php } else { ?>
								<img src="/assets/layouts/layout2/img/avatar.png" alt=""> </button>
							<?php } ?>
						<ul class="dropdown-menu-v2" role="menu">
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
					<li class="dropdown dropdown-fw dropdown-fw-disabled <?php if (strstr($_SERVER["PHP_SELF"],"/show/index.php")) { echo "active open selected"; } ?>">
						<a href="/show/index.php" class="text-uppercase">
							<i class="icon-home"></i> Calendrier </a>
					</li>
					<li class="dropdown dropdown-fw dropdown-fw-disabled <?php if (strstr($_SERVER["PHP_SELF"],"/show/dashboard/")) { echo "active open selected"; } ?>">
						<a href="/show/dashboard/" class="text-uppercase">
							<i class="fa fa-group"></i> Tableau de bord </a>
					</li>
				</ul>
			</div>
			<!-- END HEADER MENU -->
		</div>
		<!--/container-->
	</nav>
</header>
<!-- END HEADER -->