<?php
/**
 * Fonctions de cryptage et sécurité
 * Version modernisée - ATTENTION: Ces fonctions sont pour la compatibilité uniquement
 * Pour de nouveaux développements, utilisez des méthodes de chiffrement modernes
 */

/**
 * Crypte un paramètre (fonction legacy - compatible avec l'ancien système)
 * ATTENTION: Cette méthode n'est PAS sécurisée, à utiliser uniquement pour la compatibilité
 */
function crypte(int $param): string {
    $param = (1234567 * $param);
    $param = (string)$param;
    
    $replacements = [
        "1" => "a",
        "3" => "x",
        "4" => "y", 
        "8" => "b",
        "0" => "m",
        "5" => "e"
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $param);
}

/**
 * Décrypte un paramètre (fonction legacy - compatible avec l'ancien système)
 * ATTENTION: Cette méthode n'est PAS sécurisée, à utiliser uniquement pour la compatibilité
 */
function decrypte(string $param): int {
    $replacements = [
        "a" => "1",
        "x" => "3", 
        "y" => "4",
        "b" => "8",
        "m" => "0",
        "e" => "5"
    ];
    
    $param = str_replace(array_keys($replacements), array_values($replacements), $param);
    
    if (!is_numeric($param)) {
        return 0;
    }
    
    return (int)($param / 1234567);
}

/**
 * Chiffrement moderne et sécurisé pour les nouveaux développements
 * Utilise OpenSSL avec AES-256-CBC
 */
function encrypt_modern(string $data, string $key = ''): string {
    if (empty($key)) {
        global $app_config;
        $key = $app_config['encryption_key'] ?? 'default_key_change_me';
    }
    
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', hash('sha256', $key), 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

/**
 * Déchiffrement moderne et sécurisé
 */
function decrypt_modern(string $encrypted_data, string $key = ''): string {
    if (empty($key)) {
        global $app_config;
        $key = $app_config['encryption_key'] ?? 'default_key_change_me';
    }
    
    $data = base64_decode($encrypted_data);
    if ($data === false) {
        return '';
    }
    
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    
    $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', hash('sha256', $key), 0, $iv);
    
    return $decrypted !== false ? $decrypted : '';
}

/**
 * Génère un hash sécurisé pour les mots de passe
 */
function hash_password(string $password): string {
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
}

/**
 * Vérifie un mot de passe contre son hash
 */
function verify_password(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Génère un token sécurisé
 */
function generate_secure_token(int $length = 32): string {
    try {
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        // Fallback moins sécurisé
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}

/**
 * Génère un UUID v4
 */
function generate_uuid(): string {
    try {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    } catch (Exception $e) {
        // Fallback
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

/**
 * Hache de manière sécurisée avec salt
 */
function secure_hash(string $data, string $salt = ''): string {
    if (empty($salt)) {
        $salt = generate_secure_token(16);
    }
    
    return hash('sha256', $salt . $data . $salt);
}

/**
 * Vérifie l'intégrité d'une chaîne avec HMAC
 */
function verify_integrity(string $data, string $signature, string $key = ''): bool {
    if (empty($key)) {
        global $app_config;
        $key = $app_config['integrity_key'] ?? 'default_integrity_key';
    }
    
    $expected_signature = hash_hmac('sha256', $data, $key);
    
    return hash_equals($expected_signature, $signature);
}

/**
 * Génère une signature HMAC pour vérifier l'intégrité
 */
function generate_signature(string $data, string $key = ''): string {
    if (empty($key)) {
        global $app_config;
        $key = $app_config['integrity_key'] ?? 'default_integrity_key';
    }
    
    return hash_hmac('sha256', $data, $key);
}

/**
 * Nettoie et valide une entrée utilisateur
 */
function sanitize_input(string $input, string $type = 'string'): string {
    // Suppression des caractères de contrôle
    $input = preg_replace('/[\x00-\x1F\x7F]/', '', $input);
    
    return match($type) {
        'email' => filter_var(trim($input), FILTER_SANITIZE_EMAIL),
        'url' => filter_var(trim($input), FILTER_SANITIZE_URL),
        'int' => (string)filter_var($input, FILTER_SANITIZE_NUMBER_INT),
        'float' => (string)filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'html' => htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        default => trim(strip_tags($input))
    };
}

/**
 * Génère un nonce pour les formulaires (protection CSRF avancée)
 */
function generate_nonce(string $action = 'default'): string {
    $time = time();
    $data = $action . $time . (session_id() ?? '');
    $hash = generate_signature($data);
    
    return base64_encode($time . '|' . $hash);
}

/**
 * Vérifie un nonce (validité de 1 heure)
 */
function verify_nonce(string $nonce, string $action = 'default', int $max_age = 3600): bool {
    $decoded = base64_decode($nonce);
    if ($decoded === false) {
        return false;
    }
    
    $parts = explode('|', $decoded, 2);
    if (count($parts) !== 2) {
        return false;
    }
    
    [$time, $hash] = $parts;
    
    // Vérifier l'âge
    if ((time() - (int)$time) > $max_age) {
        return false;
    }
    
    // Vérifier la signature
    $expected_data = $action . $time . (session_id() ?? '');
    $expected_hash = generate_signature($expected_data);
    
    return hash_equals($expected_hash, $hash);
}
?>