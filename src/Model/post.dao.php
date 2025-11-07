<?php 

class PostDao {

    private PDO $pdo;
    private array $parametresBd;

    public function __construct(?PDO $pdo)
    {
        global $config; // chargement de la variable globale $config
        $this->parametresBd = $config['database'];
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

    public function getParametresBd(): array
    {
        return $this->parametresBd;
    }

    public function setParametresBd(array $parametresBd): void
    {
        $this->parametresBd = $parametresBd;
    }

}

?>