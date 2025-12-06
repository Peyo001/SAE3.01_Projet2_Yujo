<?php
/**
 * Classe représentant la relation Composer entre un utilisateur et un groupe.
 * 
 * Chaque instance de cette classe correspond à une entrée dans la table COMPOSER,
 * qui indique qu'un utilisateur est membre d'un groupe depuis une certaine date.
 * 
 * Exemple d'utilisation :
 * $composer = new Composer(1, 42, '2024-01-15 10:30:00');
 * echo $composer->getIdUtilisateur(); // Affiche 42
 */

class  Composer {
    //Attributs
    // Identifiant de l'utilisateur
    private ?int $idUtilisateur;
    // Identifiant du groupe
    private ?int $idGroupe;
    // Date d'ajout de l'utilisateur au groupe
    private ?string $dateAjout;

    //Constructeur
    /**
     * Constructeur de la classe Composer.
     * 
     * @param ?int $idGroupe Identifiant du groupe.
     * @param ?int $idUtilisateur Identifiant de l'utilisateur.
     * @param ?string $dateAjout Date d'ajout de l'utilisateur au groupe.
     */
    public function __construct(?int $idGroupe, ?int $idUtilisateur, ?string $dateAjout) {
        $this->idGroupe = $idGroupe;
        $this->idUtilisateur = $idUtilisateur;
        $this->dateAjout = $dateAjout;
    }

    //ENCAPSULATION
   //GETTERS

    /**
     * Obtient l'identifiant de l'utilisateur.
     * 
     * @return ?int Identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): ?int {
        return $this->idUtilisateur;
    }

    /**
     * Obtient l'identifiant du groupe.
     * 
     * @return ?int Identifiant du groupe.
     */
    public function getIdGroupe(): ?int {
        return $this->idGroupe;
    }

    /**
     * Obtient la date d'ajout de l'utilisateur au groupe.
     * 
     * @return ?string Date d'ajout de l'utilisateur au groupe.
     */
    public function getDateAjout(): ?string {
        return $this->dateAjout;
    }


    //SETTERS   
    /**
     * Définit l'identifiant de l'utilisateur.
     * 
     * @param ?int $utilisateur Identifiant de l'utilisateur.
     */
    public function setIdUtilisateur(?int $utilisateur) : void{
        $this->idUtilisateur = $utilisateur;
    }
    
    /**
     * Définit l'identifiant du groupe.
     * 
     * @param ?int $groupe Identifiant du groupe.
     */
    public function setIdGroupe(?int $groupe) : void{
        $this->idGroupe = $groupe;
    }

    /**
     * Définit la date d'ajout de l'utilisateur au groupe.
     * 
     * @param ?string $dateAjout Date d'ajout de l'utilisateur au groupe.
     */
    public function setDateAjout(?string $dateAjout) : void{
        $this->dateAjout = $dateAjout;
    }

}