<?php
require_once "Dao.class.php";
/**
 * 
 * Classe UtilisateurDao
 * 
 * Cette classe gère les opérations CRUD pour les utilisateurs dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $utilisteurDao = new UtilisateurDao();
 * $utilisateur = $utilisateurDao->findAll();
 */
class UtilisateurDao extends Dao
{   
    /** 
     * Hydrate une ligne de résultat en un objet Utilisateur.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Utilisateur Retourne un objet Utilisateur
     */
    public function hydrate(array $row): Utilisateur {
        return new Utilisateur(
            $row['nom'],
            $row['prenom'],
            $row['dateNaissance'],
            $row['genre'],
            $row['pseudo'],
            $row['email'],
            $row['motDePasse'],
            $row['typeCompte'],
            (bool)$row['estPremium'],
            $row['dateInscription'],
            (int)$row['yuPoints'],
            (int)$row['idUtilisateur'],
            $row['personnalisation']
        );
    }   
    //METHODES
    /**
     * Récupère un utilisateur spécifique par son identifiant.
     * 
     * Cette méthode récupère un utilisateur spécifique en fonction de son identifiant dans la table `UTILISATEUR`.
     * 
     * @param int $id L'identifiant de l'utilisateur à récupérer.
     * @return Utilisateur|null Retourne un objet `Utilisateur` si trouvé, sinon `null`.
     */
    public function find(int $id): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, nom, prenom, genre, dateNaissance, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Récupère tous les utilisateurs de la base de données.
     * 
     * Cette méthode permet de récupérer tous les utilisateurs présents dans la table `UTILISATEUR`.
     * 
     * @return Utilisateur[] Tableau des objets `Utilisateur`.
     */
    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur,nom,prenom,genre,dateNaissance,pseudo,email,motDePasse,typeCompte,estPremium,dateInscription,yuPoints,personnalisation FROM UTILISATEUR");
        $stmt->execute();
        $users = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = $this->hydrateAll($rows);
        return $users;
    }

    /**
     * Récupère un utilisateur par son email.
     * 
     * Cette méthode permet de récupérer un utilisateur spécifique en fonction de son email.
     * 
     * @param string $email L'email de l'utilisateur à récupérer.
     * @return Utilisateur|null Retourne un objet `Utilisateur` si trouvé, sinon `null`.
     */
    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, nom, prenom, genre, dateNaissance, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation FROM UTILISATEUR WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Récupère un utilisateur par son pseudo.
     * 
     * @param string $pseudo Le pseudo à rechercher
     * @return Utilisateur|null Retourne l'utilisateur si trouvé, sinon null
     */
    public function findByPseudo(string $pseudo): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, nom, prenom, genre, dateNaissance, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation FROM UTILISATEUR WHERE pseudo = :pseudo");
        $stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Crée un nouvel utilisateur dans la base de données.
     * 
     * Cette méthode permet d'insérer un nouvel utilisateur dans la table `UTILISATEUR` en utilisant les informations de l'objet `Utilisateur`.
     * 
     * @param Utilisateur $user L'objet `Utilisateur` à insérer dans la base de données.
     * @return bool Retourne `true` si l'insertion a réussi, sinon `false`.
     */
    public function creerUtilisateur(Utilisateur $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO UTILISATEUR (nom, prenom, dateNaissance, genre, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation) VALUES (:nom, :prenom, :dateNaissance, :genre, :pseudo, :email, :motDePasse, :typeCompte, :estPremium, :dateInscription, :yuPoints, :personnalisation)");
        $stmt->bindValue(':nom', $user->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $user->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':dateNaissance', $user->getDateNaissance(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $user->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':pseudo', $user->getPseudo(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':motDePasse', $user->getMotDePasse(), PDO::PARAM_STR);
        $stmt->bindValue(':typeCompte', $user->getTypeCompte(), PDO::PARAM_STR);
        $stmt->bindValue(':estPremium', $user->getEstPremium(), PDO::PARAM_BOOL);
        $stmt->bindValue(':dateInscription', $user->getDateInscription(), PDO::PARAM_STR);
        $stmt->bindValue(':yuPoints', $user->getYuPoints(), PDO::PARAM_INT);
        $stmt->bindValue(':personnalisation', $user->getPersonnalisation(), PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    /**
     * Supprime un utilisateur de la base de données.
     * 
     * Cette méthode permet de supprimer un utilisateur de la table `UTILISATEUR` en fonction de son identifiant.
     * 
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     * @return bool Retourne `true` si la suppression a réussi, sinon `false`.
     */
    public function supprimerUtilisateur(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Modifie les informations d'un utilisateur dans la base de données.
     * 
     * Cette méthode met à jour les informations d'un utilisateur dans la table `UTILISATEUR` en utilisant les données de l'objet `Utilisateur` passé en paramètre.
     * 
     * @param Utilisateur $user L'objet `Utilisateur` contenant les nouvelles informations.
     * @return bool Retourne `true` si la mise à jour a réussi, sinon `false`.
     */

    public function modifierUtilisateur(Utilisateur $user): bool
    {
        $stmt = $this->conn->prepare("UPDATE UTILISATEUR SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, genre = :genre, pseudo = :pseudo, email = :email, motDePasse = :motDePasse, typeCompte = :typeCompte, estPremium = :estPremium, dateInscription = :dateInscription, yuPoints = :yuPoints, personnalisation = :personnalisation WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':nom', $user->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $user->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':dateNaissance', $user->getDateNaissance(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $user->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':pseudo', $user->getPseudo(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':motDePasse', $user->getMotDePasse(), PDO::PARAM_STR);
        $stmt->bindValue(':typeCompte', $user->getTypeCompte(), PDO::PARAM_STR);
        $stmt->bindValue(':estPremium', $user->getEstPremium(), PDO::PARAM_BOOL);
        $stmt->bindValue(':dateInscription', $user->getDateInscription(), PDO::PARAM_STR);
        $stmt->bindValue(':yuPoints', $user->getYuPoints(), PDO::PARAM_INT);
        $stmt->bindValue(':personnalisation', $user->getPersonnalisation(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $user->getIdUtilisateur(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Incrémente le solde de YuPoints d'un utilisateur.
     * 
     * Cette méthode permet d'ajouter un certain montant aux YuPoints d'un utilisateur spécifié par son identifiant.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur.
     * @param int $delta Le montant à ajouter aux YuPoints.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function incrementerYuPoints(int $idUtilisateur, int $delta): bool
    {
        $stmt = $this->conn->prepare("UPDATE UTILISATEUR SET yuPoints = yuPoints + :delta WHERE idUtilisateur = :id");
        $stmt->bindValue(':delta', $delta, PDO::PARAM_INT);
        $stmt->bindValue(':id', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Décrémente le solde de YuPoints si le solde est suffisant.
     * 
     * Cette méthode permet de soustraire un certain montant aux YuPoints d'un utilisateur spécifié par son identifiant,
     * uniquement si le solde actuel est suffisant pour couvrir la soustraction.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur.
     * @param int $montant Le montant à soustraire des YuPoints.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function decrementerYuPoints(int $idUtilisateur, int $montant): bool
    {
        $stmt = $this->conn->prepare("UPDATE UTILISATEUR SET yuPoints = yuPoints - :montant WHERE idUtilisateur = :id AND yuPoints >= :montant");
        $stmt->bindValue(':montant', $montant, PDO::PARAM_INT);
        $stmt->bindValue(':id', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>