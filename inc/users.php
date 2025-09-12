<?
	class Users
	{
		var $mNum;		    // Identifiant user
		var $mLogin;	    // login
		var $mMdp;		    // Mot de passe
		var $mNom;		    // Nom de l'utilisateur
		var $mPrenom;	    // Prenom de l'utilisateur
		var $mPhoto;	    // Prenom de l'utilisateur
		var $mEmail;		// Email de l'utilisateur
		var $mEmailMdp;		// Email de l'utilisateur
		var $mDatederConn;  // Date de derniere connexion à la gestion
		var $mDateCreation; // Date de derniere connexion à la gestion
		var $mGroupe;	    // Groupe d'appartenance de l'utilisateur
		var $mCompta;	    // Acces à la compta
		var $mShowroom;		// Showroomm
		
		function Users($nu=0,$l="",$m="",$c=0,$n="",$p="",$e="",$dconn="",$dcrea="",$g=0,$s=0) {	
			$this->mLogin			   = $l;
			$this->mMdp				   = $m;
			$this->mNom				   = $n;
			$this->mPrenom		   	   = $p;
			$this->mEmail			   = $l;
			$this->mDatederConn  	   = $dconn;
			$this->mDateCreation 	   = $dcrea;
			$this->mGroupe			   = $g;
			$this->Showroom			   = $s;
			$this->mCompta			   = 1;
			$this->mConnexion		   = $c;
			$this->mNum				   = $nu;
			
			$base = new Db();
			$base->Connect();
		}
		
		function UpdateUserConnexion() {
			$sql = "update users set ";
			$sql = $sql . "user_dateconnexion='" . $this->mDateCreation . "'";
			$sql = $sql . "WHERE user_num=" . $this->mNum;
			mysql_query($sql);
		}

		function TestConnexion() {
			$sql = "select * from users where user_email='" . $this->mEmail . "' and user_mdp='" . $this->mMdp . "' and user_etat=1";
			$re=mysql_query($sql);
			
			// Si y a un resultat on finit de remplir l'objet User et on Update la derniere
			// date de connexion
			if ($row=mysql_fetch_array($re)) {
				$this->mNum				 = $row["user_num"];
				$this->mNom				 = $row["user_nom"];
				$this->mPrenom			 = $row["user_prenom"];
				$this->mEmail			 = $row["user_email"];
				$this->mEmailMdp		 = $row["user_email_mdp"];
				$this->mPhoto			 = $row["user_photo"];
				$this->mDatederConn		 = date("Y-m-d H:i:s");                         
				$this->mDateCreation     = $row["user_datecreation"];
				$this->mGroupe			 = $row["groupe_num"];
				$this->mCompta			 = $row["acces_compta"];
				$this->mShowroom		 = $row["showroom_num"];
				
				$sql = "select * from showrooms where showroom_num='" . $row["showroom_num"] . "'";
				$ss = mysql_query($sql);
				if ($rss=mysql_fetch_array($ss)) {
					$this->mShowroomInfo = $rss;
				} else {
					$this->mShowroomInfo = 0;				
				}
							
				// On update la table users
				$this->UpdateUserConnexion();
			}
			else
			{
				$this->mNum = -1;
			}
		}
	}
?>
