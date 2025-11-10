<?php

class Achat {
    private int $idObjet;
    private ?string $dateAchat;
    private int $idUtilisateur;

    public function __construct(int $idObjet, ?string $dateAchat, int $idUtilisateur) {
        $this->idObjet = $idObjet;
        $this->dateAchat = $dateAchat;
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getIdObjet(): int {
        return $this->idObjet;
    }

    public function setIdObjet(int $idObjet): void {
        $this->idObjet = $idObjet;
    }

    public function getDateAchat(): ?string {
        return $this->dateAchat;
    }

    public function setDateAchat(?string $dateAchat): void {
        $this->dateAchat = $dateAchat;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}
?>