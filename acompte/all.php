<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_invite.php"); 

	$sql = "select * from commandes co, paiements p, showrooms sh, users u, clients c where co.paiement_num=p.paiement_num and co.client_num=c.client_num and co.showroom_num=sh.showroom_num and co.user_num=u.user_num and id='" . decrypte($id) . "'";
	$cc = $db->query($sql);
	if (!$rcc=$db->row($cc)) {
		echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
	}
	
	if ($rcc["client_genre"]==0)
		$genre = "Chère";
	else
		$genre = "Cher";
	
	$date_validite = strtotime(date("Y-m-d", strtotime($rcc["devis_date"])) . " +30 day");
	$commande = montantCommande($rcc["id"]);
	
	$sql = "select * from commandes_paiements where id='" . decrypte($id) . "' and paiement_num='" . $paiement . "'";
	$pp = $db->query($sql);
	if ($rpp=$db->row($pp)) {
		//echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
		$date_commande = $rpp["paiement_date"];
	} else 
		$date_commande = $rcc["commande_date"];
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
					<img src="img/olympe-mariage-logo.jpg" style="width: 90%;">
					<h3>Acompte</h3>
				</td>
				<td class="col-sm-6 text-center idclient"><? echo $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
				<? echo $rcc["client_tel"] ?><br>
				<? echo $rcc["client_mail"] ?></td>
				<td class="col-sm-2 tabcontour text-center">
					<table class="table infofac">
						<tr>
							<td>Commande : <? echo $rcc["commande_num"] ?></td>
						</tr>
						<tr>
							<td>DATE : <? echo format_date($date_commande,11,1) ?></td>
						</tr>
						<tr>
							<td>N° CLIENT : <? echo $rcc["client_num"] ?></td>
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
					<th class="text-center">Prix Unitaire</th>
					<th class="text-center">Quantité</th>
					<th class="text-center">Remise</th>
					<th class="text-center">Montant TTC</th>
				</tr>
			</thead>
			<tbody>
			<? 																
				$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($id) . "'";
				$pp = $db->query($sql);
				foreach ($pp["tab"] as $rpp) {
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
		?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
				</tr>
			</table>		
		<? } else { 
				$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
		?>
			<table class="table tabletotaux">
				<tr>
					<td class="text-center">Total HT : </td>
					<td class="text-center">TVA 20% : </td>
					<td class="text-center">Total TTC : </td>
					<td class="text-center">Remise : </td>
					<td class="text-center">Total à Payer : </td>
				</tr>
				<tr>
					<td class="text-center"><strong><? echo number_format($commande["commande_ht"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_tva"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_ttc"],2,"."," ") ?> €</strong></td>
					<td class="text-center"><strong><? echo $commande["remise"] ?></strong></td>
					<td class="text-center"><strong><? echo number_format($commande["commande_remise_ttc"],2,"."," ") ?> €</strong></td>
				</tr>
			</table>		
		<? } ?>
		<table class="table tablepaiement">
			<tr>
				<td class="col-sm-8"><em>Mode de Règlement</em><br>
				<? echo $rcc["paiement_titre"] ?></td>
				<td class="col-sm-4">
					<table class="table tablepaiement">
				<?
						$acompte_num = 0;
						$montant_paye = 0;
						$sql = "select * from commandes_paiements where id='" . decrypte($id) . "' order by paiement_num ASC";
						$pa = $db->query($sql);
						foreach ($pa["tab"] as $rpa) {
							$acompte_num++;
							echo '<tr>
									<td><strong>Acompte ' . $acompte_num . '</strong></td>
									<td class="text-center">' . number_format($rpa["paiement_montant"],2,"."," ") . ' €</td>
								</tr>';
							$montant_paye += number_format($rpa["paiement_montant"],2,".","");
						}
						$reste_a_payer = $montant_a_payer - $montant_paye;
						if ($acompte_num<$rcc["paiement_nombre"]) {
							for ($zz=$acompte_num+1;$zz<=$rcc["paiement_nombre"];$zz++) {
								echo '<tr>
									<td><strong>Acompte ' . $zz . '</strong></td>
									<td class="text-center"> </td>
								</tr>';
							}
						}
						echo '<tr>
								<td><strong>Reste à payer</strong></td>
								<td class="text-center">' . number_format($reste_a_payer,2,"."," ") . ' €</td>
							</tr>';
				?>
					</table>
				</td>
			</tr>
		</table>
		<table class="table tablebanque" style="border: solid 1px #CCC;">
			<tbody>
				<tr>
					<td colspan="4" style="border-top: none!important;">Référence Bancaire<br></td>
				</tr>
				<tr>
					<td colspan="4"><? echo $rcc["banque_nom"] ?></td>
				</tr>
				<tr>
					<td>Code établissement<br>
					<? echo $rcc["banque_code_etablissement"] ?></td>
					<td>Code guichet<br>
					<? echo $rcc["banque_code_guichet"] ?></td>
					<td>Numéro de compte<br>
					<? echo $rcc["banque_compte"] ?></td>
					<td>Clé RIB<br>
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
	<br><p class="text-center"><button onClick="self.print();" class="btn btn-lg btn-primary"><strong>Imprimer votre facture</strong></button></p>
<? } ?>
</body>
</html>