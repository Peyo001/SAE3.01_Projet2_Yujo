<?php
/**
 * Classe de gestion de la connexion à la base de données en utilisant le pattern Singleton.
 * 
 * Cette classe assure qu'une seule instance de connexion à la base de données est créée
 * et fournit un accès global à cette instance.
 * 
 * Exemple d'utilisation :
 * $db = Database::getInstance();
 * $conn = $db->getConnection();
 */
class Database
{

    //Attributs
    // Instance unique de la classe Database
    private static ?Database $instance = null;
    // Connexion PDO à la base de données
    private PDO $conn;                        

    //Constructeur
    /** Constructeur privé pour empêcher l'instanciation directe.
     * 
     * Initialise la connexion à la base de données en utilisant les paramètres
     * définis dans la configuration.
     * 
     * @throws Exception Si la connexion à la base de données échoue.
    */
    private function __construct()
    {
        try {
            $config = Config::getParametre('database');

            $this->conn = new PDO(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['name'],
                $config['user'],
                $config['password']
            );

            $this->conn->exec("SET NAMES utf8mb4");
        } catch (Exception $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }


    /** 
     * Obtient l'instance unique de la classe Database.
     * 
     * @return Database L'instance unique de la classe Database.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}