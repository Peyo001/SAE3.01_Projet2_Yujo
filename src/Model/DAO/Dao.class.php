<?php
/**
 * Classe de base pour les objets d'accès aux données (DAO).
 * 
 * Cette classe fournit une connexion PDO à la base de données et des méthodes
 * d'encapsulation pour gérer cette connexion.
 * 
 * Exemple d'utilisation :
 * $dao = new Dao($pdo);
 * $conn = $dao->getConn();
 *
 */

class Dao {
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


    public function hydrateAll(array $tableau): array {
        $result = [];
        foreach ($tableau as $tableauAssoc) {
            $result[] = $this->hydrate($tableauAssoc);
        }
        return $result;
    }
}