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
     * Hydrate une ligne de résultat en un objet Signaler.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Signaler Retourne un objet Signaler
     */
    public function hydrate(array $row): Signaler {
        return new Signaler(
            (int)$row['idUtilisateur'],
            (int)$row['idSignalement'],
            (int)$row['idPost'],
            $row['dateSignalement'],
            $row['statut']
        );
    }

    /**
     * Récupère tous les signalements.
     * 
     * Cette méthode permet de récupérer tous les signalements présents dans la table `SIGNALER`.
     * 
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM signaler");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Récupère les signalements d'un utilisateur spécifique.
     * 
     * Cette méthode permet de récupérer tous les signalements effectués par un utilisateur donné en utilisant son identifiant.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findByIdUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM signaler WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Récupère les signalements pour un post spécifique.
     * 
     * Cette méthode permet de récupérer tous les signalements associés à un post donné en utilisant son identifiant.
     * 
     * @param int $idPost Identifiant du post.
     * @return Signaler[] Tableau des objets Signaler.
     */
    public function findByIdPost(int $idPost): array {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, idSignalement, idPost, dateSignalement, statut FROM signaler WHERE idPost = :idPost");
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Insère un nouveau signalement.
     * 
     * Cette méthode permet d'insérer un nouveau signalement dans la table `SIGNALER`.
     * 
     * @param Signaler $signaler Objet Signaler à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererSignaler(Signaler $signaler): bool {
        $stmt = $this->conn->prepare("INSERT INTO signaler (idUtilisateur, idSignalement, idPost, dateSignalement, statut) VALUES (:idUtilisateur, :idSignalement, :idPost, :dateSignalement, :statut)");
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
     * Cette méthode permet de supprimer un signalement spécifique de la table `SIGNALER` en fonction de l'identifiant de l'utilisateur, de l'identifiant du signalement et de l'identifiant du post.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @param int $idSignalement Identifiant du signalement.
     * @param int $idPost Identifiant du post.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerSignaler(int $idUtilisateur, int $idSignalement, int $idPost): bool {
        $stmt = $this->conn->prepare("DELETE FROM signaler WHERE idUtilisateur = :idUtilisateur AND idSignalement = :idSignalement AND idPost = :idPost");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
