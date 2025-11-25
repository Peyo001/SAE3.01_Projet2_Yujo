<?php
/**
 * Classe Utilisateur
 * 
 * Cette classe représente un utilisateur de l'application.
 * Elle permet de créer un objet Utilisateur et de l'utiliser avec les propriétés idUtilisateur, nom, prenom, dateNaiss, genre, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints et personnalisation.
 * 
 * Exemple d'utilisation :
 * Utilisateur utilisateur1 = 
 */
class Utilisateur
{
    // ATTRIBUTS
    private int $idUtilisateur;
    private string $nom;
    private string $prenom;
    private string $dateNaiss;
    private string $genre;
    private string $pseudo;
    private string $email;
    private string $motDePasse;
    private string $typeCompte;
    private bool $estPremium;
    private ?string $dateInscription;
    private int $yuPoints;
    private ?string $personnalisation;

    // CONSTRUCTEUR
    public function __construct(
        int $idUtilisateur,
        string $nom,
        string $prenom,
        string $dateNaiss,
        string $genre,
        string $pseudo,
        string $email,
        string $motDePasse,
        string $typeCompte,
        bool $estPremium,
        ?string $dateInscription,
        int $yuPoints,
        string $personnalisation
    ) {
        $this->setIdUtilisateur($idUtilisateur);
        $this->setPseudo($pseudo);
        $this->setEmail($email);
        $this->setMotDePasse($motDePasse);
        $this->setTypeCompte($typeCompte);
        $this->setEstPremium($estPremium);
        $this->setDateInscription($dateInscription);
        $this->setYuPoints($yuPoints);
        $this->setPersonnalisation($personnalisation);
    }

    // DESTRUCTEUR
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    //GETTERS
    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getDateNaiss(): string
    {
        return $this->dateNaiss;
    }

    public function getAge(): int
    {
        $birthDate = new DateTime($this->dateNaiss);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getTypeCompte(): string
    {
        return $this->typeCompte;
    }

    public function getEstPremium(): bool
    {
        return $this->estPremium;
    }

    public function getDateInscription(): ?string
    {
        return $this->dateInscription;
    }

    public function getYuPoints(): int
    {
        return $this->yuPoints;
    }

    public function getPersonnalisation(): ?string
    {
        return $this->personnalisation;
    }

    //SETTERS 
    public function setIdUtilisateur(int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setDateNaiss(string $dateNaiss): void
    {
        $this->dateNaiss = $dateNaiss;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }

    public function setTypeCompte(string $typeCompte): void
    {
        $this->typeCompte = $typeCompte;
    }

    public function setEstPremium(bool $estPremium): void
    {
        $this->estPremium = $estPremium;
    }

    public function setDateInscription(?string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    public function setYuPoints(int $yuPoints): void
    {
        $this->yuPoints = $yuPoints;
    }

    
    public function __toString(): string
    {
        return "Utilisateur #{$this->idUtilisateur}\n"
            . "Nom : {$this->nom}\n"
            . "Prénom : {$this->prenom}\n"
            . "Date de naissance : {$this->dateNaiss}\n"
            . "Genre : {$this->genre}\n"
            . "Pseudo : {$this->pseudo}\n"
            . "Email : {$this->email}\n"
            . "Type de compte : {$this->typeCompte}\n"
            . "Premium : " . ($this->estPremium ? "Oui" : "Non") . "\n"
            . "Date d’inscription : " . ($this->dateInscription ?? "Non renseignée") . "\n"
            . "YuPoints : {$this->yuPoints}\n";
    }

    public function setPersonnalisation($personnalisation): void
    {
        $this->personnalisation = $personnalisation;
    }
}
?>
