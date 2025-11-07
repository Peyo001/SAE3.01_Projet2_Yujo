<?php

class SignalementDao
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    public function setPdo(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $signalements = [];
        $stmt = $this->pdo->query("SELECT id, raison FROM signalements");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $signalement = new Signalement($row['id'], $row['raison']);
            $signalements[] = $signalement;
        }
        return $signalements;
    }

    public function find(int $id): ?Signalement
    {
        $stmt = $this->pdo->prepare("SELECT id, raison FROM signalements WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Signalement($row['id'], $row['raison']);
        }
        return null;
    }

}
?>