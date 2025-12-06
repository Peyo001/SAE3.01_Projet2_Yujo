<?php
/**
 * Classe Avatar
 * 
 * Cette classe représente un avatar créé par un utilisateur.
 * Elle permet de créer un objet Avatar et de l'utiliser avec les propriétés idAvatar, nom, genre, dateCreation, CouleurPeau, CouleurCheveux, vetements, accessoires et idUtilisateur.
 * 
 * Exemple d'utilisation :
 * $avatar = new Avatar(1, 'MonAvatar', 'Masculin', '2024-01-01', 'Clair', 'Brun', 'T-shirt', 'Lunettes', 42);
 * echo $avatar->getNom(); // Affiche le nom de MonAvatar
 */
class Avatar {

    //// Attributs de la classe Avatar, représentant les caractéristiques de l'avatar
    private ?int $idAvatar;
    private string $nom;
    private string $genre;
    private string $dateCreation;
    private string $CouleurPeau;
    private string $CouleurCheveux;
    private string $vetements;
    private string $accessoires;
    private int $idUtilisateur;


    //Constructeur
    /**
     * Constructeur de la classe Avatar.
     * 
     * Ce constructeur initialise un objet Avatar avec les propriétés spécifiées.
     * 
     * @param string $nom Nom de l'avatar.
     * @param string $genre Genre de l'avatar (ex. "Masculin", "Féminin").
     * @param string $dateCreation Date de création de l'avatar.
     * @param string $CouleurPeau Couleur de peau de l'avatar.
     * @param string $CouleurCheveux Couleur des cheveux de l'avatar.
     * @param string $vetements Description des vêtements de l'avatar.
     * @param string $accessoires Description des accessoires de l'avatar.
     * @param int $idUtilisateur Identifiant de l'utilisateur ayant créé l'avatar.
     * @param ?int $idAvatar Identifiant unique de l'avatar (nullable).
     */
    public function __construct(string $nom, string $genre, string $dateCreation, string $CouleurPeau, string $CouleurCheveux, string $vetements, string $accessoires, int $idUtilisateur, ?int $idAvatar = null) {
        $this->idAvatar = $idAvatar;
        $this->nom = $nom;
        $this->genre = $genre;
        $this->dateCreation = $dateCreation;
        $this->CouleurPeau = $CouleurPeau;
        $this->CouleurCheveux = $CouleurCheveux;
        $this->vetements = $vetements;
        $this->accessoires = $accessoires;
        $this->idUtilisateur = $idUtilisateur;
    }   

    /**
     * Destructeur de la classe Avatar.
     * 
     * Ce destructeur est vide mais peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct() {
    }

    //encapsulation
    //getters

    /**
     * Récupère l'identifiant de l'avatar.
     * 
     * @return int Identifiant de l'avatar.
     */
    public function getIdAvatar(): int {
        return $this->idAvatar;
    }

    /**
     * Récupère le nom de l'avatar.
     * 
     * @return string Nom de l'avatar.
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Récupère le genre de l'avatar.
     * 
     * @return string Genre de l'avatar.
     */
    public function getGenre(): string {
        return $this->genre;
    }

    /**
     * Récupère la date de création de l'avatar.
     * 
     * @return string Date de création de l'avatar.
     */
    public function getDateCreation(): string {
        return $this->dateCreation;
    }

    /**
     * Récupère la couleur de peau de l'avatar.
     * 
     * @return string Couleur de peau de l'avatar.
     */
    public function getCouleurPeau(): string {
        return $this->CouleurPeau;
    }

    /**
     * Récupère la couleur des cheveux de l'avatar.
     * 
     * @return string Couleur des cheveux de l'avatar.
     */
    public function getCouleurCheveux(): string {
        return $this->CouleurCheveux;
    }

    /**
     * Récupère la description des vêtements de l'avatar.
     * 
     * @return string Vêtements de l'avatar.
     */
    public function getVetements(): string {
        return $this->vetements;
    }

    /**
     * Récupère la description des accessoires de l'avatar.
     * 
     * @return string Accessoires de l'avatar.
     */
    public function getAccessoires(): string {
        return $this->accessoires;
    }   

    /**
     * Récupère l'identifiant de l'utilisateur ayant créé l'avatar.
     * 
     * @return int Identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }   

    //setters

    /**
     * Définit l'identifiant de l'avatar.
     * 
     * @param ?int $idAvatar L'identifiant de l'avatar à définir.
     */
    public function setIdAvatar(?int $idAvatar): void {
        $this->idAvatar = $idAvatar;
    }

    /**
     * Définit le nom de l'avatar.
     * 
     * @param string $nom Le nom de l'avatar à définir.
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }


    /**
     * Définit le genre de l'avatar.
     * 
     * @param string $genre Le genre de l'avatar à définir.
     */
    public function setGenre(string $genre): void {
        $this->genre = $genre;
    }

    /**
     * Définit la couleur de peau de l'avatar.
     * 
     * @param string $CouleurPeau La couleur de peau de l'avatar à définir.
     */
    public function setCouleurPeau(string $CouleurPeau): void {
        $this->CouleurPeau = $CouleurPeau;
    }

    /**
     * Définit la couleur des cheveux de l'avatar.
     * 
     * @param string $CouleurCheveux La couleur des cheveux de l'avatar à définir.
     */
    public function setCouleurCheveux(string $CouleurCheveux): void {
        $this->CouleurCheveux = $CouleurCheveux;
    }

    /**
     * Définit les vêtements de l'avatar.
     * 
     * @param string $vetements Les vêtements de l'avatar à définir.
     */
    public function setVetements(string $vetements): void {
        $this->vetements = $vetements;
    }

    /**
     * Définit les accessoires de l'avatar.
     * 
     * @param string $accessoires Les accessoires de l'avatar à définir.
     */
    public function setAccessoires(string $accessoires): void {
        $this->accessoires = $accessoires;
    }

    /**
     * Définit l'identifiant de l'utilisateur ayant créé l'avatar.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur à définir.
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }


    //Méthodes
    /**
     * Représente l'objet Avatar sous forme de chaîne.
     * 
     * Cette méthode retourne une représentation textuelle complète de l'objet Avatar, incluant toutes ses propriétés.
     * 
     * @return string Représentation textuelle de l'objet Avatar.
     */
    public function __toString(): string {
        return "Avatar [idAvatar=" . $this->idAvatar . ", nom=" . $this->nom . ", genre=" . $this->genre . ", dateCreation=" . $this->dateCreation . ", CouleurPeau=" . $this->CouleurPeau . ", CouleurCheveux=" . $this->CouleurCheveux . ", vetements=" . $this->vetements . ", accessoires=" . $this->accessoires . ", idUtilisateur=" . $this->idUtilisateur . "]";
    }
}
