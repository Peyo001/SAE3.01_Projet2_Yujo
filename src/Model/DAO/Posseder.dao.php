<?php 
/**
 * Classe PossederDAO
 * 
 * Cette classe gère les opérations de base de données pour la table POSSEDER.
 * Elle permet de créer, lire, mettre à jour et supprimer des enregistrements de possession.
 * 
 * Exemple d'utilisation :
 * $possederDAO = new PossederDAO();
 * $possederList = $possederDAO->findByRoom(42);
 * 
 */
class PossederDAO extends Dao {

    /**
     * Hydrate une ligne de résultat en un objet Posseder.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Posseder Retourne un objet Posseder
     */
    public function hydrate(array $row): Posseder {
        return new Posseder(
            (int)$row['idObjet'],
            (int)$row['idRoom'],
            $row['dateAjout']
        );
    }
    // MÉTHODES
    /**
     * Trouve toutes les possessions pour une room donné.
     * 
     * @param int $idRoom Identifiant de la room.
     * @return Posseder[] Tableau d'objets Posseder.
     */
    public function findByRoom(int $idRoom): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idRoom, dateAjout FROM posseder WHERE idRoom = :idRoom");
        $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Trouve toutes les possessions pour un objet donné.
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @return Posseder[] Tableau d'objets Posseder.
     */
    public function findByIdObjet(int $idObjet): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idRoom, dateAjout FROM posseder WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Récupère toutes les entrées POSSEDER dans la base de données.
     * 
     * @return Posseder[] Tableau des objets Posseder.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idRoom, dateAjout FROM posseder");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Insère une nouvelle relation room-objet.
     * 
     * @param Posseder $posseder Objet Posseder à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererPosseder(Posseder $posseder): bool {
        $stmt = $this->conn->prepare("INSERT INTO posseder (idObjet, idRoom, dateAjout) VALUES (:idObjet, :idRoom, :dateAjout)");
        $stmt->bindValue(':idObjet', $posseder->getIdObjet(), PDO::PARAM_INT);
        $stmt->bindValue(':idRoom', $posseder->getIdRoom(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $posseder->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    /** 
     * Supprime une relation room-objet.
     * 
     * @param int $idRoom Identifiant de la room.
     * @param int $idObjet Identifiant de l'objet.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerPosseder(int $idRoom, int $idObjet): bool {
        $stmt = $this->conn->prepare("DELETE FROM posseder WHERE idRoom = :idRoom AND idObjet = :idObjet");
        $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Met à jour une relation room-objet.
     * 
     * @param Posseder $posseder Objet Posseder à mettre à jour.
     * @return bool Retourne true si la mise à jour a réussi, sinon false.
     */
    public function updatePosseder(Posseder $posseder): bool {
        $stmt = $this->conn->prepare("UPDATE posseder SET dateAjout = :dateAjout WHERE idRoom = :idRoom AND idObjet = :idObjet");
        $stmt->bindValue(':dateAjout', $posseder->getDateAjout(), PDO::PARAM_STR);
        $stmt->bindValue(':idRoom', $posseder->getIdRoom(), PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $posseder->getIdObjet(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprime toutes les relations POSSEDER pour un objet donné (toutes rooms).
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @return bool True si la suppression s'est exécutée (peut supprimer 0..n lignes).
     */
    public function supprimerParObjet(int $idObjet): bool {
        $stmt = $this->conn->prepare("DELETE FROM posseder WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
 