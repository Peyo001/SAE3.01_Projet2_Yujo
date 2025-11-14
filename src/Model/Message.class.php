<?php
class Message {
    // ATTRIBUTS
    private int $idMessage;
    private string $contenuMessage;
    private ?string $dateEnvoi;
    private int $idUtilisateur;
    private int $idGroupe;

    //CONSTRUCTEUR
    public function __construct(int $idMessage, string $contenuMessage, ?string $dateEnvoi, int $idUtilisateur) {
        $this->idMessage = $idMessage;
        $this->contenuMessage = $contenuMessage;
        $this->dateEnvoi = $dateEnvoi;
        $this->idUtilisateur = $idUtilisateur;
        $this->idGroupe = $idGroupe;    
    }

    // DESTRUCTEUR
    public function __destruct() {
        // Rien à nettoyer ici
    }

    //ENCAPSULATION
    //GETTERS
    public function getIdMessage(): int {
        return $this->idMessage;
    }

    public function getContenuMessage(): string {
        return $this->contenuMessage;
    }

    public function getDateEnvoi(): ?string {
        return $this->dateEnvoi;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }
    
    public function getIdGroupe(): int
    {
        return $this->idGroupe;
    }

    //SETTERS
    public function setIdMessage(int $idMessage): void {
        $this->idMessage = $idMessage;
    }

    public function setContenuMessage(string $contenuMessage): void {
        $this->contenuMessage = $contenuMessage;
    }

    public function setDateEnvoi(?string $dateEnvoi): void {
        $this->dateEnvoi = $dateEnvoi;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function setIdGroupe(int $idGroupe): void
    {
        $this->idGroupe = $idGroupe;
    }

    //METHODES
    public function afficherMessage(): string {
        return "[" . $this->dateEnvoi . "] User " . $this->idUtilisateur . ": " . $this->contenuMessage;
    }

    public function __toString(): string {
        return $this->afficherMessage();
    }
    

    
   
}