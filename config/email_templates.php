<?php
/**
 * Configuration des templates d'emails
 * Fichier à modifier facilement pour changer les contenus d'emails
 */

return [
    // Templates pour femmes (gender = 0)
    'female' => [
        1 => [
            'titre' => 'Confirmation Rendez-vous Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l\'adresse suivante :</p>
                         <p><strong>[SHOWROOM_NOM]</strong><br>
                         [SHOWROOM_ADRESSE]<br>
                         [SHOWROOM_CP] [SHOWROOM_VILLE]<br>
                         [SHOWROOM_TEL]</p>
                         <p><small><i>[SHOWROOM_ACCES]</i></small></p>
                         <p><b>Quelques informations importantes avant notre rendez-vous :</b></p>
                         <ul>
                             <li>Pour que nous puissions vous accompagner le mieux possible dans le choix de votre robe de mariée, nous vous demandons de venir accompagnée de 3 personnes maximum.</li>
                             <li>Venir peu ou pas maquillée</li>
                             <li>Porter de la lingerie invisible</li>
                         </ul>
                         <p>Je reste à votre entière disposition si vous avez des questions, en attendant vous pouvez consulter le lien suivant : <a href="http://www.olympe-mariage.com/faq.php" target="_blank">www.olympe-mariage.com/faq.php</a></p>
                         <p>Très bonne journée,</p>'
        ],
        
        2 => [
            'titre' => 'Confirmation de la prise en charge de votre commande Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>J\'espère que vous allez bien.</p>
                         <p>Je vous confirme la prise en charge de la confection de votre robe par l\'atelier <strong>[REMARQUE]</strong> avec une livraison confirmée au <strong>[DATE]</strong>.</p>
                         <p>Je reste à votre entière disposition si vous avez des questions.</p>
                         <p>Très bonne journée,</p>'
        ],
        
        3 => [
            'titre' => 'Réception de votre robe - Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>J\'espère que vous allez bien et que la préparation de votre mariage se déroule bien.</p>
                         <p>Je suis ravie de vous annoncer la bonne réception de votre robe dans notre showroom.</p>
                         <p>Si votre rendez-vous essayage n\'est pas déjà fixé merci de nous contacter pour que nous convenions ensemble d\'une date,</p>
                         <p>Dans l\'attente de vous lire ou de vous voir !</p>
                         <p>Très bonne journée,</p>'
        ],
        
        5 => [
            'titre' => 'Confirmation Rendez-vous remise - Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Je vous confirme notre rendez-vous pour la remise de votre robe, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l\'adresse suivante :</p>
                         <p><strong>[SHOWROOM_NOM]</strong><br>
                         [SHOWROOM_ADRESSE]<br>
                         [SHOWROOM_CP] [SHOWROOM_VILLE]<br>
                         [SHOWROOM_TEL]</p>
                         <p>[SHOWROOM_ACCES]</p>
                         [ACOMPTE_VALEUR]
                         <p>Très bonne journée,</p>'
        ],
        
        7 => [
            'titre' => 'Votre sélection - Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Merci encore d\'avoir choisi Olympe pour essayer votre future robe de mariée, notre rendez-vous a été un vrai plaisir!</p>
                         <p>Pour faire suite, vous trouverez les photos des tenues que vous avez sélectionnées, accompagnées de leur prix en cliquant sur le lien suivant :</p>
                         <p><a href="' . env('URL') . '/selections/index?id=[SELECTION_NUM]" target="_blank"><u><strong>Découvrez votre sélection</strong></u></a></p>
                         <p>Je reste à votre entière disposition si vous souhaitez des informations supplémentaires ou un second rendez-vous.</p>
                         <p>Dans l\'attente de vous lire ou de vous voir !</p>
                         <p>Très bonne journée,</p>'
        ],
        
        8 => [
            'titre' => 'Votre devis - Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Je suis ravie que vous ayez trouvé votre robe de mariée chez Olympe !</p>
                         <p>Pour valider votre commande, nous avons besoin du devis signé avec la mention « bon pour accord » [ACOMPTE_VALEUR].</p>
                         <p>Vous pouvez nous faire un virement, nos coordonnées bancaires sont sur le document ou nous envoyer un chèque. Dès réception du devis signé et de l\'acompte nous passerons commande de votre robe. Nous reviendrons vers vous avec une date de livraison prévue dès que nous aurons la confirmation de la créatrice.</p>
                         [ACOMPTE_SUITE]
                         [RETOUCHE]
                         <p>Vous pouvez consulter et imprimer votre devis en cliquant sur le lien suivant :</p>
                         <p><a href="' . env('URL') . '/devis/index?devis=[DEVIS_NUM]&print=no" target="_blank"><u><strong>Votre devis Olympe Mariage</strong></u></a></p>
                         <p>Je reste à votre entière disposition si vous avez la moindre question.</p>
                         <p>À très bientôt,</p>'
        ],
        
        9 => [
            'titre' => 'Votre facture - Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>J\'espère que vous allez bien.</p>
                         <p>Vous pouvez consulter et imprimer votre facture en cliquant sur le lien suivant :</p>
                         <p><a href="' . env('URL') . '/facture/index?facture=[FACTURE_NUM]&print=no" target="_blank"><u><strong>Votre facture Olympe Mariage</strong></u></a></p>
                         <p>Je reste à votre entière disposition si vous avez la moindre question.</p>
                         <p>Toute l\'équipe Olympe vous souhaite beaucoup de bonheur et un très beau mariage.</p>
                         <p>À très bientôt,</p>'
        ],
        
        11 => [
            'titre' => 'Rappel Rendez-vous Olympe Mariage [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>J\'espère que vous allez bien,</p>
                         <p>Je vous rappelle notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l\'adresse suivante :</p>
                         <p><strong>[SHOWROOM_NOM]</strong><br>
                         [SHOWROOM_ADRESSE]<br>
                         [SHOWROOM_CP] [SHOWROOM_VILLE]<br>
                         [SHOWROOM_TEL]</p>
                         <p><small><i>[SHOWROOM_ACCES]</i></small></p>
                         <p><b>Quelques petites informations avant notre rendez-vous :</b></p>
                         <ul>
                             <li>Pour que nous puissions vous accompagner le mieux possible dans le choix de votre robe de mariée, nous vous demandons de venir accompagnée de 3 personnes maximum.</li>
                             <li>Venir peu ou pas maquillée</li>
                             <li>Porter de la lingerie invisible</li>
                         </ul>
                         <p>Merci de nous prévenir si vous ne pouvez pas être présente.</p>
                         <p>Pour préparer notre rendez-vous, vous pouvez dès à présent consulter notre sélection de robes sur notre site : <a href="http://www.olympe-mariage.com/categorie-robes-11.html">Olympe-mariage.com</a></p>
                         <p>Dans l\'attente de vous recevoir,</p>
                         <p>Très bonne journée,</p>'
        ]
    ],
    
    // Templates pour hommes (gender = 1)
    'male' => [
        1 => [
            'titre' => 'Confirmation Rendez-vous Beau. [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Je vous confirme notre rendez-vous, dans notre showroom, le <strong>[DATE_HEURE]</strong> à l\'adresse suivante :</p>
                         <p><strong>[SHOWROOM_NOM]</strong><br>
                         [SHOWROOM_ADRESSE]<br>
                         [SHOWROOM_CP] [SHOWROOM_VILLE]<br>
                         [SHOWROOM_TEL]</p>
                         <p><small><i>[SHOWROOM_ACCES]</i></small></p>
                         <p>Merci de venir avec une <b>chemise blanche</b>.</p>
                         <p>Je reste à votre entière disposition si vous avez des questions.</p>
                         <p>Très bonne journée,</p>'
        ],
        
        2 => [
            'titre' => 'Confirmation de la prise en charge de votre commande Beau. [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>J\'espère que vous allez bien.</p>
                         <p>Je vous confirme la prise en charge de la confection de votre costume par l\'atelier <strong>Beau.</strong> avec une livraison confirmée aux alentours de <strong>[DATE]</strong>.</p>
                         <p>Je reste à votre entière disposition si vous avez des questions.</p>
                         <p>Très bonne journée,</p>'
        ],
        
        7 => [
            'titre' => 'Votre sélection - Beau. [VILLE]',
            'message' => '<p>Bonjour [PRENOM],</p>
                         <p>Merci encore d\'avoir choisi Beau. pour essayer votre futur costume de marié, notre rendez-vous a été un vrai plaisir!</p>
                         <p>Pour faire suite, vous trouverez les photos des tenues que vous avez sélectionnées, accompagnées de leur prix en cliquant sur le lien suivant :</p>
                         <p><a href="' . env('URL') . '/selections/index?id=[SELECTION_NUM]" target="_blank"><u><strong>Découvrez votre sélection</strong></u></a></p>
                         <p>Je reste à votre entière disposition si vous souhaitez des informations supplémentaires ou un second rendez-vous.</p>
                         <p>Dans l\'attente de vous lire ou de vous voir !</p>
                         <p>Très bonne journée,</p>'
        ]
    ]
];
?>