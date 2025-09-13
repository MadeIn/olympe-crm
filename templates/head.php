<?php declare(strict_types=1);
/**
 * Head template — PHP 8 compatible, safe escaping
 * Expected vars (nullable): $titre_page, $desc_page, $link_plugin (HTML markup)
 */

/** @var string|null $titre_page */
/** @var string|null $desc_page */
/** @var string|null $link_plugin */

$titre_page  = (string)($titre_page  ?? '');
$desc_page   = (string)($desc_page   ?? '');
$link_plugin = $link_plugin ?? '';

if (!function_exists('h')) {
    function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title><?= h($titre_page) ?></title>
        <!-- X-UA-Compatible is obsolete, keep only if you still must support legacy IE -->
        <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="<?= h($desc_page) ?>" />
        <meta name="author" content="Made In SARL" />
        <meta name="csrf-token" content="<?= h(Auth::generateCSRFToken()) ?>">
        <!-- BEGIN LAYOUT FIRST STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
        <!-- END LAYOUT FIRST STYLES -->

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <?= $link_plugin /* trusted HTML (e.g., extra <link> tags) */ ?>
        <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <link href="/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->

        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="/assets/layouts/layout5/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/layouts/layout5/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->

        <link rel="icon" type="image/png" href="/favicon.png" />
        <script src="/assets/js/olympe.js"></script>
    </head>
    <!-- END HEAD -->

