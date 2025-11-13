<?php
class PostDao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getLastId(): int
    {
        $stmt = $this->conn->query("SELECT MAX(idPost) AS maxId FROM POST");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['maxId'] ?? 0; // si la table est vide, retourne 0
    } 
    
    public function createPost(Post $post): bool
    {
        // Générer un nouvel ID
        $newId = $this->getLastId() + 1;
        $post->setIdPost($newId);
    
        $stmt = $this->conn->prepare("
            INSERT INTO POST (idPost, contenu, typePost, datePublication, idAuteur, idRoom)
            VALUES (:idPost, :contenu, :typePost, :datePublication, :idAuteur, :idRoom)
        ");
    
        $stmt->bindValue(':idPost', $post->getIdPost(), PDO::PARAM_INT);
        $stmt->bindValue(':contenu', $post->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(':typePost', $post->getTypePost(), PDO::PARAM_STR);
        $stmt->bindValue(':datePublication', $post->getDatePublication(), PDO::PARAM_STR);
        $stmt->bindValue(':idAuteur', $post->getIdAuteur(), PDO::PARAM_INT);
        $stmt->bindValue(':idRoom', $post->getIdRoom(), PDO::PARAM_INT);
    
        return $stmt->execute();
    } 

    // Supprimer un post
    public function deletePost(int $idPost): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM POST WHERE idPost = :idPost");
        $stmt->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Récupérer un post par ID de post
    public function find(int $id): ?Post
    {
        $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idPost = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':contenu', $id, PDO::PARAM_INT);
        $stmt->bindValue(':idAuteur', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetch() ?: null;
    }

    // Récupérer les posts dans un tableau par ID d'auteur
    public function findPostsByAuteur(int $idAuteur): array
    {
    $stmt = $this->conn->prepare("SELECT * FROM POST WHERE idAuteur = :idAuteur ORDER BY datePublication DESC");
    $stmt->bindValue(':idAuteur', $idAuteur, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
    $stmt->execute();
    return $stmt->fetchAll() ?: [];
    }

    // Récupérer tous les posts
    public function findAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM POST");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll() ?: [];
    }
}
