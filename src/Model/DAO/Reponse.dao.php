<?php
require_once "Dao.class.php";
/**
 * Classe ReponseDao
 * 
 * Cette classe gère les opérations de la base de données pour les réponses aux posts.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $reponseDao = new ReponseDao();
 * $reponse = $reponseDao->findAll();
 */
class ReponseDao extends Dao
{   
    /**
     * Récupère une réponse spécifique par son identifiant.
     * 
     * Cette méthode permet de récupérer une réponse spécifique dans la base de données en fonction de son identifiant.
     * 
     * @param ?int $id Identifiant de la réponse à récupérer. Si null, retourne null.
     * @return Reponse|null Retourne un objet `Reponse` si trouvé, sinon null.
     */
    public function find(?int $id): ?Reponse{
        $sql = "SELECT * FROM REPONSE WHERE idReponse = :id";
        $pdoStatement = $this->conn->prepare($sql);
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();
        $row = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Reponse(
                $row['idReponse'],
                $row['dateReponse'],
                $row['contenu'],
                $row['idAuteur'],
                $row['idPost'],
            );
        }
        return null;
    }

    public function findResponsesByPost(int $idPost): ?array{
        $sql = "SELECT * FROM REPONSE WHERE idPost = :idPost";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Reponse::class);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function findByAuteur(int $idAuteur): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM REPONSE WHERE idAuteur = :idAuteur ORDER BY datePublication DESC");
        $stmt->bindValue(':idAuteur', $idAuteur, PDO::PARAM_INT);
        $stmt->execute();
        
        $reponses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reponses[] = new Reponse(
                (int)$row['idReponse'],
                $row['contenu'],
                $row['dateReponse'],
                (int)$row['idPost'],
                (int)$row['idAuteur']
            );
        }
        return $reponses;
    }

    /**
     * Récupère toutes les réponses de la base de données.
     * 
     * Cette méthode récupère toutes les réponses enregistrées dans la base de données.
     * 
     * @return Reponse[] Tableau contenant toutes les réponses sous forme d'objets `Reponse`.
     */
    public function findAll(): array{
        $stmt = $this->conn->query("SELECT * FROM REPONSE");
        $reponses = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reponse = new Reponse(
                $row['idReponse'],
                $row['dateReponse'],
                $row['contenu'],
                $row['idAuteur'],
                $row['idPost'],
            );
            $reponses[] = $reponse;
        }
        return $reponses;
    }

    /**
     * Crée une nouvelle réponse dans la base de données.
     * 
     * Cette méthode insère une nouvelle réponse dans la table `REPONSE` en utilisant les informations fournies par l'objet `Reponse`.
     * 
     * @param Reponse $response L'objet `Reponse` contenant les informations à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererReponse(Reponse $response): bool {
        $stmt = $this->conn->prepare("INSERT INTO REPONSE (dateReponse, contenu, idAuteur, idPost) VALUES (:dateReponse, :contenu, :idAuteur, :idPost)");
        $stmt->bindValue(':dateReponse', $response->getDateReponse(), PDO::PARAM_STR);
        $stmt->bindValue(':contenu', $response->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(':idAuteur', $response->getIdAuteur(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $response->getIdPost(), PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    
    /**
     * Supprime une réponse de la base de données.
     * 
     * Cette méthode permet de supprimer une réponse spécifique de la table `REPONSE` en fonction de son identifiant.
     * 
     * @param int $id Identifiant de la réponse à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerReponse(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM REPONSE WHERE idReponse = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    

}