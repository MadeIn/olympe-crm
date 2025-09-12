<?	
	session_start();
	
	$chemin = $_SERVER['DOCUMENT_ROOT'];
	$rep_base = "/home/sportmarlu/CLIENTS/WEB/OLYMPE";
	
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include( $chemin . "/inc/mailing/class.phpmailer.php");
	include( $chemin . "/inc/mailing/class.smtp.php");
	include($chemin . "/inc/email.php");
	
	// Base
	$crm = 1;
	include($rep_base . "/inc/config.php");
	
	// On recupere la session users
	if (!isset($connexion)) {
		if (isset($_SESSION["su"]))	{	
			// Le user est logge
			$u = unserialize($su);
			if ($u->mGroupe==2)
				echo "<script language=Javascript>document.location.href='/show/index.php';</script>";
		}
		else {
			// Le user n'est pas logge
			echo "<script language=Javascript>document.location.href='/index.php';</script>";
		}
	}
?>
