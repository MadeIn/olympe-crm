<?php declare(strict_types=1);
/**
 * Page de connexion CRM Olympe Mariage (PHP 8)
 * Chemin: /pages/login/index.php
 * URL:    /login   (via front-controller)
 */

/*-------------------------
| Bootstrap & config
--------------------------*/
$app_config = require __DIR__ . '/../../config/app.php';

if (($app_config['environment'] ?? 'prod') === 'dev') {
    ini_set('display_errors', '1');
    error_reporting((int)($app_config['error_reporting']['dev'] ?? E_ALL));
} else {
    ini_set('display_errors', '0');
    error_reporting((int)($app_config['error_reporting']['prod'] ?? 0));
}

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Auth.php';

Auth::initSession();


/*-------------------------
| Si déjà connecté → redirige
--------------------------*/
if (Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    if ($user) {
        $redirect = $user->isCouturiere() ? '/show/index' : '/home/index';
        Auth::redirect($redirect);
    }
}

/*-------------------------
| Traitement formulaire
--------------------------*/
$message = '';
$messageType = '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = (string)($_POST['action'] ?? '');

if ($method === 'POST') {
    // Vérification CSRF
    if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de sécurité invalide. Veuillez réessayer.';
        $messageType = 'danger';
    } else {
        if ($action === 'login') {
            $email    = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            // Optionnel: filtre minimal
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
                $message = 'Veuillez saisir un email et un mot de passe valides.';
                $messageType = 'danger';
            } else {
                $loginResult = Auth::login($email, $password);
                if (!empty($loginResult['success'])) {
                    // si Auth::login fournit déjà une redirection, on l’utilise
                    $to = (string)($loginResult['redirect'] ?? '/');
                    Auth::redirect($to);
                } else {
                    $message = (string)($loginResult['message'] ?? 'Identifiants invalides.');
                    $messageType = 'danger';
                }
            }
        } elseif ($action === 'forgot_password') {
            $email = trim((string)($_POST['email'] ?? ''));
            if ($email !== '' && User::validateEmail($email)) {
                // TODO: implémenter l’envoi du mail
                $message = 'Si cet email existe, vous recevrez un lien de réinitialisation.';
                $messageType = 'info';
            } else {
                $message = 'Veuillez saisir un email valide.';
                $messageType = 'danger';
            }
        }
    }
}

/*-------------------------
| CSRF token (unique pour la vue)
--------------------------*/
$csrfToken = Auth::generateCSRFToken();

// Valeur collée si tentative échouée
$postedEmail = (string)($_POST['email'] ?? '');
$appName     = (string)($app_config['app_name'] ?? 'CRM');
$appVersion  = (string)($app_config['app_version'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?= h($appName) ?> - Connexion</title>
    <!-- X-UA-Compatible est obsolète ; à enlever si plus de support IE -->
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="<?= h($appName) ?> - Connexion" name="description" />

    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/pages/css/login-5.min.css" rel="stylesheet" type="text/css" />

    <link rel="icon" href="/favicon.ico" />
</head>

<body class="login">
<div class="user-login-5">
    <div class="row bs-reset">
        <div class="col-md-6 bs-reset mt-login-5-bsfix">
            <div class="login-bg" style="background-image:url(/assets/pages/img/login/bg1.jpg)">
                <img class="login-logo" src="/assets/images/logo-olympe.png" alt="<?= h($appName) ?>" />
            </div>
        </div>

        <div class="col-md-6 login-container bs-reset mt-login-5-bsfix">
            <div class="login-content">
                <h1>Connexion <?= h($appName) ?></h1>
                <?php if ($appVersion !== ''): ?>
                    <p>Version <?= h($appVersion) ?></p>
                <?php endif; ?>

                <!-- Messages d'erreur/succès -->
                <?php if ($message !== ''): ?>
                    <div class="alert alert-<?= h($messageType) ?> alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?= h($message) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de connexion -->
                <form action="/login" class="login-form" method="post" id="loginForm" autocomplete="off" novalidate>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">

                    <div class="alert alert-danger display-hide" id="loginError">
                        <button class="close" data-close="alert"></button>
                        <span>Veuillez saisir votre email et mot de passe</span>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <input class="form-control form-control-solid placeholder-no-fix form-group"
                                   type="email"
                                   name="email"
                                   placeholder="Email"
                                   value="<?= h($postedEmail) ?>"
                                   required
                                   autocomplete="email" />
                        </div>
                        <div class="col-xs-6">
                            <input class="form-control form-control-solid placeholder-no-fix form-group"
                                   type="password"
                                   name="password"
                                   placeholder="Mot de passe"
                                   required
                                   autocomplete="current-password" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <!-- Espace réservé pour options futures -->
                        </div>
                        <div class="col-sm-8 text-right">
                            <div class="forgot-password">
                                <a href="#" id="forget-password" class="forget-password">Mot de passe oublié ?</a>
                            </div>
                            <button class="btn green" type="submit">
                                <i class="fa fa-sign-in"></i> Connexion
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Formulaire mot de passe oublié -->
                <form class="forget-form" action="/login" method="post" style="display: none;">
                    <input type="hidden" name="action" value="forgot_password">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">

                    <h3 class="font-green">Mot de passe oublié ?</h3>
                    <p>Saisissez votre email pour recevoir un lien de réinitialisation.</p>

                    <div class="form-group">
                        <input class="form-control placeholder-no-fix"
                               type="email"
                               name="email"
                               placeholder="Email"
                               required />
                    </div>

                    <div class="form-actions">
                        <button type="button" id="back-btn" class="btn green btn-outline">
                            <i class="fa fa-arrow-left"></i> Retour
                        </button>
                        <button type="submit" class="btn btn-success uppercase pull-right">
                            <i class="fa fa-envelope"></i> Envoyer
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="/assets/global/plugins/jquery.min.js"></script>
<script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/global/plugins/js.cookie.min.js"></script>
<script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="/assets/global/plugins/jquery.blockui.min.js"></script>
<script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="/assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="/assets/global/plugins/select2/js/select2.full.min.js"></script>
<script src="/assets/global/plugins/backstretch/jquery.backstretch.min.js"></script>
<script src="/assets/global/scripts/app.min.js"></script>
<script src="/assets/pages/scripts/login-5.min.js"></script>

<script>
$(function() {
    // Gestion du formulaire "mot de passe oublié"
    $('#forget-password').on('click', function(e) {
        e.preventDefault();
        $('.login-form').slideUp();
        $('.forget-form').slideDown();
    });

    $('#back-btn').on('click', function(e) {
        e.preventDefault();
        $('.forget-form').slideUp();
        $('.login-form').slideDown();
    });

    // Validation du formulaire
    $('#loginForm').validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        rules: {
            email: { required: true, email: true },
            password: { required: true, minlength: 1 }
        },
        messages: {
            email: { required: "L'email est obligatoire", email: "Format d'email invalide" },
            password: { required: "Le mot de passe est obligatoire" }
        },
        invalidHandler: function() { $('#loginError').removeClass('display-hide').show(); },
        highlight: function(el) { $(el).closest('.form-group').addClass('has-error'); },
        unhighlight: function(el) { $(el).closest('.form-group').removeClass('has-error'); },
        success: function(label) { label.closest('.form-group').removeClass('has-error'); }
    });

    // Auto-focus
    $('input[name="email"]').trigger('focus');
});
</script>
</body>
</html>
