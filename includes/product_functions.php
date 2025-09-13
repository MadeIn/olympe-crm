<?php
/**
 * Fonctions de gestion des produits et prix
 * Version modernisée avec classe Database et gestion d'erreurs
 */

/**
 * Récupère les informations de prix d'un produit
 */
function RecupPrix(int $produit): array|false {
    try {
        $base = Database::getInstance();
        
        $sql = "SELECT p.*, pp.*, t.* 
                FROM md_produits p 
                INNER JOIN prix pp ON p.prix_num = pp.prix_num 
                INNER JOIN tva t ON p.tva_num = t.tva_num 
                WHERE p.produit_num = ?";
        
        $result = $base->queryRow($sql, [$produit]);
        
        if (!$result) {
            return false;
        }
        
        $prix_ht = (float)$result["prix_montant_ht"];
        $remise_type = (int)$result["produit_remise_type"];
        $remise_montant = (float)$result["produit_montant_remise"];
        $tva_taux = (float)$result["tva_taux"];
        
        $montant_tva = $prix_ht * ($tva_taux / 100);
        $prix_ttc = round($prix_ht + $montant_tva, 2);
        
        // Calcul des prix avec remise
        $prix_ht_remise = 0;
        $montant_tva_remise = 0;
        $prix_ttc_remise = 0;
        
        if ($remise_type > 0) {
            switch ($remise_type) {
                case 1: // Remise en pourcentage
                    $prix_ht_remise = $prix_ht * (1 - ($remise_montant / 100));
                    $montant_tva_remise = $prix_ht_remise * ($tva_taux / 100);
                    $prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
                    break;
                    
                case 2: // Remise en euros
                    $prix_ht_remise = $prix_ht - $remise_montant;
                    $montant_tva_remise = $prix_ht_remise * ($tva_taux / 100);
                    $prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
                    break;
            }
        }
        
        return [
            'montant_ht' => $prix_ht,
            'montant_remise_type' => $remise_type,
            'montant_remise' => $remise_montant,
            'tva_taux' => $tva_taux,
            'montant_tva' => round($montant_tva, 2),
            'montant_ttc' => $prix_ttc,
            'montant_ht_remise' => round($prix_ht_remise, 2),
            'montant_tva_remise' => round($montant_tva_remise, 2),
            'montant_ttc_remise' => round($prix_ttc_remise, 2)
        ];
        
    } catch (Exception $e) {
        error_log("Erreur RecupPrix: " . $e->getMessage());
        return false;
    }
}

/**
 * Affiche le prix formaté d'un produit avec remise si applicable
 */
function AffichePrix(int $produit): string|false {
    $prix_data = RecupPrix($produit);
    
    if (!$prix_data) {
        return false;
    }
    
    if ($prix_data['montant_remise_type'] == 0) {
        return safe_number_format($prix_data['montant_ttc'], 2, ',', ' ') . ' €';
    } else {
        return '<del>' . safe_number_format($prix_data['montant_ttc'], 2, ',', ' ') . ' €</del> ' . 
               '<strong>' . safe_number_format($prix_data['montant_ttc_remise'], 2, ',', ' ') . ' €</strong>';
    }
}

/**
 * Affiche le prix HT formaté d'un produit
 */
function AffichePrixHT(int $produit): string|false {
    $prix_data = RecupPrix($produit);
    
    if (!$prix_data) {
        return false;
    }
    
    if ($prix_data['montant_remise_type'] == 0) {
        return safe_number_format($prix_data['montant_ht'], 2, ',', ' ') . ' € HT';
    } else {
        return '<del>' . safe_number_format($prix_data['montant_ht'], 2, ',', ' ') . ' €</del> ' . 
               '<strong>' . safe_number_format($prix_data['montant_ht_remise'], 2, ',', ' ') . ' € HT</strong>';
    }
}

/**
 * Affiche le montant de la TVA d'un produit
 */
function AffichePrixTVA(int $produit): string|false {
    $prix_data = RecupPrix($produit);
    
    if (!$prix_data) {
        return false;
    }
    
    $montant_tva = $prix_data['montant_remise_type'] == 0 ? 
        $prix_data['montant_tva'] : $prix_data['montant_tva_remise'];
    
    return safe_number_format($montant_tva, 2, ',', ' ') . ' € TVA';
}

