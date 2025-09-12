<?php

$calendly = array(
    "created_at" => "2024-07-22T14:54:47.000000Z",
    "created_by" => "https://api.calendly.com/users/df7aef5e-eef9-4b2c-947f-f6dda79b23cb",
    "event" => "invitee.created",
    "payload" => array(
            "cancel_url" => "https://calendly.com/cancellations/31f01dc7-a051-4d2a-ae82-0b2a13316b33",
            "created_at" => "2024-07-22T14:54:47.364797Z",
            "email" => "digital@madein.net",
            "event" => "https://api.calendly.com/scheduled_events/f005c334-d201-44db-9444-082811b99c9c",
            "first_name" => "Greg",
            "invitee_scheduled_by" => "",
            "last_name" => "Cottret",
            "name" => "Greg Cottret",
            "new_invitee" => "",
            "no_show" => "",
            "old_invitee" => "",
            "payment" => "",
            "questions_and_answers" => array(
                    "0" => array(
                        "answer" => "0661910571",
                        "position" => "0",
                        "question" => "Votre numéro de téléphone",
                    ),
                    "1" => array(
                        "answer" => "34170",
                        "position" => "1",
                        "question" => "Votre code postal",
                    ),
                    "2" => array(
                            "answer" => "27/12/2024",
                            "position" => "2",
                            "question" => "Quelle est la date de votre mariage ?",
                    ),

                    "3" => array(
                            "answer" => "Oui",
                            "position" => "3",
                            "question" => "Avez-vous repéré des modèles que vous aimeriez essayer ?",
                    ),

                    "4" => array(
                            "answer" => "1000 - 3000",
                            "position" => "4",
                            "question" => "Quelle est votre fourchette de prix pour votre robe de mariée ?",
                    ),

                    "5" => array(
                            "answer" => "Non",
                            "position" => "5",
                            "question" => "Souhaitez-vous ajouter un élément qui pourrait nous être utile pour mieux vous accompagner ?",
                    ),
                ),
            "reconfirmation" => "",
            "reschedule_url" => "https://calendly.com/reschedulings/31f01dc7-a051-4d2a-ae82-0b2a13316b33",
            "rescheduled" => "",
            "routing_form_submission" => "",
            "scheduled_event" => array(
                    "created_at" => "2024-07-22T14:54:47.352255Z",
                    "end_time" => "2024-09-26T10:45:00.000000Z",
                    "event_guests" => array(
                    ),

                    "event_memberships" => array(
                            "0" => array(
                                    "user" => "https://api.calendly.com/users/569aa5b6-826d-4582-a79d-785f1147c762",
                                    "user_email" => "lauriane@olympe-mariage.com",
                                    "user_name" => "L'équipe Olympe Lyon",
                            ),
                        ),

                    "event_type" => "https://api.calendly.com/event_types/0ee5836f-7ae8-4b38-a52a-3dffb57e9564",
                    "invitees_counter" => array(
                            "total" => "1",
                            "active" => "1",
                            "limit" => "1",
                    ),
                    "location" => array(
                            "location" => "",
                            "type" => "custom",
                    ),
                    "meeting_notes_html" => "",
                    "meeting_notes_plain" => "",
                    "name" => "Test",
                    "start_time" => "2024-09-26T09:30:00.000000Z",
                    "status" => "active",
                    "updated_at" => "2024-07-22T14:54:47.352255Z",
                    "uri" => "https://api.calendly.com/scheduled_events/f005c334-d201-44db-9444-082811b99c9c",
                ),

            "scheduling_method" => "",
            "status" => "active",
            "text_reminder_number" => "",
            "timezone" => "Europe/Berlin",
            "tracking" => array(
                    "utm_campaign" => "",
                    "utm_source" => "",
                    "utm_medium" => "",
                    "utm_content" => "",
                    "utm_term" => "",
                    "salesforce_uuid" => "",
            ),

            "updated_at" => "2024-07-22T14:54:47.364797Z",
            "uri" => "https://api.calendly.com/scheduled_events/f005c334-d201-44db-9444-082811b99c9c/invitees/31f01dc7-a051-4d2a-ae82-0b2a13316b33",
        ),
);

$content = json_encode($calendly);

include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_auto.inc"); 

// Assurez-vous que ce script est accessible via une URL publique.

// Récupération du corps de la requête
//$content = file_get_contents("php://input");
// Conversion du JSON en objet PHP
$data = json_decode($content, true);

