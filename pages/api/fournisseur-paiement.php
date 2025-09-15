<?php declare(strict_types=1);

// /pages/api/fournisseur-paiement.php
require_once __DIR__ . '/../../param.php'; // page protégée

header('Content-Type: application/json; charset=utf-8');

// 1) Méthode + CSRF
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'error'=>'Méthode invalide']); exit;
}
if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'CSRF']); exit;
}

// 2) Inputs
$mode     = trim((string)($_POST['mode'] ?? 'toggle'));
$id       = (int)($_POST['id'] ?? 0);
$produit  = (int)($_POST['produit'] ?? 0);
$paiement = (int)($_POST['paiement'] ?? 0);

// val peut arriver "123,45" → normaliser → float
$rawVal = (string)($_POST['val'] ?? '0');
$val = (float)str_replace([' ', ','], ['', '.'], $rawVal);

// Sanity
if ($id<=0 || $produit<=0 || !in_array($paiement, [1,2], true)) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Paramètres invalides']); exit;
}

try {
    /** @var Database $base */
    $base = Database::getInstance();

    // 3) S’assurer que la ligne existe dans commandes_fournisseurs_paiements
    //    (colonne paiement3 existe dans ton code existant)
    $sql = "SELECT * FROM commandes_fournisseurs_paiements WHERE id='".$id."' AND produit_num='".$produit."'";
    $row = $base->queryRow($sql);

    if (!$row) {
        // Crée une ligne neutre ('0000-00-00')
        $base->query("
            INSERT INTO commandes_fournisseurs_paiements
            (id, produit_num, paiement1, paiement2, paiement3, paiement1_date, paiement2_date, paiement3_date)
            VALUES ('".$id."', '".$produit."', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00')
        ");
    }

    // 4) Update paiement1 ou paiement2
    if ($paiement === 1) {
        if ($val > 0) {
            $base->query("
                UPDATE commandes_fournisseurs_paiements
                SET paiement1='". $val ."', paiement1_date=CURDATE()
                WHERE id='".$id."' AND produit_num='".$produit."'
            ");
        } else {
            $base->query("
                UPDATE commandes_fournisseurs_paiements
                SET paiement1=0, paiement1_date='0000-00-00 00:00:00'
                WHERE id='".$id."' AND produit_num='".$produit."'
            ");
        }
    } else { // paiement === 2
        if ($val > 0) {
            $base->query("
                UPDATE commandes_fournisseurs_paiements
                SET paiement2='". $val ."', paiement2_date=CURDATE()
                WHERE id='".$id."' AND produit_num='".$produit."'
            ");
        } else {
            $base->query("
                UPDATE commandes_fournisseurs_paiements
                SET paiement2=0, paiement2_date='0000-00-00 00:00:00'
                WHERE id='".$id."' AND produit_num='".$produit."'
            ");
        }
    }

    // 5) Recalcule le reste à payer
    $rtt = $base->queryRow("
        SELECT (COALESCE(p.paiement1,0)+COALESCE(p.paiement2,0)+COALESCE(p.paiement3,0)) AS total_paye
        FROM commandes_fournisseurs_paiements p
        WHERE p.id='".$id."' AND p.produit_num='".$produit."'
    ");
    $totalPaye = $rtt ? (float)$rtt['total_paye'] : 0.0;

    $rcf = $base->queryRow("
        SELECT commande_montant
        FROM commandes_fournisseurs
        WHERE id='".$id."' AND produit_num='".$produit."'
    ");
    $montant = $rcf ? (float)$rcf['commande_montant'] : 0.0;

    $reste = max(0, $montant - $totalPaye);

    $html = safe_number_format($reste, 2, '.', ' ') . ' €';

    echo json_encode([
        'ok'    => true,
        'place' => 'reste_'.$id,
        'html'  => $html
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Erreur serveur']);
}
