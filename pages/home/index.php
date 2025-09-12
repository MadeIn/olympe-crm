<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";

$titre_page = "Dashboard - Olympe Mariage";
$desc_page = "Dashboard - Olympe Mariage";

// Vérification des permissions et initialisation des données utilisateur
if ($u->mGroupe == 0) {
    if (!isset($showroom_choix)) {
        $u->mShowroom = 1; // Si on est admin par défaut c'est Montpellier 
    } else {
        $u->mShowroom = $showroom_choix;
    }
    
    // Utilisation de la nouvelle classe Database
    $sql = "SELECT * FROM showrooms WHERE showroom_num = ?";
    $showroom_data = $base->queryRow($sql, [$u->mShowroom]);
    if ($showroom_data) {
        $u->mShowroomInfo = $showroom_data;
    }
}

// Calcul des dates pour l'année fiscale (septembre à août)
$mois_deb = 8;
$mois_encours = date("n");
if ($mois_encours < 9) {
    $annee_deb = date("Y") - 1;
} else {
    $annee_deb = date("Y");
}

// Initialisation des objectifs
$objectif_nbr_total = 0;
$objectif_ca_total = 0;
$objectif = [];
$objectif_nbr = [];
$objectif_homme = [];
$objectif_homme_nbr = [];

for ($i = 0; $i < 12; $i++) {
    $mois_deb++;
    if ($mois_deb == 13) {
        $mois_deb = 1;
        $annee_deb = $annee_deb + 1;
    }
    
    // Objectifs Femme
    $sql = "SELECT * FROM showrooms_objectifs WHERE showroom_num = ? AND genre_num = 0 AND mois = ? AND annee = ?";
    $objectif_data = $base->queryRow($sql, [$u->mShowroom, $mois_deb, $annee_deb]);
    
    if ($objectif_data) {
        $objectif[$mois_deb] = $objectif_data["ca"];
        $objectif_nbr[$mois_deb] = $objectif_data["nbr"];
        $objectif_nbr_total += $objectif_data["nbr"];
        $objectif_ca_total += $objectif_data["ca"];
    } else {
        $objectif[$mois_deb] = 0;
        $objectif_nbr[$mois_deb] = 0;
    }
    
    // Objectifs Homme
    $sql = "SELECT * FROM showrooms_objectifs WHERE showroom_num = ? AND genre_num = 1 AND mois = ? AND annee = ?";
    $objectif_homme_data = $base->queryRow($sql, [$u->mShowroom, $mois_deb, $annee_deb]);
    
    if ($objectif_homme_data) {
        $objectif_homme[$mois_deb] = $objectif_homme_data["ca"];
        $objectif_homme_nbr[$mois_deb] = $objectif_homme_data["nbr"];
    } else {
        $objectif_homme[$mois_deb] = 0;
        $objectif_homme_nbr[$mois_deb] = 0;
    }
}

$u->mShowroomInfo["nbr_annee"] = $objectif_nbr_total;
$u->mShowroomInfo["ca_annee"] = $objectif_ca_total;

// Tableau des jours par mois (pour les calculs de dates)
$mois_jour = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

// Calcul du CA du mois actuel
$date_debut = date("Y-m") . "-01 00:00:00";
$date_fin = date("Y-m") . "-" . $mois_jour[date("n")] . " 23:59:59";

$sql = "SELECT c.id FROM commandes c WHERE commande_num != 0 AND commande_date >= ? AND commande_date <= ? AND c.showroom_num = ?";
$commandes_mois = $base->query($sql, [$date_debut, $date_fin, $u->mShowroom]);

$ca_mois = 0;
$nbr_commande_mois = count($commandes_mois);

foreach ($commandes_mois as $commande) {
    $ca_mois += montantCommandeHT($commande["id"]);
}

// Calcul du CA robes du mois
$sql = "SELECT cp.*, p.categorie_num FROM commandes c 
        INNER JOIN commandes_produits cp ON c.id = cp.id 
        INNER JOIN md_produits p ON cp.produit_num = p.produit_num 
        WHERE p.categorie_num IN (11, 25, 27) AND c.commande_num != 0 
        AND c.commande_date >= ? AND c.commande_date <= ? AND c.showroom_num = ?";

