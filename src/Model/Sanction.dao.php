<?php

class SanctionDao {
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findAll(): array {
        $sanctions = [];
        $stmt = $this->conn->query("SELECT idSignalement, idUtilisateur, idPost, dateSignalement, statut FROM SIGNALER");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sanction = new Sanction(
                $row['idSignalement'],
                $row['idUtilisateur'],
                $row['idPost'],
                $row['dateSignalement'],
                $row['statut']
            );
            $sanctions[] = $sanction;
        }
        return $sanctions;
    }

    public function find(int $idSignalement): ?Sanction {
        $stmt = $this->conn->prepare("SELECT idSignalement, idUtilisateur, idPost, dateSignalement, statut FROM SIGNALER WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Sanction(
                $row['idSignalement'],
                $row['idUtilisateur'],
                $row['idPost'],
                $row['dateSignalement'],
                $row['statut']
            );
        }
        return null;
    }

    public function insert(Sanction $sanction): bool {
        $stmt = $this->conn->prepare("INSERT INTO SIGNALER (idSignalement, idUtilisateur, idPost, dateSignalement, statut) VALUES (:idSignalement, :idUtilisateur, :idPost, :dateSignalement, :statut)");
        $stmt->bindValue(':idSignalement', $sanction->getIdSignalement(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $sanction->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $sanction->getIdPost(), PDO::PARAM_INT);
        $stmt->bindValue(':dateSignalement', $sanction->getDateSignalement(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $sanction->getStatus(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $idSignalement): bool {
        $stmt = $this->conn->prepare("DELETE FROM SIGNALER WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        return $stmt->execute();
    }
}