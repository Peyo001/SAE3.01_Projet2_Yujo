<?php
require_once 'Dao.class.php';
/**
 * Classe SignalementDao
 * 
 * Cette classe gère les opérations CRUD pour les signalements dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $signalementDao = new SignalementDao();
 * $signalement = $signalementDao->findAll();
 * 
 */
class SignalementDao extends Dao    
{   
    /** 
     * Hydrate une ligne de résultat en un objet Signalement.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Signalement Retourne un objet Signalement
     */
    public function hydrate(array $row): Signalement {
        return new Signalement(
            (int)$row['idSignalement'],
            $row['raison']
        );
    }

    /**
     * Récupère tous les signalements de la base de données.
     * 
     * Cette méthode permet de récupérer tous les signalements présents dans la table `SIGNALEMENT`.
     * 
     * @return Signalement[] Tableau des objets `Signalement`.
     */
    public function findAll(): array
    {
        $signalements = [];
        $stmt = $this->conn->prepare("SELECT idSignalement, raison FROM signalement");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $signalements = $this->hydrateAll($rows);
        return $signalements;
    }

    /**
     * Récupère un signalement spécifique par son identifiant.
     * 
     * Cette méthode permet de récupérer un signalement spécifique en fonction de son identifiant dans la table `SIGNALEMENT`.
     * 
     * @param int $id Identifiant du signalement à récupérer.
     * @return Signalement|null Retourne un objet `Signalement` si trouvé, sinon null.
     */
    public function find(int $id): ?Signalement
    {
        $stmt = $this->conn->prepare("SELECT idSignalement, raison FROM signalement WHERE idSignalement = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Insère un nouveau signalement dans la base de données.
     * 
     * Cette méthode permet d'insérer un nouvel enregistrement de signalement dans la table `SIGNALEMENT`.
     * 
     * @param Signalement $signalement L'objet `Signalement` à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererSignalement(Signalement $signalement): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO signalement (raison) VALUES (:raison)");
        $stmt->bindParam(':raison', $signalement->getRaison(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    
    
    /**
     * Supprime un signalement de la base de données.
     * 
     * Cette méthode permet de supprimer un signalement en fonction de son identifiant de la table `SIGNALEMENT`.
     * 
     * @param int $idSignalement Identifiant du signalement à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerSignalement(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM signalement WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
?>