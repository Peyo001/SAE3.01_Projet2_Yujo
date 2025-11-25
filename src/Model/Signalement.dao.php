<?php

class SignalementDao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getConn(): ?PDO
    {
        return $this->conn;
    }

    public function setConn(PDO $conn): void
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        $signalements = [];
        $stmt = $this->conn->query("SELECT idSignalement, raison FROM SIGNALEMENT");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $signalement = new Signalement($row['idSignalement'], $row['raison']);
            $signalements[] = $signalement;
        }
        return $signalements;
    }

    public function find(int $id): ?Signalement
    {
        $stmt = $this->conn->prepare("SELECT idSignalement, raison FROM SIGNALEMENT WHERE idSignalement = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Signalement($row['idSignalement'], $row['raison']);
        }
        return null;
    }

    public function insert(Signalement $signalement): bool
    {
<<<<<<< HEAD
        $stmt = $this->conn->prepare("INSERT INTO SIGNALEMENT (idSignalement, raison) VALUES (:idSignalement, :raison)");
        $stmt->bindValue(':idSignalement', $signalement->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':raison', $signalement->getRaison(), PDO::PARAM_STR);
=======
        $stmt = $this->pdo->prepare("INSERT INTO signalements (raison) VALUES (:raison)");
        $stmt->bindParam(':raison', $signalement->getRaison(), PDO::PARAM_STR);
>>>>>>> Signalement
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM SIGNALEMENT WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>