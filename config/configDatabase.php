<?php

    class Database {
        private string $host = "localhost";
        private string $dbName = "SAE_301";
        private string $username = "mxcr";
        private string $password = "spiderman";
        private ?PDO $conn = null;

        public function connect(): PDO {
            if ($this->conn === null) {
                try {
                    $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbName};charset=utf8", $this->username, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("Erreur de connexion à la base de données : " . $e->getMessage());
                }
            }
            return $this->conn;
        }
    }