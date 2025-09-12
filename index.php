<?php
/**
 * Page de connexion CRM Olympe Mariage
 */
// Configuration des erreurs
$app_config = require 'config/app.php';
if ($app_config['environment'] === 'dev') {
    ini_set('display_errors', 1);
    error_reporting($app_config['error_reporting']['dev']);
} else {
    ini_set('display_errors', 0);
    error_reporting($app_config['error_reporting']['prod']);
}


// Configuration et initialisation
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

// Initialisation des sessions
Auth::initSession();

// Si déjà connecté, rediriger
if (Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    if ($user) {
        $redirect = $user->isCouturiere() ? '/show/index.php' : '/home/index.php';
        Auth::redirect($redirect);
    }
}

$message = '';
$messageType = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        // Vérification CSRF
        if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $message = 'Token de sécurité invalide. Veuillez réessayer.';
            $messageType = 'danger';
        } else {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $loginResult = Auth::login($email, $password);
            
            if ($loginResult['success']) {
                Auth::redirect($loginResult['redirect']);
            } else {
                $message = $loginResult['message'];
                $messageType = 'danger';
            }
        }
    } elseif ($action === 'forgot_password') {
        // TODO: Implémenter la récupération de mot de passe
        $email = trim($_POST['email'] ?? '');
        if (!empty($email) && User::validateEmail($email)) {
            $message = 'Si cet email existe, vous recevrez un lien de réinitialisation.';
            $messageType = 'info';
            // Ici, implémenter l'envoi d'email
        } else {
            $message = 'Veuillez saisir un email valide.';
            $messageType = 'danger';
        }
    }
}

// Fonction d'échappement HTML
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?= h($app_config['app_name']) ?> - Connexion</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="<?= h($app_config['app_name']) ?> - Connexion" name="description" />
    
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
    
    <link rel="shortcut icon" href="favicon.ico" />
</head>

<body class="login">
    <div class="user-login-5">
        <div class="row bs-reset">
            <div class="col-md-6 bs-reset mt-login-5-bsfix">
                <div class="login-bg" style="background-image:url(/assets/pages/img/login/bg1.jpg)">
                    <img class="login-logo" src="/assets/images/logo-olympe.png" alt="<?= h($app_config['app_name']) ?>" />
                </div>
            </div>
            
            <div class="col-md-6 login-container bs-reset mt-login-5-bsfix">
                <div class="login-content">
                    <h1>Connexion <?= h($app_config['app_name']) ?></h1>
                    <p>Version <?= h($app_config['app_version']) ?></p>
                    
                    <!-- Messages d'erreur/succès -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?= $messageType ?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= h($message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Formulaire de connexion -->
                    <form action="<?= h($_SERVER['PHP_SELF']) ?>" class="login-form" method="post" id="loginForm">
                        <input type="hidden" name="action" value="login">
                        <input type="hidden" name="csrf_token" value="<?= h(Auth::generateCSRFToken()) ?>">
                        
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
                                       value="<?= h($_POST['email'] ?? '') ?>"
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
                                    <a href="javascript:;" id="forget-password" class="forget-password">Mot de passe oublié ?</a>
                                </div>
                                <button class="btn green" type="submit">
                                    <i class="fa fa-sign-in"></i> Connexion
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Formulaire mot de passe oublié -->
                    <form class="forget-form" action="<?= h($_SERVER['PHP_SELF']) ?>" method="post" style="display: none;">
                        <input type="hidden" name="action" value="forgot_password">
                        <input type="hidden" name="csrf_token" value="<?= h(Auth::generateCSRFToken()) ?>">
                        
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
    <script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/login-5.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            // Gestion du formulaire "mot de passe oublié"
            $('#forget-password').click(function(e) {
                e.preventDefault();
                $('.login-form').slideUp();
                $('.forget-form').slideDown();
            });
            
            $('#back-btn').click(function(e) {
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
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 1
                    }
                },
                messages: {
                    email: {
                        required: "L'email est obligatoire",
                        email: "Format d'email invalide"
                    },
                    password: {
                        required: "Le mot de passe est obligatoire"
                    }
                },
                invalidHandler: function(event, validator) {
                    $('#loginError').removeClass('display-hide').show();
                },
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                }
            });
            
            // Auto-focus sur le premier champ
            $('input[name="email"]').focus();
        });
    </script>
</body>
</html>