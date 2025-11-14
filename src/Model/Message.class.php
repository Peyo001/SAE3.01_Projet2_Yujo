<?php
class Message {
    private int $idMessage;
    private string $contenuMessage;
    private ?string $dateEnvoi;
    private int $idUtilisateur;
    private int $idGroupe;

    public function __construct(int $idMessage, string $contenuMessage, ?string $dateEnvoi, int $idUtilisateur) {
        $this->idMessage = $idMessage;
        $this->contenuMessage = $contenuMessage;
        $this->dateEnvoi = $dateEnvoi;
        $this->idUtilisateur = $idUtilisateur;
    }

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
    


    public function setIdMessage(int $idMessage): void {
        $this->idMessage = $idMessage;
    }

    
   
}