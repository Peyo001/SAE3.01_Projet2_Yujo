<?php
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
     * Définit la connexion à la base de données.
     * 
     * Cette méthode permet de définir une connexion PDO personnalisée.
     * 
     * @param PDO $conn La connexion à la base de données.
     */
    public function setConn(PDO $conn): void
    {
        $this->conn = $conn;
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
        $stmt = $this->conn->query("SELECT idSignalement, raison FROM SIGNALEMENT");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $signalement = new Signalement($row['idSignalement'], $row['raison']);
            $signalements[] = $signalement;
        }
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
        $stmt = $this->conn->prepare("SELECT idSignalement, raison FROM SIGNALEMENT WHERE idSignalement = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Signalement($row['idSignalement'], $row['raison']);
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
    public function insert(Signalement $signalement): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO SIGNALEMENT (raison) VALUES (:raison)");
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
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM SIGNALEMENT WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>