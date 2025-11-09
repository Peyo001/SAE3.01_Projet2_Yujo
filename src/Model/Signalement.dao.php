<?php
class SignalementDao
{
    private PDO $conn;

    public function __construct(PDO $pdo=null)
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function __destruct()
    {
        Database::getInstance()->__destruct();
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
        $stmt = $this->conn->query("SELECT idSignalement, raison FROM Signalement");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $signalement = new Signalement($row['idSignalement'], $row['raison']);
            $signalements[] = $signalement;
        }
        return $signalements;
    }

    public function find(int $id): ?Signalement
    {
        $stmt = $this->conn->prepare("SELECT idSignalement, raison FROM Signalement WHERE idSignalement = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Signalement($row['idSignalement'], $row['raison']);
        }
        return null;
    }

    public function insert(Signalement $signalement): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO Signalement (idSignalement, raison) VALUES (:id, :raison)");
        $stmt->bindParam(':id', $signalement->getId(), PDO::PARAM_INT);
        $stmt->bindParam(':raison', $signalement->getRaison(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM Signalement WHERE idSignalement = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>