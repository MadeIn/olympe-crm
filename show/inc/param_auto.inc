<?	$chemin = "/home/madeinpr/www/CRM/olympe-mariage";
	include($chemin . "/inc/users.inc"); 
	include($chemin . "/inc/divers.inc");
	include($chemin . "/inc/object.inc");
	include($chemin . "/inc/produits.inc");
	include( $chemin . "/inc/mailing/class.phpmailer.php");
	include( $chemin . "/inc/mailing/class.smtp.php");
	include($chemin . "/inc/email.inc");
	include($chemin . "/inc/db.inc");
	
	$base = new Db();
	$base->Connect();
?>
