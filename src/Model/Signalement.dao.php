<?php
require_once __DIR__ . 'include.php';
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

    public function insert(Signalement $signalement): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO signalements (raison) VALUES (:raison)");
        $stmt->bindParam(':raison', $signalement->getRaison(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM signalements WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>