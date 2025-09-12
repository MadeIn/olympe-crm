<?	$chemin = "/home/madeinpr/www/CRM/olympe-mariage";
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include( $chemin . "/inc/mailing/class.phpmailer.php");
	include( $chemin . "/inc/mailing/class.smtp.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
?>
