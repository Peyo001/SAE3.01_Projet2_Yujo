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
    //Attributs
    private PDO $conn;

    //Constructeur
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    //destructeur
    public function __destruct() {
    }

    //Méthodes
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

    public function delete(int $idAvatar): bool {
        $stmt = $this->conn->prepare("DELETE FROM avatar WHERE idAvatar = :idAvatar");
        $stmt->bindValue(':idAvatar', $idAvatar, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
}