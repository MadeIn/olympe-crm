<?php  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
	
	// on regarde si le paiement existe
	$sql = "select * from commandes_fournisseurs_paiements where id='" . $id . "'";
	$cc = $base->query($sql);
	if (!$rcc = mysql_fetch_array($cc)) { // Si elle existe pas on l'a créé
		$sql = "insert into commandes_fournisseurs_paiements values('" . $id . "','" . $produit . "',0,0,0,'0000-00-00','0000-00-00','0000-00-00')";
		$base->query($sql);
	} 
	
	if ($paiement==1) {
		if ($val!="0") {
			$sql = "update commandes_fournisseurs_paiements set paiement1='" . $val . "', paiement1_date='" . Date("Y-m-d") . "' where id='" . $id . "' and produit_num='" . $produit . "'";
			$base->query($sql);
		} else {
			$sql = "update commandes_fournisseurs_paiements set paiement1='0', paiement1_date='0000-00-00' where id='" . $id . "' and produit_num='" . $produit . "'";
			$base->query($sql);
		}
	} else {
		if ($val!="0") {
			$sql = "update commandes_fournisseurs_paiements set paiement2='" . $val . "', paiement2_date='" . Date("Y-m-d") . "' where id='" . $id . "' and produit_num='" . $produit . "'";
			$base->query($sql);
		} else {
			$sql = "update commandes_fournisseurs_paiements set paiement2='0', paiement2_date='0000-00-00' where id='" . $id . "' and produit_num='" . $produit . "'";
			$base->query($sql);
		}
	}
	
	// ON recupere le reste à payer
	$paiement = 0;
	$sql = "select * from commandes_fournisseurs_paiements where id='" . $id . "' and produit_num='" . $produit . "'";
	$tt = $base->query($sql);
	if ($rtt=mysql_fetch_array($tt)) {
		$paiement = $rtt["paiement1"] + $rtt["paiement2"] + $rtt["paiement3"];
	}
	
	$reste = 0;
	$sql = "select * from commandes_fournisseurs where id='" . $id . "' and produit_num='" . $produit . "'";
	$tt = $base->query($sql);
	if ($rtt=mysql_fetch_array($tt)) {
		$reste = $rtt["commande_montant"] - $paiement;
	}
	
	echo number_format($reste,2,'.',' ') . " &euro;";
?>