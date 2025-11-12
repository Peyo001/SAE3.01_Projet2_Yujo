<?php 

class AmiDao{
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findAll(): array {
        $amis = [];
        $stmt = $this->conn->query("SELECT idUtilisateur1, idUtilisateur2, dateAjout FROM AMI");
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

    public function find(int $idUtilisateur): ?Ami {
        $stmt = $this->conn->prepare("SELECT idUtilisateur1, idUtilisateur2, dateAjout FROM AMI WHERE idUtilisateur1 = :idUtilisateur OR idUtilisateur2 = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
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

    public function insert(Ami $ami): bool {
        $stmt = $this->conn->prepare("INSERT INTO AMI (idUtilisateur1, idUtilisateur2, dateAjout) VALUES (:idUtilisateur1, :idUtilisateur2, :dateAjout)");
        $stmt->bindValue(':idUtilisateur1', $ami->getIdUtilisateur1(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur2', $ami->getIdUtilisateur2(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $ami->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $idUtilisateur): bool {
        $stmt = $this->conn->prepare("DELETE FROM AMI WHERE idUtilisateur1 = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }
}