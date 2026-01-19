<?php
require_once "Dao.class.php";

/**
 * Classe DAO pour la table COMPOSER
 * 
 * Cette classe fournit des méthodes pour interagir avec la table COMPOSER de la base de données,   
 * permettant de gérer les relations entre les groupes et leurs membres.
 * 
 * Exemple d'utilisation :
 * $composerDao = new ComposerDao();
 * $composers = $composerDao->findAll();
 * 
 * 
 */
class ComposerDao extends Dao{

    /**
     * Hydrate une ligne de résultat en un objet Composer.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Composer Retourne un objet Composer
     */
    public function hydrate(array $row): Composer {
        return new Composer(
            (int)$row['idGroupe'],
            (int)$row['idUtilisateur'],
            $row['dateAjout']
        );
    }

        

    /**
     * Récupère tous les enregistrements de la table COMPOSER pour un utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return Composer[] Tableau des objets Composer.
     */
    public function findByIdUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT idGroupe, idUtilisateur, dateAjout FROM COMPOSER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }


    /**
     * Récupère un avatar spécifique en fonction de son identifiant.
     * 
     * Cette méthode exécute une requête pour récupérer un avatar par son identifiant et retourne un objet Avatar.
     * 
     * @param int $idAvatar L'identifiant de l'avatar à récupérer.
     * @return Avatar|null Retourne un objet Avatar si l'avatar est trouvé, sinon null.
     */
    public function findByIdGroupe(int $idGroupe): array {
        $stmt = $this->conn->prepare("SELECT idGroupe, idUtilisateur, dateAjout FROM COMPOSER WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }



    /**
     * Récupère tous les enregistrements de la table COMPOSER.
     * 
     * Cette méthode exécute une requête pour récupérer tous les enregistrements de la table COMPOSER
     * et retourne un tableau d'objets Composer.
     * 
     * @return Composer[] Tableau d'objets Composer représentant tous les enregistrements de la table COMPOSER.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idGroupe, idUtilisateur, dateAjout FROM COMPOSER");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->hydrateAll($rows);
    }

    public function insererComposer(Composer $composer): bool {
        $stmt = $this->conn->prepare("INSERT INTO COMPOSER (idGroupe, idUtilisateur, dateAjout) VALUES (:idGroupe, :idUtilisateur, :dateAjout)");
        $stmt->bindValue(':idGroupe', $composer->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $composer->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $composer->getDateAjout(), PDO::PARAM_STR);
        return $stmt->execute();
    }


    /**     
     * Supprime un enregistrement de la table COMPOSER en fonction de l'identifiant du groupe et de l'utilisateur.
     * 
     * Cette méthode permet de supprimer une relation entre un groupe et un utilisateur dans la table COMPOSER.
     * 
     * @param int $idGroupe Identifiant du groupe.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */

    public function supprimerComposer(int $idGroupe, int $idUtilisateur): bool {
        $stmt = $this->conn->prepare("DELETE FROM COMPOSER WHERE idGroupe = :idGroupe AND idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**     
     * Supprime tous les enregistrements de la table COMPOSER pour un groupe donné.
     * 
     * Cette méthode permet de supprimer toutes les relations entre un groupe et ses utilisateurs dans la table COMPOSER.
     * 
     * @param int $idGroupe Identifiant du groupe.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerParIdGroupe(int $idGroupe): bool {
        $stmt = $this->conn->prepare("DELETE FROM COMPOSER WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**     
     * Met à jour la date d'ajout pour un enregistrement spécifique dans la table COMPOSER.
     * 
     * Cette méthode permet de modifier la date d'ajout d'un utilisateur à un groupe dans la table COMPOSER.
     * 
     * @param int $idGroupe Identifiant du groupe.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @param string $newDateAjout Nouvelle date d'ajout au format 'YYYY-MM-DD HH:MM:SS'.
     * @return bool Retourne true si la mise à jour a réussi, sinon false.
     */
    public function updateDateAjout(int $idGroupe, int $idUtilisateur, string $newDateAjout): bool {
        $stmt = $this->conn->prepare("UPDATE COMPOSER SET dateAjout = :dateAjout WHERE idGroupe = :idGroupe AND idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':dateAjout', $newDateAjout, PDO::PARAM_STR);
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }
}