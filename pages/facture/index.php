<?php include( $_SERVER['DOCUMENT_ROOT'] . "/param_invite.php"); 
	$sql = "select * from commandes co, paiements p, showrooms sh, users u, clients c where co.paiement_num=p.paiement_num and co.client_num=c.client_num and co.showroom_num=sh.showroom_num and co.user_num=u.user_num and id='" . decrypte($facture) . "'";
	$rcc = $base->queryRow($sql);
	if (!$rcc) {
		echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
	}

	if ($rcc["client_genre"]==0)
		$genre = "Chère";
	else
		$genre = "Cher";
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
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
	<div class="container">
		<table class="table tablesansbordure" style="margin: 25px 0 15px;">
			<tr>
				<td class="col-sm-4 text-center">
					<img src="img/olympe-mariage-logo.jpg" style="width: 70%;">
					<h5><?php echo $rcc["showroom_raison"] ?><br>
						SIRET : <?php echo $rcc["showroom_siret"] ?><br>
						TVA : <?php echo $rcc["showroom_tva"] ?></h5>
				</td>
				<td class="col-sm-6 text-center idclient"><h3>facture</h3><br><?php echo $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
				<?php echo $rcc["client_tel"] ?><br>
				<?php echo $rcc["client_mail"] ?></td>
				<td class="col-sm-2 tabcontour text-center">
					<table class="table infofac">
						<tr>
							<td>Facture : <?php echo $rcc["facture_num"] ?></td>
						</tr>
						<tr>
							<td>DATE : <?php echo format_date($rcc["facture_date"],11,1) ?></td>
						</tr>
						<tr>
							<td>N° CLIENT : <?php echo $rcc["client_num"] ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table class="table bordure">
			<thead>
				<tr>
					<th>Description</th>
					<th>Référence</th>					
					<th class="text-center">Taille</th>
					<th class="text-center">Prix HT</th>
					<th class="text-center">TVA</th>
					<th class="text-center">Quantité</th>
					<th class="text-center">Remise</th>
					<th class="text-center">Montant TTC</th>
				</tr>
			</thead>
			<tbody>
			<?php 																
				$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($facture) . "'";
				$pp = $base->query($sql);
				foreach ($pp as $rpp) {
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
							<td class="text-center">' . number_format($rpp["montant_ht"],2,"."," ") . ' €</td>
							<td class="text-center">' . number_format($rpp["montant_tva"],2,"."," ") . ' €</td>
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
		<?php if ($commande["remise"]==0) { ?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><?php echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><?php echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><?php echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
				</tr>
			</table>		
		<?php } else { ?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
					<td class="text-center">Remise : </td>
					<td class="text-center">Total à Payer : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><?php echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><?php echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><?php echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><?php echo $commande["remise"] ?></strong></td>
					<td class="text-center"><strong><?php echo number_format($commande["commande_remise_ttc"],2,"."," ") ?> €</strong></td>
				</tr>
			</table>		
		<?php } ?>
		<table class="table tablepaiement">
			<tr>
				<td><em><strong>Mode de Règlement</strong></em><br>
				<?php echo $rcc["paiement_titre"] ?></td>
				<td>
				<?php if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes 
					echo '<table class="table table-bordered pull-right">';
					$sql = "select * from commandes_paiements where id='" . decrypte($facture) . "' order by paiement_num ASC";
					$pp = $base->query($sql);
					foreach ($pp as $rpp) {
						echo '<tr>
								<td><strong>Acompte ' . $rpp["paiement_num"] . ' versé</td>
								<td>' . number_format($rpp["paiement_montant"],2,"."," ") . '€</td>
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
					<td colspan="4" style="border-top: none!important;">Référence Bancaire<br></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $rcc["banque_nom"] ?></td>
				</tr>
				<tr>
					<td>Code établissement<br>
					<?php echo $rcc["banque_code_etablissement"] ?></td>
					<td>Code guichet<br>
					<?php echo $rcc["banque_code_guichet"] ?></td>
					<td>Numéro de compte<br>
					<?php echo $rcc["banque_compte"] ?></td>
					<td>Clé RIB<br>
					<?php echo $rcc["banque_cle_rib"] ?></td>
				</tr>
				<tr>
					<td colspan="2">
						Code BIC (Bank Identification Code) - Code swift<br><?php echo $rcc["banque_swift"] ?>						
					</td>
					<td colspan="2">
						IBAN (International Bank Account Number)<br><?php echo $rcc["banque_iban"] ?>
					</td>
			</tbody>
		</table>
		<p><small>Tout retard de paiement fera l'objet d'une pénalité de retard, exigible de plein droit, égale au taux de refinancement de la Banque Centrale Européenne (BCE) majorée de 10 points, et ce sans qu'un rappel soit nécessaire. Le débiteur des sommes dues qui ne seraient pas réglées à bonne date est redevable d'une indemnité forfaitaire pour frais de recouvrement d'un montant de 40 € (article D.441-5 du Code de Commerce).<br>
		Réserve de propriété : les produits restent la propriété du vendeur jusqu'au paiement complet du prix.</small></p>
		<footer class="text-center ">
			<span>OLYMPE - <?php echo $rcc["showroom_adr1"] ?> <?php if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <?php echo $rcc["showroom_cp"] ?> <?php echo $rcc["showroom_ville"] ?> - <?php echo $rcc["showroom_tel"] ?> - www.olympe-mariage.com</span>
			<small>N° <?php echo $rcc["showroom_rcs"] ?></small>
		</footer>
	</div>
<?php if ($print=="auto") { ?>
	<script language="JavaScript">self.print()</script>
<?php } else { ?>
	<br><p class="text-center"><button onClick="self.print();" class="btn btn-lg btn-primary"><strong>Imprimer votre facture</strong></button></p>
<?php } ?>
</body>
</html>