<?php
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
            return new Utilisateur(
                $row['idUtilisateur'],
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
                $row['personnalisation']
            );
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
        $stmt = $this->conn->prepare("SELECT idUtilisateur,nom,prenom,genre,dateNaissance,pseudo,email,motDePasse,typeCompte,estPremium,dateInscription,yuPoints FROM UTILISATEUR");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new Utilisateur(
                $row['idUtilisateur'],
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
                $row['personnalisation'],
            );
        }
        return $users;
    }

    /**
     * Crée un nouvel utilisateur dans la base de données.
     * 
     * Cette méthode permet d'insérer un nouvel utilisateur dans la table `UTILISATEUR` en utilisant les informations de l'objet `Utilisateur`.
     * 
     * @param Utilisateur $user L'objet `Utilisateur` à insérer dans la base de données.
     * @return bool Retourne `true` si l'insertion a réussi, sinon `false`.
     */
    public function createUser(Utilisateur $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO UTILISATEUR (nom, prenom, dateNaissance, genre, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation) VALUES (:nom, :prenom, :dateNaissance, :genre, :pseudo, :email, :motDePasse, :typeCompte, :estPremium, :dateInscription, :yuPoints, :personnalisation)");
        $stmt->bindValue(':nom', $user->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $user->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':dateNaissance', $user->getDateNaiss(), PDO::PARAM_STR);
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
    public function deleteUser(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>