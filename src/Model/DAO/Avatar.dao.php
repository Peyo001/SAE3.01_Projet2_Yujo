<?php
require_once "Dao.class.php";
/**
 * Classe AvatarDao
 * 
 * Cette classe gère les opérations CRUD pour les objets Avatar dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $avatarDao = new AvatarDao();
 * $avatar = $avatarDao->findAll();
 */
class AvatarDao extends Dao
{
    /** 
     * Hydrate une ligne de résultat en un objet Avatar.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Avatar Retourne un objet Avatar
     */
    public function hydrate(array $row): Avatar{
        return new Avatar(
            $row['nom'],
            $row['genre'],
            $row['dateCreation'],
            $row['CouleurPeau'],
            $row['CouleurCheveux'],
            $row['vetements'],
            $row['accessoires'],
            (int)$row['idUtilisateur'],
            (int)$row['idAvatar']
        );
    }
    
    

    //Méthodes
    /**
     * Récupère tous les avatars enregistrés dans la base de données.
     * 
     * Cette méthode exécute une requête pour récupérer tous les avatars et retourne un tableau d'objets Avatar.
     * 
     * @return Avatar[] Tableau d'objets Avatar représentant tous les avatars de la base de données.
     */
    public function findAll(): array {
        $avatars = [];
        $stmt = $this->conn->prepare("SELECT idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur FROM AVATAR");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $avatars = $this->hydrateAll($rows);
        return $avatars;
    }

    /**
     * Récupère un avatar spécifique en fonction de son identifiant.
     * 
     * Cette méthode exécute une requête pour récupérer un avatar par son identifiant et retourne un objet Avatar.
     * 
     * @param int $idAvatar L'identifiant de l'avatar à récupérer.
     * @return Avatar|null Retourne un objet Avatar si l'avatar est trouvé, sinon null.
     */
    public function findByIdAvatar(int $idAvatar): ?Avatar {
        $stmt = $this->conn->prepare("SELECT idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur FROM AVATAR WHERE idAvatar = :idAvatar");
        $stmt->bindValue(':idAvatar', $idAvatar, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }   

    /**
     * Insère un nouvel avatar dans la base de données.
     * 
     * Cette méthode permet d'ajouter un avatar en utilisant les données de l'objet Avatar passé en paramètre.
     * 
     * @param Avatar $avatar L'objet Avatar à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */

    public function insererAvatar(Avatar $avatar): bool {
        $stmt = $this->conn->prepare("INSERT INTO AVATAR (nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur) VALUES (:nom, :genre, :dateCreation, :CouleurPeau, :CouleurCheveux, :vetements, :accessoires, :idUtilisateur)");
        $stmt->bindValue(':nom', $avatar->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $avatar->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':dateCreation', $avatar->getDateCreation(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurPeau', $avatar->getCouleurPeau(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurCheveux', $avatar->getCouleurCheveux(), PDO::PARAM_STR);
        $stmt->bindValue(':vetements', $avatar->getVetements(), PDO::PARAM_STR);
        $stmt->bindValue(':accessoires', $avatar->getAccessoires(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $avatar->getIdUtilisateur(), PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result) {
            $avatar->setIdAvatar((int)$this->conn->lastInsertId());
        }
        return $result;
    }

   
    /**
     * Supprime un avatar de la base de données.
     * 
     * Cette méthode supprime un avatar spécifié par son identifiant de la base de données.
     * 
     * @param int $idAvatar L'identifiant de l'avatar à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerAvatar(int $idUtilisateur): bool {
        $stmt = $this->conn->prepare("DELETE FROM AVATAR WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Récupère l'avatar d'un utilisateur spécifique.
     * 
     * Cette méthode exécute une requête pour récupérer l'avatar associé à un utilisateur.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur.
     * @return Avatar|null Retourne un objet Avatar si trouvé, sinon null.
     */
    public function findByUtilisateur(int $idUtilisateur): ?Avatar {
        $stmt = $this->conn->prepare("SELECT idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur FROM AVATAR WHERE idUtilisateur = :idUtilisateur LIMIT 1");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Crée un nouvel avatar dans la base de données.
     * 
     * Alias de insererAvatar pour correspondre aux conventions de nommage du contrôleur.
     * 
     * @param Avatar $avatar L'objet Avatar à créer.
     * @return bool Retourne true si la création a réussi, sinon false.
     */
    public function creerAvatar(Avatar $avatar): bool {
        return $this->insererAvatar($avatar);
    }

    /**
     * Met à jour un avatar existant dans la base de données.
     * 
     * Cette méthode met à jour toutes les propriétés d'un avatar.
     * 
     * @param Avatar $avatar L'objet Avatar avec les nouvelles valeurs.
     * @return bool Retourne true si la mise à jour a réussi, sinon false.
     */
    public function mettreAJourAvatar(Avatar $avatar): bool {
        $stmt = $this->conn->prepare("UPDATE AVATAR SET nom = :nom, genre = :genre, CouleurPeau = :CouleurPeau, CouleurCheveux = :CouleurCheveux, vetements = :vetements, accessoires = :accessoires WHERE idAvatar = :idAvatar");
        $stmt->bindValue(':nom', $avatar->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $avatar->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurPeau', $avatar->getCouleurPeau(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurCheveux', $avatar->getCouleurCheveux(), PDO::PARAM_STR);
        $stmt->bindValue(':vetements', $avatar->getVetements(), PDO::PARAM_STR);
        $stmt->bindValue(':accessoires', $avatar->getAccessoires(), PDO::PARAM_STR);
        $stmt->bindValue(':idAvatar', $avatar->getIdAvatar(), PDO::PARAM_INT);
        return $stmt->execute();
    }
}