<?php declare(strict_types=1);

// /pages/api/produit.php
require_once __DIR__ . '/../../param.php'; // pages privées (sessions + $base + helpers)

/** Sanitize helpers */
$g = fn(string $k, string $def='') => isset($_GET[$k]) ? trim((string)$_GET[$k]) : $def;

$what     = $g('what'); // 'factures' | 'reglements'
$dateDeb  = $g('date_deb'); // YYYY-mm-dd
$dateFin  = $g('date_fin'); // YYYY-mm-dd
$showroom = (int)$g('showroom', '0');

if (!in_array($what, ['factures','reglements'], true)) {
    http_response_code(400);
    echo "Paramètre 'what' invalide";
    exit;
}
if (!$dateDeb || !$dateFin || !$showroom) {
    http_response_code(400);
    echo "Paramètres manquants";
    exit;
}

// Ajoute heures
$from = $dateDeb . ' 00:00:00';
$to   = $dateFin . ' 23:59:59';

// Sélection du contenu
header('Content-Type: text/csv; charset=UTF-8');
$filename = $what . '-' . str_replace('-', '', $dateDeb) . '-' . str_replace('-', '', $dateFin) . '-' . $showroom . '.csv';
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Pragma: no-cache');
header('Expires: 0');

// Ouverture sortie
$out = fopen('php://output', 'w');
// BOM UTF-8 si Excel Windows
fwrite($out, "\xEF\xBB\xBF");

if ($what === 'factures') {
    // En-têtes CSV
    fputcsv($out, [
        'Date de facture',
        'Num de facture',
        'Nom cliente',
        'Genre',
        'Montant TTC',
        'Montant HT Robes avant remise',
        'Montant HT Accessoires avant remise',
        'Montant HT Total Remise'
    ], ';');

    // Requête
    $sql = "SELECT *
        FROM commandes c
        JOIN clients cl ON c.client_num = cl.client_num
        JOIN paiements p ON c.paiement_num = p.paiement_num
        WHERE c.facture_date >= '" . $from . "'
          AND c.facture_date <= '" . $to . "'
          AND c.showroom_num = '" . $showroom . "'
          AND c.commande_num != 0
          AND c.facture_num  != 0
        ORDER BY c.facture_date ASC";

    $rows = $base->query($sql);

    foreach ($rows as $r) {
        $montant_ttc = (float)montantCommandeTTC((int)$r['id']);
        $montant_ht  = (float)montantCommandeHT((int)$r['id']);
        $genre = ((int)$r['client_genre'] === 0) ? 'Femme' : 'Homme';

        // HT Robes
        $htRobe = 0.0;
        $pp = $base->query("SELECT c.montant_ht 
                            FROM commandes_produits c 
                            JOIN md_produits p ON c.produit_num=p.produit_num 
                            WHERE p.categorie_num=11 AND c.id='".(int)$r['id']."'");
        foreach ($pp as $row) { $htRobe += (float)$row['montant_ht']; }

        // HT Accessoires
        $htAcc = 0.0;
        $pp = $base->query("SELECT c.montant_ht 
                            FROM commandes_produits c 
                            JOIN md_produits p ON c.produit_num=p.produit_num 
                            WHERE p.categorie_num<>11 AND c.id='".(int)$r['id']."'");
        foreach ($pp as $row) { $htAcc += (float)$row['montant_ht']; }

        fputcsv($out, [
            format_date($r['facture_date'], 6, 1),
            $r['facture_num'],
            $r['client_nom'] . ' ' . $r['client_prenom'],
            $genre,
            safe_number_format($montant_ttc, 2, '.', ''),
            safe_number_format($htRobe, 2, '.', ''),
            safe_number_format($htAcc, 2, '.', ''),
            safe_number_format($montant_ht, 2, '.', '')
        ], ';');
    }
} else { // reglements
    fputcsv($out, [
        'Date du règlement',
        'Mode de Règlement',
        'N° de remise',
        'Nom cliente',
        'Montant TTC'
    ], ';');

    $sql = "SELECT cp.*, c.*, cl.*, p.*
        FROM commandes c
        JOIN commandes_paiements cp ON c.id = cp.id
        JOIN clients cl ON c.client_num = cl.client_num
        JOIN paiements_modes p ON cp.mode_num = p.mode_num
        WHERE c.facture_date >= '" . $from . "'
          AND c.facture_date <= '" . $to . "'
          AND c.showroom_num = '" . $showroom . "'
        ORDER BY cp.paiement_date ASC";

    $rows = $base->query($sql);

    foreach ($rows as $r) {
        fputcsv($out, [
            format_date($r['paiement_date'], 6, 1),
            $r['mode_nom'],
            (string)($r['cheque_num'] ?? ''),
            $r['client_nom'] . ' ' . $r['client_prenom'],
            safe_number_format((float)$r['paiement_montant'], 2, '.', '')
        ], ';');
    }
}

fclose($out);
exit;
