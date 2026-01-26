<?php
require_once "Dao.class.php";
/**
 * Classe AchatDao
 * 
 * Cette classe gère les opérations CRUD pour les objets Achat dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $achatDao = new AchatDao();
 * $achat = $achatDao->findAll();
 * 
 */

class AchatDao extends Dao
{

    /**
     * Trouve un achat en fonction de son identifiant d'objet.
     * 
     * Cette méthode recherche un achat spécifique dans la base de données en fonction de l'identifiant de l'objet.
     * 
     * @param int $idObjet Identifiant de l'objet à rechercher.
     * @return Achat|null Retourne un objet Achat si trouvé, sinon null.
     */
    public function findByIdObjet(int $idObjet): ?Achat{
        $stmt = $this->conn->prepare("SELECT * FROM ACHETER WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Récupère tous les achats enregistrés dans la base de données.
     * 
     * Cette méthode récupère tous les achats et retourne un tableau d'objets Achat.
     * 
     * @return Achat[] Tableau contenant tous les objets Achat.
     */
    public function findAll(): array
    {
        $achats = [];
        $stmt = $this->conn->query("SELECT * FROM ACHETER");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $achats = $this->hydrateAll($rows);
        return $achats;
    }

    /**
     * Vérifie si un utilisateur a déjà acheté un objet spécifique.
     * 
     * Cette méthode recherche un achat en fonction de l'identifiant de l'objet et de l'identifiant de l'utilisateur.
     * 
     * @param int $idObjet Identifiant de l'objet à vérifier.
     * @param int $idUtilisateur Identifiant de l'utilisateur à vérifier.
     * @return Achat|null Retourne un objet Achat si l'achat existe, sinon null. 
     */
    public function findByObjetUtilisateur(int $idObjet, int $idUtilisateur): ?Achat
    {
        $stmt = $this->conn->prepare("SELECT * FROM ACHETER WHERE idObjet = :idObjet AND idUtilisateur = :idUtilisateur LIMIT 1");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Récupère tous les achats effectués par un utilisateur spécifique.
     * 
     * Cette méthode recherche tous les achats associés à un identifiant d'utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur dont les achats doivent être récupérés.
     * @return Achat[] Tableau contenant les objets Achat associés à l'utilisateur.
     */
    public function findByUtilisateur(int $idUtilisateur): array
    {
        $achats = [];
        $stmt = $this->conn->prepare("SELECT * FROM ACHETER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $achats = $this->hydrateAll($rows);
        return $achats;
    }

    /** 
     * Hydrate une ligne de résultat en un objet Achat.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Achat|null Retourne un objet Achat ou null si les données sont invalides
     */
    public function hydrate(array $row): ?Achat
    {
        return new Achat(
            (int)$row['idObjet'],
            $row['dateAchat'],
            (int)$row['idUtilisateur']
        );
    }

    /**
     * Compte le nombre d'achats effectués par un utilisateur spécifique.
     * 
     * Cette méthode retourne le nombre total d'achats associés à un identifiant d'utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return int Nombre total d'achats effectués par l'utilisateur.
     */
    public function countAchatByUtilisateur(int $idUtilisateur): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM ACHETER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    /**
     * Insère un nouvel achat dans la base de données.
     * 
     * Cette méthode permet d'ajouter un nouvel achat pour un utilisateur.
     * 
     * @param Achat $achat L'objet Achat à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererAchat(Achat $achat): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO ACHETER (idObjet, dateAchat, idUtilisateur) VALUES (:idObjet, :dateAchat, :idUtilisateur)");
        $stmt->bindValue(':idObjet', $achat->getIdObjet(), PDO::PARAM_INT);
        $stmt->bindValue(':dateAchat', $achat->getDateAchat(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $achat->getIdUtilisateur(), PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**
     * Supprime un achat en fonction de l'identifiant de l'objet.
     * 
     * Cette méthode permet de supprimer un achat en fonction de l'identifiant de l'objet acheté.
     * 
     * @param int $idObjet Identifiant de l'objet dont l'achat doit être supprimé.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerAchat(int $idObjet, ?int $idUtilisateur = null): bool
    {
        $sql = "DELETE FROM ACHETER WHERE idObjet = :idObjet" . ($idUtilisateur !== null ? " AND idUtilisateur = :idUtilisateur" : "");
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        if ($idUtilisateur !== null) {
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    /**
     * Supprime tous les achats liés à un objet (pour nettoyer avant suppression d'objet).
     *
     * @param int $idObjet Identifiant de l'objet.
     * @return bool True si la suppression s'exécute (0..n lignes possibles).
     */
    public function supprimerParObjet(int $idObjet): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM ACHETER WHERE idObjet = :idObjet");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprime l'achat d'un objet pour un utilisateur précis.
     *
     * @param int $idObjet Identifiant de l'objet.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool True si au moins la requête s'exécute (0..n lignes supprimées).
     */
    public function supprimerParObjetEtUtilisateur(int $idObjet, int $idUtilisateur): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM ACHETER WHERE idObjet = :idObjet AND idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Liste des idObjet déjà achetés par un utilisateur.
     * 
     * Cette méthode récupère les identifiants des objets achetés par un utilisateur spécifique.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return int[] Tableau des identifiants des objets achetés par l'utilisateur.
     */
    public function listObjetsAchetesByUtilisateur(int $idUtilisateur): array
    {
        $stmt = $this->conn->prepare("SELECT idObjet FROM ACHETER WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $ids = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ids[] = (int)$row['idObjet'];
        }
        return $ids;
    }
}
?>