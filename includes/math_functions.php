<?php
/**
 * Fonctions mathématiques personnalisées pour compatibilité PHP
 */

if (!function_exists('round_prix')) {
    /**
     * Fonction d'arrondi compatible pour les prix
     * Remplace round() dans tous les calculs de prix
     */
    function round_prix($number, $precision = 2, $mode = null) {
        // Si bcmath est disponible, utiliser pour plus de précision
        if (function_exists('bcadd')) {
            bcscale($precision + 2); // Précision interne supérieure
            
            // Convertir en string pour bcmath
            $number_str = (string)$number;
            
            // Arrondir avec bcmath
            $factor = bcpow('10', (string)$precision);
            $multiplied = bcmul($number_str, $factor);
            
            // Arrondi commercial (0.5 -> vers le haut)
            if (bccomp(bcsub($multiplied, bcfloor($multiplied)), '0.5') >= 0) {
                $rounded = bcadd(bcfloor($multiplied), '1');
            } else {
                $rounded = bcfloor($multiplied);
            }
            
            return (float)bcdiv($rounded, $factor, $precision);
        }
        
        // Fallback : utiliser round standard avec mode forcé
        return round($number, $precision, PHP_ROUND_HALF_UP);
    }
}

if (!function_exists('bcfloor')) {
    /**
     * Fonction floor pour bcmath si pas disponible
     */
    function bcfloor($number) {
        if (function_exists('bccomp') && bccomp($number, '0') >= 0) {
            return bcadd($number, '0', 0);
        }
        return floor((float)$number);
    }
}

// Alias pour compatibilité si vous voulez garder le nom round
if (!function_exists('round_safe')) {
    function round_safe($number, $precision = 2) {
        return round_prix($number, $precision);
    }
}
?>