<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $conn;                        

    private function __construct()
    {
        try {
<<<<<<< HEAD
            $config = Config::getParameter('database');
=======
            $config = json_decode(file_get_contents(__DIR__ . '/../../config/configDatabase.json'), true)['database'];
>>>>>>> c44f315ee8a16e01a37b5011c3a47ce7c02970a9

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