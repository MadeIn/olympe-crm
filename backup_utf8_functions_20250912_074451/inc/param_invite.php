<?	$chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
?>
