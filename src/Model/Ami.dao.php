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
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Recupérer tous les amis
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

    // Récupérer un ami d'un utilisateur par leurs IDs
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

    // Récupérer tous les amis d'un utilisateur par son ID
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

    public function insert(Ami $ami): bool {
        $stmt = $this->conn->prepare("INSERT INTO AMI (idUtilisateur1, idUtilisateur2, dateAjout) VALUES (:idUtilisateur1, :idUtilisateur2, :dateAjout)");
        $stmt->bindValue(':idUtilisateur1', $ami->getIdUtilisateur1(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $ami->getIdUtilisateur2(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $ami->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $idUtilisateur1, int $idUtilisateur2): bool {
        $stmt = $this->conn->prepare("DELETE FROM AMI WHERE idUtilisateur1 = :idUtilisateur1 AND idUtilisateur2 = :idUtilisateur2");
        $stmt->bindValue(':idUtilisateur1', $idUtilisateur1, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $idUtilisateur2, PDO::PARAM_INT);
        return $stmt->execute();
    }
}