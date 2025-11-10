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
        $pdoStatement->bindParam(':id', $id, PDO::PARAM_INT);
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

    public function createResponse(Reponse $response): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO REPONSE (idReponse, dateRepoonse, contenu, idAuteur, idPost) VALUES (:pseudo, :email, :motDePasse, :typeCompte, :estPremium, :dateInscription, :yuPoints)");
        $stmt->bindValue(':idReponse', $response->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':dateReponse', $response->getDateReponse(), PDO::PARAM_STR);
        $stmt->bindValue(':contenu', $response->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(':idAuteur', $response->getIdAuteur(), PDO::PARAM_INT);
        $stmt->bindValue(':idPost', $response->getIdPost(), PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function deleteResponse(int $id): bool{
        $stmt = $this->conn->prepare("DELETE FROM REPONSE WHERE idReponse = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


}