<?php  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
	
	$sql = "update prixachats set prixachat_montant='" . $prix . "',prixachat_date='" . Date("Y-m-d H:i:s") . "' where prixachat_num='" . $id . "'";
	mysql_query($sql);
?>