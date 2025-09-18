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
function h(null|string|int|float $v): string {
    // si tu veux garder le format exact des nombres, transforme juste en string
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

function json_ok(array $payload = [], int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true] + $payload, JSON_UNESCAPED_UNICODE);
    exit;
}
function json_err(string $msg, int $status = 400, array $extra = []): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $msg] + $extra, JSON_UNESCAPED_UNICODE);
    exit;
}

// Normalise un nombre de formulaire: "1 234,56" -> "1234.56" ou '' -> ''
function norm_num_str(string $v): string {
    $v = trim($v);
    if ($v === '') return '';
    return str_replace([' ', ','], ['', '.'], $v);
}

/**
 * Fonction helper pour échapper les valeurs SQL
 */
function sql_safe($value): string {
    global $base;
    return $base->quote($value);
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        // Vérifier dans $_ENV d'abord
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Puis dans getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Retourner la valeur par défaut
        return $default;
    }
}

?>