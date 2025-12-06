<?php
require_once "Dao.class.php";

/**
 * Classe SignalerDao
 * 
 * Cette classe gère les opérations CRUD pour les signalements (table SIGNALER).
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $signalerDao = new SignalerDao();
 * $signalers = $signalerDao->findAll();
 */

class SignalerDao extends Dao {

    /**
     * Récupère tous les signalements.
     * 
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findAll(): array {
        $stmt = $this->conn->query("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM SIGNALER");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Signaler($row['idUtilisateur'], $row['idSignalement'], $row['idPost'], $row['dateSignalement'], $row['statut']), $rows);
    }

    /**
     * Récupère les signalements d'un utilisateur spécifique.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findByIdUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM SIGNALER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Signaler($row['idUtilisateur'], $row['idSignalement'], $row['idPost'], $row['dateSignalement'], $row['statut']), $rows);
    }

    /**
     * Récupère les signalements pour un post spécifique.
     * 
     * @param int $idPost Identifiant du post.
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findByIdPost(int $idPost): array {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM SIGNALER WHERE idPost = :idPost");
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => new Signaler($row['idUtilisateur'], $row['idSignalement'], $row['idPost'], $row['dateSignalement'], $row['statut']), $rows);
    }

    /**
     * Insère un nouveau signalement.
     * 
     * @param Signaler $signaler Objet Signaler à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererSignaler(Signaler $signaler): bool {
        $stmt = $this->conn->prepare("INSERT INTO SIGNALER (idUtilisateur, idSignalement, idPost, dateSignalement, statut) VALUES (:idUtilisateur, :idSignalement, :idPost, :dateSignalement, :statut)");
        $stmt->bindValue(':idUtilisateur', $signaler->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idSignalement', $signaler->getIdSignalement(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $signaler->getIdPost(), PDO::PARAM_INT);
        $stmt->bindValue(':dateSignalement', $signaler->getDateSignalement(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $signaler->getStatut(), PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Supprime un signalement.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @param int $idSignalement Identifiant du signalement.
     * @param int $idPost Identifiant du post.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerSignaler(int $idUtilisateur, int $idSignalement, int $idPost): bool {
        $stmt = $this->conn->prepare("DELETE FROM SIGNALER WHERE idUtilisateur = :idUtilisateur AND idSignalement = :idSignalement AND idPost = :idPost");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
