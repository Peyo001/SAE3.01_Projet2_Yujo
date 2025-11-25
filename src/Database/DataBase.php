<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $conn;                        

    private function __construct()                                                                                                                                                                                     
    {
        try {
            $config = Config::getParameter('database');

            $this->conn = new PDO(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['name'],
                $config['user'],
                $config['password']
            );

            $this->conn->exec("SET NAMES utf8mb4");
        }
        catch (Exception $e) {
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