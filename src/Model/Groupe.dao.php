<?php
class GroupeDao
{
    private PDO $conn;

    public function __construct(PDO $connexion)
    {
        $this->conn = DATABASE::getInstance()->getConnection();
    }

    // DESTRUCTEUR
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    // Récupère les membres d’un groupe
    private function getMembres(int $idGroupe): array
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur FROM Groupe WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Trouver un groupe par son ID
    public function find(int $idGroupe): ?Groupe
    {
        $stmt = $this->conn->prepare("SELECT * FROM Groupe WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $membres = $this->getMembres($idGroupe);
            return new Groupe(
                $row['idGroupe'],
                $row['nomGroupe'],
                $row['descriptionGroupe'],
                $row['dateCreation'],
                $membres
            );
        }
        return null;
    }

    // Récupérer tous les groupes
    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM Groupe");
        $stmt->execute();
        $groupes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $membres = $this->getMembres($row['idGroupe']);
            $groupes[] = new Groupe(
                $row['idGroupe'],
                $row['nomGroupe'],
                $row['descriptionGroupe'],
                $row['dateCreation'],
                $membres
            );
        }

        return $groupes;
    }

    public function insert(Groupe $groupe): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO Groupe (idGroupe, nomGroupe, descriptionGroupe, dateCreation) VALUES (:idGroupe, :nomGroupe, :descriptionGroupe, :dateCreation)");
        $stmt->bindValue(':idGroupe', $groupe->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':nomGroupe', $groupe->getNomGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':descriptionGroupe', $groupe->getDescriptionGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':dateCreation', $groupe->getDateCreation(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(int $idGroupe): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM Groupe WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function insertMembre(int $idGroupe, int $idUtilisateur): bool
    {
        if ($idGroupe->estMembre($idUtilisateur)) {
            return false; // Membre déjà dans le groupe
        }
        $stmt = $this->conn->prepare("INSERT INTO Groupe (idGroupe, idUtilisateur) VALUES (:idGroupe, :idUtilisateur)");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute(); // renvoie true si succès, false sinon
    }
}
?>