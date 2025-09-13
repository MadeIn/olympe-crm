<?php declare(strict_types=1);
/**
 * Page de déconnexion (legacy-compatible + front-controller)
 * URL d'accès : /logout (réécrite par le routeur vers ce fichier)
 */

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Auth.php';

// Config
$app_config = require __DIR__ . '/config/app.php';

// Session
Auth::initSession();

/**
 * Valide une URL de retour : doit être relative (commencer par "/") pour éviter l'open redirect.
 */
function safe_return_url(?string $u, string $fallback = '/home'): string {
    $u = trim((string)$u);
    if ($u !== '' && str_starts_with($u, '/')) return $u;
    return $fallback;
}

/**
 * Déconnexion sûre (si ta classe Auth::logout() ne redirige pas elle-même).
 */
function do_logout_and_redirect(string $to = '/login'): never {
    // Si Auth::logout() s'occupe de tout, tu peux simplement l'appeler :
    if (method_exists('Auth', 'logout')) {
        Auth::logout();
    } else {
        // Fallback : on gère ici
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
    header('Location: ' . $to, true, 302);
    exit;
}

/* ===========================================================
   1) Déconnexion immédiate si confirm=1 en GET (pratique pour liens)
   =========================================================== */
if (($_GET['confirm'] ?? '') === '1') {
    $returnUrl = safe_return_url($_GET['return'] ?? null, '/login');
    do_logout_and_redirect($returnUrl);
}

/* ===========================================================
   2) Traitement POST (boutons du formulaire)
   =========================================================== */
$message = '';
$messageType = 'info';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'confirm_logout') {
        if (Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $returnUrl = safe_return_url($_POST['return_url'] ?? null, '/login');
            do_logout_and_redirect($returnUrl);
        } else {
            $message = 'Token de sécurité invalide.';
            $messageType = 'danger';
        }
    } elseif ($action === 'cancel') {
        $returnUrl = safe_return_url($_POST['return_url'] ?? null, '/home');
        header('Location: ' . $returnUrl, true, 302);
        exit;
    }
}

/* ===========================================================
   3) Si pas connecté → va direct vers /login
   =========================================================== */
if (!Auth::isLoggedIn()) {
    header('Location: /login', true, 302);
    exit;
}

/* ===========================================================
   4) Rendu de la page de confirmation
   =========================================================== */
$user = Auth::getCurrentUser();
$returnUrl = safe_return_url($_GET['return'] ?? ($_SERVER['HTTP_REFERER'] ?? '/home'), '/home');
$csrf = Auth::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?= h(($app_config['app_name'] ?? 'CRM') . ' - Déconnexion') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500&family=Inter:wght@300;400;500&display=swap" rel="stylesheet" />
    
    <!-- External CSS -->
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/global/css/components.min.css" rel="stylesheet" />
    
    <!-- Page specific CSS -->
    <link href="/assets/css/logout.css" rel="stylesheet" />
</head>
<body>
<div class="logout-container">
    <div class="logout-box">
        <img src="/assets/images/olympe-mariage-logo.jpg" alt="<?= h($app_config['app_name'] ?? 'CRM') ?>" class="logo" />
        <h2>Déconnexion</h2>

        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= h($messageType) ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= h($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($user): ?>
            <div class="user-info">
                <p><strong>Utilisateur connecté :</strong><br>
                    <?= h($user->getFullName()) ?><br>
                    <small class="text-muted"><?= h($user->mEmail ?? '') ?></small>
                </p>
                <?php if (!empty($user->mDatederConn ?? '')): ?>
                    <p><strong>Dernière connexion :</strong><br>
                        <small><?= h((new DateTime($user->mDatederConn))->format('d/m/Y H:i')) ?></small>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p class="text-muted">
            Êtes-vous sûr(e) de vouloir vous déconnecter ?<br>
            <small>Cette action fermera votre session en toute sécurité.</small>
        </p>

        <!-- Boutons -->
        <div class="mt-3">
            <a href="/logout?confirm=1&return=/login" class="btn btn-logout">
                Oui, me déconnecter
            </a>
            <a href="/home" class="btn btn-cancel">
                Annuler
            </a>
        </div>

        <div class="security-info">
            <small><strong>Sécurité :</strong>
                fermez votre navigateur après déconnexion sur un poste partagé.</small>
        </div>

        <!-- Liens utiles -->
        <div class="bottom-links">
            <small class="text-muted">
                <a href="/login">Retour à la page de connexion</a>
                &nbsp;•&nbsp;
                <a href="/logout?confirm=1&return=/login">Déconnexion directe</a>
            </small>
        </div>
    </div>
</div>

<script src="/assets/global/plugins/jquery.min.js"></script>
<script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
<script>
$(function(){
    $('.logout-box').hide().fadeIn(200);
    $('.btn-logout').focus();
    $(document).on('keyup', function(e){
        if(e.key === 'Escape'){ 
            window.location.href = '/home';
        }
    });
});
</script>
</body>
</html>