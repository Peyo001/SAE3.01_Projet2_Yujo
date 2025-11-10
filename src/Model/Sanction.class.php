<?php

class Sanction {
    private int $idSignalement;
    private int $idUtilisateur;
    private int $idPost;
    private ?string $dateSignalement;
    private ?string $status;

    public function __construct(int $idSignalement, int $idUtilisateur, int $idPost, ?string $dateSignalement, ?string $status) {
        $this->idSignalement = $idSignalement;
        $this->idUtilisateur = $idUtilisateur;
        $this->idPost = $idPost;
        $this->dateSignalement = $dateSignalement;
        $this->status = $status;
    }

    public function getIdSignalement(): int {
        return $this->idSignalement;
    }

    public function setIdSignalement(int $idSignalement): void {
        $this->idSignalement = $idSignalement;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getIdPost(): int {
        return $this->idPost;
    }

    public function setIdPost(int $idPost): void {
        $this->idPost = $idPost;
    }

    public function getDateSignalement(): ?string {
        return $this->dateSignalement;
    }

    public function setDateSignalement(?string $dateSignalement): void {
        $this->dateSignalement = $dateSignalement;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): void {
        $this->status = $status;
    }
}