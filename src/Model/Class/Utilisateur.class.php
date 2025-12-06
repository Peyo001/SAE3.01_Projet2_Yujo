<?php
/**
 * Classe Utilisateur
 * 
 * Cette classe représente un utilisateur de l'application.
 * Elle permet de créer un objet Utilisateur et de l'utiliser avec les propriétés idUtilisateur, nom, prenom, dateNaissance, genre, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints et personnalisation.
 * 
 * Exemple d'utilisation :
 *
 */
class Utilisateur
{
    // ATTRIBUTS
    private ?int $idUtilisateur;// Identifiant unique de l'utilisateur
    private string $nom;// Nom de l'utilisateur
    private string $prenom;// Prénom de l'utilisateur
    private string $dateNaissance;// Date de naissance de l'utilisateur
    private string $genre;// Genre de l'utilisateur (Homme, Femme, Autre)
    private string $pseudo;// Pseudo de l'utilisateur
    private string $email;// Email de l'utilisateur
    private string $motDePasse;// Mot de passe de l'utilisateur
    private string $typeCompte;// Type de compte (Standard, Premium, Admin)
    private bool $estPremium;// Indique si l'utilisateur est premium
    private ?string $dateInscription;// Date d'inscription de l'utilisateur
    private int $yuPoints;// Nombre de YuPoints de l'utilisateur
    private ?string $personnalisation;// Personnalisation de l'utilisateur (JSON ou autre format)