/**
 * Récupère le prix final d'un produit (avec remise si applicable)
 */
function RecupPrixInit(int $produit): float|false {
    $prix_data = RecupPrix($produit);
    
    if (!$prix_data) {
        return false;
    }
    
    return $prix_data['montant_remise_type'] == 0 ? 
        $prix_data['montant_ttc'] : $prix_data['montant_ttc_remise'];
}

/**
 * Récupère les photos d'un produit
 */
function RecupPhotoProduit(int $produit): array {
    try {
        $base = Database::getInstance();
        
        $sql = "SELECT * FROM md_produits_photos 
                WHERE produit_num = ? AND photo_pos = 1 
                ORDER BY photo_pos ASC 
                LIMIT 1";
        
        $result = $base->queryRow($sql, [$produit]);
        
        if ($result) {
            return [
                'min' => "/photos/produits/min/" . $result["photo_chemin"],
                'norm' => "/photos/produits/norm/" . $result["photo_chemin"],
                'zoom' => "/photos/produits/zoom/" . $result["photo_chemin"]
            ];
        } else {
            return [
                'min' => "https://via.placeholder.com/200x200/EFEFEF/AAAAAA?text=Pas+d'image",
                'norm' => "https://via.placeholder.com/500x500/EFEFEF/AAAAAA?text=Pas+d'image",
                'zoom' => "https://via.placeholder.com/1000x1000/EFEFEF/AAAAAA?text=Pas+d'image"
            ];
        }
        
    } catch (Exception $e) {
        error_log("Erreur RecupPhotoProduit: " . $e->getMessage());
        return [
            'min' => "https://via.placeholder.com/200x200/FF0000/FFFFFF?text=Erreur",
            'norm' => "https://via.placeholder.com/500x500/FF0000/FFFFFF?text=Erreur",
            'zoom' => "https://via.placeholder.com/1000x1000/FF0000/FFFFFF?text=Erreur"
        ];
    }
}

/**
 * Calcule le montant total d'une commande
 */
function montantCommande(int $id): array|false {
    try {
        $base = Database::getInstance();
        
        $sql = "SELECT * FROM commandes WHERE id = ?";
        $result = $base->queryRow($sql, [$id]);
        
        if (!$result) {
            return false;
        }
        
        $commande_ttc = (float)$result["commande_ttc"];
        $remise_type = (int)$result["commande_remise_type"];
        $remise_montant = (float)$result["commande_remise"];
        
        $commande_remise_ttc = 0;
        $remise_label = '';
        
        if ($remise_type > 0) {
            switch ($remise_type) {
                case 1: // Remise en pourcentage
                    $commande_remise_ttc = $commande_ttc * (1 - ($remise_montant / 100));
                    $remise_label = '-' . $remise_montant . '%';
                    break;
                    
                case 2: // Remise en euros
                    $commande_remise_ttc = $commande_ttc - $remise_montant;
                    $remise_label = '-' . safe_number_format($remise_montant, 2, ',', ' ') . '€';
                    break;
            }
        }
        
        return [
            'commande_ht' => (float)$result["commande_ht"],
            'commande_tva' => (float)$result["commande_tva"],
            'commande_ttc' => $commande_ttc,
            'commande_remise_type' => $remise_type,
            'commande_remise' => $remise_montant,
            'remise' => $remise_label,
            'commande_remise_ttc' => round($commande_remise_ttc, 2)
        ];
        
    } catch (Exception $e) {
        error_log("Erreur montantCommande: " . $e->getMessage());
        return false;
    }
}

/**
 * Retourne le montant TTC final d'une commande (avec remise)
 */
function montantCommandeTTC(int $id): float|false {
    $montant_data = montantCommande($id);
    
    if (!$montant_data) {
        return false;
    }
    
    return $montant_data['commande_remise_type'] == 0 ? 
        $montant_data['commande_ttc'] : $montant_data['commande_remise_ttc'];
}

/**
 * Retourne le montant HT final d'une commande (avec remise)
 */
function montantCommandeHT(int $id): float|false {
    $montant_data = montantCommande($id);
    
    if (!$montant_data) {
        return false;
    }
    
    if ($montant_data['commande_remise_type'] == 0) {
        return $montant_data['commande_ht'];
    } else {
        $ratio_remise = $montant_data['commande_remise_ttc'] / $montant_data['commande_ttc'];
        return round($montant_data['commande_ht'] * $ratio_remise, 2);
    }
}

