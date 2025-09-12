<?php  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
	
	$sql = "update md_produits set produit_ref='" . $ref . "' where produit_num='" . $produit . "'";
	$base->query($sql);
?>