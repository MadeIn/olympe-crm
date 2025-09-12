<?
function SendMail($email,$titre,$message,$user,$client_num) {

//if ($user==3) { // Pour les test y a que moi qui envoie les mails
	// On récupere les infos du user et on ouvre qu'au showroom de Montpellier
	$sql = "select * from users u, showrooms s where u.showroom_num=s.showroom_num and u.user_num='" . $user . "' and u.showroom_num IN (1,2,3,5)";
	$uu = mysql_query($sql);
	if ($ruu=mysql_fetch_array($uu)) {
		$contenu_head =  '<html>
		<head></head>
		<body style="margin: 0; padding: 0;">
		<center>';
		$contenu = '<table width="600" cellpadding="0" cellspacing="0" style="border-bottom: solid 1px #EAEAEA; margin-bottom: 25px;">
			<tbody>
			  <tr>
				<td align="center">
				  <a href="http://www.olympe-mariage.com" style="display: block;"><img src="https://crm.olympe-mariage.com/mails/img/olympe-mariage-logo.jpg"></a><br>
				</td>
			  </tr>
			</tbody>
			</table>
			<table width="600" cellpadding="0" cellspacing="0">
			<tbody>
			  <tr>
				<td>
				  <p style="line-height: 21px; font: normal 14px Helvetica,Arial,Sans-Serif; color: #666; text-align: justify; line-height: 20px">' . $message . '</p>
				</td>
			  </tr>
			</tbody>
			</table>

			<table width="600" cellpadding="0" cellspacing="0" style="border-top: solid 1px #EAEAEA; margin-top: 25px;">
			  <tbody>
				<tr>
				  <td align="center" style="font: normal 15px Helvetica,Arial,Sans-Serif; color: #666;"><br>
					<strong style="color: #333;">' . utf8_decode($ruu["user_prenom"]) . '</strong><br>
					<hr style="border: none; border-top: solid 1px #EAEAEA; width: 20%; margin: 10px 40%;">
					Olympe Mariage - ' . $ruu["showroom_ville"] . '<br>
					<small style="font-size: .85em; color: #999;">' . $ruu["showroom_adr1"] . ', ' . $ruu["showroom_cp"] . ' ' . $ruu["showroom_ville"] . ' - <a href="tel:' . $ruu["showroom_tel"] . '" style="text-decoration: none; color: #999;">' . $ruu["showroom_tel"] . '</a> - <a href="http://www.olympe-mariage.com" style="text-decoration: none; font: normal 14px Helvetica,Arial,Sans-Serif; color: #999;">www.olympe-mariage.com</a><br>
					<hr size="1" color="#EAEAEA">
					Facebook : <a href="https://www.facebook.com/olympemariage/" style="text-decoration: none; font: normal 14px Helvetica,Arial,Sans-Serif; color: #999;">https://www.facebook.com/olympemariage/</a> - Instagram : <a href="https://www.instagram.com/__olympe/" style="text-decoration: none; font: normal 14px Helvetica,Arial,Sans-Serif; color: #999;">@__olympe</a></small>			
				  </td>
				</tr>
			  </tbody>
			</table>';
		$contenu_bottom = '</center>
		</body>
		</html>';
		
		$contenu_mail = $contenu_head . $contenu . $contenu_bottom;
		
		//echo $contenu;
		$reply = $ruu["user_email"];
		//$expediteur = $ruu["user_nom"] . ' ' . $ruu["user_prenom"];
		$expediteur = utf8_decode($ruu["user_prenom"]) . ' Olympe';
		
	
		$mail             = new PHPMailer();
		
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->IsMail(); // telling the class to use SMTP
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure  = 'ssl';                  // enable SMTP authentication
		$mail->Host       = "ssl0.ovh.net"; // sets the SMTP server
		$mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		$mail->Username   = "showroom@olympe-mariage.com"; // SMTP account username
		$mail->Password   = "@lympe-mariage";        // SMTP account password

		$mail->SetFrom('showroom@olympe-mariage.com', $expediteur);
		$mail->AddReplyTo($reply,$expediteur);

		$mail->Subject    = $titre;

		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

		$mail->MsgHTML($contenu_mail);

		$address = $email;
		$mail->AddAddress($address, $email);

		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

		if(!$mail->Send()) {
			//echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			//echo "Message sent!";
			// On stocke le mail en BDD
			$titre = str_replace("'","\'",$titre);
			$contenu = str_replace("'","\'",$contenu);
			
			$sql = "insert into mails values(0,'" . Date("Y-m-d H:i:s") . "','" . $titre . "','" . $contenu . "','" . $client_num . "','" . $user . "')";
			mysql_query($sql);
		}
	}
//}
}


