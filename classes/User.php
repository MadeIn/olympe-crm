<?php
/**
 * Classe User moderne pour PHP8
 */
class User {
    private Database $db;
    
    // Propriétés utilisateur
    public int $mNum = 0;
    public string $mLogin = '';
    public string $mMdp = '';
    public string $mNom = '';
    public string $mPrenom = '';
    public string $mPhoto = '';
    public string $mEmail = '';
    public string $mEmailMdp = '';
    public string $mDatederConn = '';
    public string $mDateCreation = '';
    public int $mGroupe = 0;
    public int $mCompta = 0;
    public int $mShowroom = 0;
    public array $mShowroomInfo = [];
    
    public function __construct(int $nu = 0, string $l = "", string $m = "", int $c = 0, string $n = "", string $p = "", string $e = "", string $dconn = "", string $dcrea = "", int $g = 0, int $s = 0) {
        $this->db = Database::getInstance();
        
        $this->mNum = $nu;
        $this->mLogin = $l;
        $this->mMdp = $m;
        $this->mNom = $n;
        $this->mPrenom = $p;
        $this->mEmail = $e ?: $l; // Si pas d'email spécifique, utilise le login
        $this->mDatederConn = $dconn;
        $this->mDateCreation = $dcrea;
        $this->mGroupe = $g;
        $this->mShowroom = $s;
        $this->mCompta = 1; // Par défaut
    }
    
    /**
     * Test de connexion utilisateur
     */
    public function TestConnexion(): void {
        // Requête sécurisée avec requête préparée
        $sql = "SELECT * FROM users WHERE user_email = ? AND user_mdp = ? AND user_etat = 1";
        $result = $this->db->queryRow($sql, [$this->mEmail, $this->mMdp]);
        
        if ($result) {
            // Remplir l'objet utilisateur
            $this->mNum = (int)$result["user_num"];
            $this->mNom = $result["user_nom"];
            $this->mPrenom = $result["user_prenom"];
            $this->mEmail = $result["user_email"];
            $this->mEmailMdp = $result["user_email_mdp"] ?? '';
            $this->mPhoto = $result["user_photo"] ?? '';
            $this->mDatederConn = date("Y-m-d H:i:s");
            $this->mDateCreation = $result["user_datecreation"];
            $this->mGroupe = (int)$result["groupe_num"];
            $this->mCompta = (int)$result["acces_compta"];
            $this->mShowroom = (int)$result["showroom_num"];
            
            // Récupérer les informations du showroom
            $this->loadShowroomInfo();
            
            // Mettre à jour la date de dernière connexion
            $this->UpdateUserConnexion();
        } else {
            $this->mNum = -1; // Connexion échouée
        }
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public function UpdateUserConnexion(): void {
        if ($this->mNum > 0) {
            $sql = "UPDATE users SET user_dateconnexion = ? WHERE user_num = ?";
            $this->db->update($sql, [date("Y-m-d H:i:s"), $this->mNum]);
        }
    }
    
    /**
     * Charge les informations du showroom
     */
    private function loadShowroomInfo(): void {
        if ($this->mShowroom > 0) {
            $sql = "SELECT * FROM showrooms WHERE showroom_num = ?";
            $showroom = $this->db->queryRow($sql, [$this->mShowroom]);
            $this->mShowroomInfo = $showroom ?: [];
        }
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public function isLoggedIn(): bool {
        return $this->mNum > 0;
    }
    
    /**
     * Retourne le nom complet de l'utilisateur
     */
    public function getFullName(): string {
        return trim($this->mPrenom . ' ' . $this->mNom);
    }
    
    /**
     * Vérifie si l'utilisateur a accès à la comptabilité
     */
    public function hasComptaAccess(): bool {
        return $this->mCompta === 1;
    }
    
    /**
     * Vérifie si l'utilisateur est une couturière (groupe 2)
     */
    public function isCouturiere(): bool {
        return $this->mGroupe === 2;
    }
    
    /**
     * Retourne les données utilisateur pour la session (sécurisé)
     */
    public function getSessionData(): array {
        return [
            'user_id' => $this->mNum,
            'nom' => $this->mNom,
            'prenom' => $this->mPrenom,
            'email' => $this->mEmail,
            'groupe' => $this->mGroupe,
            'showroom' => $this->mShowroom,
            'compta' => $this->mCompta,
            'showroom_info' => $this->mShowroomInfo,
            'last_login' => $this->mDatederConn
        ];
    }
    
    /**
     * Crée un utilisateur à partir des données de session
     */
    public static function fromSessionData(array $data): User {
        $user = new User();
        $user->mNum = (int)$data['user_id'];
        $user->mNom = $data['nom'];
        $user->mPrenom = $data['prenom'];
        $user->mEmail = $data['email'];
        $user->mGroupe = (int)$data['groupe'];
        $user->mShowroom = (int)$data['showroom'];
        $user->mCompta = (int)$data['compta'];
        $user->mShowroomInfo = $data['showroom_info'];
        $user->mDatederConn = $data['last_login'];
        
        return $user;
    }
    
    /**
     * Méthodes de validation
     */
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePassword(string $password): bool {
        return strlen($password) >= 4; // Minimum 4 caractères (à adapter selon vos règles)
    }
}
?>