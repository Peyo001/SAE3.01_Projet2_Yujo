<?php 
/**
 * Classe AmiDao
 * 
 * Cette classe gère les opérations CRUD pour les objets Ami dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $amiDao = new AmiDao();
 * $ami = $amiDao->findAll();
 * 
 */

class AmiDao{
    // Propriété représentant la connexion à la base de données via PDO.
    private PDO $conn;

    /**
     * Constructeur de la classe AmiDao.
     * 
     * Ce constructeur initialise une connexion à la base de données en utilisant la classe Database.
     */
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

     /**
     * Récupère tous les amis enregistrés dans la base de données.
     * 
     * Cette méthode retourne un tableau d'objets Ami, représentant tous les liens d'amitié dans la base de données.
     * 
     * @return Ami[] Tableau contenant tous les objets Ami.
     */
    public function findAll(): array {
        $amis = [];
        $stmt = $this->conn->query("SELECT idUtilisateur1, idUtilisateur2, dateAjout FROM AMIS");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ami = new Ami(
                $row['idUtilisateur1'],
                $row['idUtilisateur2'],
                $row['dateAjout']
            );
            $amis[] = $ami;
        }
        return $amis;
    }

    /**
     * Récupère une relation d'amitié entre deux utilisateurs en fonction de leurs identifiants.
     * 
     * Cette méthode recherche un lien d'amitié précis entre deux utilisateurs dans la base de données.
     * 
     * @param int $idUtilisateur1 Identifiant du premier utilisateur.
     * @param int $idUtilisateur2 Identifiant du deuxième utilisateur.
     * @return Ami|null Retourne un objet Ami si une relation d'amitié est trouvée, sinon null.
     */
    public function find(int $idUtilisateur1, int $idUtilisateur2): ?Ami {
        $stmt = $this->conn->prepare("SELECT idUtilisateur1, idUtilisateur2, dateAjout FROM AMI WHERE idUtilisateur1 = :idUtilisateur1 AND idUtilisateur2 = :idUtilisateur2");
        $stmt->bindValue(':idUtilisateur1', $idUtilisateur1, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $idUtilisateur2, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Ami(
                $row['idUtilisateur1'],
                $row['idUtilisateur2'],
                $row['dateAjout']
            );
        }
        return null;
    }

    
    /**
     * Récupère tous les amis d'un utilisateur en fonction de son identifiant.
     * 
     * Cette méthode recherche toutes les relations d'amitié d'un utilisateur spécifique et retourne un tableau d'objets Ami.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur dont on veut récupérer les amis.
     * @return Ami[] Tableau contenant tous les objets Ami représentant les amis de l'utilisateur.
     */
    public function findAmis(int $idUtilisateur): array {
    $amis = [];

    $stmt = $this->conn->prepare("
        SELECT idUtilisateur1, idUtilisateur2 
        FROM AMI 
        WHERE idUtilisateur1 = :idUtilisateur OR idUtilisateur2 = :idUtilisateur
    ");
    $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // On veut toujours l'autre utilisateur comme "ami"
        if ($row['idUtilisateur1'] == $idUtilisateur) {
            $amis[] = new Ami($idUtilisateur, $row['idUtilisateur2'], null);
        } else {
            $amis[] = new Ami($idUtilisateur, $row['idUtilisateur1'], null);
        }
    }

    return $amis;
}

    /**
     * Insère une nouvelle relation d'amitié dans la base de données.
     * 
     * Cette méthode ajoute un lien d'amitié entre deux utilisateurs dans la base de données.
     * 
     * @param Ami $ami L'objet Ami représentant la relation d'amitié à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insert(Ami $ami): bool {
        $stmt = $this->conn->prepare("INSERT INTO AMI (idUtilisateur1, idUtilisateur2, dateAjout) VALUES (:idUtilisateur1, :idUtilisateur2, :dateAjout)");
        $stmt->bindValue(':idUtilisateur1', $ami->getIdUtilisateur1(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $ami->getIdUtilisateur2(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $ami->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Supprime une relation d'amitié entre deux utilisateurs.
     * 
     * Cette méthode permet de supprimer une relation d'amitié entre deux utilisateurs de la base de données.
     * 
     * @param int $idUtilisateur1 Identifiant du premier utilisateur.
     * @param int $idUtilisateur2 Identifiant du deuxième utilisateur.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function delete(int $idUtilisateur1, int $idUtilisateur2): bool {
        $stmt = $this->conn->prepare("DELETE FROM AMI WHERE idUtilisateur1 = :idUtilisateur1 AND idUtilisateur2 = :idUtilisateur2");
        $stmt->bindValue(':idUtilisateur1', $idUtilisateur1, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $idUtilisateur2, PDO::PARAM_INT);
        return $stmt->execute();
    }
}