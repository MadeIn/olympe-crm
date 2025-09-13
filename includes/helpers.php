<?php

/**
 * Génération d'URL sécurisée
 */
function url(string $path = ''): string {
    global $app_config;
    return $app_config['base_url'] . $path;
}

/**
 * Génération d'URL pour les assets (CSS, JS, images)
 */
function asset(string $path): string {
    return url('/assets/' . ltrim($path, '/'));
}

/**
 * Échappement HTML sécurisé
 */
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/* Helpers appel formulaire SAME PAGE */
function form_action_same(): string {
    return htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Helpers Rappel SAME PAGE */
function current_path(): string {
    return htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Version safe de number_format
 * - accepte null ou string vide
 * - retourne "" si valeur absente
 */
function safe_number_format($value, int $decimals = 2, string $dec_point = ".", string $thousands_sep = " "): string {
    if ($value === null || $value === '') {
        return "";
    }
    return number_format((float) $value, $decimals, $dec_point, $thousands_sep);
}

?>