<?php
/**
 * Classe Newsletter
 * 
 * Cette classe représente une inscription à la newsletter.
 * 
 * Exemple d'utilisation :
 * $newsletter = new Newsletter('user@example.com', '2024-01-01', true);
 */
class Newsletter
{
    // ATTRIBUTS
    private ?int $idNewsletter;
    private string $email;
    private string $dateInscription;
    private bool $estActif;

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Newsletter.
     * 
     * @param string $email Email de l'inscrit.
     * @param string $dateInscription Date d'inscription.
     * @param bool $estActif Statut de l'inscription (actif/inactif).
     * @param ?int $idNewsletter Identifiant de l'inscription (nullable).
     */
    public function __construct(
        string $email,
        string $dateInscription,
        bool $estActif = true,
        ?int $idNewsletter = null
    ) {
        $this->email = $email;
        $this->dateInscription = $dateInscription;
        $this->estActif = $estActif;
        $this->idNewsletter = $idNewsletter;
    }

    // ENCAPSULATION - Getters
    public function getIdNewsletter(): ?int
    {
        return $this->idNewsletter;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    public function getEstActif(): bool
    {
        return $this->estActif;
    }

    // ENCAPSULATION - Setters
    public function setIdNewsletter(?int $idNewsletter): void
    {
        $this->idNewsletter = $idNewsletter;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setDateInscription(string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    public function setEstActif(bool $estActif): void
    {
        $this->estActif = $estActif;
    }
}
