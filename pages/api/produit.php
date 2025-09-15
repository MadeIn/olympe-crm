<?php declare(strict_types=1);

// /pages/api/produit.php
require_once __DIR__ . '/../../param.php'; // pages privées (sessions + $base + helpers)

// Sécurité
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') json_err('Méthode invalide', 405);
if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) json_err('CSRF', 400);

// Inputs
$int = fn(string $k): int => (int)($_POST[$k] ?? 0);
$str = fn(string $k): string => trim((string)($_POST[$k] ?? ''));

// Mode
$mode = $_POST['mode'] ?? '';
try {
    switch ($mode) {

        case 'changePrixAchat': {
            $id   = $int('id');        // prixachat_num
            $prix = $str('prix');      // laisser tel quel, validé en amont si besoin

            if ($id <= 0) json_err('Paramètre manquant: id');
            // Optionnel: validation du format numérique
            if ($prix === '' || !is_numeric(str_replace([',',' '], ['.',''], $prix))) {
                json_err('Prix invalide');
            }

            // Normalise le séparateur décimal
            $prix_sql = str_replace(',', '.', $prix);
            $sql = "UPDATE prixachats SET prixachat_montant='" . addslashes($prix_sql) . "' WHERE prixachat_num='" . $id . "'";
            $base->query($sql);

            json_ok(); // rien à renvoyer
        }

        case 'delete': {
            // suppr = produit_num crypté
            $supprCrypt = $str('suppr');
            if ($supprCrypt === '') json_err('Paramètre manquant: suppr');

            $produit_num = (int) decrypte($supprCrypt);
            if ($produit_num <= 0) json_err('Produit invalide');

            // Supprime produit + photos associées
            $base->query("DELETE FROM md_produits_photos WHERE produit_num='" . $produit_num . "'");
            $base->query("DELETE FROM md_produits WHERE produit_num='" . $produit_num . "'");

            json_ok();
        }

        case 'changeRef': {
            $produit = $int('produit');    // produit_num
            $ref     = $str('ref');        // nouvelle référence

            if ($produit <= 0) json_err('Produit invalide');
            // autorise vide si tu veux effacer la ref — sinon teste: if ($ref === '') json_err('Référence vide');

            // Si ta classe Db ne gère pas les paramètres PDO, on sécurise a minima:
            $sql = "UPDATE md_produits SET produit_ref='".$ref."' WHERE produit_num='".$produit."'";
            $base->query($sql);

            json_ok();
        }

        default:
            json_err('Mode inconnu', 400);
    }
} catch (Throwable $e) {
    $env = $app_config['environment'] ?? 'prod';
    if ($env === 'dev') {
        json_err('Exception: ' . $e->getMessage(), 500);
    }
    json_err('Erreur serveur', 500);
}
