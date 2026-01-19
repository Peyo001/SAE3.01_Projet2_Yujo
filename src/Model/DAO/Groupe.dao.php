<?php
require_once "Dao.class.php";
/**
 * Classe GroupeDao
 * 
 * Cette classe gère les opérations de la base de données pour les groupes.
 * Elle utilise la classe DATABASE pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $groupeDao = new GroupeDao();
 * $groupe = $groupeDao->findAll();
 * 
 */
class GroupeDao extends Dao
{   

    /**
     * Récupère les membres d'un groupe.
     * 
     * Cette méthode récupère la liste des utilisateurs membres d'un groupe à partir de la table `COMPOSER`.
     * 
     * @param int $idGroupe Identifiant du groupe pour lequel on veut récupérer les membres.
     * @return array Liste des identifiants des utilisateurs membres du groupe.
     */
    private function getMembres(int $idGroupe): array
    {
        $stmt = $this->conn->prepare("
            SELECT COMPOSER.idUtilisateur 
            FROM GROUPE 
            JOIN COMPOSER ON GROUPE.idGroupe = COMPOSER.idGroupe 
            WHERE GROUPE.idGroupe = :idGroupe
        ");
        
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Trouve un groupe par son identifiant.
     * 
     * Cette méthode recherche un groupe dans la base de données en fonction de son identifiant et retourne un objet Groupe.
     * 
     * @param int $idGroupe Identifiant du groupe à récupérer.
     * @return Groupe|null Retourne un objet Groupe si le groupe est trouvé, sinon null.
     */
    public function find(int $idGroupe): ?Groupe
    {
        $stmt = $this->conn->prepare("SELECT * FROM GROUPE WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $membres = $this->getMembres($idGroupe);
            return new Groupe(
                $row['nomGroupe'],
                $row['description'],
                $row['dateCreationGroupe'],
                $membres,
                (int)$row['idGroupe']
            );
        }
        return null;
    }

    /**
     * Récupère tous les groupes.
     * 
     * Cette méthode récupère tous les groupes et retourne un tableau d'objets Groupe.
     * 
     * @return Groupe[] Tableau contenant tous les groupes.
     */
    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM GROUPE");
        $stmt->execute();
        $groupes = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $groupes = $this->hydrateAll($rows);

        return $groupes;
    }

    /**
     * Hydrate un objet Groupe à partir d'un tableau de données.
     * 
     * @param array $data Tableau associatif contenant les données du groupe.
     * @return Groupe L'objet Groupe hydraté.
     */
    public function hydrate(array $data): Groupe
    {
        $membres = $this->getMembres($data['idGroupe']);
        return new Groupe(
            $data['nomGroupe'],
            $data['description'],
            $data['dateCreationGroupe'],
            $membres,
            (int)$data['idGroupe']
        );
    }


    /**
     * Recherche des groupes par nom ou description.
     * @param string $term Terme de recherche.
     * @return Groupe[]
     */
    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->conn->prepare("SELECT * FROM GROUPE WHERE nomGroupe LIKE :like OR description LIKE :like");
        $stmt->bindValue(':like', $like, PDO::PARAM_STR);
        $stmt->execute();
        $groupes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $membres = $this->getMembres($row['idGroupe']);
            $groupes[] = new Groupe(
                $row['nomGroupe'],
                $row['description'],
                $row['dateCreationGroupe'],
                $membres,
                (int)$row['idGroupe']
            );
        }

        return $groupes;
    }

    /**
     * Insère un nouveau groupe dans la base de données.
     * 
     * Cette méthode ajoute un nouveau groupe dans la base de données avec les données de l'objet Groupe passé en paramètre.
     * 
     * @param Groupe $groupe L'objet Groupe à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererGroupe(Groupe $groupe): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO GROUPE (nomGroupe, description, dateCreationGroupe) 
            VALUES (:nomGroupe, :description, :dateCreationGroupe)
        ");
        $stmt->bindValue(':nomGroupe', $groupe->getNomGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $groupe->getDescriptionGroupe(), PDO::PARAM_STR);
        $stmt->bindValue(':dateCreationGroupe', $groupe->getDateCreation(), PDO::PARAM_STR);

        $res = $stmt->execute();
        if ($res) {
            $groupe->setIdGroupe((int)$this->conn->lastInsertId());
        }
        
        return $res;
    }

   
     /**
     * Supprime un groupe de la base de données.
     * 
     * Cette méthode supprime un groupe de la base de données en fonction de son identifiant.
     * 
     * @param int $idGroupe L'identifiant du groupe à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerGroupe(int $idGroupe): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM GROUPE WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
     /**
     * Ajoute un membre au groupe.
     * 
     * Cette méthode permet d'ajouter un membre à un groupe, si le membre n'est pas déjà présent.
     * 
     * @param Groupe $groupe L'objet Groupe auquel le membre doit être ajouté.
     * @param int $idUtilisateur L'identifiant de l'utilisateur à ajouter.
     * @param string $dateAjout La date d'ajout du membre au groupe.
     * @return bool Retourne true si l'ajout a réussi, sinon false.
     */
    public function ajouterMembre(Groupe $groupe, int $idUtilisateur, string $dateAjout): bool
    {
        if ($groupe->estMembre($idUtilisateur)) {
            return false; 
        }
        $stmt = $this->conn->prepare("INSERT INTO COMPOSER (idGroupe, idUtilisateur, dateAjout) VALUES (:idGroupe, :idUtilisateur, :dateAjout)");
        $stmt->bindValue(':idGroupe', $groupe->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':dateAjout', $dateAjout, PDO::PARAM_STR);
        return $stmt->execute(); 
    }    
}
?>