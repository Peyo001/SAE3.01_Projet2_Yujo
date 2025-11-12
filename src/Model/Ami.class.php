<?php 
class Ami {
    private int $idUtilisateur1;
    private int $idUtilisateur2;
    private ?string $dateAjout;

    public function __construct(int $idUtilisateur1, int $idUtilisateur2, ?string $dateAjout) {
        $this->idUtilisateur1 = $idUtilisateur1;
        $this->idUtilisateur2 = $idUtilisateur2;
        $this->dateAjout = $dateAjout;
    }

    public function getIdUtilisateur1(): int {
        return $this->idUtilisateur1;
    }

    public function setIdUtilisateur1(int $idUtilisateur1): void {
        $this->idUtilisateur1 = $idUtilisateur1;
    }

    public function getIdUtilisateur2(): int {
        return $this->idUtilisateur2;
    }

    public function setIdUtilisateur2(int $idUtilisateur2): void {
        $this->idUtilisateur2 = $idUtilisateur2;
    }

    public function getDateAjout(): int {
        return $this->dateAjout;
    }

    public function setDateAjout(int $dateAjout): void {
        $this->dateAjout = $dateAjout;
    }
}