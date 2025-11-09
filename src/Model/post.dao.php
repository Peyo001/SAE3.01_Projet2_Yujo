<?php 

class PostDao {

    private PDO $pdo;
    public function __construct(?PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): ?Post
    {
        $sql = "SELECT * FROM POST WHERE idPost = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(":id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        $post = $pdoStatement->fetch();
        return $post;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM POST";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        $pdoStatement->execute();
        $post = $pdoStatement->fetchAll();
        return $post;
    }


}

?>