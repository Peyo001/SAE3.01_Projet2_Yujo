<?php

class Database
{
    private static ?Database $instance = null; // Singleton
    private PDO $conn;                          // Connexion PDO

    
    // Constructeur privé
    private function __construct()
    {
        try {
            // Connexion PDO simple
            $this->conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASSWORD
            );

            // Définir le charset UTF-8
            $this->conn->exec("SET NAMES utf8mb4");
        } catch (Exception $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
        echo "Connexion à la base de données réussie.";
    }


    //destructeur
    public function __destruct()
    {
        
    }   

    // Récupérer l'instance unique
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Récupérer la connexion PDO
    public function getConnection(): PDO
    {
        return $this->conn;
    }

    // Empêcher clonage et désérialisation
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Un singleton ne doit pas être désérialisé");
    }
}
?>
