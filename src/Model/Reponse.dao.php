<?php

class ReponseDao
{
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }


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
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Post::class);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

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

    public function insert(Reponse $response): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO REPONSE (idReponse, dateRepoonse, contenu, idAuteur, idPost) VALUES (:idReponse, :dateReponse, :contenu, :idAuteur, :idPost)");
        $stmt->bindValue(':idReponse', $response->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':dateReponse', $response->getDateReponse(), PDO::PARAM_STR);
        $stmt->bindValue(':contenu', $response->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(':idAuteur', $response->getIdAuteur(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $response->getIdPost(), PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM REPONSE WHERE idReponse = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


}