$produits_robes_mois = $base->query($sql, [$date_debut, $date_fin, $u->mShowroom]);

$ca_mois_robe = 0;
$nbr_mois_robe = 0;

foreach ($produits_robes_mois as $produit) {
    $prix_produit = $produit["montant_ht"];
    if ($produit["montant_ht_remise"] != 0) {
        $prix_produit = $produit["montant_ht_remise"];
    }
    
    $prix_produit = $prix_produit * $produit["qte"];
    
    switch ($produit["commande_produit_remise_type"]) {
        case 1: // Remise en %
            $prix_produit = $prix_produit * (1 - ($produit["commande_produit_remise"] / 100));
            break;
        case 2: // Remise en euro
            $prix_produit = $prix_produit - $produit["commande_produit_remise"];
            break;
    }
    
    if ($produit["categorie_num"] == 11) {
        $nbr_mois_robe += $produit["qte"];
    }
    
    $ca_mois_robe += $prix_produit;
}

// Calcul des pourcentages d'objectifs
$objectif_ca = 0;
$objectif_nbr_robe = 0;

if ($objectif[date("n")] > 0) {
    $objectif_ca = ($ca_mois_robe / $objectif[date("n")]);
    if ($objectif_ca > 1) $objectif_ca = 1;
}

if ($objectif_nbr[date("n")] > 0) {
    $objectif_nbr_robe = ($nbr_mois_robe / $objectif_nbr[date("n")]);
    if ($objectif_nbr_robe > 1) $objectif_nbr_robe = 1;
}

$objectif_ca = number_format($objectif_ca * 100, 0);
$objectif_nbr_robe = number_format($objectif_nbr_robe * 100, 0);

?>