    // CONSTRUCTEUR
    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Utilisateur.
     * 
     * Ce constructeur initialise un objet `Utilisateur` avec toutes ses propriétés définies.
     * 
     * @param string $nom Nom de l'utilisateur.
     * @param string $prenom Prénom de l'utilisateur.
     * @param string $dateNaissance Date de naissance de l'utilisateur.
     * @param string $genre Genre de l'utilisateur.
     * @param string $pseudo Pseudo de l'utilisateur.
     * @param string $email Email de l'utilisateur.
     * @param string $motDePasse Mot de passe de l'utilisateur.
     * @param string $typeCompte Type de compte (standard, premium).
     * @param bool $estPremium Statut premium (true ou false).
     * @param string $dateInscription Date d'inscription.
     * @param int $yuPoints Nombre de YuPoints de l'utilisateur.
     * @param ?int $idUtilisateur Identifiant de l'utilisateur (nullable).
     * @param ?string $personnalisation Personnalisation du profil de l'utilisateur.
     */
    public function __construct(
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
        ?int $idUtilisateur = null,
        ?string $personnalisation = null
    ) {
        $this->setIdUtilisateur($idUtilisateur);
        $this->setNom($nom);
        $this->setPrenom($prenom);
        $this->setDateNaissance($dateNaissance);
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

    // DESTRUCTEUR
    /**
     * Destructeur de la classe Utilisateur.
     * 
     * Ce destructeur est vide, mais il peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    //GETTERS
    /**
     * Récupère l'identifiant de l'utilisateur.
     * 
     * @return int L'identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    /**
     * Récupère le nom de l'utilisateur.
     * 
     * @return string Le nom de l'utilisateur.
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Récupère le prénom de l'utilisateur.
     * 
     * @return string Le prénom de l'utilisateur.
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * Récupère le nom complet de l'utilisateur (prénom + nom).
     * 
     * @return string Le nom complet de l'utilisateur.
     */
    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Récupère la date de naissance de l'utilisateur.
     * 
     * @return string La date de naissance de l'utilisateur.
     */
    public function getDateNaiss(): string
    {
        return $this->dateNaissance;
    }

    /**
     * Récupère l'âge de l'utilisateur.
     * 
     * @return int L'âge de l'utilisateur calculé à partir de sa date de naissance.
     */
    public function getAge(): int
    {
        $birthDate = new DateTime($this->dateNaissance);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        return $age;
    }   

    /**
     * Récupère le genre de l'utilisateur.
     * 
     * @return string Le genre de l'utilisateur.
     */
    public function getGenre(): string
    {
        return $this->genre;
    }

    /**
     * Récupère le pseudo de l'utilisateur.
     * 
     * @return string Le pseudo de l'utilisateur.
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Récupère l'email de l'utilisateur.
     * 
     * @return string L'email de l'utilisateur.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Récupère le mot de passe de l'utilisateur.
     * 
     * @return string Le mot de passe de l'utilisateur.
     */
    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    /**
     * Récupère le type de compte de l'utilisateur (standard ou premium).
     * 
     * @return string Le type de compte de l'utilisateur.
     */
    public function getTypeCompte(): string
    {
        return $this->typeCompte;
    }

    
    
    /**
     * Récupère le statut premium de l'utilisateur.
     * 
     * @return bool True si l'utilisateur est premium, false sinon.
     */
    public function getEstPremium(): bool
    {
        return $this->estPremium;
    }

    /**
     * Récupère la date d'inscription de l'utilisateur.
     * 
     * @return ?string La date d'inscription de l'utilisateur, ou null si non renseignée.
     */
    public function getDateInscription(): ?string
    {
        return $this->dateInscription;
    }

    /**
     * Récupère les YuPoints de l'utilisateur.
     * 
     * @return int Le nombre de YuPoints de l'utilisateur.
     */
    public function getYuPoints(): int
    {
        return $this->yuPoints;
    }

    /**
     * Récupère la personnalisation du profil de l'utilisateur.
     * 
     * @return ?string La personnalisation du profil de l'utilisateur, ou null si non renseignée.
     */
    public function getPersonnalisation(): ?string
    {
        return $this->personnalisation;
    }

    //SETTERS 
    /**
     * Définit l'identifiant de l'utilisateur.
     * 
     * @param ?int $idUtilisateur L'identifiant de l'utilisateur.
     */
    public function setIdUtilisateur(?int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Définit le pseudo de l'utilisateur.
     * 
     * @param string $pseudo Le pseudo de l'utilisateur.
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * Définit le nom de l'utilisateur.
     * 
     * @param string $nom Le nom de l'utilisateur.
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Définit le prénom de l'utilisateur.
     * 
     * @param string $prenom Le prénom de l'utilisateur.
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * Définit la date de naissance de l'utilisateur.
     * 
     * @param string $dateNaissance La date de naissance de l'utilisateur.
     */
    public function setDateNaissance(string $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    /**
     * Définit le genre de l'utilisateur.
     * 
     * @param string $genre Le genre de l'utilisateur.
     */
    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * Définit l'email de l'utilisateur.
     * 
     * @param string $email L'email de l'utilisateur.
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Définit le mot de passe de l'utilisateur.
     * 
     * @param string $motDePasse Le mot de passe de l'utilisateur.
     */
    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }
 
    /**
     * Définit le type de compte de l'utilisateur.
     * 
     * @param string $typeCompte Le type de compte de l'utilisateur.
     */
    public function setTypeCompte(string $typeCompte): void
    {
        $this->typeCompte = $typeCompte;
    }

    /**
     * Définit le statut premium de l'utilisateur.
     * 
     * @param bool $estPremium True si l'utilisateur est premium, false sinon.
     */
    public function setEstPremium(bool $estPremium): void
    {
        $this->estPremium = $estPremium;
    }

    /**
     * Définit la date d'inscription de l'utilisateur.
     * 
     * @param ?string $dateInscription La date d'inscription de l'utilisateur.
     */
    public function setDateInscription(?string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }
    
    /**
     * Définit les YuPoints de l'utilisateur.
     * 
     * @param int $yuPoints Le nombre de YuPoints de l'utilisateur.
     */
    public function setYuPoints(int $yuPoints): void
    {
        $this->yuPoints = $yuPoints;
    }

    /**
     * Retourne une représentation textuelle de l'objet Utilisateur.
     * 
     * @return string Une chaîne de caractères représentant l'utilisateur.
     */
    public function __toString(): string
    {
        return "Utilisateur #{$this->idUtilisateur}\n"
            . "Nom : {$this->nom}\n"
            . "Prénom : {$this->prenom}\n"
            . "Date de naissance : {$this->dateNaissance}\n"
            . "Genre : {$this->genre}\n"
            . "Pseudo : {$this->pseudo}\n"
            . "Email : {$this->email}\n"
            . "Type de compte : {$this->typeCompte}\n"
            . "Premium : " . ($this->estPremium ? "Oui" : "Non") . "\n"
            . "Date d’inscription : " . ($this->dateInscription ?? "Non renseignée") . "\n"
            . "YuPoints : {$this->yuPoints}\n";
    }

    /**
     * Définit la personnalisation du profil de l'utilisateur.
     * 
     * @param ?string $personnalisation La personnalisation du profil de l'utilisateur.
     */
    public function setPersonnalisation($personnalisation): void
    {
        $this->personnalisation = $personnalisation;
    }
}
