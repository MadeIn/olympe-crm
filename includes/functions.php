<?php
/**
 * Fonctions utilitaires du CRM
 * Fichier centralisé pour toutes les fonctions
 */
// CHARGEMENT DU FICHIER .ENV
if (!function_exists('loadEnvFile')) {
    function loadEnvFile($path) {
        if (!file_exists($path)) {
            return false;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, '"\'');
                
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        return true;
    }
}

// Chargement automatique du .env
loadEnvFile(__DIR__ . '/../.env');

/**
 * Génération d'un champ CSRF pour les formulaires
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . h(Auth::generateCSRFToken()) . '">';
}

/**
 * Vérification CSRF dans les formulaires POST
 */
function verify_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return Auth::verifyCSRFToken($_POST['csrf_token'] ?? '');
    }
    return true;
}

/**
 * Validation d'email
 */
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Nettoyage d'une chaîne de caractères
 */
function sanitize_string(string $str): string {
    return trim(strip_tags($str));
}

/**
 * GESTION D'ERREURS ET MESSAGES
 */

/**
 * Affichage sécurisé d'erreurs
 */
function show_error(string $message): void {
    global $app_config;
    
    error_log("Erreur CRM: " . $message);
    
    if ($app_config['environment'] === 'dev') {
        echo '<div class="alert alert-danger" style="margin: 20px;">';
        echo '<strong>Erreur:</strong> ' . h($message);
        echo '</div>';
    } else {
        echo '<div class="alert alert-danger" style="margin: 20px;">';
        echo '<strong>Erreur système.</strong> Veuillez contacter l\'administrateur.';
        echo '</div>';
    }
}

/**
 * Affichage d'un message de succès
 */
function show_success(string $message): void {
    echo '<div class="alert alert-success alert-dismissible" style="margin: 20px;">';
    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
    echo '<strong>Succès:</strong> ' . h($message);
    echo '</div>';
}

/**
 * Affichage d'un message d'information
 */
function show_info(string $message): void {
    echo '<div class="alert alert-info alert-dismissible" style="margin: 20px;">';
    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
    echo h($message);
    echo '</div>';
}

/**
 * NAVIGATION ET URLS
 */

/**
 * Redirection sécurisée
 */
function redirect(string $url): void {
    Auth::redirect($url);
}


/**
 * PERMISSIONS ET CONTRÔLE D'ACCÈS
 */

/**
 * Vérification des permissions utilisateur
 */
function require_permission(string $permission): void {
    global $u;
    
    if (!$u) {
        show_error('Utilisateur non connecté');
        exit();
    }
    
    switch ($permission) {
        case 'admin':
            if ($u->mGroupe !== 1) {
                show_error('Accès administrateur requis');
                exit();
            }
            break;
            
        case 'compta':
            if (!$u->hasComptaAccess()) {
                show_error('Accès à la comptabilité non autorisé');
                exit();
            }
            break;
            
        case 'couturiere':
            if (!$u->isCouturiere()) {
                show_error('Accès réservé aux couturières');
                exit();
            }
            break;
            
        default:
            show_error('Permission inconnue: ' . $permission);
            exit();
    }
}

/**
 * Vérifie si l'utilisateur a une permission spécifique
 */
function has_permission(string $permission): bool {
    global $u;
    
    if (!$u) {
        return false;
    }
    
    switch ($permission) {
        case 'admin':
            return $u->mGroupe === 1;
            
        case 'compta':
            return $u->hasComptaAccess();
            
        case 'couturiere':
            return $u->isCouturiere();
            
        default:
            return false;
    }
}

/**
 * FORMATAGE ET DATES
 */

/**
 * Formatage monétaire
 */
function format_currency(float $amount, string $currency = '€'): string {
    return safe_number_format($amount, 2, ',', ' ') . ' ' . $currency;
}

/**
 * FICHIERS ET INCLUSIONS
 */

/**
 * Inclusion sécurisée de fichiers
 */
function include_once_safe(string $file): bool {
    $filepath = dirname(dirname(__FILE__)) . '/' . ltrim($file, '/');
    
    if (file_exists($filepath) && is_readable($filepath)) {
        include_once $filepath;
        return true;
    }
    
    error_log("Fichier non trouvé ou non lisible: " . $filepath);
    return false;
}

/**
 * Obtenir la taille d'un fichier formatée
 */
function format_file_size(int $bytes): string {
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $factor = floor((strlen($bytes) - 1) / 3);
    
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
}

/**
 * LOGS ET MONITORING
 */

/**
 * Log d'accès aux pages
 */
function log_page_access(): void {
    global $u, $app_config;
    
    if (!$u) return;
    
    $logFile = $app_config['app_root'] . '/logs/access.log';
    
    $logEntry = sprintf(
        "[%s] User: %s (%d) - Page: %s - IP: %s - User-Agent: %s\n",
        date('Y-m-d H:i:s'),
        $u->getFullName(),
        $u->mNum,
        $_SERVER['REQUEST_URI'] ?? 'unknown',
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100)
    );
    
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Log d'événement personnalisé
 */
