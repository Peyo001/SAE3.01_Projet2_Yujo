<?php
require_once "Dao.class.php";
/**
 * Classe PostDao
 * 
 * Cette classe gère les opérations CRUD pour les objets Post dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $postDao = new PostDao();
 * $post = $postDao->findAll();
 * 
 */
class PostDao extends Dao
{   
    public function hydrate(array $row): Post {
        return new Post(
            (int)$row['idPost'],
            $row['contenu'],
            $row['typePost'],
            $row['datePublication'],
            (int)$row['idAuteur'],
            (int)$row['idRoom']
        );
    }
    /**
     * Crée un nouveau post dans la base de données.
     * 
     * Cette méthode insère un nouveau post dans la table `POST` en utilisant les données de l'objet `Post` passé en paramètre.
     * 
     * @param Post $post L'objet `Post` à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insererPost(Post $post): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO POST (contenu, typePost, datePublication, idAuteur, idRoom)
            VALUES (:contenu, :typePost, :datePublication, :idAuteur, :idRoom)
        ");

        $stmt->bindValue(':contenu', $post->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(':typePost', $post->getTypePost(), PDO::PARAM_STR);
        $stmt->bindValue(':datePublication', $post->getDatePublication(), PDO::PARAM_STR);
        $stmt->bindValue(':idAuteur', $post->getIdAuteur(), PDO::PARAM_INT);
        $stmt->bindValue(':idRoom', $post->getIdRoom(), PDO::PARAM_INT);

        $success = $stmt->execute();

        if ($success) {
            $nouveauId = (int) $this->conn->lastInsertId();
            $post->setIdPost($nouveauId);
        }

        return $success;
    }

    
    /**
     * Supprime un post de la base de données.
     * 
     * Cette méthode supprime un post en fonction de son identifiant de la table `POST`.
     * 
     * @param int $idPost L'identifiant du post à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerPost(int $idPost): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM POST WHERE idPost = :idPost");
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    /**
     * Trouve un post dans la base de données par son identifiant.
     * 
     * Cette méthode récupère un post spécifique en fonction de son identifiant et retourne un objet `Post`.
     * 
     * @param int $id Identifiant du post à récupérer.
     * @return Post|null Retourne un objet `Post` si trouvé, sinon null.
     */
    public function find(int $id): ?Post
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idPost = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * Récupère les posts d'un auteur spécifique.
     * 
     * Cette méthode récupère tous les posts d'un utilisateur en fonction de son identifiant.
     * Les posts sont triés par date de publication, du plus récent au plus ancien.
     * 
     * @param int $idAuteur Identifiant de l'auteur des posts.
     * @return Post[] Tableau d'objets `Post` représentant les posts de l'auteur.
     */
    public function findPostsByAuteur(int $idAuteur): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idAuteur = :idAuteur ORDER BY datePublication DESC");
        $stmt->bindValue(':idAuteur', $idAuteur, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = $this->hydrateAll($rows);  
        return $posts;
    }

   /**
     * Récupère tous les posts dans la base de données.
     * 
     * Cette méthode récupère tous les posts de la table `POST`.
     * 
     * @return Post[] Tableau de tous les objets `Post` dans la base de données.
     */
    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST");
        $stmt->execute();
        $posts = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = $this->hydrateAll($rows);
        return $posts;
    }
    
    /**
     * Récupère tous les posts d'une room spécifique.
     * 
     * Cette méthode récupère tous les posts publiés dans une room en fonction de l'identifiant de la room.
     * Les posts sont triés par date de publication, du plus récent au plus ancien.
     * 
     * @param int $idRoom Identifiant de la room.
     * @return Post[] Tableau des objets `Post` dans la room spécifiée.
     */
    public function findPostsByRoom(int $idRoom): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idRoom = :idRoom ORDER BY datePublication DESC");
        $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = [];
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = $this->hydrateAll($rows);
        return $posts;
    }

/**
 * Récupère tous les posts publiés par un ensemble d'auteurs.
 *
 * @param int[] $ids Tableau des identifiants des auteurs.
 * @return Post[] Tableau des objets Post
 */
public function findByAuteurs(array $ids): array
{
    if (empty($ids)) {
        return []; // Aucun auteur, on retourne un tableau vide
    }

    // Crée une liste de placeholders pour la requête SQL (?,?,?)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $this->conn->prepare(
        "SELECT * FROM POST WHERE idAuteur IN ($placeholders) ORDER BY datePublication DESC"
    );

    // Exécute avec les IDs des auteurs
    $stmt->execute($ids);

    $posts = [];
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $posts = $this->hydrateAll($rows);

    return $posts;
}
}
