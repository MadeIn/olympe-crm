<?  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.inc"); 
	include($chemin . "/inc/divers.inc");
	include($chemin . "/inc/object.inc");
	include($chemin . "/inc/produits.inc");
	include($chemin . "/inc/email.inc");
	include($chemin . "/inc/db.inc");
	
	$base = new Db();
	$base->Connect();
	
	$sql = "update md_produits set produit_ref='" . $ref . "' where produit_num='" . $produit . "'";
	mysql_query($sql);
?>