<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_invite.php"); ?>
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
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?
	$sql = "select * from commandes c, clients cl, paiements p  where c.paiement_num=p.paiement_num and c.client_num=cl.client_num and facture_date>='" . $date_deb . "' and facture_date<='" . $date_fin . "' and c.showroom_num='" . $showroom . "' and commande_num!=0 and facture_num!=0 order by facture_date DESC";
	$tt = mysql_query($sql);
	
	while ($rtt=mysql_fetch_array($tt)) {
	
		$sql = "select * from commandes co, paiements p, showrooms sh, users u, clients c where co.paiement_num=p.paiement_num and co.client_num=c.client_num and co.showroom_num=sh.showroom_num and co.user_num=u.user_num and id='" . $rtt["id"] . "'";
		$cc = mysql_query($sql);
		if (!$rcc=mysql_fetch_array($cc)) {
			echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
		}

		if ($rcc["client_genre"]==0)
			$genre = "Ch�re";
		else
			$genre = "Cher";
		$commande = montantCommande($rcc["id"]);
?>
	<div class="container">
		<table class="table tablesansbordure" style="margin: 25px 0 15px;">
			<tr>
				<td class="col-sm-4 text-center">
					<img src="img/olympe-mariage-logo.jpg" style="width: 90%;">
					<h5><? echo $rcc["showroom_raison"] ?><br>
						SIRET : <? echo $rcc["showroom_siret"] ?><br>
						TVA : <? echo $rcc["showroom_tva"] ?></h5>
				</td>
				<td class="col-sm-6 text-center idclient"><h3>facture</h3><br><? echo $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
				<? echo $rcc["client_tel"] ?><br>
				<? echo $rcc["client_mail"] ?></td>
				<td class="col-sm-2 tabcontour text-center">
					<table class="table infofac">
						<tr>
							<td>Facture : <? echo $rcc["facture_num"] ?></td>
						</tr>
						<tr>
							<td>DATE : <? echo format_date($rcc["facture_date"],11,1) ?></td>
						</tr>
						<tr>
							<td>N� CLIENT : <? echo $rcc["client_num"] ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table class="table bordure">
			<thead>
				<tr>
					<th>Description</th>
					<th>R�f�rence</th>					
					<th class="text-center">Taille</th>
					<th class="text-center">Prix HT</th>
					<th class="text-center">TVA</th>
					<th class="text-center">Quantit�</th>
					<th class="text-center">Remise</th>
					<th class="text-center">Montant TTC</th>
				</tr>
			</thead>
			<tbody>
			<? 																
				$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . $rtt["id"] . "'";
				$pp = mysql_query($sql);
				while ($rpp=mysql_fetch_array($pp)) {
					//$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
					$prix_total_ttc = $rpp["montant_ttc"]*$rpp["qte"];
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
							<td class="text-center">' . number_format($rpp["montant_ht"],2,"."," ") . ' �</td>
							<td class="text-center">' . number_format($rpp["montant_tva"],2,"."," ") . ' �</td>
							<td class="text-center">' . $rpp["qte"] . '</td>
							<td class="text-center">';
					if ($rpp["commande_produit_remise_type"]!=0) {
						if ($rpp["commande_produit_remise_type"]==1)
							echo $rpp["commande_produit_remise"] . '%';
						else
							echo '-' . $rpp["commande_produit_remise"] . '&euro;';
					} else {
						echo ' ';
					}						
					echo '	</td>
							<td class="text-center">';
					if (number_format($prix_total_ttc,2)<=0)
						echo "OFFERT";
					else
						echo number_format($prix_total_ttc,2,".", " ") . ' &euro;';
					echo '	</td>
						</tr>';
				} ?>
			</tbody>
		</table>
		<? if ($commande["remise"]==0) { ?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> &euro;</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> &euro;</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> &euro;</strong></td>
				</tr>
			</table>		
		<? } else { ?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
					<td class="text-center">Remise : </td>
					<td class="text-center">Total � Payer : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> &euro;</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> &euro;</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> &euro;</strong></td>
					<td class="text-center"><strong><? echo $commande["remise"] ?></strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_remise_ttc"],2,"."," ") ?> &euro;</strong></td>
				</tr>
			</table>		
		<? } ?>
		<table class="table tablepaiement">
			<tr>
				<td><em><strong>Mode de R�glement</strong></em><br>
				<? echo $rcc["paiement_titre"] ?></td>
				<td>
				<? if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes 
					echo '<table class="table table-bordered pull-right">';
					$sql = "select * from commandes_paiements where id='" . decrypte($facture) . "' order by paiement_num ASC";
					$pp = mysql_query($sql);
					while ($rpp=mysql_fetch_array($pp)) {
						echo '<tr>
								<td><strong>Acompte ' . $rpp["paiement_num"] . ' vers�</td>
								<td>' . number_format($rpp["paiement_montant"],2,"."," ") . '&euro;</td>
							  </tr>';
					}
					echo '</table>';
				} ?>	
				</td>
			</tr>
		</table>
		<table class="table tablebanque" style="border: solid 1px #CCC;">
			<tbody>
				<tr>
					<td colspan="4" style="border-top: none!important;">R�f�rence Bancaire<br></td>
				</tr>
				<tr>
					<td colspan="4"><? echo $rcc["banque_nom"] ?></td>
				</tr>
				<tr>
					<td>Code �tablissement<br>
					<? echo $rcc["banque_code_etablissement"] ?></td>
					<td>Code guichet<br>
					<? echo $rcc["banque_code_guichet"] ?></td>
					<td>Num�ro de compte<br>
					<? echo $rcc["banque_compte"] ?></td>
					<td>Cl� RIB<br>
					<? echo $rcc["banque_cle_rib"] ?></td>
				</tr>
				<tr>
					<td colspan="2">
						Code BIC (Bank Identification Code) - Code swift<br><? echo $rcc["banque_swift"] ?>						
					</td>
					<td colspan="2">
						IBAN (International Bank Account Number)<br><? echo $rcc["banque_iban"] ?>
					</td>
			</tbody>
		</table>
		<p><small>Tout retard de paiement fera l'objet d'une p�nalit� de retard, exigible de plein droit, �gale au taux de refinancement de la Banque Centrale Europ�enne (BCE) major�e de 10 points, et ce sans qu'un rappel soit n�cessaire. Le d�biteur des sommes dues qui ne seraient pas r�gl�es � bonne date est redevable d'une indemnit� forfaitaire pour frais de recouvrement d'un montant de 40 � (article D.441-5 du Code de Commerce).<br>
		R�serve de propri�t� : les produits restent la propri�t� du vendeur jusqu'au paiement complet du prix.</small></p>
		<footer class="text-center ">
			<span>OLYMPE - <? echo utf8_encode($rcc["showroom_adr1"]) ?> <? if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <? echo $rcc["showroom_cp"] ?> <? echo $rcc["showroom_ville"] ?> - <? echo $rcc["showroom_tel"] ?> - www.olympe-mariage.com</span>
			<small>N� <? echo $rcc["showroom_rcs"] ?></small>
		</footer>
	</div>
<? } ?>
<script language="JavaScript">self.print()</script>
</body>
</html>