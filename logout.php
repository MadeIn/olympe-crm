<?php
/**
 * Page de déconnexion
 */

// Chargement des classes nécessaires
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

// Configuration
$app_config = require 'config/app.php';

// Initialisation des sessions
Auth::initSession();

// Message de confirmation
$message = '';
$messageType = 'info';

// Si c'est une requête POST avec confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'confirm_logout') {
        // Vérification CSRF
        if (Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Auth::logout();
            // La méthode logout() redirige automatiquement, mais au cas où...
            header('Location: /index.php');
            exit();
        } else {
            $message = 'Token de sécurité invalide.';
            $messageType = 'danger';
        }
    } elseif ($action === 'cancel') {
        // Retour vers la page d'accueil ou la page précédente
        $redirect = $_POST['return_url'] ?? '/home.php';
        
        // Validation de l'URL de retour pour éviter les redirections malveillantes
        if (filter_var($redirect, FILTER_VALIDATE_URL) === false && strpos($redirect, '/') === 0) {
            header('Location: ' . $redirect);
            exit();
        }
        header('Location: /home.php');
        exit();
    }
}

// Si pas encore connecté, rediriger vers la page de connexion
if (!Auth::isLoggedIn()) {
    header('Location: /index.php');
    exit();
}

// Récupérer l'utilisateur actuel pour affichage
$user = Auth::getCurrentUser();

// URL de retour (page précédente)
$returnUrl = $_GET['return'] ?? $_SERVER['HTTP_REFERER'] ?? '/home.php';

// Fonction d'échappement HTML
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?= h($app_config['app_name']) ?> - Déconnexion</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
        }
        
        .logout-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logout-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        
        .logout-box h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .logout-box .user-info {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .logout-box .user-info strong {
            color: #495057;
        }
        
        .btn-logout {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
            margin: 10px;
            min-width: 120px;
        }
        
        .btn-logout:hover {
            background: #c82333;
            border-color: #bd2130;
            color: white;
        }
        
        .btn-cancel {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
            margin: 10px;
            min-width: 120px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            border-color: #545b62;
            color: white;
        }
        
        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }
        
        .security-info {
            margin-top: 30px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        
        .security-info small {
            color: #856404;
        }
    </style>
</head>

<body>
    <div class="logout-container">
        <div class="logout-box">
            <!-- Logo -->
            <img src="/assets/images/logo-olympe.png" alt="<?= h($app_config['app_name']) ?>" class="logo" />
            
            <h2><i class="fa fa-sign-out"></i> Déconnexion</h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= h($messageType) ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= h($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($user): ?>
                <div class="user-info">
                    <p><strong>Utilisateur connecté :</strong><br>
                       <?= h($user->getFullName()) ?><br>
                       <small class="text-muted"><?= h($user->mEmail) ?></small>
                    </p>
                    
                    <p><strong>Dernière connexion :</strong><br>
                       <small><?= h(format_datetime($user->mDatederConn ?? date('Y-m-d H:i:s'))) ?></small>
                    </p>
                </div>
            <?php endif; ?>
            
            <p class="text-muted">
                Êtes-vous sûr(e) de vouloir vous déconnecter ?<br>
                <small>Cette action fermera votre session en toute sécurité.</small>
            </p>
            
            <!-- Formulaire de confirmation -->
            <form method="post" action="<?= h($_SERVER['PHP_SELF']) ?>">
                <input type="hidden" name="csrf_token" value="<?= h(Auth::generateCSRFToken()) ?>">
                <input type="hidden" name="return_url" value="<?= h($returnUrl) ?>">
                
                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" name="action" value="confirm_logout" class="btn btn-logout">
                        <i class="fa fa-sign-out"></i> Oui, me déconnecter
                    </button>
                    
                    <button type="submit" name="action" value="cancel" class="btn btn-cancel">
                        <i class="fa fa-arrow-left"></i> Annuler
                    </button>
                </div>
            </form>
            
            <!-- Information de sécurité -->
            <div class="security-info">
                <small>
                    <i class="fa fa-shield"></i>
                    <strong>Sécurité :</strong> Pour votre protection, fermez complètement votre navigateur après déconnexion si vous utilisez un ordinateur partagé.
                </small>
            </div>
            
            <!-- Lien direct si JavaScript désactivé -->
            <div style="margin-top: 20px;">
                <small class="text-muted">
                    <a href="/index.php" class="text-muted">
                        <i class="fa fa-home"></i> Retour à la page de connexion
                    </a>
                </small>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            // Focus sur le bouton de déconnexion
            $('.btn-logout').focus();
            
            // Raccourci clavier : Echap pour annuler
            $(document).keyup(function(e) {
                if (e.keyCode === 27) { // Echap
                    $('button[value="cancel"]').click();
                }
            });
            
            // Raccourci clavier : Entrée pour confirmer
            $(document).keyup(function(e) {
                if (e.keyCode === 13 && !$('input, textarea').is(':focus')) { // Entrée
                    $('.btn-logout').click();
                }
            });
            
            // Animation au chargement
            $('.logout-box').hide().fadeIn(500);
            
            // Confirmation supplémentaire avant déconnexion
            $('.btn-logout').click(function(e) {
                $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Déconnexion...');
                // Le formulaire se soumet normalement
            });
        });
    </script>
</body>
</html>

<?php
// Fonction utilitaire pour le formatage des dates (si pas déjà définie)
if (!function_exists('format_datetime')) {
    function format_datetime(string $datetime, string $format = 'd/m/Y H:i'): string {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return 'Non disponible';
        }
        
        try {
            $dt = new DateTime($datetime);
            return $dt->format($format);
        } catch (Exception $e) {
            return 'Date invalide';
        }
    }
}
?>