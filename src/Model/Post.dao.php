<?php
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
class PostDao
{
    // Propriété représentant la connexion à la base de données via PDO.
    private PDO $conn;

    /**
     * Constructeur de la classe PostDao.
     * 
     * Ce constructeur initialise la connexion à la base de données en utilisant la classe Database.
     */
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    /**
     * Crée un nouveau post dans la base de données.
     * 
     * Cette méthode insère un nouveau post dans la table `POST` en utilisant les données de l'objet `Post` passé en paramètre.
     * 
     * @param Post $post L'objet `Post` à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function createPost(Post $post): bool
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
    // Supprimer un post
    public function deletePost(int $idPost): bool
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
    // Récupérer un post par ID de post
    public function find(int $id): ?Post
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idPost = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetch() ?: null;
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
    $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
    $stmt->execute();
    return $stmt->fetchAll() ?: [];
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
        $stmt = $this->conn->query("SELECT * FROM POST");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll() ?: [];
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
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}