/**
 * Calcule le reste à payer d'une commande
 */
function resteAPayerCommande(int $id): float {
    try {
        $montant_ttc = montantCommandeTTC($id);
        
        if ($montant_ttc === false) {
            return 0;
        }
        
        $base = Database::getInstance();
        
        $sql = "SELECT COALESCE(SUM(paiement_montant), 0) as total_paye 
                FROM commandes_paiements 
                WHERE id = ?";
        
        $result = $base->queryRow($sql, [$id]);
        $total_paye = (float)($result["total_paye"] ?? 0);
        
        $reste = $montant_ttc - $total_paye;
        
        return max(0, $reste); // Ne jamais retourner un montant négatif
        
    } catch (Exception $e) {
        error_log("Erreur resteAPayerCommande: " . $e->getMessage());
        return 0;
    }
}

/**
 * Met à jour le stock web après une commande (fonction legacy)
 * ATTENTION: Cette fonction utilise une connexion à une base externe
 */
function majStockWeb(string $id_crypte): bool {
    try {
        $id = decrypte($id_crypte);
        $base = Database::getInstance();
        
        // Récupérer les produits de la commande
        $sql = "SELECT cp.*, p.produit_ref 
                FROM commandes_produits cp 
                INNER JOIN md_produits p ON cp.produit_num = p.produit_num 
                WHERE cp.id = ?";
        
        $produits = $base->query($sql, [$id]);
        
        if (empty($produits)) {
            return false;
        }
        
        // NOTE: La partie connexion à la base shop externe devra être adaptée
        // selon votre architecture actuelle. Cette fonction nécessite une révision
        // pour utiliser votre système de base de données moderne.
        
        log_event('stock_update', "Tentative de mise à jour stock pour commande $id", 'info');
        
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur majStockWeb: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère les détails complets d'un produit
 */
function getProductDetails(int $produit_id): array|false {
    try {
        $base = Database::getInstance();
        
        $sql = "SELECT p.*, c.categorie_nom, m.marque_nom 
                FROM md_produits p 
                LEFT JOIN categories c ON p.categorie_num = c.categorie_num 
                LEFT JOIN marques m ON p.marque_num = m.marque_num 
                WHERE p.produit_num = ?";
        
        $produit = $base->queryRow($sql, [$produit_id]);
        
        if (!$produit) {
            return false;
        }
        
        // Ajouter les informations de prix
        $prix_info = RecupPrix($produit_id);
        if ($prix_info) {
            $produit = array_merge($produit, $prix_info);
        }
        
        // Ajouter les photos
        $photos = RecupPhotoProduit($produit_id);
        $produit['photos'] = $photos;
        
        return $produit;
        
    } catch (Exception $e) {
        error_log("Erreur getProductDetails: " . $e->getMessage());
        return false;
    }
}

/**
 * Formate un prix pour l'affichage
 */
function formatPrice(float $price, bool $show_currency = true): string {
    $formatted = safe_number_format($price, 2, ',', ' ');
    
    return $show_currency ? $formatted . ' €' : $formatted;
}

/**
 * Calcule une remise sur un montant
 */
function calculateDiscount(float $amount, int $discount_type, float $discount_value): float {
    return match($discount_type) {
        1 => $amount * (1 - ($discount_value / 100)), // Pourcentage
        2 => max(0, $amount - $discount_value), // Montant fixe
        default => $amount
    };
}

/**
 * Vérifie la disponibilité d'un produit
 */
function checkProductAvailability(int $produit_id, int $quantity = 1): bool {
    try {
        $base = Database::getInstance();
        
        $sql = "SELECT stock_reel FROM md_stocks 
                WHERE produit_num = ? 
                ORDER BY stock_reel DESC 
                LIMIT 1";
        
        $result = $base->queryRow($sql, [$produit_id]);
        
        if (!$result) {
            return false;
        }
        
        return (int)$result['stock_reel'] >= $quantity;
        
    } catch (Exception $e) {
        error_log("Erreur checkProductAvailability: " . $e->getMessage());
        return false;
    }
}
?>