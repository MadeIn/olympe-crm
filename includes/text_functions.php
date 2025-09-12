<?php
/**
 * Fonctions de manipulation de texte et formatage
 * Fichier modernisé pour PHP8
 */

/**
 * Génère un extrait d'introduction de texte (120 caractères par défaut)
 */
function GenereIntro(string $desc): string {
    $desc = strip_tags($desc);
    $pos_pt = strpos($desc, ".");
    
    if ($pos_pt !== false && $pos_pt < 120) {
        $desc = trim(substr($desc, 0, $pos_pt));
    } else {
        $pos_pt = strpos($desc, " ", 120);
        if ($pos_pt !== false) {
            $desc = trim(substr($desc, 0, $pos_pt));
        } else {
            $desc = trim(substr($desc, 0, 120));
        }
    }
    
    return $desc . "...";
}

/**
 * Génère un extrait d'introduction de texte avec longueur personnalisée
 */
function GenereIntroLg(string $desc, int $lg): string {
    $desc = strip_tags($desc);
    $pos_pt = strpos($desc, ".");
    
    if ($pos_pt !== false && $pos_pt < $lg) {
        $desc = trim(substr($desc, 0, $pos_pt));
    } else {
        $pos_pt = strpos($desc, " ", $lg);
        if ($pos_pt !== false) {
            $desc = trim(substr($desc, 0, $pos_pt));
        } else {
            $desc = trim(substr($desc, 0, $lg));
        }
    }
    
    return $desc . "...";
}

/**
 * Supprime les accents d'une chaîne de caractères
 */
function remove_accent(string $str): string {
    $a = [
        'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
        'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
        'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
        'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Œ', 'œ'
    ];

    $b = [
        'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D',
        'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a',
        'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o',
        'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'OE', 'oe'
    ];
    
    return str_replace($a, $b, $str);
}

/**
 * Génère un slug (URL friendly) à partir d'une chaîne
 */
function Slug(string $str): string {
    // Supprimer les accents
    $str = remove_accent($str);
    
    // Convertir en minuscules
    $str = mb_strtolower($str, 'UTF-8');
    
    // Remplacer les caractères non alphanumériques par des tirets
    $str = preg_replace('/[^a-z0-9\s\-]/', '', $str);
    
    // Remplacer les espaces et tirets multiples par un seul tiret
    $str = preg_replace('/[\s\-]+/', '-', $str);
    
    // Supprimer les tirets en début et fin
    return trim($str, '-');
}

/**
 * Traite un nom de page pour en faire une URL propre (fonction legacy)
 */
function xtTraiter(string $nompage): string {
    $nompage = strtolower($nompage);
    $nompage = remove_accent($nompage);
    $nompage = str_replace([":", " - ", "...", "/"], ["", "-", "", "-"], $nompage);
    $nompage = preg_replace('/[^a-z0-9_:~\/\-]/', "-", $nompage);
    
    return $nompage;
}

/**
 * Nettoie le texte des entités HTML
 */
function clean_text(string $text, string $encodage = 'utf-8'): string {
    $text = str_replace(['&lt;', '&gt;', '&quot;', '&amp;'], ['<', '>', '"', '&'], $text);
    return $text;
}

/**
 * Remplace les caractères spéciaux par leur entité HTML
 */
function Replace_Carac_Special(string $chaine): string {
    return htmlspecialchars($chaine, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Remet les caractères spéciaux depuis leurs entités HTML
 */
function Remet_Carac_Special(string $chaine): string {
    return html_entity_decode($chaine, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Complète une chaîne avec des caractères pour atteindre une longueur donnée
 */
function Comble_Chaine_Vide(string $chaine, int $nbcar, string $carac, string $pos): string {
    $lgchaine = strlen($chaine);
    
    if ($lgchaine < $nbcar) {
        $champ = str_repeat($carac, $nbcar - $lgchaine);
        
        return match($pos) {
            'prec' => $champ . $chaine,
            'suiv' => $chaine . $champ,
            default => $chaine . $champ
        };
    } elseif ($lgchaine == $nbcar) {
        return $chaine;
    } else {
        return substr($chaine, 0, $nbcar);
    }
}

/**
 * Récupère les valeurs entre deux balises dans un texte
 */
function recupValeurEntreBalise(string $text, string $baliseDebut, string $baliseFin): array {
    $pattern = '/' . preg_quote($baliseDebut, '/') . '(.*?)' . preg_quote($baliseFin, '/') . '/s';
    preg_match_all($pattern, $text, $matches);
    
    return $matches[1] ?? [];
}
?>