function log_event(string $event, string $details = '', string $level = 'info'): void {
    global $u, $app_config;
    
    $logFile = $app_config['app_root'] . '/logs/events.log';
    
    $username = $u ? $u->getFullName() . ' (' . $u->mNum . ')' : 'Anonyme';
    
    $logEntry = sprintf(
        "[%s] [%s] User: %s - Event: %s - Details: %s - IP: %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $username,
        $event,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    );
    
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * UTILITAIRES DIVERS
 */

/**
 * Génération d'un ID unique
 */
function generate_unique_id(string $prefix = ''): string {
    return $prefix . uniqid() . '_' . mt_rand(1000, 9999);
}

/**
 * Vérification si la requête est AJAX
 */
function is_ajax_request(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Envoi d'une réponse JSON (pour AJAX)
 */
function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * Nettoyage d'un nom de fichier
 */
function sanitize_filename(string $filename): string {
    // Remplacer les caractères dangereux
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    // Éviter les noms de fichiers système
    $forbidden = ['con', 'prn', 'aux', 'nul', 'com1', 'com2', 'com3', 'com4', 'com5', 'com6', 'com7', 'com8', 'com9', 'lpt1', 'lpt2', 'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9'];
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    if (in_array(strtolower($name), $forbidden)) {
        $filename = '_' . $filename;
    }
    
    return $filename;
}

/**
 * COMPATIBILITÉ AVEC L'ANCIEN CODE
 */


/**
 * Simule register_globals de façon plus sûre.
 * - Charge GET puis POST (POST écrase GET si même clé).
 * - Blacklist de noms réservés pour éviter les collisions dangereuses.
 * - Option: n'écrase pas si la variable existe déjà dans $GLOBALS.
 */
function secure_register_globals(bool $dontOverrideExisting = true): void
{
    // 1) Fusion dans l'ordre: GET puis POST (POST > GET)
    $data = array_merge($_GET ?? [], $_POST ?? []);

    // 2) Noms interdits
    static $reserved = [
        'GLOBALS','_GET','_POST','_COOKIE','_FILES','_SERVER','_ENV','_REQUEST','_SESSION',
        'argv','argc','this','php_errormsg'
    ];

    foreach ($data as $key => $value) {
        // Clé valide: style variable PHP
        if (!is_string($key) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            continue;
        }
        if (in_array($key, $reserved, true)) {
            continue;
        }
        // Option: ne pas écraser si déjà défini
        if ($dontOverrideExisting && array_key_exists($key, $GLOBALS)) {
            continue;
        }

        // Sanitize
        if (is_array($value)) {
            $GLOBALS[$key] = secure_array($value);
        } else {
            $GLOBALS[$key] = secure_value($value);
        }
    }
}

/**
 * Sécurise une valeur simple
 */
function secure_value($value): string {
    if ($value === null) {
        return '';
    }
    
    $value = (string)$value;
    
    // Supprimer les caractères de contrôle dangereux
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    
    // Nettoyer les caractères potentiellement dangereux mais garder les accents
    //$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return trim($value);
}

/**
 * Sécurise un tableau récursivement
 */
function secure_array(array $arr): array {
    $result = [];
    
    foreach ($arr as $key => $value) {
        // Sécuriser aussi les clés du tableau
        $safe_key = secure_value($key);
        
        if (is_array($value)) {
            $result[$safe_key] = secure_array($value);
        } else {
            $result[$safe_key] = secure_value($value);
        }
    }
    
    return $result;
}

/**
 * Fonction helper pour déterminer si un menu est actif
 */
function isActiveMenu(string $path, bool $prefixMatch = true): string {
    // 1) Path courant sans query string
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    // 2) Base du site (si l’app tourne dans un sous-dossier)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseDir = rtrim(str_replace(basename($scriptName), '', $scriptName), '/'); // ex: "/crm" ou ""

    // 3) Cible à comparer (on préfixe par la base si $path est absolu)
    $target = $path;
    if ($path !== '/' && str_starts_with($path, '/')) {
        $target = ($baseDir ? $baseDir : '') . $path;
    }

    // 4) Normalisation (éviter les soucis de slash final)
    $norm = fn(string $p) => $p === '/' ? '/' : rtrim($p, '/');
    $current = $norm($currentPath);
    $target  = $norm($target);

    // 5) Match exact
    if ($current === $target) {
        return 'active open selected';
    }

    // 6) Match préfixe (utile pour /orders et /orders/123)
    if ($prefixMatch && $target !== '/' && str_starts_with($current . '/', $target . '/')) {
        return 'active open selected';
    }

    return '';
}

function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return $value !== false ? $value : $default;
}

?>