$mail_type[1]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";


$mail_type[2]["titre"] = "Confirmation de la prise en charge de votre commande Olympe Mariage [VILLE]";
$mail_type[2]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Je vous confirme la prise en charge de la confection de votre robe par l'atelier <strong>[REMARQUE]</strong> avec une livraison confirmée au <strong>[DATE]</strong>.</p>
							<p>Je reste à votre entière disposition si vous avez des questions.</p>
							<p>Très bonne journée,</p>";

$mail_type[3]["titre"] = "Réception de votre robe - Olympe Mariage [VILLE]";
$mail_type[3]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien et que la préparation de votre mariage se déroule bien.</p>
							<p>Je suis ravie de vous annoncer la bonne reception de votre robe dans notre showroom. </p>
							<p>Si votre rendez-vous essayage n’est pas déjà fixé merci de nous contacter pour que nous convenions ensemble d’une date, </p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[5]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[5]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous pour la remise de votre robe, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p>[SHOWROOM_ACCES]</p>
							[ACOMPTE_VALEUR]
							<p>Très bonne journée,</p>";

$mail_type[7]["titre"] = "Votre sélection - Olympe Mariage [VILLE]";
$mail_type[7]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Merci encore d'avoir choisi Olympe pour essayer votre future robe de mariée, notre rendez-vous a été un vrai plaisir!</p>
							<p>Pour faire suite, vous trouverez les photos des tenues que vous avez sélectionnées, accompagnées de leur prix en cliquant sur le lien suivant : </p>
							<p><a href=\"https://crm.olympe-mariage.com/selections/index.php?id=[SELECTION_NUM]\" target=\"_blank\"><u><strong>Découvrez votre sélection</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous souhaitez des informations supplémentaires ou un second rendez-vous.</p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[8]["titre"] = "Votre devis - Olympe Mariage [VILLE]";
$mail_type[8]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je suis ravie que vous ayez trouvé votre robe de mariée chez Olympe !</p>
							<p>Pour valider votre  commande, nous avons besoin du devis signé avec la mention « bon pour accord » [ACOMPTE_VALEUR].</p>
							<p>Vous pouvez nous faire un virement, nos coordonnées bancaires sont sur le document ou nous envoyer un chèque. Dès réception du devis signé et de l'acompte nous passerons commande de votre robe. Nous reviendrons vers vous avec une date de livraison prévue dès que nous aurons la confirmation de la créatrice.</p>
							[ACOMPTE_SUITE]
							[RETOUCHE]
							<p>Vous pouvez consulter et imprimer votre devis en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/devis/index.php?devis=[DEVIS_NUM]&print=no\" target=\"_blank\"><u><strong>Votre devis Olympe Mariage</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>À très bientôt,</p>";

$mail_type[9]["titre"] = "Votre facture - Olympe Mariage [VILLE]";
$mail_type[9]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Vous pouvez consulter et imprimer votre facture en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/facture/index.php?facture=[FACTURE_NUM]&print=no\" target=\"_blank\"><u><strong>Votre facture Olympe Mariage</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>Toute l'équipe Olympe vous souhaite beaucoup de bonheur et un très beau mariage.</p>
							<p>À très bientôt,</p>";

$mail_type[10]["titre"] = "Votre facture d'acompte [PAIEMENT_NUM] - Olympe Mariage [VILLE]";
$mail_type[10]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Nous avons bien reçu votre paiement et vous en remercions.</p>
							<p>Vous pouvez consulter et imprimer votre facture d'acompte en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/acompte/index.php?id=[COMMANDE_NUM]&paiement=[PAIEMENT_NUM]&print=no\" target=\"_blank\"><u><strong>Acompte [PAIEMENT_NUM] : Votre facture Olympe Mariage</strong></u></a></p>
							<p>Dans l'attente de vous revoir</p>
							<p>À très bientôt,</p>";
							
