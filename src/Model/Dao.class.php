<?php

abstract class Dao {
    // ATTRIBUT
    // Propriété représentant la connexion à la base de données via PDO.
    protected PDO $conn;

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Dao.
     * 
     * Ce constructeur initialise la connexion à la base de données en utilisant la classe Database.
     */
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    //DESTRUCTEUR
    /**
     * Destructeur de la classe Dao.
     * 
     * Ce destructeur libère les ressources de la connexion à la base de données.
     */
    public function __destruct() {

    }

    // ENCAPSULATION
    /**
     * Récupère la connexion PDO utilisée pour interagir avec la base de données.
     * 
     * @return PDO|null Retourne la connexion PDO si elle est disponible, sinon null.
     */
    public function getConn(): ?PDO
    {
        return $this->conn;
    }


    /** 
     * Setter pour la propriété conn.
     * 
     * @param PDO $conn La nouvelle connexion PDO à définir.
     * 
     */
    public function setConn(PDO $conn): void {
        $this->conn = $conn;
    }
}