<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_invite.php"); 

	$sql = "select * from commandes co, commandes_fournisseurs cf, showrooms sh, clients c, marques m, md_produits p where co.id=cf.id and cf.marque_num=m.marque_num and cf.produit_num=p.produit_num and co.client_num=c.client_num and co.showroom_num=sh.showroom_num and cf.id='" . decrypte($id) . "' and cf.produit_num='" . decrypte($produit) . "'";
	$cc = mysql_query($sql);
	if (!$rcc=mysql_fetch_array($cc)) {
		//echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
	}
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
	<table class="table tablesansbordure" style="margin: 25px 0 10px;">
		<tr>
			<td class="col-sm-8">
				<figure style="display: inline-grid">
					<img src="img/olympe-mariage-logo.jpg">
					<figcaption class="text-center"><h5 style="display: inline-block;"><? echo str_replace("-","",$rcc["showroom_nom"]) ?><br>Bon de commande fournisseur</h5></figcaption>
				</figure>
			</td>
			<td class="col-sm-4 tabcontour text-center">
				<table class="table infofac">
					<tr>
						<td>Date de commande : <? echo format_date($rcc["commande_fournisseur_date"],11,1) ?></td>
					</tr>
					<tr>
						<td>Référence : <? echo $rcc["reference"] ?></td>
					</tr>
					<tr>
						<td><strong>Fournisseur :</strong><br>
						<? echo $rcc["marque_raison_social"] ?><br>
						<? echo $rcc["marque_adr1"] ?><br>
						<? if ($rcc["marque_adr2"]!="") echo $rcc["marque_adr2"] . "<br>" ?>
						<? echo $rcc["marque_cp"] . " " . $rcc["marque_ville"] ?><br>
						RCS : <? echo $rcc["marque_rcs"] ?><br>
						TVA : <? echo $rcc["marque_tva"] ?><br>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<table class="table tablesansbordure">
		<tr>
			<td class="col-md-4"><strong><? if ($rcc["client_genre"]==0) echo 'Nom de la future mariée'; else echo 'Nom du futur marié'; ?></strong> : <? echo $rcc["client_prenom"] . ' ' . $rcc["client_nom"] ?><br>
			<strong>Tel : </strong><? echo $rcc["client_tel"] ?><br>
			<strong>Mail : </strong><? echo $rcc["client_mail"] ?><br>
			<strong>Date de mariage : </strong><? echo format_date($rcc["client_date_mariage"],11,1) ?><br>
			<strong>Livraison avant : </strong><? echo $rcc["livraison"] ?></td>
			<td class="col-md-8"><strong>Référence modèle</strong> : <? echo $rcc["produit_nom"] ?>
			<hr><strong>Remarques</strong> :<br> <? echo $rcc["remarques"] ?></td>
		</tr>
	</table>
	
	<hr>
	
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<p><strong>Mesure Cliente</strong></p>
			<table class="table table-bordered table-striped table-condensed flip-content">
				<? if ($rcc["poitrine"]!=0) { ?>
				<tr>
					<td><strong>Tour de poitrine</strong></td>
					<td align="center"><? echo $rcc["poitrine"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["sous_poitrine"]!=0) { ?>
				<tr>
					<td><strong>Tour de sous poitrine</strong></td>
					<td align="center"><? echo $rcc["sous_poitrine"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["taille"]!=0) { ?>
				<tr>
					<td><strong>Tour de taille</strong></td>
					<td align="center"><? echo $rcc["taille"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["hanche1"]!=0) { ?>
				<tr>
					<td><strong>Tour de petite hanche</strong></td>
					<td align="center"><? echo $rcc["hanche1"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["hanche2"]!=0) { ?>
				<tr>
					<td><strong>Tour de grande hanche</strong></td>
					<td align="center"><? echo $rcc["hanche2"] ?></td>
				</tr>
				<? } ?>
				<? if ($rcc["biceps"]!=0) { ?>
				<tr>
					<td><strong>Tour de biceps</strong></td>
					<td align="center"><? echo $rcc["biceps"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["carrure_avant"]!=0) { ?>
				<tr>
					<td><strong>Carrure Avant</strong></td>
					<td align="center"><? echo $rcc["carrure_avant"] ?></td>
				</tr>
				<? } ?>
				<? if ($rcc["carrure_dos"]!=0) { ?>
				<tr>
					<td><strong>Carrure dos</strong></td>
					<td align="center"><? echo $rcc["carrure_dos"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["longueur_dos"]!=0) { ?>
				<tr>
					<td><strong>Longueur dos</strong></td>
					<td align="center"><? echo $rcc["longueur_dos"] ?></td>
				</tr> 
				<? } ?>
				<? if ($rcc["taille_sol"]!=0) { ?>
				<tr>
					<td><strong>Hauteur taille-sol avec talons</strong></td>
					<td align="center"><? echo $rcc["taille_sol"] ?></td>
				</tr> 
				<? } ?>
				<tr>
					<td><div style="position: absolute; width: calc(100% + 2px); background-color: #FFF; margin-left: -2px;"></div></td>
				</tr>
				<tr>
					<td style="background-color: #DEDEDE!important;"><strong style="font-size: 22px;">Taille choisie</strong></td>
					<td align="center" style="background-color: #DEDEDE!important;"><strong style="font-size: 22px"><? echo $rcc["taille_choisie"] ?></strong></td>
				</tr>
			</table>
		</div>
	</div>
	<footer class="text-center footerfournisseur">
		<span style="display: block;"><? echo str_replace("-","",$rcc["showroom_nom"]) ?> - <? echo $rcc["showroom_adr1"] ?> <? if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <? echo $rcc["showroom_cp"] ?> <? echo $rcc["showroom_ville"] ?> - <? echo $rcc["showroom_tel"] ?> - www.olympe-mariage.com</span>
		<small style="margin-bottom: 0;">N° <? echo $rcc["showroom_rcs"] ?></small>
	</footer>
</div>
<? if ($print=="auto") { ?>
	<script language="JavaScript">self.print()</script>
<? } else { ?>
	<br><p class="text-center"><button onClick="self.print();" class="btn btn-lg btn-primary"><strong>Imprimer votre facture</strong></button></p>
<? } ?>
</body>
</html>