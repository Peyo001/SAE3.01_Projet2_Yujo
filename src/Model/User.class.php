<?php
class Utilisateur
{
    // ATTRIBUTS
    private int $idUtilisateur;
    private string $pseudo;
    private string $email;
    private string $motDePasse;
    private string $typeCompte;
    private bool $estPremium;
    private ?string $dateInscription;
    private int $yuPoints;

    // CONSTRUCTEUR
    public function __construct(
        int $idUtilisateur,
        string $pseudo,
        string $email,
        string $motDePasse,
        string $typeCompte,
        bool $estPremium,
        ?string $dateInscription,
        int $yuPoints
    ) {
        $this->setIdUtilisateur($idUtilisateur);
        $this->setPseudo($pseudo);
        $this->setEmail($email);
        $this->setMotDePasse($motDePasse);
        $this->setTypeCompte($typeCompte);
        $this->setEstPremium($estPremium);
        $this->setDateInscription($dateInscription);
        $this->setYuPoints($yuPoints);
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

    //SETTERS 
    public function setIdUtilisateur(int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
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
            . "Pseudo : {$this->pseudo}\n"
            . "Email : {$this->email}\n"
            . "Type de compte : {$this->typeCompte}\n"
            . "Premium : " . ($this->estPremium ? "Oui" : "Non") . "\n"
            . "Date d’inscription : " . ($this->dateInscription ?? "Non renseignée") . "\n"
            . "YuPoints : {$this->yuPoints}\n";
    }
}
?>
