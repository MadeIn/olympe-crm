<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_invite.php"); 

	$sql = "select * from commandes co, paiements p, showrooms sh, users u, clients c where co.paiement_num=p.paiement_num and co.client_num=c.client_num and co.showroom_num=sh.showroom_num and co.user_num=u.user_num and id='" . decrypte($devis) . "'";
	$cc = mysql_query($sql);
	if (!$rcc=mysql_fetch_array($cc)) {
		echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
	}
	
	if ($rcc["client_genre"]==0)
		$genre = "Chère";
	else
		$genre = "Cher";
	
	$showroom_num = $rcc["showroom_num"];

	$date_validite = strtotime(date("Y-m-d", strtotime($rcc["devis_date"])) . " +30 day");
	$commande = montantCommande($rcc["id"]);
?>
<html>
<head>
<title></title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<META NAME="language" CONTENT="fr">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<div class="container">
		<table class="table tablesansbordure" style="margin: 25px 0 15px;">
			<tr>
				<td class="col-sm-4 text-center">
					<img src="img/olympe-mariage-logo.jpg" style="width: 90%;">
					<h3>devis</h3>
				</td>
				<td class="col-sm-6 text-center idclient"><? echo $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
				<? echo $rcc["client_tel"] ?><br>
				<? echo $rcc["client_mail"] ?></td>
				<td class="col-sm-2 tabcontour text-center">
					<small>Date Mariage : <? echo format_date($rcc["client_date_mariage"],11,1) ?></small>
					<table class="table infofac">
						<tr>
							<td>Devis : <? echo $rcc["devis_num"] ?></td>
						</tr>
						<tr>
							<td>DATE : <? echo format_date($rcc["devis_date"],11,1) ?></td>
						</tr>
						<tr>
							<td>N° CLIENT : <? echo $rcc["client_num"] ?></td>
						</tr>
					</table>
					<small>Validité du Devis : <? echo format_date(date("Y-m-d",$date_validite),11,1) ?></small>
				</td>
			</tr>
		</table>
		<table class="table bordure">
			<thead>
				<tr>
					<th>Description</th>
					<th>Référence</th>					
					<th class="text-center">Taille</th>
					<th class="text-center">Prix Unitaire</th>
					<th class="text-center">Quantité</th>
					<th class="text-center">Remise</th>
					<th class="text-center">Montant TTC</th>
				</tr>
			</thead>
			<tbody>
			<? 																
				$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($devis) . "'";
				$pp = mysql_query($sql);
				while ($rpp=mysql_fetch_array($pp)) {
					$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
					switch ($rpp["commande_produit_remise_type"]) {
						case 1: // Remise en %
							$prix_total_ttc = $prix_total_ttc*(1-($rpp["commande_produit_remise"]/100));
						break;
						
						case 2: // Remise en euro
							$prix_total_ttc = $prix_total_ttc - $rpp["commande_produit_remise"];
						break;
					}
					echo '<tr>
							<td>' . $rpp["categorie_nom"] . ' - ' . $rpp["marque_nom"] . '</td>
							<td>' . $rpp["produit_nom"] . '</td>
							<td class="text-center">' . $rpp["taille_nom"] . '</td>
							<td class="text-center">' . AffichePrix($rpp["produit_num"]) . '</td>
							<td class="text-center">' . $rpp["qte"] . '</td>
							<td class="text-center">';
					if ($rpp["commande_produit_remise_type"]!=0) {
						if ($rpp["commande_produit_remise_type"]==1)
							echo $rpp["commande_produit_remise"] . '%';
						else
							echo '-' . $rpp["commande_produit_remise"] . '€';
					} else {
						echo ' ';
					}						
					echo '	</td>
							<td class="text-center">';
					if (number_format($prix_total_ttc,2)<=0)
						echo "OFFERT";
					else
						echo number_format($prix_total_ttc,2,".", " ") . ' €';
					echo '	</td>
						</tr>';
				} ?>
			</tbody>
		</table>
		<? if ($commande["remise"]==0) { 
				$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
				if ($rcc["paiement_nombre"]>1) {
					$echeance = explode("/",$rcc["paiement_modele"]);
					$acompte = number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				}
		?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
					<? if ($rcc["paiement_nombre"]>1) { ?>
						<td class="text-center">Acompte demandé <? echo $echeance[0] ?>% : </td>
					<? } ?>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
					<? if ($rcc["paiement_nombre"]>1) { ?>
						<td class="text-center"><strong><? echo $acompte ?> €</strong></td>
					<? } ?>
				</tr>
			</table>		
		<? } else { 
				$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
				if ($rcc["paiement_nombre"]>1) {
					$echeance = explode("/",$rcc["paiement_modele"]);
					$acompte = number_format(($montant_a_payer*($echeance[0]/100)),2,"."," ");
				}
		?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
					<td class="text-center">Remise : </td>
					<td class="text-center">Total à Payer : </td>
					<? if ($rcc["paiement_nombre"]>1) { ?>
						<td class="text-center">Acompte demandé <? echo $echeance[0] ?>% : </td>
					<? } ?>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo $commande["remise"] ?></strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_remise_ttc"],2,"."," ") ?> €</strong></td>
					<? if ($rcc["paiement_nombre"]>1) { ?>
						<td class="text-center"><strong><? echo $acompte ?> €</strong></td>
					<? } ?>
				</tr>
			</table>		
		<? } ?>
		<?
			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
				echo '<table class="table tablepaiement">
					  <tr>
						<td><em><strong>Mode de règlement</strong></em><br>';
						
				$echeance = explode("/",$rcc["paiement_modele"]);
				$echeance_desc = explode("/",$rcc["paiement_description"]);
				$acompte_num = 0;
				foreach ($echeance as $val) {
					$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
					$acompte_nbr = $acompte_num+1;
					if ($acompte_nbr==$rcc["paiement_nombre"])
						$acompte_nombre = "Solde";
					else
						$acompte_nombre = "Acompte " . $acompte_nbr;
					echo $val .'% ' . $echeance_desc[$acompte_num] . ' / ' . $acompte_nombre . ' : ' . $acompte_val . ' €<br>';
					$acompte_num++;
				}				
				echo '</tr>
					</table>';
			}
		?>						
		<table class="table tablepaiement">
			<tr>
				<td><small style="display: block; margin-bottom: 12px;">
				<? if ($rcc["client_genre"]==0) { // Message Femme ?>
					Les tenues sont confectionnées en semi mesure dans l'atelier des créateurs. Elles sont ensuite ajustées à vos mesures par nos couturières pour un forfait retouche de 100€.<br>
					Des frais supplémentaires seront à la charge de la cliente si les mesures de celle-ci varient de plus d'une taille entre la prise de mesure (soit une variation de plus de 4 cm au niveau de la poitrine, de la taille ou des hanches) et la réception de la robe ou si la cliente souhaite apporter des modifications au modèle initial choisi.<br>
					Les retouches des robes civiles et des robes soldées sont à la charge totale de la cliente.<br>
					En cas d'acceptation de la présente offre, nous retourner le devis daté et signé, avec la Mention manuscrite "Bon pour accord".
				<? } else { // Message homme ?>
					Les costumes sont confectionnés sur mesure. S'il y Il a des ajustements nécessaires ils seront à notre charge et réalisés par notre couturière.<br> 
					Cependant, des frais supplémentaires seront à la charge du client si les mesures de ce-dernier varient de plus d'une taille entre la prise de mesure (soit une variation de plus de 4 cm au niveau de la poitrine, de la taille ou des hanches) et la réception du costume ou si le client souhaite apporter des modifications au modèle initial choisi.<br>
					Les retouches des costumes soldés sont à la charge de la cliente.<br>
					En cas d'acceptation de la présente offre, nous retourner le devis daté et signé, avec la mention manuscrite "Bon pour accord".
				<? } ?>
				
				<!--<? if ($showroom_num==2) { ?>
					Les tenues sont fabriquées en semi mesure dans l'atelier des créateurs. Elles sont ensuite ajustées à vos mesures par nos couturières.
				<? } else { ?>
					Les tenues sont fabriquées en semi mesure dans l'atelier des créateurs. Elles sont ensuite ajustées à vos mesures par nos couturières sans aucun frais supplémentaire.<br>Cependant, les retouches seront à la charge de la cliente si les mesures de celle-ci varient de plus d’une taille entre la prise de mesure (soit une variation de plus de 4 cm au niveau de la poitrine, de la taille ou des hanches) et la reception de la robe ou si la cliente souhaite apporter des modifications au modèle initial choisi.<br>Les retouches des robes courtes de chez Laure de Sagazan, Sessun OUI et des robes soldées sont à la charge de la cliente.</small>
					En cas d'acceptation de la présente offre, nous retourner le devis daté et signé, avec la mention manuscrite "Bon pour accord"
				<? } ?>-->
				</td>
				<td style="border: solid 2px #AAA!important;width:300px;"></td>
			</tr>
		</table>
		<table class="table" style="border: solid 1px #CCC;">
			<tbody>
				<tr>
					<td colspan="4" style="border-top: none!important;">Référence Bancaire<br></td>
				</tr>
				<tr>
					<td colspan="4"><strong><? echo $rcc["banque_nom"] ?></strong></td>
				</tr>
				<tr>
					<td>Code établissement<br>
					<strong><? echo $rcc["banque_code_etablissement"] ?></strong></td>
					<td>Code guichet<br>
					<strong><? echo $rcc["banque_code_guichet"] ?></strong></td>
					<td>Numéro de compte<br>
					<strong><? echo $rcc["banque_compte"] ?></strong></td>
					<td>Clé RIB<br>
					<strong><? echo $rcc["banque_cle_rib"] ?></strong></td>
				</tr>
				<tr>
					<td colspan="2">
						Code BIC (Bank Identification Code) - Code swift<br><strong><? echo $rcc["banque_swift"] ?></strong>						
					</td>
					<td colspan="2">
						IBAN (International Bank Account Number)<br><strong><? echo $rcc["banque_iban"] ?></strong>
					</td>
			</tbody>
		</table>
		<p><small>Tout retard de paiement fera l'objet d'une pénalité de retard, exigible de plein droit, égale au taux de refinancement de la Banque Centrale Européenne (BCE) majorée de 10 points, et ce sans qu'un rappel soit nécessaire. Le débiteur des sommes dues qui ne seraient pas réglées à bonne date est redevable d'une indemnité forfaitaire pour frais de recouvrement d'un montant de 40 € (article D.441-5 du Code de Commerce).<br>
		Réserve de propriété : les produits restent la propriété du vendeur jusqu'au paiement complet du prix.</small></p>
		
		<footer class="text-center ">
			<span>OLYMPE - <? echo $rcc["showroom_adr1"] ?> <? if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <? echo $rcc["showroom_cp"] ?> <? echo $rcc["showroom_ville"] ?> - <? echo $rcc["showroom_tel"] ?> - www.olympe-mariage.com</span>
			<small>N° <? echo $rcc["showroom_rcs"] ?></small>
		</footer>
	</div>
<? if ($print=="auto") { ?>
	<script language="JavaScript">self.print()</script>
<? } else { ?>
	<br><p class="text-center"><button onClick="self.print();" class="btn btn-lg btn-primary"><strong>Imprimer votre bon de commande</strong></button></p>
<? } ?>
</body>
</html>