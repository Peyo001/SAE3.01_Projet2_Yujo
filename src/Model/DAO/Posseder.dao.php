<?php 
/**
 * Classe PossederDAO
 * 
 * Cette classe gère les opérations de base de données pour la table POSSEDER.
 * Elle permet de créer, lire, mettre à jour et supprimer des enregistrements de possession.
 * 
 * Exemple d'utilisation :
 * $possederDAO = new PossederDAO();
 * $possederList = $possederDAO->findByIdUtilisateur(42);
 * 
 */
class PossederDAO extends Dao {

    // MÉTHODES
    /**
     * Trouve toutes les possessions pour un utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return Posseder[] Tableau d'objets Posseder.
     */
    public function findByIdUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idUtilisateur, dateAjout FROM POSSEDER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Posseder($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
    }

    /**
     * Trouve toutes les possessions pour un objet donné.
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @return Posseder[] Tableau d'objets Posseder.
     */
    public function findByIdObjet(int $idObjet): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idUtilisateur, dateAjout FROM POSSEDER WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Posseder($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
    }

    /**
     * Récupère toutes les entrées POSSEDER dans la base de données.
     * 
     * @return Posseder[] Tableau des objets Posseder.
     */
    public function findAll(): array {
        $stmt = $this->conn->query("SELECT idObjet, idUtilisateur, dateAjout FROM POSSEDER");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Posseder($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
    }

    /**
     * Insère une nouvelle relation utilisateur-objet.
     * 
     * @param Posseder $posseder Objet Posseder à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererPosseder(Posseder $posseder): bool {
        $stmt = $this->conn->prepare("INSERT INTO POSSEDER (idObjet, idUtilisateur, dateAjout) VALUES (:idObjet, :idUtilisateur, :dateAjout)");
        $stmt->bindValue(':idObjet', $posseder->getIdObjet(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $posseder->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $posseder->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    /** 
     * Supprime une relation utilisateur-objet.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @param int $idObjet Identifiant de l'objet.
     */
    public function supprimerPosseder(int $idUtilisateur, int $idObjet): bool {
        $stmt = $this->conn->prepare("DELETE FROM POSSEDER WHERE idUtilisateur = :idUtilisateur AND idObjet = :idObjet");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Met à jour une relation utilisateur-objet.
     * 
     * @param Posseder $posseder Objet Posseder à mettre à jour.
     * @return bool Retourne true si la mise à jour a réussi, sinon false.
     */
    public function updatePosseder(Posseder $posseder): bool {
        $stmt = $this->conn->prepare("UPDATE POSSEDER SET dateAjout = :dateAjout WHERE idUtilisateur = :idUtilisateur AND idObjet = :idObjet");
        $stmt->bindValue(':dateAjout', $posseder->getDateAjout(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $posseder->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $posseder->getIdObjet(), PDO::PARAM_INT);
        return $stmt->execute();
    }


}
 