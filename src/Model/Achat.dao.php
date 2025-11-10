<?php


class AchatDao
{
    private PDO $conn;

    public function __construct()
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

    public function find(int $idObjet): ?Achat
    {
        $stmt = $this->conn->prepare("SELECT * FROM acheter WHERE idObjet = :idObjet");
        $stmt->bindParam(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->bindParam(':dateAchat', $idObjet, PDO::PARAM_INT);
        $stmt->bindParam(':idUtilisateur', $idObjet, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Achat($row['idObjet'], $row['dateAchat'], $row['idUtilisateur']);
        }
        return null;
    }

    public function findAll(): array
    {
        $achats = [];
        $stmt = $this->conn->query("SELECT * FROM acheter");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $achat = new Achat($row['idObjet'], $row['dateAchat'], $row['idUtilisateur']);
            $achats[] = $achat;
        }
        return $achats;
    }

    public function insert(Achat $achat): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO acheter (idObjet, dateAchat, idUtilisateur) VALUES (:idObjet, :dateAchat, :idUtilisateur)");
        $stmt->bindParam(':idObjet', $achat->getIdObjet(), PDO::PARAM_INT);
        $stmt->bindParam(':dateAchat', $achat->getDateAchat(), PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateur', $achat->getIdUtilisateur(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $idObjet): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM acheter WHERE idObjet = :idObjet");
        $stmt->bindParam(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>