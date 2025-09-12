<?
	
	class userConnect {
		public $mNum;		     // Identifiant user
		public $mLogin;	   	  // login
		public $mNom;		     // Nom de l'utilisateur
		public $mPrenom;	     // Prenom de l'utilisateur
		public $mEmail;			 // Email de l'utilisateur
		public $mGroupe;	     // Groupe d'appartenance de l'utilisateur
		public $mDroit;			// tableau des droits du user
		public $mNbrMenu;		// Nbr Menu accessible
		public $mCompta;	    // Acces à la compta
		public $mShowroom;		// Showroomm
				
		public function __construct($num=0,$login="",$nom="",$prenom="",$email="",$groupe="",$mDroit="",$nbrMenu=0,$mCompta=0,$mShowroom=0) {	
			$this->mNum				   	= $num;
			$this->mLogin			  	= $login;
			$this->mNom				   	= $nom;
			$this->mPrenom		  	   	= $prenom;
			$this->mEmail			   	= $email;
			$this->mGroupe			 	= $groupe;
			$this->mCompta				= $mCompta;
			$this->mShowroom			= $mShowroom;
		}
		
		public function TestDroit($num) {
			$i=0;
			$test=0;
			if (isset($this->mDroit)) {
				while (($i<$this->mNbrMenu) && ($test==0)) {
					if ($this->mDroit[$i]==$num)
						$test=1;
					else
						$i++;
				}
			}
			if ($test==0)
				return false;
			else
				return true;
		}
	}
	
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
		var $mDb;
		
		public function __construct($db,$nu=0,$l="",$m="",$c=0,$n="",$p="",$e="",$dconn="",$dcrea="",$g=0,$s=0) {	
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
			$this->mDb				   = $db;
		}
		
		function UpdateUserConnexion() {
			$sql = "update users set ";
			$sql = $sql . "user_dateconnexion='" . $this->mDateCreation . "'";
			$sql = $sql . "WHERE user_num=" . $this->mNum;
			$this->mDb->query($sql);
		}

		function TestConnexion() {
			$sql = "select * from users where user_email='" . $this->mEmail . "' and user_mdp='" . $this->mMdp . "' and user_etat=1";
			$re=$this->mDb->query($sql);
			
			// Si y a un resultat on finit de remplir l'objet User et on Update la derniere
			// date de connexion
			if ($row=$this->mDb->row($re)) {
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
				$ss = $this->mDb->query($sql);
				if ($rss=$this->mDb->row($ss)) {
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
