<?	$chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include( $chemin . "/inc/mailing/class.phpmailer.php");
	include( $chemin . "/inc/mailing/class.smtp.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
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
