<?php
require_once 'Dao.class.php';
/** 
 * Classe SanctionDao
 * 
 * Cette classe gère les opérations CRUD pour les objets Sanction dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $sanctionDao = new SanctionDao();
 * $sanction = $sanctionDao->findAll();
 * 
 */
class SanctionDao extends Dao
{

    /**
     * Récupère toutes les sanctions de la base de données.
     * 
     * Cette méthode récupère toutes les sanctions présentes dans la table `SIGNALER` et les retourne sous forme d'objets `Sanction`.
     * 
     * @return Sanction[] Tableau des objets `Sanction`.
     */
    public function findAll(): array {
        $sanctions = [];
        $stmt = $this->conn->query("SELECT idSignalement, idUtilisateur, idPost, dateSignalement, statut FROM SIGNALER");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sanction = new Sanction(
                $row['idSignalement'],
                $row['idUtilisateur'],
                $row['idPost'],
                $row['dateSignalement'],
                $row['statut']
            );
            $sanctions[] = $sanction;
        }
        return $sanctions;
    }   

    /**
     * Récupère une sanction spécifique par son identifiant.
     * 
     * Cette méthode récupère une sanction spécifique en fonction de son identifiant de la table `SIGNALER`.
     * 
     * @param int $idSignalement Identifiant du signalement à récupérer.
     * @return Sanction|null Retourne un objet `Sanction` si trouvé, sinon null.
     */
    public function findByIdSignalement(int $idSignalement): ?Sanction {
        $stmt = $this->conn->prepare("SELECT idSignalement, idUtilisateur, idPost, dateSignalement, statut FROM SIGNALER WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Sanction(
                $row['idSignalement'],
                $row['idUtilisateur'],
                $row['idPost'],
                $row['dateSignalement'],
                $row['statut']
            );
        }
        return null;
    }

    /**
     * Insère une nouvelle sanction dans la base de données.
     * 
     * Cette méthode insère un nouvel enregistrement dans la table `SIGNALER` en utilisant les informations de l'objet `Sanction`.
     * 
     * @param Sanction $sanction L'objet `Sanction` à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererSanction(Sanction $sanction): bool {
        $stmt = $this->conn->prepare("INSERT INTO SIGNALER (idSignalement, idUtilisateur, idPost, dateSignalement, statut) VALUES (:idSignalement, :idUtilisateur, :idPost, :dateSignalement, :statut)");
        $stmt->bindValue(':idSignalement', $sanction->getIdSignalement(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $sanction->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $sanction->getIdPost(), PDO::PARAM_INT);
        $stmt->bindValue(':dateSignalement', $sanction->getDateSignalement(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $sanction->getStatus(), PDO::PARAM_STR);
        return $stmt->execute();
    }

   
     /**
     * Supprime une sanction de la base de données.
     * 
     * Cette méthode supprime une sanction en fonction de son identifiant de la table `SIGNALER`.
     * 
     * @param int $idSignalement Identifiant de la sanction à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerSanction(int $idSignalement): bool {
        $stmt = $this->conn->prepare("DELETE FROM SIGNALER WHERE idSignalement = :idSignalement");
        $stmt->bindValue(':idSignalement', $idSignalement, PDO::PARAM_INT);
        return $stmt->execute();
    }

}