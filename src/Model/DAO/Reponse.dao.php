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

    /**
     * Récupère toutes les réponses associées à un post spécifique.
     * 
     * Cette méthode permet de récupérer toutes les réponses liées à un post donné en utilisant l'identifiant du post.
     * 
     * @param int $idPost Identifiant du post dont on souhaite récupérer les réponses.
     * @return Reponse[] Tableau d'objets `Reponse` associés au post.
     */
    public function findResponsesByPost(int $idPost): ?array{
        $sql = "SELECT * FROM REPONSE WHERE idPost = :idPost";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Reponse::class);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Récupère toutes les réponses d'un auteur spécifique.
     * 
     * Cette méthode permet de récupérer toutes les réponses soumises par un auteur donné en utilisant l'identifiant de l'auteur.
     * 
     * @param int $idAuteur Identifiant de l'auteur dont on souhaite récupérer les réponses.
     * @return Reponse[]|null Tableau d'objets `Reponse` soumis par l'auteur, ou null si aucune réponse trouvée.
     */
    public function findByAuteur(int $idAuteur): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM REPONSE WHERE idAuteur = :idAuteur ORDER BY dateReponse DESC");
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