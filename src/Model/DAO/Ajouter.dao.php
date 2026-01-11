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
        return array_map(fn($row) => new Ajouter($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
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
        return array_map(fn($row) => new Ajouter($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
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
        return array_map(fn($row) => new Ajouter($row['idObjet'], $row['idUtilisateur'], $row['dateAjout']), $rows);
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


    
}