<?php
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
class ReponseDao
{   
    // Propriété représentant la connexion à la base de données via PDO.
    private PDO $conn;

    /**
     * Constructeur de la classe ReponseDao.
     * 
     * Ce constructeur initialise la connexion à la base de données en utilisant la classe Database.
     */
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }


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
     * Récupère toutes les réponses de la base de données.
     * 
     * Cette méthode récupère toutes les réponses enregistrées dans la base de données.
     * 
     * @return Reponse[] Tableau contenant toutes les réponses sous forme d'objets `Reponse`.
     */
    public function findAll(): array{
        $sql = "SELECT * FROM REPONSE";
        $pdoStatement = $this->conn->prepare($sql);
        $pdoStatement->execute();
        $reponse = [];

        while ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC)) {
            $reponse[] = new Reponse(
                $row['idReponse'],
                $row['dateReponse'],
                $row['contenu'],
                $row['idAuteur'],
                $row['idPost'],
            );
        }
        return $reponse;
    }

    /**
     * Crée une nouvelle réponse dans la base de données.
     * 
     * Cette méthode insère une nouvelle réponse dans la table `REPONSE` en utilisant les informations fournies par l'objet `Reponse`.
     * 
     * @param Reponse $response L'objet `Reponse` contenant les informations à insérer.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function createResponse(Reponse $response): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO REPONSE (idReponse, dateRepoonse, contenu, idAuteur, idPost) VALUES (:idReponse, :dateReponse, :contenu, :idAuteur, :idPost)");
        $stmt->bindValue(':idReponse', $response->getId(), PDO::PARAM_INT);
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
    public function deleteResponse(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM REPONSE WHERE idReponse = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


}