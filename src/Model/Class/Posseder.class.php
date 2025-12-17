<?php
/**
 * Classe Posseder
 * 
 * Cette classe représente la relation de possession entre une room et un objet.
 * Elle permet de créer un objet Posseder et de l'utiliser avec les propriétés idObjet et idRoom.
 * 
 * Exemple d'utilisation :
 * $posseder = new Posseder(1, 42);
 * echo $posseder->getIdObjet(); // Affiche 1
 * 
 */
class Posseder {
    private int $idObjet;
    private int $idRoom;
    private ?string $dateAjout;

     /**
     * Constructeur de la classe Posseder.
     * 
     * @param int $idObjet Identifiant de l'objet.
     * @param int $idRoom Identifiant de le la room.
     * @param ?string $datePossession Date de possession (peut être nulle).
     */

    public function __construct(int $idObjet, int $idRoom, ?string $dateAjout = null) {
        $this->idObjet = $idObjet;
        $this->idRoom = $idRoom;
        $this->dateAjout = $dateAjout;
    }

    //ENCAPSULATION 
    /**
     * Récupère l'identifiant de l'objet possédé.
     * @return int Identifiant de l'objet possédé.
     * 
    */
    public function getIdObjet(): int {
        return $this->idObjet;
    }

    /**
     * Définit l'identifiant de l'objet possédé.
     * @param int $idObjet Identifiant de l'objet à définir.
     * 
    */
    public function setIdObjet(int $idObjet): void {
        $this->idObjet = $idObjet;
    }

    /**
     * Récupère l'identifiant de le la room possédant l'objet.
     * @return int Identifiant de la room possédant l'objet.
     * 
    */
    public function getIdRoom(): int {
        return $this->$idRoom;
    }

    /**
     * Définit l'identifiant de la room possédant l'objet.
     * @param int $idRoom Identifiant de la room à définir.
     * 
    */
    public function setIdRoom(int $idRoom): void {
        $this->idRoom = $idRoom;
    }

    /**
     * Récupère la date d'ajout de l'objet possédé.
     * @return ?string Date d'ajout de l'objet possédé, ou null si non définie.
     * 
    */    
    public function getDateAjout(): ?string {
        return $this->dateAjout;
    }

    /**
     * Définit la date d'ajout de l'objet possédé.
     * @param ?string $dateAjout Date d'ajout de l'objet à définir (peut être nulle).
     * 
    */
    public function setDateAjout(?string $dateAjout): void {
        $this->dateAjout = $dateAjout;
    }
}