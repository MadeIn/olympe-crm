<?	$chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.inc"); 
	include($chemin . "/inc/divers.inc");
	include($chemin . "/inc/object.inc");
	include($chemin . "/inc/produits.inc");
	include( $chemin . "/inc/mailing/class.phpmailer.php");
	include( $chemin . "/inc/mailing/class.smtp.php");
	include($chemin . "/inc/email.inc");
	include($chemin . "/inc/db.inc");
	$rep_serveur = "/homez.387/madeinpr";
	session_start();
	
	// On recupere la session users
	if (isset($_SESSION['su']))	{	
		// Le user est logge
		$u = unserialize($_SESSION['su']);
		if ($u->mGroupe==2)
			echo "<script language=Javascript>document.location.href='/show/index.php';</script>";
	}
	else {
		// Le user n'est pas logge
		echo "<script language=Javascript>document.location.href='/index.php';</script>";
	}
	
	$base = new Db();
	$base->Connect();
?>
