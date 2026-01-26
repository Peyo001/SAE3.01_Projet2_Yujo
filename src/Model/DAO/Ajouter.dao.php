<?php
require_once "Dao.class.php";

/**
 * Classe AjouterDao
 * 
 * Cette classe permet d'accéder aux données de la table AJOUTER dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $ajouterDao = new AjouterDao();
 * $ajout = $ajouterDao->findByIdUtilisateur(1);
 */
class AjouterDao extends Dao {

    /**
     * Hydrate une ligne de résultat en un objet Ajouter.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Ajouter|null Retourne un objet Ajouter ou null si les données sont invalides
     */
    public function hydrate(array $row): ?Ajouter {
        return new Ajouter(
            (int)$row['idObjet'],
            (int)$row['idUtilisateur'],
            $row['dateAjout']
        );
    }

    

    /**
     * Retourne toutes les entrées AJOUTER pour un utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return Ajouter[] Tableau des objets Ajouter.
     */
    public function findByIdUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idUtilisateur, dateAjout FROM AJOUTER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }


    /**
     * Retourne toutes les entrées AJOUTER pour un objet donné.
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @return Ajouter[] Tableau des objets Ajouter.
     */
    public function findByIdObjet(int $idObjet): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idUtilisateur, dateAjout FROM AJOUTER WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Récupère toutes les entrées AJOUTER dans la base de données.
     * 
     * @return Ajouter[] Tableau des objets Ajouter.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idObjet, idUtilisateur, dateAjout FROM AJOUTER");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    /**
     * Insère une nouvelle relation utilisateur-objet.
     * 
     * @param Ajouter $ajout Objet Ajouter à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererAjouter(Ajouter $ajout): bool {
        $stmt = $this->conn->prepare("INSERT INTO AJOUTER (idUtilisateur, idObjet, dateAjout) VALUES (:idUtilisateur, :idObjet, :dateAjout)");
        $stmt->bindValue(':idUtilisateur', $ajout->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $ajout->getIdObjet(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $ajout->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }


    /**
     * Supprime une relation utilisateur-objet.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @param int $idObjet Identifiant de l'objet.
     */
    public function supprimerAjouter(int $idUtilisateur, int $idObjet): bool {
        $stmt = $this->conn->prepare("DELETE FROM AJOUTER WHERE idUtilisateur = :idUtilisateur AND idObjet = :idObjet");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**
     * Supprime toutes les relations AJOUTER pour un objet donné.
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @return bool True si la suppression s'est exécutée (peut supprimer 0..n lignes).
     */
    public function supprimerParObjet(int $idObjet): bool {
        $stmt = $this->conn->prepare("DELETE FROM AJOUTER WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
}