$mail_type[11]["titre"] = "Rappel Rendez-vous Olympe Mariage [VILLE]";
$mail_type[11]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien,</p>
							<p>Je vous rappelle notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</i></small></p>
							<p>Merci de nous prévenir si vous ne pouvez pas être présente.</p>
							<p>Pour préparer notre rendez-vous, vous pouvez dès à présent consulter notre sélection de robes sur notre site : <a href=\"http://www.olympe-mariage.com/categorie-robes-11.html\">Olympe-mariage.com</a>
							<p>Dans l'attente de vous recevoir,</p>
							<p>Très bonne journée,</p>";

$mail_type[12]["titre"] = "Votre robe de mariée Olympe Mariage [VILLE]";
$mail_type[12]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien, et que la préparation de votre mariage se passe pour le mieux.</p>
							<p>Je me permets de revenir vers vous quant au choix de votre robe de mariée. Avez vous avancé dans votre recherche ? Sachez que nous nous tenons à votre disposition pour toutes questions ou informations complémentaires.</p>
							<p>En attendant de vos nouvelles, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";	
							
$mail_type[13][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien. Nous allons recevoir votre robe au showroom, aux alentours du [DATE_RECEPTION], nous pouvons donc dès à présent convenir d’un rendez-vous pour le premier essayage avec notre couturière, quelles sont vos disponibilités ? 
							<p>J’en profite pour vous rappeler que vous devez avoir les chaussures que vous porterez le jour de votre mariage pour ce rendez-vous. Sachez que nous avons au showroom toutes les chaussures disponibles sur notre site internet : <a href=\"http://shop.olympe-mariage.com/fr/type/chaussures/13\">http://shop.olympe-mariage.com/fr/type/chaussures/13</a></p>
							<p>Dans l’attente de vous lire, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";		

$mail_type[14]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[14]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Nous vous rappelons qu’il est indispensable d’avoir les chaussures que vous porterez pour votre mariage lors de ce rendez-vous.</p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";

$mail_type[15]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[15]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							Maison Buon<br>
							84 85 rue Grignan<br>
							13006 Marseille<br>
							Porte fond de la cour à gauche / 1er étage<br>
							Contact Charlotte 04 11 75 68 33
							</p>
							<p>Nous vous rappelons qu’il est indispensable d’avoir les chaussures que vous porterez pour votre mariage lors de ce rendez-vous.</p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";								
							
							
// Mail FEMME
$mail_type[1][0]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[1][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";


$mail_type[2][0]["titre"] = "Confirmation de la prise en charge de votre commande Olympe Mariage [VILLE]";
$mail_type[2][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Je vous confirme la prise en charge de la confection de votre robe par l'atelier <strong>[REMARQUE]</strong> avec une livraison confirmée au <strong>[DATE]</strong>.</p>
							<p>Je reste à votre entière disposition si vous avez des questions.</p>
							<p>Très bonne journée,</p>";

$mail_type[3][0]["titre"] = "Réception de votre robe - Olympe Mariage [VILLE]";
$mail_type[3][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien et que la préparation de votre mariage se déroule bien.</p>
							<p>Je suis ravie de vous annoncer la bonne reception de votre robe dans notre showroom. </p>
							<p>Si votre rendez-vous essayage n’est pas déjà fixé merci de nous contacter pour que nous convenions ensemble d’une date, </p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[5][0]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[5][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous pour la remise de votre robe, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p>[SHOWROOM_ACCES]</p>
							[ACOMPTE_VALEUR]
							<p>Très bonne journée,</p>";

$mail_type[7][0]["titre"] = "Votre sélection - Olympe Mariage [VILLE]";
$mail_type[7][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Merci encore d'avoir choisi Olympe pour essayer votre future robe de mariée, notre rendez-vous a été un vrai plaisir!</p>
							<p>Pour faire suite, vous trouverez les photos des tenues que vous avez sélectionnées, accompagnées de leur prix en cliquant sur le lien suivant : </p>
							<p><a href=\"https://crm.olympe-mariage.com/selections/index.php?id=[SELECTION_NUM]\" target=\"_blank\"><u><strong>Découvrez votre sélection</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous souhaitez des informations supplémentaires ou un second rendez-vous.</p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[8][0]["titre"] = "Votre devis - Olympe Mariage [VILLE]";
$mail_type[8][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je suis ravie que vous ayez trouvé votre robe de mariée chez Olympe !</p>
							<p>Pour valider votre  commande, nous avons besoin du devis signé avec la mention « bon pour accord » [ACOMPTE_VALEUR].</p>
							<p>Vous pouvez nous faire un virement, nos coordonnées bancaires sont sur le document ou nous envoyer un chèque. Dès réception du devis signé et de l'acompte nous passerons commande de votre robe. Nous reviendrons vers vous avec une date de livraison prévue dès que nous aurons la confirmation de la créatrice.</p>
							[ACOMPTE_SUITE]
							[RETOUCHE]
							<p>Vous pouvez consulter et imprimer votre devis en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/devis/index.php?devis=[DEVIS_NUM]&print=no\" target=\"_blank\"><u><strong>Votre devis Olympe Mariage</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>À très bientôt,</p>";

$mail_type[9][0]["titre"] = "Votre facture - Olympe Mariage [VILLE]";
$mail_type[9][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Vous pouvez consulter et imprimer votre facture en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/facture/index.php?facture=[FACTURE_NUM]&print=no\" target=\"_blank\"><u><strong>Votre facture Olympe Mariage</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>Toute l'équipe Olympe vous souhaite beaucoup de bonheur et un très beau mariage.</p>
							<p>À très bientôt,</p>";

$mail_type[10][0]["titre"] = "Votre facture d'acompte [PAIEMENT_NUM] - Olympe Mariage [VILLE]";
$mail_type[10][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Nous avons bien reçu votre paiement et vous en remercions.</p>
							<p>Vous pouvez consulter et imprimer votre facture d'acompte en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/acompte/index.php?id=[COMMANDE_NUM]&paiement=[PAIEMENT_NUM]&print=no\" target=\"_blank\"><u><strong>Acompte [PAIEMENT_NUM] : Votre facture Olympe Mariage</strong></u></a></p>
							<p>Dans l'attente de vous revoir</p>
							<p>À très bientôt,</p>";
							
$mail_type[11][0]["titre"] = "Rappel Rendez-vous Olympe Mariage [VILLE]";
$mail_type[11][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien,</p>
							<p>Je vous rappelle notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</i></small></p>
							<p>Merci de nous prévenir si vous ne pouvez pas être présente.</p>
							<p>Pour préparer notre rendez-vous, vous pouvez dès à présent consulter notre sélection de robes sur notre site : <a href=\"http://www.olympe-mariage.com/categorie-robes-11.html\">Olympe-mariage.com</a>
							<p>Dans l'attente de vous recevoir,</p>
							<p>Très bonne journée,</p>";

$mail_type[12][0]["titre"] = "Votre robe de mariée Olympe Mariage [VILLE]";
$mail_type[12][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien, et que la préparation de votre mariage se passe pour le mieux.</p>
							<p>Je me permets de revenir vers vous quant au choix de votre robe de mariée. Avez vous avancé dans votre recherche ? Sachez que nous nous tenons à votre disposition pour toutes questions ou informations complémentaires.</p>
							<p>En attendant de vos nouvelles, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";			

$mail_type[13][0]["titre"] = "Rendez-vous retouche Olympe Mariage [VILLE]";
$mail_type[13][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien. Nous allons recevoir votre robe au showroom, aux alentours du [DATE_RECEPTION], nous pouvons donc dès à présent convenir d’un rendez-vous [RETOUCHE], quelles sont vos disponibilités ? 
							<p>J’en profite pour vous rappeler que vous devez avoir les chaussures que vous porterez le jour de votre mariage pour ce rendez-vous. Sachez que nous avons au showroom toutes les chaussures disponibles sur notre site internet : <a href=\"http://shop.olympe-mariage.com/fr/type/chaussures/13\">http://shop.olympe-mariage.com/fr/type/chaussures/13</a></p>
							<p>Dans l’attente de vous lire, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";

$mail_type[14][0]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[14][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Nous vous rappelons qu'il est indispensable d’avoir les chaussures que vous porterez pour votre mariage lors de ce rendez-vous.</p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";	
							
$mail_type[15][0]["titre"] = "Confirmation Rendez-vous Olympe Mariage [VILLE]";
$mail_type[15][0]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							Maison Buon<br>
							84 85 rue Grignan<br>
							13006 Marseille<br>
							Porte fond de la cour à gauche / 1er étage<br>
							Contact Charlotte 04 11 75 68 33</p>
							<p>Nous vous rappelons qu’il est indispensable d’avoir les chaussures que vous porterez pour votre mariage lors de ce rendez-vous.</p>
							<p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href=\"http://www.olympe-mariage.com/faq.php\" target=\"_blank\">www.olympe-mariage.com/faq.php</a></p>
							<p>Très bonne journée,</p>";	
							
$mail_type[16][0]["titre"] = "Félicitation";
$mail_type[16][0]["message"] = "<p>Chère [PRENOM],</p>
							<p>L'équipe Olympe se joint à moi pour vous adresser tous nos voeux de bonheur! Nous espérons  que votre mariage s’est bien passé, que vous étiez à l’aise dans votre tenue et que vous avez profité de chaque instant.</p>
							<p>Vous accompagner a été un réel plaisir pour nous!</p>
							<p>N’hésitez pas à nous faire parvenir quelques photos quand vous les aurez.<br>
							Nous vous souhaitons le meilleur pour la suite, </p>
							<p>Très bonne journée,</p>";	
							
// Mail Homme
$mail_type[1][1]["titre"] = "Confirmation Rendez-vous Faubourg Saint Sulpice [VILLE]";
$mail_type[1][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Merci de venir avec une <b>chemise blanche</b>. </p>
							<p>Je reste à votre entière disposition si vous avez des questions.</p>
							<p>Très bonne journée,</p>";


$mail_type[2][1]["titre"] = "Confirmation de la prise en charge de votre commande Faubourg Saint Sulpice [VILLE]";
$mail_type[2][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Je vous confirme la prise en charge de la confection de votre costume par l'atelier <strong>Faubourg Saint Sulpice</strong> avec une livraison confirmée au <strong>[DATE]</strong>.</p>
							<p>Je reste à votre entière disposition si vous avez des questions.</p>
							<p>Très bonne journée,</p>";

$mail_type[3][1]["titre"] = "Réception de votre costume - Faubourg Saint Sulpice [VILLE]";
$mail_type[3][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien et que la préparation de votre mariage se déroule bien.</p>
							<p>Je suis ravie de vous annoncer la bonne reception de votre costume dans notre showroom. </p>
							<p>Si votre rendez-vous essayage n’est pas déjà fixé merci de nous contacter pour que nous convenions ensemble d’une date, </p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[5][1]["titre"] = "Confirmation Rendez-vous Faubourg Saint Sulpice [VILLE]";
$mail_type[5][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous pour la remise de votre costume, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p>[SHOWROOM_ACCES]</p>
							[ACOMPTE_VALEUR]
							<p>Très bonne journée,</p>";

$mail_type[7][1]["titre"] = "Votre sélection - Faubourg Saint Sulpice [VILLE]";
$mail_type[7][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Merci encore d'avoir choisi Faubourg Saint Sulpice pour essayer votre future costume de marié, notre rendez-vous a été un vrai plaisir!</p>
							<p>Pour faire suite, vous trouverez les photos des tenues que vous avez sélectionnées, accompagnées de leur prix en cliquant sur le lien suivant : </p>
							<p><a href=\"https://crm.olympe-mariage.com/selections/index.php?id=[SELECTION_NUM]\" target=\"_blank\"><u><strong>Découvrez votre sélection</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous souhaitez des informations supplémentaires ou un second rendez-vous.</p>
							<p>Dans l'attente de vous lire ou de vous voir !</p>
							<p>Très bonne journée,</p>";

$mail_type[8][1]["titre"] = "Votre devis - Faubourg Saint Sulpice [VILLE]";
$mail_type[8][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je suis ravie que vous ayez trouvé votre costume chez Faubourg Saint Sulpice !</p>
							<p>Pour valider votre  commande, nous avons besoin du devis signé avec la mention « bon pour accord » [ACOMPTE_VALEUR].</p>
							<p>Vous pouvez nous faire un virement, nos coordonnées bancaires sont sur le document ou nous envoyer un chèque. Dès réception du devis signé et de l'acompte nous lancerons la confecction de votre costume. Nous reviendrons vers vous avec une date de livraison prévue dès que nous aurons la confirmation de notre atelier.</p>
							[ACOMPTE_SUITE]
							<p>Vous pouvez consulter et imprimer votre devis en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/devis/index.php?devis=[DEVIS_NUM]&print=no\" target=\"_blank\"><u><strong>Votre devis Faubourg Saint Sulpice</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>À très bientôt,</p>";

$mail_type[9][1]["titre"] = "Votre facture - Faubourg Saint Sulpice [VILLE]";
$mail_type[9][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien.</p>
							<p>Vous pouvez consulter et imprimer votre facture en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/facture/index.php?facture=[FACTURE_NUM]&print=no\" target=\"_blank\"><u><strong>Votre facture Faubourg Saint Sulpice</strong></u></a></p>
							<p>Je reste à votre entière disposition si vous avez la moindre question.</p>
							<p>Toute l'équipe Olympe vous souhaite beaucoup de bonheur et un très beau mariage.</p>
							<p>À très bientôt,</p>";

$mail_type[10][1]["titre"] = "Votre facture d'acompte [PAIEMENT_NUM] - Faubourg Saint Sulpice [VILLE]";
$mail_type[10][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Nous avons bien reçu votre paiement et vous en remercions.</p>
							<p>Vous pouvez consulter et imprimer votre facture d'acompte en cliquant sur le lien suivant :</p>
							<p><a href=\"https://crm.olympe-mariage.com/acompte/index.php?id=[COMMANDE_NUM]&paiement=[PAIEMENT_NUM]&print=no\" target=\"_blank\"><u><strong>Acompte [PAIEMENT_NUM] : Votre facture Faubourg Saint Sulpice</strong></u></a></p>
							<p>Dans l'attente de vous revoir</p>
							<p>À très bientôt,</p>";
							
$mail_type[11][1]["titre"] = "Rappel Rendez-vous Faubourg Saint Sulpice [VILLE]";
$mail_type[11][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien,</p>
							<p>Je vous rappelle notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</i></small></p>
							<p>Merci de nous prévenir si vous ne pouvez pas être présent.</p>
							<p>Dans l'attente de vous recevoir,</p>
							<p>Très bonne journée,</p>";

$mail_type[12][1]["titre"] = "Votre costume Faubourg Saint Sulpice [VILLE]";
$mail_type[12][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J'espère que vous allez bien, et que la préparation de votre mariage se passe pour le mieux.</p>
							<p>Je me permets de revenir vers vous quant au choix de votre costume. Avez vous avancé dans votre recherche ? Sachez que nous nous tenons à votre disposition pour toutes questions ou informations complémentaires.</p>
							<p>En attendant de vos nouvelles, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";		

$mail_type[13][1]["titre"] = "Rendez-vous retouche Olympe Mariage [VILLE]";
$mail_type[13][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>J’espère que vous allez bien. Nous allons recevoir votre costume au showroom, aux alentours du [DATE_RECEPTION], nous pouvons donc dès à présent convenir d’un rendez-vous pour le premier essayage avec notre couturière, quelles sont vos disponibilités ? 
							<p>Dans l’attente de vous lire, je vous souhaite une très belle journée !</p>
							<p>À bientôt,</p>";							

$mail_type[14][1]["titre"] = "Confirmation Rendez-vous Faubourg Saint Sulpice [VILLE]";
$mail_type[14][1]["message"] = "<p>Bonjour [PRENOM],</p>
							<p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l’adresse suivante :</p>
							<p><strong>[SHOWROOM_NOM]</strong><br>
							[SHOWROOM_ADRESSE]<br>
							[SHOWROOM_CP] [SHOWROOM_VILLE]<br>
							[SHOWROOM_TEL]</p>
							<p><small><i>[SHOWROOM_ACCES]</I></small></p>
							<p>Merci de venir avec une <b>chemise blanche</b>. </p>
							<p>Je reste à votre entière disposition si vous avez des questions.</p>
							<p>Très bonne journée,</p>";

$mail_type[16][1]["titre"] = "Félicitation";
$mail_type[16][1]["message"] = "<p>Chèr [PRENOM],</p>
							<p>L'équipe Olympe se joint à moi pour vous adresser tous nos voeux de bonheur! Nous espérons  que votre mariage s’est bien passé, que vous étiez à l’aise dans votre tenue et que vous avez profité de chaque instant.</p>
							<p>Vous accompagner a été un réel plaisir pour nous!</p>
							<p>N’hésitez pas à nous faire parvenir quelques photos quand vous les aurez.<br>
							Nous vous souhaitons le meilleur pour la suite, </p>
							<p>Très bonne journée,</p>";	
?>
