<?php

class Utilisateur
{
    // --- ATTRIBUTS ---
    private ?int $idUtilisateur = null; // Nullable car n'existe pas encore à l'inscription
    private string $nom;
    private string $prenom;
    private string $dateNaissance; // String car vient du formulaire (Y-m-d)
    private string $genre;
    private string $pseudo;
    private string $email;
    private string $motDePasse;
    private string $typeCompte;
    private bool $estPremium;
    private string $dateInscription;
    private int $yuPoints;
    private ?string $personnalisation; // Peut être null

    // --- CONSTRUCTEUR ---
    public function __construct(
        ?int $idUtilisateur = null,
        string $nom,
        string $prenom,
        string $dateNaissance,
        string $genre,
        string $pseudo,
        string $email,
        string $motDePasse,
        string $typeCompte,
        bool $estPremium,
        string $dateInscription,
        int $yuPoints,
        ?string $personnalisation
    ) {
        // C'est ici que tu avais sûrement oublié des lignes !
        $this->setIdUtilisateur($idUtilisateur);
        $this->setNom($nom);                 // <--- C'était l'erreur (ligne manquante ?)
        $this->setPrenom($prenom);
        $this->setDateNaiss($dateNaissance);
        $this->setGenre($genre);
        $this->setPseudo($pseudo);
        $this->setEmail($email);
        $this->setMotDePasse($motDePasse);
        $this->setTypeCompte($typeCompte);
        $this->setEstPremium($estPremium);
        $this->setDateInscription($dateInscription);
        $this->setYuPoints($yuPoints);
        $this->setPersonnalisation($personnalisation);
    }

    // --- GETTERS ---

    public function getIdUtilisateur(): ?int
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
    public function getDateNaiss(): string
    {
        return $this->dateNaissance;
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
    public function getDateInscription(): string
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

    // --- SETTERS ---

    public function setIdUtilisateur(?int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
    public function setDateNaiss(string $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }
    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
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
    public function setDateInscription(string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }
    public function setYuPoints(int $yuPoints): void
    {
        $this->yuPoints = $yuPoints;
    }
    public function setPersonnalisation(?string $personnalisation): void
    {
        $this->personnalisation = $personnalisation;
    }
}
