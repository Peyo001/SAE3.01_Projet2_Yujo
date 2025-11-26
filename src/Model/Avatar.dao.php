<?php
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
class AvatarDao {
    //// Attribut représentant la connexion à la base de données via PDO.
    private PDO $conn;

    //Constructeur
    /**
     * Constructeur de la classe AvatarDao.
     * 
     * Ce constructeur initialise la connexion à la base de données en utilisant la classe Database.
     */
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    //destructeur
    /**
     * Destructeur de la classe AvatarDao.
     * 
     * Ce destructeur est vide mais peut être utilisé pour nettoyer des ressources si nécessaire.
     */
    public function __destruct() {
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
        $stmt = $this->conn->query("SELECT idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur FROM avatar");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $avatar = new Avatar(
                $row['idAvatar'],
                $row['nom'],
                $row['genre'],
                $row['dateCreation'],
                $row['CouleurPeau'],
                $row['CouleurCheveux'],
                $row['vetements'],
                $row['accessoires'],
                $row['idUtilisateur']
            );
            $avatars[] = $avatar;
        }
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
    public function find(int $idAvatar): ?Avatar {
        $stmt = $this->conn->prepare("SELECT idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur FROM AVATAR WHERE idAvatar = :idAvatar");
        $stmt->bindValue(':idAvatar', $idAvatar, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Avatar(
                $row['idAvatar'],
                $row['nom'],
                $row['genre'],
                $row['dateCreation'],
                $row['CouleurPeau'],
                $row['CouleurCheveux'],
                $row['vetements'],
                $row['accessoires'],
                $row['idUtilisateur']
            );
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

    public function insert(Avatar $avatar): bool {
        $stmt = $this->conn->prepare("INSERT INTO avatar (idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires, idUtilisateur) VALUES (:idAvatar, :nom, :genre, :dateCreation, :CouleurPeau, :CouleurCheveux, :vetements, :accessoires, :idUtilisateur)");
        $stmt->bindValue(':idAvatar', $avatar->getIdAvatar(), PDO::PARAM_INT);
        $stmt->bindValue(':nom', $avatar->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $avatar->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':dateCreation', $avatar->getDateCreation(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurPeau', $avatar->getCouleurPeau(), PDO::PARAM_STR);
        $stmt->bindValue(':CouleurCheveux', $avatar->getCouleurCheveux(), PDO::PARAM_STR);
        $stmt->bindValue(':vetements', $avatar->getVetements(), PDO::PARAM_STR);
        $stmt->bindValue(':accessoires', $avatar->getAccessoires(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $avatar->getIdUtilisateur(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprime un avatar de la base de données.
     * 
     * Cette méthode supprime un avatar spécifié par son identifiant de la base de données.
     * 
     * @param int $idAvatar L'identifiant de l'avatar à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function delete(int $idAvatar): bool {
        $stmt = $this->conn->prepare("DELETE FROM avatar WHERE idAvatar = :idAvatar");
        $stmt->bindValue(':idAvatar', $idAvatar, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
}