// Vous pouvez ici ajouter des logiques pour gérer les données, par exemple :
//file_put_contents("calendly_events_group.log", print_r($data, true), FILE_APPEND);

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

        $organisateur = $event["scheduled_event"]["event_memberships"][0]["user_email"];
        if ($organisateur=="lauriane@olympe-mariage.com")
            $user_num = 8;
        else
            $user_num = 22;

        // On test si le client n'exite pas
        $sql = "select * from clients where client_mail='" . $mail . "'";
        $tt = mysql_query($sql);
        $nbr = mysql_num_rows($tt);
        if ($nbr==0) {
            $sql = "insert into clients values (0,0,'" . $user["nom"] . "','" . $user["prenom"] . "','','','" . $urser["cp"] . "','','" . $user["tel"] . "','" . $user["email"] . "','" . $user["date-mariage"] . "','','','','3','" . $user_num . "','" . Date("Y-m-d H:i:s") . "','" . Date("Y-m-d H:i:s") . "','','','','','','','','','','','','',0,0)";
            mysql_query($sql);
            $client_num = mysql_insert_id();
        } else {
           if ($rtt = mysql_fetch_array($tt)) {
                $client_num = $rtt["client_num"];
           }
        }	

        // Premier RDV
        $type = 1;

        // On recherche le client 
        $sql = "select * from clients where client_num='" . $client_num . "'";
        $cl = mysql_query($sql);
        if ($rcl = mysql_fetch_array($cl)) {
            if ($rcl["client_genre"]==0)
                $genre = "Mme";
            else
                $genre = "Mr";

            $client_nom_complet = str_replace("'","\'",$rcl["client_nom"]) . " " . $rcl["client_prenom"];
            
            // On regarde si on a pas déjà un rendez vous 
            $sql = "select * from rendez_vous where client_num='" . $client_num . "' and type_num='" . $type . "'";
            $tt = mysql_query($sql);
            if ($rtt=mysql_fetch_array($tt)) {
                $sql = "delete from rendez_vous where rdv_num='" . $rtt["rdv_num"] . "'";
                mysql_query($sql);
                    
                $sql = "delete from calendriers where rdv_num='" . $rtt["rdv_num"] . "'";
                mysql_query($sql);
            }
            
            // On insere un Rendez vous
            $date_rdv = $date_debut;
            $sql = "insert into rendez_vous values(0,'" . $client_num . "','" . $type . "','" . $date_rdv . "','',0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00','" . $user_num . "')";
            mysql_query($sql);
            $num = mysql_insert_id();
            
            // On ajoute dans le calendrier du user
            switch ($type) {
                case 1: // 1er RDV
                    $theme = 1;
                    
                    $titre = $client_nom_complet;
                    $desc = $description;
                    
                    // On insere en bdd
                    $sql = "insert into calendriers values(0,'" . $date_debut . "','" . $date_fin . "','" . $theme . "','" . $titre . "','" . $desc . "','" . $user_num . "','3','" . $client_num . "','" . $num . "')";
                    mysql_query($sql);
                    
                    // On envoi le mail selon le type de RDV
                    $titre_mail = $mail_type[1][$rcl["client_genre"]]["titre"];
                    $titre_mail = str_replace("[VILLE]",$u->mShowroomInfo["showroom_ville"],$titre_mail);
                    $message_mail = $mail_type[1][$rcl["client_genre"]]["message"];
                    $message_mail = str_replace("[PRENOM]",utf8_decode($rcl["client_prenom"]),$message_mail);
                    $message_mail = str_replace("[DATE_HEURE]",format_date($date_debut,2,1),$message_mail);
                    $message_mail = str_replace("[SHOWROOM_NOM]",$u->mShowroomInfo["showroom_nom"],$message_mail);
                    $adresse = $u->mShowroomInfo["showroom_adr1"];
                    if ($u->mShowroomInfo["showroom_adr2"]!="")
                        $adresse .= "<br>" . $u->mShowroomInfo["showroom_adr2"];
                    $adresse = utf8_decode($adresse);
                    $message_mail = str_replace("[SHOWROOM_ADRESSE]",$adresse,$message_mail);
                    $message_mail = str_replace("[SHOWROOM_CP]",$u->mShowroomInfo["showroom_cp"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_VILLE]",$u->mShowroomInfo["showroom_ville"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_TEL]",$u->mShowroomInfo["showroom_tel"],$message_mail);
                    $message_mail = str_replace("[SHOWROOM_ACCES]",utf8_decode($u->mShowroomInfo["showroom_acces"]),$message_mail);
        
                    // On envoi le mail
                    SendMail($rcl["client_mail"],$titre_mail,$message_mail,$u->mNum,$client_num);
                    
                    $sql = "update rendez_vous set rdv_mail=1, rdv_mail_date='" . Date("Y-m-d H:i:s") . "' where rdv_num='" . $num . "'";
                    mysql_query($sql);
                    
                break;
            }
        }
    break;

    case "invitee.canceled": // Annulation de rendez vous
    break;
}

?>