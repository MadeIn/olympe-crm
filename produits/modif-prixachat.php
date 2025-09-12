<?  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.inc"); 
	include($chemin . "/inc/divers.inc");
	include($chemin . "/inc/object.inc");
	include($chemin . "/inc/produits.inc");
	include($chemin . "/inc/email.inc");
	include($chemin . "/inc/db.inc");
	
	$base = new Db();
	$base->Connect();
	
	$sql = "update prixachats set prixachat_montant='" . $prix . "',prixachat_date='" . Date("Y-m-d H:i:s") . "' where prixachat_num='" . $id . "'";
	mysql_query($sql);
?>