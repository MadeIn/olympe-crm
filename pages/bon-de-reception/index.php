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
					<img src="img/olympe-mariage-logo.jpg" style="width: 90%;">
					<h3>bon de réception</h3>
				</td>
				<td class="col-sm-6 text-center idclient"><?= $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
				<?= $rcc["client_tel"] ?><br>
				<?= $rcc["client_mail"] ?></td>
				<td class="col-sm-2 tabcontour text-center">
					<table class="table infofac">
						<tr>
							<td>Facture : <?= $rcc["facture_num"] ?></td>
						</tr>
						<tr>
							<td>DATE : <?= format_date($rcc["facture_date"],11,1) ?></td>
						</tr>
						<tr>
							<td>N° CLIENT : <?= $rcc["client_num"] ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table class="table bordure" style="margin-bottom: 35px;">
			<thead>
				<tr>
					<th>Description</th>
					<th>Référence</th>					
					<th class="text-center">Taille</th>
					<th class="text-center">Quantité</th>
				</tr>
			</thead>
			<tbody>
			<?php 																
				$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . decrypte($facture) . "'";
				$pp = $base->query($sql);
				foreach ($pp as $rpp) {
					echo '<tr>
							<td>' . $rpp["categorie_nom"] . ' - ' . $rpp["marque_nom"] . '</td>
							<td>' . $rpp["produit_nom"] . '</td>
							<td class="text-center">' . $rpp["taille_nom"] . '</td>
							<td class="text-center">' . $rpp["qte"] . '</td>
						</tr>';
				} ?>
			</tbody>
		</table>
		<table class="table tablepaiement">
			<tr>
				<td><small style="display: block; margin-bottom: 12px;">Les tenues sont fabriquées en semi mesure dans l'atelier des créatreurs. Elles sont ensuite ajustées à vos mesures par nos couturières.</small>
				En signant ce bon de réception, je reconnais prendre possession des articles mentionnés ci-dessus et être pleinement satisfaite du travail d'ajustement accompli.<br><br>
				Aucune réclamation ou modification ne pourra être faite après la présente réception.</td>
			</tr>
		</table>
		<table class="table tablesansbordure">
			<tr>
				<td><small>Date :</small></td>
				<td></td>
				<td><small>Signature :</small></td>
			</tr>
			<tr>
				<td style="border: solid 1px #CCC!important;width:300px;height:125px;"></td>
				<td></td>
				<td style="border: solid 1px #CCC!important;width:300px;height:125px;"></td>
			</tr>
		</table>
		
		<footer class="text-center ">
			<span>OLYMPE - <?= $rcc["showroom_adr1"] ?> <?php if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <?= $rcc["showroom_cp"] ?> <?= $rcc["showroom_ville"] ?> - <?= $rcc["showroom_tel"] ?> - www.olympe-mariage.com</span>
			<small>N° <?= $rcc["showroom_rcs"] ?></small>
		</footer>
	</div>
<?php if ($print=="auto") { ?>
	<script language="JavaScript">self.print()</script>
<?php } ?>
</body>
</html>