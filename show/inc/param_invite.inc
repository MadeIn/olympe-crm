<?	$chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/divers.inc");
	include($chemin . "/inc/object.inc");
	include($chemin . "/inc/produits.inc");
	include($chemin . "/inc/db.inc");
	
	$base = new Db();
	$base->Connect();
?>
