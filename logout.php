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
 * Valide une URL de retour : doit être relative (commencer par "/") pour éviter l’open redirect.
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
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&display=swap" rel="stylesheet" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/global/css/components.min.css" rel="stylesheet" />
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;font-family:'Open Sans',sans-serif}
        .logout-container{min-height:100vh;display:flex;align-items:center;justify-content:center}
        .logout-box{background:#fff;border-radius:10px;box-shadow:0 15px 35px rgba(0,0,0,.1);padding:40px;max-width:520px;width:92%;text-align:center}
        .logo{max-width:200px;margin-bottom:30px}
        .user-info{background:#f8f9fa;border-radius:5px;padding:20px;margin:20px 0}
        .btn-logout{background:#dc3545;border-color:#dc3545;color:#fff;margin:10px;min-width:140px}
        .btn-logout:hover{background:#c82333;border-color:#bd2130}
        .btn-cancel{background:#6c757d;border-color:#6c757d;color:#fff;margin:10px;min-width:140px}
        .btn-cancel:hover{background:#5a6268;border-color:#545b62}
        .security-info{margin-top:30px;padding:15px;background:#fff3cd;border-radius:5px;border-left:4px solid #ffc107}
    </style>
</head>
<body>
<div class="logout-container">
    <div class="logout-box">
        <img src="/assets/images/logo-olympe.png" alt="<?= h($app_config['app_name'] ?? 'CRM') ?>" class="logo" />
        <h2><i class="fa fa-sign-out"></i> Déconnexion</h2>

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

        <!-- Formulaire -->
        <div class="mt-3">
            <a href="/logout?confirm=1&return=/login" class="btn btn-logout">
                <i class="fa fa-sign-out"></i> Oui, me déconnecter
			</a>
			 <a href="/home" class="btn btn-cancel">
               <i class="fa fa-arrow-left"></i> Annuler
			</a>
		</div>

        <div class="security-info">
            <small><i class="fa fa-shield"></i> <strong>Sécurité :</strong>
                fermez votre navigateur après déconnexion sur un poste partagé.</small>
        </div>

        <!-- Liens utiles -->
        <div style="margin-top:20px">
            <small class="text-muted">
                <a href="/login"><i class="fa fa-home"></i> Retour à la page de connexion</a>
                &nbsp;•&nbsp;
                <a href="/logout?confirm=1&return=/login"><i class="fa fa-bolt"></i> Déconnexion directe</a>
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
        if(e.key === 'Escape'){ $('button[name="action"][value="cancel"]').trigger('click'); }
    });

});
</script>
</body>
</html>
