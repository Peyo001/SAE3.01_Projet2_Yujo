<?php
/**
 * Classe ListerDAO
 * 
 * Cette classe gère les opérations de base de données pour la table LISTER.
 * Elle permet de créer, lire, mettre à jour et supprimer des enregistrements d'association.
 * 
 * Exemple d'utilisation :
 * $listerDAO = new ListerDAO();
 * $listerList = $listerDAO->findByQuestion(42);
 * 
 */
class ListerDAO extends Dao {

    /**
     * Hydrate une ligne de résultat en un objet Lister.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Lister Retourne un objet Lister
     */
    public function hydrate(array $row): Lister {
        return new Lister(
            (int)$row['idReponsePossible'],
            (int)$row['idQuestion']
        );
    }


    // MÉTHODES
    /**
     * Trouve toutes les associations pour une question donnée.
     * 
     * @param int $idQuestion Identifiant de la question.
     * @return Lister[] Tableau d'objets Lister.
     */
    public function findByQuestion(int $idQuestion): array {
        $stmt = $this->conn->prepare("SELECT idReponsePossible, idQuestion FROM lister WHERE idQuestion = :idQuestion");
        $stmt->bindValue(':idQuestion', $idQuestion, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Trouve toutes les associations pour une réponse donnée.
     * 
     * @param int $idReponsePossible Identifiant de la réponse.
     * @return Lister[] Tableau d'objets Lister.
     */
    public function findByReponsePossible(int $idReponsePossible): array {
        $stmt = $this->conn->prepare("SELECT idReponsePossible, idQuestion FROM lister WHERE idReponsePossible = :idReponsePossible");
        $stmt->bindValue(':idReponsePossible', $idReponsePossible, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Récupère toutes les entrées LISTER dans la base de données.
     * 
     * @return Lister[] Tableau des objets Lister.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idReponsePossible, idQuestion FROM lister");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Insère une nouvelle relation question-réponse.
     * 
     * @param Lister $lister Objet Lister à insérer.
     */
    public function insererLister(Lister $lister): void {
        $stmt = $this->conn->prepare("INSERT INTO lister (idReponsePossible, idQuestion) VALUES (:idReponsePossible, :idQuestion)");
        $stmt->bindValue(':idReponsePossible', $lister->getIdReponsePossible(), PDO::PARAM_INT);
        $stmt->bindValue(':idQuestion', $lister->getIdQuestion(), PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Supprime une relation question-réponse.
     * 
     * @param int $idReponsePossible Identifiant de la réponse.
     * @param int $idQuestion Identifiant de la question.
     */
    public function supprimerLister(int $idReponsePossible, int $idQuestion): void {
        $stmt = $this->conn->prepare("DELETE FROM lister WHERE idReponsePossible = :idReponsePossible AND idQuestion = :idQuestion");
        $stmt->bindValue(':idReponsePossible', $idReponsePossible, PDO::PARAM_INT);
        $stmt->bindValue(':idQuestion', $idQuestion, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Met à jour une relation question-réponse.
     * 
     * @return bool Retourne true si la mise à jour a réussi, sinon false.
     */
    public function mettreAJourLister(Lister $lister): bool {
        $stmt = $this->conn->prepare("UPDATE lister SET idReponsePossible = :idReponsePossible WHERE idQuestion = :idQuestion");
        $stmt->bindValue(':idReponsePossible', $lister->getIdReponsePossible(), PDO::PARAM_INT);
        $stmt->bindValue(':idQuestion', $lister->getIdQuestion(), PDO::PARAM_INT);
        return $stmt->execute();
    }
}