<?php include TEMPLATE_PATH . 'head.php'; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo">
    <!-- BEGIN CONTAINER -->
    <div class="wrapper">
        <?php include TEMPLATE_PATH . 'top.php'; ?>
        <div class="container-fluid">
            <div class="page-content">
                <!-- BEGIN BREADCRUMBS -->
                <?php if ($u->mGroupe != 0) { ?>
                    <div class="breadcrumbs">
                        <h1>Olympe Mariage - <?php echo h(format_date(date("Y-m-d"), 13)); ?></h1>
                        <ol class="breadcrumb">
                            <li><a href="#">Accueil</a></li>
                            <li class="active">Dashboard</li>
                        </ol>
                    </div>
                <?php } else { ?>
                    <form name="choix" action="<?php echo h($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="breadcrumbs">
                            <h1>Olympe Mariage 
                                <select name="showroom_choix" onChange="this.form.submit()" class="form-inline">
                                    <?php
                                    $sql = "SELECT * FROM showrooms ORDER BY showroom_num ASC";
                                    $showrooms = $base->query($sql);
                                    
                                    foreach ($showrooms as $showroom) {
                                        $selected = ($showroom["showroom_num"] == $u->mShowroom) ? " SELECTED" : "";
                                        echo '<option value="' . h($showroom["showroom_num"]) . '"' . $selected . '>' . h($showroom["showroom_ville"]) . '</option>';
                                    }
                                    ?>
                                </select>
                                - <?php echo h(format_date(date("Y-m-d"), 13)); ?>
                            </h1>
                            <ol class="breadcrumb">
                                <li><a href="#">Accueil</a></li>
                                <li class="active">Dashboard</li>
                            </ol>
                        </div>
                    </form>
                <?php } ?>
                <!-- END BREADCRUMBS -->
                
                <!-- BEGIN PAGE BASE CONTENT -->
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-green-sharp">
                                        <span data-counter="counterup" data-value="<?php echo number_format($ca_mois, 2); ?>">0</span>
                                        <small class="font-green-sharp">€</small>
                                    </h3>
                                    <small>CA HT du mois</small>
                                </div>
                                <div class="icon">
                                    <i class="icon-credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-red-haze">
                                        <span data-counter="counterup" data-value="<?php echo $nbr_commande_mois; ?>">0</span>
                                    </h3>
                                    <small>Nombre de commande du mois</small>
                                </div>
                                <div class="icon">
                                    <i class="icon-basket"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-blue-sharp">
                                        <span data-counter="counterup" data-value="<?php echo number_format($ca_mois_robe, 2); ?>">0</span>
                                        <small class="font-blue-sharp">€</small>
                                    </h3>
                                    <small>CA HT Vente de robes du mois</small>
                                </div>
                                <div class="icon">
                                    <i class="icon-basket-loaded"></i>
                                </div>
                            </div>
                            <div class="progress-info">
                                <div class="progress">
                                    <span style="width: <?php echo $objectif_ca; ?>%;" class="progress-bar progress-bar-success blue-sharp">
                                        <span class="sr-only"><?php echo $objectif_ca; ?>% objectif</span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title">objectif</div>
                                    <div class="status-number"><?php echo $objectif_ca; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-purple-soft">
                                        <span data-counter="counterup" data-value="<?php echo $nbr_mois_robe; ?>">0</span>
                                    </h3>
                                    <small>Nbr de robes vendues du mois</small>
                                </div>
                                <div class="icon">
                                    <i class="icon-user-female"></i>
                                </div>
                            </div>
                            <div class="progress-info">
                                <div class="progress">
                                    <span style="width: <?php echo $objectif_nbr_robe; ?>%;" class="progress-bar progress-bar-success purple-soft">
                                        <span class="sr-only"><?php echo $objectif_nbr_robe; ?>% objectif</span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title">objectif</div>
                                    <div class="status-number"><?php echo $objectif_nbr_robe; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div id="calendar" class="has-toolbar"></div>
                        <hr>
                        
                        <?php if ($u->mCompta == 1) { 
                            // Calculs pour les statistiques annuelles
                            $mois_deb_annee = 8;
                            $mois_encours = date("n");
                            if ($mois_encours < 9) {
                                $annee_deb_calc = date("Y") - 1;
                            } else {
                                $annee_deb_calc = date("Y");
                            }
                            
                            $annee_fin = $annee_deb_calc + 1;
                            $date_debut_annee = $annee_deb_calc . "-09-01 00:00:00";
                            $date_fin_annee = $annee_fin . "-08-31 23:59:59";
                            
                            // CA annuel robes
                            $sql = "SELECT cp.*, p.categorie_num FROM commandes c 
                                    INNER JOIN commandes_produits cp ON c.id = cp.id 
                                    INNER JOIN md_produits p ON cp.produit_num = p.produit_num 
                                    WHERE p.categorie_num IN (11, 25, 27) AND c.commande_num != 0 
                                    AND c.commande_date >= ? AND c.commande_date <= ? AND c.showroom_num = ?";
                            
                            $produits_annee = $base->query($sql, [$date_debut_annee, $date_fin_annee, $u->mShowroom]);
                            
                            $ca_annee_robe = 0;
                            $nbr_annee_robe = 0;
                            $commandes_traitees = [];
                            
                            foreach ($produits_annee as $produit) {
                                $prix_produit = $produit["montant_ht"];
                                if ($produit["montant_ht_remise"] != 0) {
                                    $prix_produit = $produit["montant_ht_remise"];
                                }
                                
                                $prix_produit = $prix_produit * $produit["qte"];
                                
                                switch ($produit["commande_produit_remise_type"]) {
                                    case 1:
                                        $prix_produit = $prix_produit * (1 - ($produit["commande_produit_remise"] / 100));
                                        break;
                                    case 2:
                                        $prix_produit = $prix_produit - $produit["commande_produit_remise"];
                                        break;
                                }
                                
                                if (!in_array($produit["id"], $commandes_traitees)) {
                                    $nbr_annee_robe += $produit["qte"];
                                    $commandes_traitees[] = $produit["id"];
                                }
                                
                                $ca_annee_robe += $prix_produit;
                            }
                            
                            $objectif_ca_annee = 0;
                            if ($u->mShowroomInfo["ca_annee"] > 0) {
                                $objectif_ca_annee = ($ca_annee_robe / $u->mShowroomInfo["ca_annee"]);
                                if ($objectif_ca_annee > 1) $objectif_ca_annee = 1;
                                $objectif_ca_annee = number_format($objectif_ca_annee * 100, 0);
                            }
                            ?>
                            
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="dashboard-stat2 bordered">
                                        <div class="display">
                                            <div class="number">
                                                <h3 class="font-green-sharp">
                                                    <span data-counter="counterup" data-value="<?php echo number_format($ca_annee_robe, 2); ?>">0</span>
                                                    <small class="font-green-sharp">€</small>
                                                </h3>
                                                <small>CA HT Robes de l'année</small>
                                            </div>
                                            <div class="number">
                                                <h3 class="font-red-soft">
                                                    <span style="margin-left:30px;margin-right:30px;">/</span> 
                                                    <span data-counter="counterup" data-value="<?php echo number_format($u->mShowroomInfo["ca_annee"], 2); ?>">0</span>
                                                    <small class="font-red-soft">€</small>
                                                </h3>
                                                <span style="margin-left:50px;margin-right:30px;"></span><small>Objectif annuel</small>
                                            </div>
                                            <div class="icon">
                                                <i class="icon-credit-card"></i>
                                            </div>
                                        </div>
                                        <div class="progress-info">
                                            <div class="progress">
                                                <span style="width: <?php echo $objectif_ca_annee; ?>%;" class="progress-bar progress-bar-success blue-sharp">
                                                    <span class="sr-only"><?php echo $objectif_ca_annee; ?>% objectif</span>
                                                </span>
                                            </div>
                                            <div class="status">
                                                <div class="status-title">objectif</div>
                                                <div class="status-number"><?php echo $objectif_ca_annee; ?>%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject font-dark bold uppercase">C.A. / Objectifs - Vente de robes</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="dashboard_amchart_3" class="CSSAnimationChart"></div>
                            </div>
                        </div>
                        
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject font-dark bold uppercase">Nbr / Objectifs - Vente de robes</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="dashboard_amchart_2" class="CSSAnimationChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PAGE BASE CONTENT -->
            </div>
            
            <?php include TEMPLATE_PATH . 'footer.php'; ?>
        </div>
    </div>
    
    <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    
    <!-- Scripts pour les graphiques et le calendrier -->
    <script src="/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/fullcalendar/lang/fr.js" type="text/javascript"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    
    <?php
    // Génération des données pour les graphiques
    echo '<script type="text/javascript">';
    echo 'var chartData = [];';
    echo 'var chartDataNbr = [];';
    
    // Réinitialisation des variables pour la boucle des graphiques
    $mois_deb = 8;
    $mois_encours = date("n");
    if ($mois_encours < 9) {
        $annee_deb = date("Y") - 1;
    } else {
        $annee_deb = date("Y");
    }
    
    for ($i = 0; $i < 12; $i++) {
        $mois_deb++;
        if ($mois_deb == 13) {
            $mois_deb = 1;
            $annee_deb = $annee_deb + 1;
        }
        
        $abscisse_mois = $mois_deb . "/" . $annee_deb;
        $date_debut = $annee_deb . "-" . sprintf("%02d", $mois_deb) . "-01 00:00:00";
        $date_fin = $annee_deb . "-" . sprintf("%02d", $mois_deb) . "-" . $mois_jour[$mois_deb] . " 23:59:59";
        
        // Requête pour les données du mois
        $sql = "SELECT cp.*, p.categorie_num FROM commandes c 
                INNER JOIN commandes_produits cp ON c.id = cp.id 
                INNER JOIN md_produits p ON cp.produit_num = p.produit_num 
                WHERE p.categorie_num IN (11, 25, 27) AND c.commande_num != 0 
                AND c.commande_date >= ? AND c.commande_date <= ? AND c.showroom_num = ?";
        
        $produits_mois = $base->query($sql, [$date_debut, $date_fin, $u->mShowroom]);
        
        $ca_mois_graph = 0;
        $nbr_mois_graph = 0;
        
        foreach ($produits_mois as $produit) {
            $prix_produit = $produit["montant_ht"];
            if ($produit["montant_ht_remise"] != 0) {
                $prix_produit = $produit["montant_ht_remise"];
            }
            
            $prix_produit = $prix_produit * $produit["qte"];
            
            switch ($produit["commande_produit_remise_type"]) {
                case 1:
                    $prix_produit = $prix_produit * (1 - ($produit["commande_produit_remise"] / 100));
                    break;
                case 2:
                    $prix_produit = $prix_produit - $produit["commande_produit_remise"];
                    break;
            }
            
            if ($produit["categorie_num"] == 11) {
                $nbr_mois_graph += $produit["qte"];
            }
            
            $ca_mois_graph += $prix_produit;
        }
        
        echo 'chartData.push({mois:"' . $abscisse_mois . '", income:' . number_format($ca_mois_graph, 2, '.', '') . ', expenses:' . ($objectif[$mois_deb] ?? 0) . '});';
        echo 'chartDataNbr.push({mois:"' . $abscisse_mois . '", income:' . $nbr_mois_graph . ', expenses:' . ($objectif_nbr[$mois_deb] ?? 0) . '});';
    }
    ?>
    
    // Initialisation des graphiques AmCharts
    AmCharts.makeChart("dashboard_amchart_3", {
        type: "serial",
        addClassNames: true,
        theme: "light",
        autoMargins: false,
        marginLeft: 55,
        marginRight: 8,
        marginTop: 10,
        marginBottom: 26,
        balloon: {
            adjustBorderColor: false,
            horizontalPadding: 10,
            verticalPadding: 8,
            color: "#ffffff"
        },
        dataProvider: chartData,
        valueAxes: [{
            axisAlpha: 0,
            position: "left"
        }],
        startDuration: 1,
        graphs: [{
            alphaField: "alpha",
            balloonText: "<span style='font-size:12px;'>[[title]] [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            fillAlphas: 1,
            title: "CA",
            type: "column",
            valueField: "income",
            dashLengthField: "dashLengthColumn"
        }, {
            id: "graph2",
            balloonText: "<span style='font-size:12px;'>[[title]] [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            bullet: "round",
            lineThickness: 3,
            bulletSize: 7,
            bulletBorderAlpha: 1,
            bulletColor: "#FFFFFF",
            useLineColorForBulletBorder: true,
            bulletBorderThickness: 3,
            fillAlphas: 0,
            lineAlpha: 1,
            title: "Objectif",
            valueField: "expenses"
        }],
        categoryField: "mois",
        categoryAxis: {
            gridPosition: "start",
            axisAlpha: 0,
            tickLength: 0
        },
        "export": {
            enabled: true
        }
    });
    
    AmCharts.makeChart("dashboard_amchart_2", {
        type: "serial",
        addClassNames: true,
        theme: "light",
        autoMargins: false,
        marginLeft: 55,
        marginRight: 8,
        marginTop: 10,
        marginBottom: 26,
        balloon: {
            adjustBorderColor: false,
            horizontalPadding: 10,
            verticalPadding: 8,
            color: "#ffffff"
        },
        dataProvider: chartDataNbr,
        valueAxes: [{
            axisAlpha: 0,
            position: "left"
        }],
        startDuration: 1,
        graphs: [{
            alphaField: "alpha",
            balloonText: "<span style='font-size:12px;'>[[title]] [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            fillAlphas: 1,
            title: "Nbr",
            type: "column",
            valueField: "income",
            dashLengthField: "dashLengthColumn"
        }, {
            id: "graph2",
            balloonText: "<span style='font-size:12px;'>[[title]] [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            bullet: "round",
            lineThickness: 3,
            bulletSize: 7,
            bulletBorderAlpha: 1,
            bulletColor: "#FFFFFF",
            useLineColorForBulletBorder: true,
            bulletBorderThickness: 3,
            fillAlphas: 0,
            lineAlpha: 1,
            title: "Objectif",
            valueField: "expenses"
        }],
        categoryField: "mois",
        categoryAxis: {
            gridPosition: "start",
            axisAlpha: 0,
            tickLength: 0
        },
        "export": {
            enabled: true
        }
    });
    </script>
</body>
</html>