<?php
class User
{
    //ATTRIBUTS
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $typeCompte;
    private bool $isPremium;
    private ?string $dateInscription;
    private int $yupoints;

    //CONSTRUCTEUR

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $password,
        string $typeCompte,
        bool $isPremium,
        string $dateInscription,
        int $yupoints
    ) {
        $this->setId($id);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setTypeCompte($typeCompte);
        $this->setIsPremium($isPremium);
        $this->setDateInscription($dateInscription);
        $this->setYupoints($yupoints);
    }

    //DESTRUCTEUR
    
    public function __destruct()
    {
        
    }
    //ENCAPSULATION
    //GETTERS

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getTypeCompte(): string
    {
        return $this->typeCompte;
    }

    public function getIsPremium(): bool
    {
        return $this->isPremium;
    }

    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    public function getYupoints(): int
    {
        return $this->yupoints;
    }

    //SETTERS

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setTypeCompte(string $typeCompte): void
    {
        $this->typeCompte = $typeCompte;
    }

    public function setIsPremium(bool $isPremium): void
    {
        $this->isPremium = $isPremium;
    }

    public function setDateInscription(string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    public function setYupoints(int $yupoints): void
    {
        $this->yupoints = $yupoints;
    }
}
?>
