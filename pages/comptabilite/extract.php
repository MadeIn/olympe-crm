<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Comptabilite - Olympe Mariage";
$desc_page = "Comptabilite - Olympe Mariage";

// On traite le fichier des factures
$param = "Date de facture;Num de facture;Nom cliente;Genre;Montant TTC;Montant HT Robes avant remise;Montant HT Accessoires avant remise;Montant HT Remise\n";

$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and facture_date>='" . $date_debut . "' and facture_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and commande_num!=0 and facture_num!=0 order by facture_date ASC";
$cc = $base->query($sql);
$total_ht = 0;
$total_tva = 0;
$total_ttc = 0;
$total_encaisse = 0;
$total_reste_a_payer = 0;
$nbr_commande = 0;
foreach ($cc as $rcc) {
	$nbr_commande++;
	$nbr_echeance = $rcc["paiement_nombre"];
													
	$montant_ttc = montantCommandeTTC($rcc["id"]);
	$montant_ht = montantCommandeHT($rcc["id"]);
	if ($rcc["client_genre"]==0)
		$genre = "Femme";
	else	
		$genre = "Homme";
	
	// On calcul le tarif Robes
	$montant_ht_robe = 0;
	$sql = "select * from commandes_produits c, md_produits p where c.produit_num=p.produit_num and categorie_num=11 and c.id='" . $rcc["id"] . "'";
	$co = $base->query($sql);
	foreach ($co as $rco) {
		$montant_ht_robe += $rco["montant_ht"];
	}
	
	// On calcul le tarif Accessoires
	$montant_ht_acc = 0;
	$sql = "select * from commandes_produits c, md_produits p where c.produit_num=p.produit_num and categorie_num<>11 and c.id='" . $rcc["id"] . "'";
	$co = $base->query($sql);
	foreach ($co as $rco) {
		$montant_ht_acc += $rco["montant_ht"];
	}
	$param .= format_date($rcc["facture_date"],6,1) . ";" . $rcc["facture_num"] . ";" . $rcc["client_nom"] . " " . $rcc["client_prenom"] . ";" . $genre . ";" . $montant_ttc . ";" . number_format($montant_ht_robe,2,".","") . ";" . number_format($montant_ht_acc,2,".","") . ";" . number_format($montant_ht,2,".","") . "\n";
}

// On enregistre le fichier
$nom_fic_facture = "factures-" . format_date($date_debut,7,1) . "-" . format_date($date_fin,7,1) . "-" . $showroom . ".csv";
$filename= "UPLOAD/" . $nom_fic_facture;
if (!$handle = fopen($filename, 'w')) {
       echo "Impossible d'ouvrir le fichier ($filename)";
	exit;
}
else {	
	fwrite($handle, $param);
	fclose($handle);
}


// On traite le fichier des paiements
$param = "Date du règlement;Mode de Règlement;N° de remise;Nom cliente;Montant TTC\n";
$sql = "select * from commandes c, clients cl, commandes_paiements cp, paiements_modes p where c.id=cp.id and c.client_num=cl.client_num and cp.mode_num=p.mode_num and paiement_date>='" . $date_debut . "' and paiement_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' order by paiement_date ASC";
$cc = $base->query($sql);
foreach ($cc as $rcc) {
	$param .= format_date($rcc["paiement_date"],6,1) . ";" . $rcc["mode_nom"] . ";" . $rcc["cheque_num"] . ";" . $rcc["client_nom"] . " " . $rcc["client_prenom"] . ";" . $rcc["paiement_montant"] . "\n";
}

// On enregistre le fichier
$nom_fic_reglement = "reglements-" . format_date($date_debut,7,1) . "-" . format_date($date_fin,7,1) . "-" . $showroom . ".csv";
$filename= "UPLOAD/" . $nom_fic_reglement;
if (!$handle = fopen($filename, 'w')) {
       echo "Impossible d'ouvrir le fichier ($filename)";
	exit;
}
else {	
	fwrite($handle, $param);
	fclose($handle);
}
	
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
                                <a href="/home">Accueil</a>
                            </li>
                            <li class="active">Export comptables</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
					<div class="col-md-3"><a href="UPLOAD/<?php echo $nom_fic_facture ?>" target="_blank"><img src="excel.png" class="responsive" height="100"> Télécharger le fichier des factures</a></div>
					<div class="col-md-3"><a href="UPLOAD/<?php echo $nom_fic_reglement ?>" target="_blank"><img src="excel.png" class="responsive" height="100"> Télécharger le fichier des reglements</a></div>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
    </body>

</html>