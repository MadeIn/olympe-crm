<?php

include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_auto.php"); 

// Assurez-vous que ce script est accessible via une URL publique.

// Récupération du corps de la requête
$content = file_get_contents("php://input");
// Conversion du JSON en objet PHP
$data = json_decode($content, true);

// Vous pouvez ici ajouter des logiques pour gérer les données, par exemple :
file_put_contents("calendly_events_group.log", print_r($data, true), FILE_APPEND);

// Réponse simple pour confirmer la réception
//echo "Webhook received";

switch ($data["event"]) {
    case "invitee.created" : // Nouvelle inscription
        $event = $data["payload"];
        $timestamp = strtotime(str_replace('/', '-', $event["questions_and_answers"][2]["answer"]));
        $datemariage = date('Y-m-d', $timestamp);
        $user = array(
            'nom'           => $event["last_name"],
            'prenom'        => $event["first_name"],
            'email'         => $event["email"],
            'tel'           => $event["questions_and_answers"][0]["answer"],
            'cp'            => $event["questions_and_answers"][1]["answer"],
            'date-mariage'  => $datemariage
        );


        $description = "Avez-vous repéré des modèles que vous aimeriez essayer ? " . $event["questions_and_answers"][3]["answer"] . ". Quelle est votre fourchette de prix pour votre robe de mariée ? " . $event["questions_and_answers"][4]["answer"] . ". Souhaitez-vous ajouter un élément qui pourrait nous être utile pour mieux vous accompagner ? " . $event["questions_and_answers"][5]["answer"] . ".";

        $date_debut = str_replace("T"," ",$event["scheduled_event"]["start_time"]);
        $date_fin = str_replace("T"," ",$event["scheduled_event"]["end_time"]);
        $date_debut = substr($date_debut,0,19);
        $date_fin = substr($date_fin,0,19);
        
		$date_debut = date("Y-m-d H:i:s",strtotime($date_debut . ' +2 hours'));
		$date_fin = date("Y-m-d H:i:s",strtotime($date_fin . ' +2 hours'));
        $organisateur = $event["scheduled_event"]["event_memberships"][0]["user_email"];
        if ($organisateur=="lauriane@olympe-mariage.com")
            $user_num = 8;
        else
            $user_num = 22;

        // On test si le client n'exite pas
        $sql = "select * from clients where client_mail=" . sql_safe($mail);
        $rtt = $base->queryRow($sql);
        $nbr = count($rtt);
        if ($nbr==0) {
            $sql = "insert into clients values (0,0," . sql_safe($user["nom"]) . "," . sql_safe($user["prenom"]) . ",'',''," . sql_safe($user["cp"]) . ",''," . sql_safe($user["tel"]) . "," . sql_safe($user["email"]) . "," . sql_safe($user["date-mariage"]) . ",'','','','3'," . sql_safe($user_num) . "," . sql_safe(Date("Y-m-d H:i:s")) . "," . sql_safe(Date("Y-m-d H:i:s")) . ",'','','','','','','','','','','','',0,0)";
            $client_num = $base->insert($sql);
        } else {
            if ($rtt) {
                $client_num = $rtt["client_num"];
            }
        }	

        // Premier RDV
        $type = 1;

        // On recherche le client 
        $sql = "select * from clients where client_num=" . sql_safe($client_num) . "";
        $rcl = $base->queryRow($sql);
        if ($rcl) {
            if ($rcl["client_genre"]==0)
                $genre = "Mme";
            else
                $genre = "Mr";

            $client_nom_complet = str_replace("'","\'",$rcl["client_nom"]) . " " . $rcl["client_prenom"];
            
            // On regarde si on a pas déjà un rendez vous 
            $sql = "select * from rendez_vous where client_num=" . sql_safe($client_num) . " and type_num=" . sql_safe($type) . "";
            $rtt = $base->queryRow($sql);
 if ($rtt) {
                $sql = "delete from rendez_vous where rdv_num=" . sql_safe($rtt["rdv_num"]) . "";
                $base->query($sql);
                    
                $sql = "delete from calendriers where rdv_num=" . sql_safe($rtt["rdv_num"]) . "";
                $base->query($sql);
            }
            
            // On insere un Rendez vous
            $date_rdv = $date_debut;
            $sql = "insert into rendez_vous values(0," . sql_safe($client_num) . "," . sql_safe($type) . "," . sql_safe($date_rdv) . ",'',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00'," . sql_safe($user_num) . ")";
            file_put_contents("calendly_events_group.log", $sql, FILE_APPEND);
            $num = $base->insert($sql);
            
            // On ajoute dans le calendrier du user
            switch ($type) {
                case 1: // 1er RDV
                    $theme = 1;
                    
                    $titre = $client_nom_complet;
                    $desc = str_replace("'","\'",$description);
                    
                    // On insere en bdd
                    $sql = "insert into calendriers values(0," . sql_safe($date_debut) . "," . sql_safe($date_fin) . "," . sql_safe($theme) . "," . sql_safe($titre) . "," . sql_safe($desc) . "," . sql_safe($user_num) . ",'3'," . sql_safe($client_num) . "," . sql_safe($num) . ")";
                    file_put_contents("calendly_events_group.log", $sql, FILE_APPEND);
                    $base->query($sql);
                    
                    // On envoi le mail selon le type de RDV
                    $template = getEmailTemplate(1,$rcl["client_genre"]);
				    $titre_mail = $template["titre"];
				    $message_mail = $template["message"];
                    $titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
                    $message_mail = str_replace("[PRENOM]",$rcl["client_prenom"],$message_mail);
                    $message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
                    $message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
                    $adresse = $u->mShowroomInfo["showroom_adr1"];
                    if ($u->mShowroomInfo["showroom_adr2"]!="")
                        $adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
                    $adresse = $adresse;
                    $message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
                    $message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_ACCES]",$u->mShowroomInfo["showroom_acces"],$message_mail);
        
                    // On envoi le mail
                    SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
                    
                    $sql = "update rendez_vous set rdv_mail=1, rdv_mail_date=" . sql_safe(Date("Y-m-d H:i:s")) . " where rdv_num=" . sql_safe($num) . "";
                    $base->query($sql);
                    
                break;
            }
        }
    break;

    case "invitee.canceled": // Annulation de rendez vous
    break;
}

?>