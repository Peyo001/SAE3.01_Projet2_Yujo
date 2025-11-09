<?php

class ReponseDao
{
    private ?PDO $pdo;

    public function __construct(PDO $PDO){
        $this->pdo = $pdo;
    }

    public function getPdo() : ?PDO {
        return $this->pdo;
    }

    public function setPdo($pdo): void {
        $this->pdo = $pdo;
    }

    public function find(?int $id): ?Reponse{
        $sql = "SELECT * FROM ".PREFIXE_TABLE."reponse WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"->$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Reponse');
        $reponse = $pdoStatement->fetch();
        return $reponse;
    }

    public function findAll(){
        $sql = "SELECT * FROM ".PREFIXE_TABLE."reponse";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Reponse');
        $reponse = $pdoStatement->fetchAll();
        return $reponse;
    }

    public function findAssoc(?int $id): ?array{
        $sql = "SELECT * FROM ".PREFIXE_TABLE."reponse WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"->$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $reponse = $pdoStatement->fetch();
        return $reponse;
    }

    public function findAllAssoc(){
        $sql = "SELECT * FROM ".PREFIXE_TABLE."reponse";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $reponse = $pdoStatement->fetchAll();
        return $reponse;
    }
}