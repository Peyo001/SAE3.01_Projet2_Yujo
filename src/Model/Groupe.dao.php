<?php
class GroupeDao
{
    private PDO $conn;

    public function __construct()
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
        $stmt = $this->conn->prepare("SELECT idUtilisateur FROM GROUPE JOIN COMPOSER ON GROUPE.idGroupe = COMPOSER.idGroupe WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Trouver un groupe par son ID
    public function find(int $idGroupe): ?Groupe
    {
        $stmt = $this->conn->prepare("SELECT * FROM GROUPE WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $membres = $this->getMembres($idGroupe);
            return new Groupe(
                $row['idGroupe'],
                $row['nomGroupe'],
                $row['description'],
                $row['dateCreationGroupe'],
                $membres
            );
        }
        return null;
    }

    // Récupérer tous les groupes
    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM GROUPE");
        $stmt->execute();
        $groupes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $membres = $this->getMembres($row['idGroupe']);
            $groupes[] = new Groupe(
                $row['idGroupe'],
                $row['nomGroupe'],
                $row['description'],
                $row['dateCreationGroupe'],
                $membres
            );
        }

        return $groupes;
    }

    public function EnregistrerGroupe(Groupe $groupe): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO GROUPE (nomGroupe, description, dateCreationGroupe) 
            VALUES (:nomGroupe, :description, :dateCreationGroupe)
        ");
        $stmt->bindValue(':nomGroupe', $groupe->getNomGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $groupe->getDescriptionGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':dateCreationGroupe', $groupe->getDateCreation(), PDO::PARAM_STR);

        $res = $stmt->execute();
        if ($res) {
            $groupe->setIdGroupe((int)$this->conn->lastInsertId());
        }
        
        return $res;
    }

    public function delete(int $idGroupe): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM GROUPE WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function AjouterMembre(Groupe $groupe, int $idUtilisateur, string $dateAjout): bool
    {
        if ($groupe->estMembre($idUtilisateur)) {
            return false; 
        }
        $stmt = $this->conn->prepare("INSERT INTO COMPOSER (idGroupe, idUtilisateur, dateAjout) VALUES (:idGroupe, :idUtilisateur, :dateAjout)");
        $stmt->bindValue(':idGroupe', $groupe->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $dateAjout, PDO::PARAM_STR);
        return $stmt->execute(); 
    }
}
?>