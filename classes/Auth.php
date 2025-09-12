<?php
/**
 * Classe d'authentification moderne
 */
class Auth {
    private static ?User $currentUser = null;
    
    /**
     * Initialise les sessions sécurisées
     */
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
            
            // Sécurité sessions
            ini_set('session.cookie_httponly', '1');
            // FIX: détecter HTTPS de manière robuste (https=on/1 ou server port 443)
            $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) === '443');
            ini_set('session.cookie_secure', $https ? '1' : '0');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_samesite', 'Lax');
            
            session_name($app_config['session_name']);
            session_start();
            
            // Régénération périodique de l'ID de session
            if (!isset($_SESSION['last_regeneration'])) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Tentative de connexion
     */
    public static function login(string $email, string $password): array {
        // Validation des données
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email et mot de passe requis'];
        }
        
        if (!User::validateEmail($email)) {
            return ['success' => false, 'message' => 'Format d\'email invalide'];
        }
        
        // Protection contre le brute force (simple)
        if (self::isBlocked($email)) {
            return ['success' => false, 'message' => 'Trop de tentatives. Réessayez dans 15 minutes.'];
        }
        
        // Tentative de connexion
        $user = new User(0, $email, $password);
        $user->TestConnexion();
        
        if ($user->isLoggedIn()) {
            // Connexion réussie
            $_SESSION['user_data'] = $user->getSessionData();
            $_SESSION['login_time'] = time();
            
            // Nettoyer les tentatives échouées
            unset($_SESSION['failed_login_attempts'][$email]);
            
            // Log de sécurité
            self::logSecurityEvent('login_success', $email);
            
            return [
                'success' => true, 
                'message' => 'Connexion réussie',
                'user' => $user,
                'redirect' => $user->isCouturiere() ? '/show/index.php' : '/home'
            ];
        } else {
            // Connexion échouée
            self::recordFailedLogin($email);
            self::logSecurityEvent('login_failed', $email);
            
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        }
    }
    
    /**
     * Déconnexion
     */
    public static function logout(): void {
        if (self::isLoggedIn()) {
            $user = self::getCurrentUser();
            if ($user) {
                self::logSecurityEvent('logout', $user->mEmail);
            }
        }
        
        // Destruction complète de la session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_data']) && isset($_SESSION['login_time']);
    }
    
    /**
     * Récupère l'utilisateur actuel
     */
    public static function getCurrentUser(): ?User {
        if (self::$currentUser === null && self::isLoggedIn()) {
            self::$currentUser = User::fromSessionData($_SESSION['user_data']);
        }
        return self::$currentUser;
    }
    
    /**
     * Vérifie l'accès et redirige si nécessaire
     */
    public static function requireAuth(): void {
        if (!self::isLoggedIn()) {
            self::redirect('/login');
        }
        
        $user = self::getCurrentUser();
        if ($user && $user->isCouturiere()) {
            self::redirect('/show/index.php');
        }
    }
    
    /**
     * Protection contre le brute force
     */
    private static function isBlocked(string $email): bool {
        if (!isset($_SESSION['failed_login_attempts'][$email])) {
            return false;
        }
        
        $attempts = $_SESSION['failed_login_attempts'][$email];
        $maxAttempts = 5;
        $blockDuration = 900; // 15 minutes
        
        if ($attempts['count'] >= $maxAttempts) {
            return (time() - $attempts['last_attempt']) < $blockDuration;
        }
        
        return false;
    }
    
    /**
     * Enregistre une tentative de connexion échouée
     */
    private static function recordFailedLogin(string $email): void {
        if (!isset($_SESSION['failed_login_attempts'][$email])) {
            $_SESSION['failed_login_attempts'][$email] = ['count' => 0, 'last_attempt' => 0];
        }
        
        $_SESSION['failed_login_attempts'][$email]['count']++;
        $_SESSION['failed_login_attempts'][$email]['last_attempt'] = time();
    }
    
    /**
     * Redirection sécurisée
     */
    public static function redirect(string $url): void {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        if (!str_starts_with($url, 'http')) {
            $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
            $url = $app_config['base_url'] . $url;
        }
        
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Génération de token CSRF
     */
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérification de token CSRF
     */
    public static function verifyCSRFToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Log des événements de sécurité
     */
    private static function logSecurityEvent(string $event, string $email): void {
        $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
        $logFile = $app_config['app_root'] . '/logs/security.log';
        
        $logEntry = sprintf(
            "[%s] %s - Email: %s - IP: %s - User-Agent: %s\n",
            date('Y-m-d H:i:s'),
            $event,
            $email,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>