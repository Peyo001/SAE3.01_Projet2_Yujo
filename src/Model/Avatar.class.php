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

    //Attributs
    private int $idAvatar;
    private string $nom;
    private string $genre;
    private string $dateCreation;
    private string $CouleurPeau;
    private string $CouleurCheveux;
    private string $vetements;
    private string $accessoires;
    private int $idUtilisateur;


    //Constructeur
    public function __construct(int $idAvatar, string $nom, string $genre, string $dateCreation, string $CouleurPeau, string $CouleurCheveux, string $vetements, string $accessoires, int $idUtilisateur) {
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

    //destructeur
    public function __destruct() {
    }

    //encapsulation
    //getters
    public function getIdAvatar(): int {
        return $this->idAvatar;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getGenre(): string {
        return $this->genre;
    }

    public function getDateCreation(): string {
        return $this->dateCreation;
    }

    public function getCouleurPeau(): string {
        return $this->CouleurPeau;
    }

    public function getCouleurCheveux(): string {
        return $this->CouleurCheveux;
    }

    public function getVetements(): string {
        return $this->vetements;
    }

    public function getAccessoires(): string {
        return $this->accessoires;
    }   

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }   

    //setters
    public function setIdAvatar(int $idAvatar): void {
        $this->idAvatar = $idAvatar;
    }
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setGenre(string $genre): void {
        $this->genre = $genre;
    }

    public function setCouleurPeau(string $CouleurPeau): void {
        $this->CouleurPeau = $CouleurPeau;
    }

    public function setCouleurCheveux(string $CouleurCheveux): void {
        $this->CouleurCheveux = $CouleurCheveux;
    }

    public function setVetements(string $vetements): void {
        $this->vetements = $vetements;
    }

    public function setAccessoires(string $accessoires): void {
        $this->accessoires = $accessoires;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    //Méthodes
    public function __toString(): string {
        return "Avatar [idAvatar=" . $this->idAvatar . ", nom=" . $this->nom . ", genre=" . $this->genre . ", dateCreation=" . $this->dateCreation . ", CouleurPeau=" . $this->CouleurPeau . ", CouleurCheveux=" . $this->CouleurCheveux . ", vetements=" . $this->vetements . ", accessoires=" . $this->accessoires . ", idUtilisateur=" . $this->idUtilisateur . "]";
    }
}
