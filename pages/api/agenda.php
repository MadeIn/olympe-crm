<?php declare(strict_types=1);

// /pages/api/agenda.php
require_once __DIR__ . '/../../param.php';
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Méthode invalide']); exit; }
if (!Auth::verifyCSRFToken($_POST['csrf_token'] ?? '')) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'CSRF']); exit; }

$int = fn(string $k): int => (int)($_POST[$k] ?? 0);
$str = fn(string $k): string => trim((string)($_POST[$k] ?? ''));

try {
  $mode = $_POST['mode'] ?? '';

  switch ($mode) {

    // Ancien "case 1" → remplir #select_client
    case 'prepare': {
      $theme = $int('type'); // valeur du <select id="theme">
      $html = '';

      if ($theme === 1) {
        // Select "Type"
        $html .= '<div class="form-group">';
        $html .=   '<label>Type</label>';
        $html .=   '<div class="input-group">';
        $html .=     '<span class="input-group-addon"><i class="fa fa-share-alt"></i></span>';
        $html .=     '<select name="type" id="type" class="form-control">';

        $sql = "SELECT * FROM rdv_types WHERE type_num NOT IN (2,3) ORDER BY type_pos ASC";
        $rows = $base->query($sql);
        foreach ($rows as $r) {
          $sel = ((int)$r['type_num'] === 1) ? ' selected' : '';
          $html .= '<option value="'.(int)$r['type_num'].'"'.$sel.'>'.h($r['type_nom']).'</option>';
        }

        $html .=     '</select>';
        $html .=   '</div>';
        $html .= '</div>';

        // Champ "Client" (on déclenche l’acompte au blur)
        $html .= '<div class="form-group">';
        $html .=   '<label>Client</label>';
        $html .=   '<div class="input-group">';
        $html .=     '<span class="input-group-addon"><i class="fa fa-search"></i></span>';
        $html .=     '<input type="text" name="client" id="client" class="form-control" placeholder="Nom du client" onblur="addAcompte();">';
        $html .=   '</div>';
        $html .= '</div>';
      } else {
        // Pas de client pour ce thème
        $html .= '<input type="hidden" name="type" value="0"><input type="hidden" name="client" value="0">';
      }

      echo json_encode(['ok'=>true,'html'=>$html,'place'=>'select_client']);
      break;
    }

    // Ancien "case 2" → remplir #select_acompte
    case 'acompte': {
      $type   = $int('type');
      $client = $str('client');                 // "Nom Prénom [123]"
      $ids    = recupValeurEntreBalise($client, "[", "]");
      $client_num = (int)($ids[0] ?? 0);

      $html = '';

      if ($client_num > 0) {
        $rcl = $base->queryRow("SELECT * FROM clients WHERE client_num='".(int)$client_num."'");
        if ($rcl) {
          $sql = "SELECT * FROM commandes
                  WHERE client_num='".(int)$client_num."'
                    AND devis_num != 0
                    AND commande_num != 0
                    AND facture_num = 0
                  ORDER BY commande_date DESC";
          $co = $base->query($sql);

          if (count($co) > 0) {
            $html .= '<label>Acompte</label>';
            $html .= '<div class="input-group">';
            $html .=   '<span class="input-group-addon"><i class="fa fa-euro"></i></span>';
            $html .=   '<select name="dernier_acompte" class="form-control">';
            foreach ($co as $rco) {
              $reste = safe_number_format(resteAPayerCommande((int)$rco['id']), 2, '.', ' ');
              $html .= '<option value="'.h($reste).'">Commande : '.h($rco['commande_num']).' - Acompte : '.h($reste).' €</option>';
            }
            $html .=   '</select>';
            $html .= '</div>';
          } else {
            $html .= '<input type="hidden" name="dernier_acompte" value="0">';
          }
        } else {
          $html .= '<input type="hidden" name="dernier_acompte" value="0">';
        }
      } else {
        $html .= '<input type="hidden" name="dernier_acompte" value="0">';
      }

      echo json_encode(['ok'=>true,'html'=>$html,'place'=>'select_acompte']);
      break;
    }

    default:
      http_response_code(400);
      echo json_encode(['ok'=>false,'error'=>'Mode inconnu']);
  }

} catch (Throwable $e) {
  $env = $app_config['environment'] ?? 'prod';
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$env==='dev' ? ('Exception: '.$e->getMessage()) : 'Erreur serveur']);
}
