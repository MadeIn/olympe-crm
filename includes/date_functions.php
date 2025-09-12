<?php
/**
 * Fonctions de gestion des dates et temps
 * Modernisé pour PHP8 avec gestion des exceptions
 */

/**
 * Calcule la différence en jours entre deux dates
 */
function diff_date(string $date_deb, string $date_fin): int {
    try {
        $date1 = new DateTime($date_deb);
        $date2 = new DateTime($date_fin);
        $diff = $date2->diff($date1);
        
        return (int)$diff->days;
    } catch (Exception $e) {
        error_log("Erreur diff_date: " . $e->getMessage());
        return 0;
    }
}

/**
 * Formate une date selon différents choix d'affichage
 * Fonction legacy maintenue pour compatibilité
 */
function format_date(string $date_traiter, int $choix, int $langue = 1): string {
    if (empty($date_traiter) || $date_traiter === '0000-00-00' || $date_traiter === '0000-00-00 00:00:00') {
        return '';
    }
    
    try {
        $dt = new DateTime($date_traiter);
    } catch (Exception $e) {
        error_log("Erreur format_date: " . $e->getMessage());
        return '';
    }
    
    // Noms des mois selon la langue
    $mois_fr = [
        "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
        "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
    ];
    
    $mois_en = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    
    $date_mois = match($langue) {
        2 => $mois_en,
        3 => $mois_fr, // Espagnol - à adapter si nécessaire
        default => $mois_fr
    };
    
    $jour = $dt->format('d');
    $mois = (int)$dt->format('m');
    $mois_nom = $date_mois[$mois - 1];
    $annee = $dt->format('Y');
    $heure = $dt->format('H');
    $minute = $dt->format('i');
    
    return match($choix) {
        0 => $jour . " " . $mois_nom . " " . $annee,
        1 => $heure . "h" . $minute,
        2 => $jour . " " . $mois_nom . " " . $annee . " à " . $heure . ":" . $minute,
        3 => $dt->format('d/m'),
        4 => $dt->format('d/m/Y H:i'),
        5 => $jour . " " . $mois_nom . " " . $heure . "h" . $minute,
        6 => $dt->format('d/m/Y'),
        7 => $dt->format('Y-m-d'),
        8 => $jour,
        9 => $heure . "h" . $minute,
        10 => $dt->format('d/m H\hi'),
        11 => $dt->format('d/m/Y'),
        12 => $dt->format('H:i:s'),
        13 => $mois_nom . " " . $annee,
        default => $dt->format('d/m/Y')
    };
}

/**
 * Formate une date pour RSS
 */
function format_date_rss(string $date_traiter, int $choix): string {
    if (empty($date_traiter)) {
        return '';
    }
    
    return match($choix) {
        1 => substr($date_traiter, 5, 12),
        2 => substr($date_traiter, 5, 18),
        3 => substr($date_traiter, 18, 20),
        default => $date_traiter
    };
}

/**
 * Formate une date française moderne
 */
function format_date_fr(string $date, string $format = 'd/m/Y'): string {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }
    
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        error_log("Erreur format_date_fr: " . $e->getMessage());
        return '';
    }
}

/**
 * Formate une date avec heure française moderne
 */
function format_datetime_fr(string $datetime, string $format = 'd/m/Y H:i'): string {
    return format_date_fr($datetime, $format);
}

/**
 * Convertit une date française (dd/mm/yyyy) vers format MySQL
 */
function date_fr_to_mysql(string $date_fr): string {
    if (empty($date_fr)) {
        return '';
    }
    
    // Essayer plusieurs formats français
    $formats = ['d/m/Y', 'd-m-Y', 'd/m/Y H:i:s', 'd-m-Y H:i:s'];
    
    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $date_fr);
        if ($dt !== false) {
            return $dt->format('Y-m-d');
        }
    }
    
    // Fallback avec strtotime
    $timestamp = strtotime(str_replace('/', '-', $date_fr));
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return '';
}

/**
 * Convertit une date MySQL vers format français
 */
function date_mysql_to_fr(string $date_mysql): string {
    return format_date_fr($date_mysql, 'd/m/Y');
}

/**
 * Vérifie si une date est valide
 */
function is_valid_date(string $date, string $format = 'Y-m-d'): bool {
    $dt = DateTime::createFromFormat($format, $date);
    return $dt !== false && $dt->format($format) === $date;
}

/**
 * Retourne la date actuelle au format français
 */
function date_fr_now(): string {
    return date('d/m/Y');
}

/**
 * Retourne la date et heure actuelle au format français
 */
function datetime_fr_now(): string {
    return date('d/m/Y H:i');
}

/**
 * Calcule l'âge à partir d'une date de naissance
 */
function calculate_age(string $birth_date): int {
    try {
        $birth = new DateTime($birth_date);
        $today = new DateTime();
        $age = $today->diff($birth);
        
        return (int)$age->y;
    } catch (Exception $e) {
        error_log("Erreur calculate_age: " . $e->getMessage());
        return 0;
    }
}

/**
 * Retourne le nombre de jours entre aujourd'hui et une date
 */
function days_until(string $target_date): int {
    try {
        $target = new DateTime($target_date);
        $today = new DateTime();
        $diff = $target->diff($today);
        
        return $target > $today ? (int)$diff->days : -(int)$diff->days;
    } catch (Exception $e) {
        error_log("Erreur days_until: " . $e->getMessage());
        return 0;
    }
}

/**
 * Formate une durée en français
 */
function format_duration(int $seconds): string {
    if ($seconds < 60) {
        return $seconds . ' seconde' . ($seconds > 1 ? 's' : '');
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } elseif ($seconds < 86400) {
        $hours = floor($seconds / 3600);
        return $hours . ' heure' . ($hours > 1 ? 's' : '');
    } else {
        $days = floor($seconds / 86400);
        return $days . ' jour' . ($days > 1 ? 's' : '');
    }
}
?>