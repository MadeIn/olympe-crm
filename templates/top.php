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
                <a id="index" class="page-logo" href="/home">
                    <img src="/assets/images//logo-olympe.png" alt="Logo Olympe Mariage"> 
                    <?php 
                    if ($u && $u->mGroupe == 0) {
                        echo ' - <span style="color:#fff;font-size:20px;margin-left:20px;">ADMINISTRATION</span>'; 
                    } elseif ($u && isset($u->mShowroomInfo["showroom_ville"])) {
                        echo ' <span style="color:#fff;font-size:20px;margin-left:20px;">Showroom ' . h($u->mShowroomInfo["showroom_ville"]) . '</span>';
                    }
                    ?>    
                </a>
                <!-- END LOGO -->
                
                <!-- BEGIN TOPBAR ACTIONS -->
                <div class="topbar-actions">
                    <!-- BEGIN USER PROFILE -->
                    <div class="btn-group-img btn-group">
                        <button type="button" class="btn btn-sm md-skip dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span>Bonjour, <?= h(($u->mPrenom ?? '') . " " . ($u->mNom ?? '')) ?></span>
                            <?php if ($u && !empty($u->mPhoto)): ?>
                                <img src="/photos/users/<?= h($u->mPhoto) ?>" alt="Photo de profil">
                            <?php else: ?>
                                <img src="/assets/layouts/layout2/img/avatar.png" alt="Avatar par défaut">
                            <?php endif; ?>
                        </button>
                        
                        <ul class="dropdown-menu-v2" role="menu">
                            <?php if ($u && $u->mGroupe != 0): ?>
                                <li>
                                    <a href="/calendrier/">
                                        <i class="icon-calendar"></i> Mon agenda
                                    </a>
                                </li>
                                <li class="divider"></li>
                            <?php endif; ?>
                            <li>
                                <a href="/email/">
                                    <i class="icon-envelope"></i> Mes emails envoyés
                                </a>
                            </li>
                            <li>
                                <a href="/logout">
                                    <i class="icon-key"></i> Déconnexion
                                </a>
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
                    
                    <!-- Dashboard -->
                    <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/home') ?>">
                        <a href="/home" class="text-uppercase">
                            <i class="icon-home"></i> Dashboard
                        </a>
                        <ul class="dropdown-menu dropdown-menu-fw">
                            <li>
                                <a href="/home">
                                    <i class="fa fa-clone"></i> Accueil
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Calendrier -->
                    <?php if ($u && $u->mGroupe != 0): ?>
                        <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/calendrier/') ?>">
                            <a href="/calendrier/" class="text-uppercase">
                                <i class="fa fa-calendar"></i> Mon calendrier
                            </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li>
                                    <a href="/calendrier/rendez-vous">
                                        <i class="fa fa-list-alt"></i> Prendre Rendez-vous
                                    </a>
                                </li>
                                <li>
                                    <a href="/calendrier/">
                                        <i class="fa fa-list-alt"></i> Consultation
                                    </a>
                                </li>
                                <li>
                                    <a href="/calendrier/liste">
                                        <i class="fa fa-calendar-plus-o"></i> Gestion des RDV
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/calendrier/') ?>">
                            <a href="/calendrier/showroom" class="text-uppercase">
                                <i class="fa fa-calendar"></i> RDV / Showroom
                            </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li>
                                    <a href="/calendrier/showroom">
                                        <i class="fa fa-list-alt"></i> Consultation
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Clients -->
                    <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/clients/') ?>">
                        <a href="javascript:;" class="text-uppercase">
                            <i class="fa fa-group"></i> Clients
                        </a>
                        <ul class="dropdown-menu dropdown-menu-fw">
                            <?php if ($u && $u->mGroupe != 0): ?>
                                <li>
                                    <a href="/clients/creation">
                                        <i class="fa fa-plus"></i> Création
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="/clients/liste">
                                    <i class="fa fa-list"></i> Consultation
                                </a>
                            </li>
                            <li>
                                <a href="/clients/relance">
                                    <i class="fa fa-envelope"></i> Relance
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Tableau de bord -->
                    <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/dashboard/') ?>">
                        <a href="/dashboard/" class="text-uppercase">
                            <i class="fa fa-group"></i> Tableau de bord
                        </a>
                    </li>
                    
                    <!-- Produits -->
                    <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/produits/') ?>">
                        <a href="javascript:;" class="text-uppercase">
                            <i class="fa fa-female"></i> Produits
                        </a>
                        <ul class="dropdown-menu dropdown-menu-fw">
                            <li><a href="/produits/produit"><i class="fa fa-plus"></i> Création</a></li>
                            <li><a href="/produits/liste"><i class="fa fa-list"></i> Consultation</a></li>
                            <li><a href="/produits/categories"><i class="fa fa-bookmark-o"></i> Catégories</a></li>
                            <li><a href="/produits/marques"><i class="fa fa-fire"></i> Marques</a></li>
                            <li><a href="/produits/tailles"><i class="fa fa-black-tie"></i> Tailles</a></li>
                            <li><a href="/produits/commandes"><i class="fa fa-industry"></i> Commandes Fournisseurs</a></li>
                            <li><a href="/produits/paiements-fournisseurs"><i class="fa fa-euro"></i> Paiements Fournisseurs</a></li>
                        </ul>
                    </li>
                    
                    <!-- Comptabilité -->
                    <?php if ($u && $u->mCompta == 1): ?>
                        <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/compta/') ?>">
                            <a href="/comptabilite/index" class="text-uppercase">
                                <i class="icon-layers"></i> Comptabilité
                            </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li>
                                    <a href="/comptabilite/index">
                                        <i class="fa fa-pie-chart"></i> Tableau de bord
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Statistiques -->
                    <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/stats/') ?>">
                        <a href="javascript:;" class="text-uppercase">
                            <i class="fa fa-area-chart"></i> Statistiques
                        </a>
                        <ul class="dropdown-menu dropdown-menu-fw">
                            <li><a href="/stats/index"><i class="fa fa-pie-chart"></i> Tableau de bord</a></li>
                            <li><a href="/stats/vente-objectif"><i class="fa fa-euro"></i> Ventes / Objectif / RDV / Mois</a></li>
                            <li><a href="/stats/rdv_conseillere"><i class="fa fa-black-tie"></i> Taux de transformation vendeuse</a></li>
                            <li><a href="/stats/categorie"><i class="fa fa-pie-chart"></i> Stats par catégorie</a></li>
                            
                            <?php if ($u && $u->mGroupe == 0): ?>    
                                <li><a href="/stats/dashboard"><i class="fa fa-black-tie"></i> Dashboard annuel</a></li>
                                <li><a href="/stats/email"><i class="fa fa-envelope"></i> Extraction email</a></li>
                                <li><a href="/stats/addresses"><i class="fa fa-envelope"></i> Adresses clientes</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <!-- Administration -->
                    <?php if ($u && $u->mGroupe == 0): ?>
                        <li class="dropdown dropdown-fw dropdown-fw-disabled <?= isActiveMenu('/admin/') ?>">
                            <a href="javascript:;" class="text-uppercase">
                                <i class="fa fa-cogs"></i> Administration
                            </a>
                            <ul class="dropdown-menu dropdown-menu-fw">
                                <li><a href="/admin/users"><i class="fa fa-user"></i> Utilisateurs</a></li>
                                <li><a href="/admin/showroom"><i class="fa fa-industry"></i> Showroom</a></li>
                                
                                <?php if ($u->mCompta == 1): ?>
                                    <li><a href="/admin/showroom-objectif?genre=0"><i class="fa fa-industry"></i> Showroom objectif Femme</a></li>
                                    <li><a href="/admin/showroom-objectif?genre=1"><i class="fa fa-industry"></i> Showroom objectif Homme</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- END HEADER MENU -->
        </div>
    </nav>
</header>
<!-- END HEADER -->
