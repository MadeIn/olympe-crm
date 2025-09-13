<?php declare(strict_types=1);
// /pages/api/display.php

require_once __DIR__ . '/../../param.php'; // pages privées. Pour public: param_invite.php
header('Content-Type: application/json; charset=utf-8');

// ———————————————————————————————————
// 1) Méthode + CSRF
// ———————————————————————————————————
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    json_err(['ok'=>false,'error'=>'Méthode invalide']); exit;
}
if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    json_err(['ok'=>false,'error'=>'CSRF']); exit;
}

// ———————————————————————————————————
// 2) Helpers d’input & rendu
// ———————————————————————————————————
$int = fn(string $k): int => (int)($_POST[$k] ?? 0);
$str = fn(string $k): string => trim((string)($_POST[$k] ?? ''));

function html_out(callable $fn): string {
    ob_start();
    $fn();
    return (string)ob_get_clean();
}

// ———————————————————————————————————
// 3) Fonctions locales (portage depuis display.php)
// ———————————————————————————————————
function renderDevisHTML(int $id): string {
    /** @var Db $base */ global $base;
    /** @var User $u */  global $u;

    $rcc = $base->queryRow(
        "SELECT * FROM commandes c JOIN paiements p ON c.paiement_num=p.paiement_num WHERE c.id='".(int)$id."'"
    );
    if (!$rcc) return '<p><i>Devis introuvable</i></p>';

    $commande = montantCommande((int)$rcc["id"]);

    $pp = $base->query("
        SELECT cp.*, p.*, t.*, m.*, c.*
        FROM commandes_produits cp
        JOIN md_produits p  ON cp.produit_num = p.produit_num
        JOIN tailles t      ON cp.taille_num  = t.taille_num
        JOIN marques m      ON p.marque_num   = m.marque_num
        JOIN categories c   ON p.categorie_num= c.categorie_num
        WHERE cp.id = '".(int)$id."'
    ");

    $out = '';

    foreach ($pp as $rpp) {
        $image_pdt = RecupPhotoProduit((int)$rpp["produit_num"]);
        $prix_total_ttc = (float)$rpp["montant_ttc"] * (int)$rpp["qte"];
        switch ((int)$rpp["commande_produit_remise_type"]) {
            case 1: $prix_total_ttc *= 1 - ((float)$rpp["commande_produit_remise"]/100); break;
            case 2: $prix_total_ttc -= (float)$rpp["commande_produit_remise"]; break;
        }

        $rss = $base->queryRow("
            SELECT * FROM stocks
            WHERE taille_num=".(int)$rpp["taille_num"]."
              AND produit_num=".(int)$rpp["produit_num"]."
              AND showroom_num='".(int)$u->mShowroom."'
        ");
        $stock = $rss ? (int)$rss["stock_virtuel"] : 10;

        $selectId = 'taille_'.$rpp["produit_num"].'_'.$rpp["taille_num"];
        $qSelId   = 'qte_'.$rpp["produit_num"].'_'.$rpp["taille_num"];
        $rpId     = 'remise_produit_'.$rpp["produit_num"];
        $rtId     = 'remise_type_produit_'.$rpp["produit_num"];

        // options de tailles
        $optsTailles = '<option value="-1">A renseigner</option>';
        $ss = $base->query("
            SELECT t.* FROM tailles t
            JOIN categories_tailles c ON t.taille_num=c.taille_num
            WHERE c.categorie_num=".(int)$rpp["categorie_num"]
        );
        foreach ($ss as $st) {
            $sel = ((int)$st["taille_num"] === (int)$rpp["taille_num"]) ? ' selected' : '';
            $optsTailles .= '<option value="'.(int)$st["taille_num"].'"'.$sel.'>'.h($st["taille_nom"]).'</option>';
        }

        // options qté
        $optsQte = '';
        for ($i=0; $i <= $stock; $i++) {
            $sel = ($i === (int)$rpp["qte"]) ? ' selected' : '';
            $optsQte .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
        }

        $prixTotalTxt = ((float)safe_number_format($prix_total_ttc,2) <= 0.0)
            ? 'OFFERT'
            : safe_number_format($prix_total_ttc,2,"."," ").' €';

        $out .= '
            <tr>
            <td><img src="'.h($image_pdt["min"] ?? '').'" style="width:90px"/></td>
            <td>'.h($rpp["categorie_nom"]).'<br>'.h($rpp["marque_nom"]).'<br><strong>'.h($rpp["produit_nom"]).'</strong></td>
            <td>
                <select name="'.h($selectId).'" id="'.h($selectId).'" onChange="modifTaille('.(int)$id.','.(int)$rpp["produit_num"].','.(int)$rpp["taille_num"].');">
                '.$optsTailles.'
                </select>
            </td>
            <td>'.safe_number_format($rpp["montant_ttc"],2,"."," ").' €</td>
            <td align="center">
                <select name="'.h($qSelId).'" id="'.h($qSelId).'" onChange="modifQte('.(int)$id.','.(int)$rpp["produit_num"].','.(int)$rpp["taille_num"].');">
                '.$optsQte.'
                </select>
            </td>
            <td>'.$prixTotalTxt.'</td>
            <td>
                <input type="text" name="'.h($rpId).'" id="'.h($rpId).'" value="'.h($rpp["commande_produit_remise"]).'" class="form-inline input-xsmall">
                <select name="'.h($rtId).'" id="'.h($rtId).'" class="form-inline input-xsmall" onChange="remiseProduit('.(int)$rcc["id"].','.(int)$rpp["produit_num"].','.(int)$rpp["taille_num"].')">
                <option value="0">--</option>
                <option value="1"'.((int)$rpp["commande_produit_remise_type"]===1?' selected':'').'>%</option>
                <option value="2"'.((int)$rpp["commande_produit_remise_type"]===2?' selected':'').'>€</option>
                </select>
            </td>
            </tr>';
    }

    // Totaux + remise + paiement + acomptes + boutons
    $out .= '
        <tr><td colspan="5" align="right"><strong>Total HT</strong></td><td colspan="2">'.safe_number_format($commande["commande_ht"],2,"."," ").' €</td></tr>
        <tr><td colspan="5" align="right"><strong>TVA (20%)</strong></td><td colspan="2">'.safe_number_format($commande["commande_tva"],2,"."," ").' €</td></tr>
        <tr><td colspan="5" align="right"><strong>Total TTC</strong></td><td colspan="2">'.safe_number_format($commande["commande_ttc"],2,"."," ").' €</td></tr>';

            $out .= '
        <tr>
        <td colspan="5" align="right"><strong>Remise</strong></td>
        <td colspan="2">
            <input type="text" name="remise_montant" id="remise_montant" value="'.h($commande["commande_remise"]).'" class="form-inline input-xsmall">
            <select name="remise_type" id="remise_type" class="form-inline input-xsmall" onChange="remiseCommande('.(int)$rcc["id"].')">
            <option value="0">--</option>
            <option value="1"'.(((int)$commande["commande_remise_type"]===1)?' selected':'').'>%</option>
            <option value="2"'.(((int)$commande["commande_remise_type"]===2)?' selected':'').'>€</option>
            </select>
        </td>
        </tr>';

    $montant_a_payer = ((int)$commande["commande_remise_type"]!==0)
        ? safe_number_format($commande["commande_remise_ttc"],2,".","")
        : safe_number_format($commande["commande_ttc"],2,".","");

    $out .= '
        <tr><td colspan="5" align="right"><strong>Total à payer</strong></td><td colspan="2">'.h($montant_a_payer).' €</td></tr>';

    // Paiements
    $out .= '<tr><td colspan="5" align="right"><strong>Méthode de paiement</strong></td><td colspan="2"><select name="paiement_'.(int)$rcc["id"].'" id="paiement_'.(int)$rcc["id"].'" onChange="modifPaiement('.(int)$rcc["id"].')">';
    $pps = $base->query("SELECT * FROM paiements ORDER BY paiement_pos ASC");
    foreach ($pps as $rpp) {
        $sel = ((int)$rpp["paiement_num"] === (int)$rcc["paiement_num"]) ? ' selected' : '';
        $out .= '<option value="'.(int)$rpp["paiement_num"].'"'.$sel.'>'.h($rpp["paiement_titre"]).'</option>';
    }
    $out .= '</select></td></tr>';

    if ((int)$rcc["paiement_nombre"] > 1) {
        $echeance = explode("/", (string)$rcc["paiement_modele"]);
        $i=1;
        foreach ($echeance as $val) {
            $acompte_val = safe_number_format(((float)$montant_a_payer * ((float)$val/100)), 2,"."," ");
            $out .= '<tr><td colspan="6" align="right"><strong>Acompte '.$i.' ('.h($val).'%)</strong></td><td>'.$acompte_val.' €</td></tr>';
            $i++;
        }
    }

    $out .= '
        <tr><td colspan="7" align="right">
        <a href="/clients/client?client_num='.h(crypte((int)$rcc["client_num"])).'&tab=tab_1_3" class="btn red">Fermer</a>
        <a href="/clients/client?client_num='.h(crypte((int)$rcc["client_num"])).'&tab=tab_1_4&commande_passage='.h(crypte((int)$rcc["id"])).'" class="btn blue" onClick="return confirme_commande('.(int)$rcc["id"].')">Passer la commande</a>
        </td></tr>';

    return $out;
}

function renderSelectionHTML(array $pp, Db $base, int $selection): string {
    if (count($pp) === 0) return '<p><i>Aucun produit dans votre sélection</i></p>';

    $out = '<div class="mt-element-card mt-element-overlay">';
    foreach ($pp as $rpp) {
        $rph = $base->queryRow("SELECT * FROM md_produits_photos WHERE produit_num='".(int)$rpp["produit_num"]."' AND photo_pos=1");
        $image_pdt = $rph ? "/photos/produits/min/".$rph["photo_chemin"] : "https://placehold.co/50x50?text=No+image";
        $out .= '
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
        <div class="mt-card-item">
            <div class="mt-card-avatar mt-overlay-1">
            <figure style="height:100px;overflow:hidden;position:relative;line-height:100px;">
                <img src="'.h($image_pdt).'" />
            </figure>
            <div class="mt-overlay">
                <ul class="mt-info">
                <li>
                    <a class="btn default btn-outline" href="javascript:addWidget('.(int)$selection.','.(int)$rpp["produit_num"].',2)">
                    <i class="fa fa-trash"></i>
                    </a>
                </li>
                </ul>
            </div>
            </div>
            <div class="mt-card-content"><h5><small>'.h($rpp["produit_nom"]).'</small></h5></div>
        </div>
        </div>';
    }
    $out .= '</div>';
    return $out;
}

/** Recalcule les totaux d’une commande (version 2023) */
function api_recalculMontantCommande(int $id): void {
    /** @var Db $base */ global $base;
    $sql = "SELECT cp.*, p.*, t.*
            FROM commandes_produits cp
            JOIN md_produits p ON cp.produit_num=p.produit_num
            JOIN tva t         ON p.tva_num=t.tva_num
            WHERE cp.id='".(int)$id."'";
    $dd = $base->query($sql);

    $ht=0.0; $tva=0.0; $ttc=0.0;
    foreach ($dd as $rdd) {
        // valeurs déjà stockées (2023)
        $montant_ht  = (float)$rdd["montant_ht"];
        $montant_tva = (float)$rdd["montant_tva"];
        $montant_ttc = (float)$rdd["montant_ttc"];
        $montant_ht_r  = (float)$rdd["montant_ht_remise"];
        $montant_tva_r = (float)$rdd["montant_tva_remise"];
        $montant_ttc_r = (float)$rdd["montant_ttc_remise"];
        $q = (int)$rdd["qte"];

        if ((int)$rdd["montant_remise_type"] === 0) {
            $p_ht=$montant_ht*$q; $p_tva=$montant_tva*$q; $p_ttc=$montant_ttc*$q;
        } else {
            $p_ht=$montant_ht_r*$q; $p_tva=$montant_tva_r*$q; $p_ttc=$montant_ttc_r*$q;
        }

        switch ((int)$rdd["commande_produit_remise_type"]) {
            case 1:
                $p_ttc *= 1 - ((float)$rdd["commande_produit_remise"]/100);
                $p_ht   = $p_ttc/(1+((float)$rdd["tva_taux"]/100));
                $p_tva  = $p_ttc - $p_ht;
                break;
            case 2:
                $p_ttc -= (float)$rdd["commande_produit_remise"];
                $p_ht   = $p_ttc/(1+((float)$rdd["tva_taux"]/100));
                $p_tva  = $p_ttc - $p_ht;
                break;
        }

        $ht  += $p_ht;
        $tva += $p_tva;
        $ttc += $p_ttc;
    }

    $sqlU = "UPDATE commandes
             SET commande_ht='".$ht."', commande_tva='".$tva."', commande_ttc='".$ttc."'
             WHERE id='".(int)$id."'";
    $base->query($sqlU);
}

// ———————————————————————————————————
// 4) Router “mode” (compat)
// ———————————————————————————————————
$modeRaw = $_POST['mode'] ?? '';
$map = [
  'addWidget'           => 1, 
  'deleteWidget'        => 2, 
  'modifTaille'         => 3, 
  'modifQte'            => 4,
  'modifPaiement'       => 5, 
  'addPdtDevis'         => 6, 
  'remiseCommande'      => 7,
  'remiseProduit'       => 8, 
  'commandeFournisseur' => 9, 
  'modifDateCommande'   => 10,
];
if (is_numeric($modeRaw)) {
    $mode = (int)$modeRaw;
} else {
    $mode = $map[$modeRaw] ?? 0;
}

try {
    switch ($mode) {
        case 1: // add widget
        {
            $selection = $int('selection'); $pdt = $int('pdt');
            $cc = $base->query("SELECT 1 FROM selections_produits WHERE selection_num='".$selection."' AND produit_num='".$pdt."'");
            if (count($cc) === 0) {
                $base->query("INSERT INTO selections_produits VALUES('".$selection."','".$pdt."')");
            }
            $pp = $base->query("SELECT * FROM selections_produits s JOIN md_produits p ON s.produit_num=p.produit_num WHERE selection_num='".$selection."'");
            $html = renderSelectionHTML($pp, $base, $selection);
            json_ok(['html'=>$html, 'place'=>'select_'.$selection]);
        }

        case 2: // delete widget item
        {
            $selection = $int('selection');
            $pdt = $int('pdt');
            $base->query("DELETE FROM selections_produits WHERE selection_num='".$selection."' AND produit_num='".$pdt."'");
            $pp = $base->query("SELECT * FROM selections_produits s JOIN md_produits p ON s.produit_num=p.produit_num WHERE selection_num='".$selection."'");
            $html = renderSelectionHTML($pp, $base, $selection);
            json_ok(['ok'=>true,'html'=>$html,'place'=>'select_'.$selection]);
        }

        case 3: // modif taille dans devis
        {
            $devis = $int('devis'); $pdt = $int('pdt'); $taille = $int('taille'); $taille_new = $int('taille_new');
            $base->query("UPDATE commandes_produits SET taille_num='".$taille_new."' WHERE id='".$devis."' AND produit_num='".$pdt."' AND taille_num='".$taille."'");
            json_ok(['ok'=>true]); 
        }

        case 4: // modif qte
        {
            $devis=$int('devis'); $pdt=$int('pdt'); $taille=$int('taille'); $qte_new=$int('qte_new');
            if ($qte_new>0) {
                $base->query("UPDATE commandes_produits SET qte='".$qte_new."' WHERE id='".$devis."' AND produit_num='".$pdt."' AND taille_num='".$taille."'");
            } else {
                $base->query("DELETE FROM commandes_produits WHERE id='".$devis."' AND produit_num='".$pdt."' AND taille_num='".$taille."'");
            }
            api_recalculMontantCommande($devis);
            $html = renderDevisHTML($devis);
            json_ok(['html'=>$html, 'place'=>'devis_'.$devis]);
        }

        case 5: // modif paiement
        {
            $devis=$int('devis'); $paiement=$int('paiement');
            $base->query("UPDATE commandes SET paiement_num='".$paiement."' WHERE id='".$devis."'");
            $html = renderDevisHTML($devis);
            json_ok(['ok'=>true,'html'=>$html,'place'=>'devis_'.$devis]); 
        }

        case 6: // ajout produit devis (taille -1)
        {
            $devis=$int('devis'); $pdt=$int('pdt');
            $tt = $base->query("SELECT * FROM commandes_produits WHERE id='".$devis."' AND produit_num='".$pdt."' AND taille_num=-1");
            if (count($tt)===0) {
                $prix = RecupPrix($pdt);
                $sql = "INSERT INTO commandes_produits
                        VALUES ('".$devis."','".$pdt."','-1',1,'".$prix["montant_ht"]."','".$prix["montant_tva"]."','".$prix["montant_ttc"]."','".$prix["montant_remise"]."','".$prix["montant_remise_type"]."','".$prix["montant_ht_remise"]."','".$prix["montant_tva_remise"]."','".$prix["montant_ttc_remise"]."','0','0')";
                $base->query($sql);
            }
            api_recalculMontantCommande($devis);
            $html = renderDevisHTML($devis);
            json_ok(['ok'=>true,'html'=>$html,'place'=>'devis_'.$devis]); break;
        }

        case 7: // remise commande
        {
            $devis=$int('devis'); $montant=$str('remise_montant'); $type=$int('remise_type');
            $base->query("UPDATE commandes SET commande_remise='".addslashes($montant)."', commande_remise_type='".$type."' WHERE id='".$devis."'");
            $html = renderDevisHTML($devis);
            json_ok(['ok'=>true,'html'=>$html,'place'=>'devis_'.$devis]); break;
        }

        case 8: // remise produit
        {
            $devis=$int('devis'); $produit=$int('produit'); $taille=$int('taille'); $montant=$str('remise_montant'); $type=$int('remise_type');
            $base->query("UPDATE commandes_produits SET commande_produit_remise='".addslashes($montant)."', commande_produit_remise_type='".$type."' WHERE id='".$devis."' AND produit_num='".$produit."' AND taille_num='".$taille."'");
            api_recalculMontantCommande($devis);
            $html = renderDevisHTML($devis);
            json_ok(['ok'=>true,'html'=>$html,'place'=>'devis_'.$devis]);
        }

        case 9: // commande fournisseur toggle
        {
            $id=$int('id'); $val=$int('val');
            if ($val===1) {
                $base->query("INSERT INTO commandes_fournisseurs VALUES('".$id."','1','".date("Y-m-d H:i:s")."')");
                json_ok(['ok'=>true,'html'=>date("d/m/Y"),'place'=>'fournisseur_date_'.$id]);
            } else {
                $base->query("DELETE FROM commandes_fournisseurs WHERE id='".$id."'");
                json_ok(['ok'=>true,'html'=>'','place'=>'fournisseur_date_'.$id]);
            }

        }

        case 10: // modif date commande
        {
            $devis=$int('devis'); $date=$str('date_commande');
            $base->query("UPDATE commandes SET commande_date='".addslashes($date)." 14:30:00' WHERE id='".$devis."'");
            json_ok(['ok'=>true]); 
        }

        default:
            json_err('Mode inconnu', 400);
    }
} catch (Throwable $e) {
    // En DEV : remontre l’erreur précise (pratique pour déboguer rapidement)
    $env = $app_config['environment'] ?? 'prod';
    if ($env === 'dev') {
        json_err('Exception: '.$e->getMessage(), 500);
    }
    json_err('Erreur serveur', 500);
}
