<?php
class Post
{
    private ?int $idPost = null;
    private ?string $contenu = null;
    private ?string $typePost = null;
    private ?string $datePublication = null;
    private ?int $idAuteur = null;
    private ?int $idRoom = null;

    public function __construct() {} // constructeur vide pour PDO::FETCH_CLASS

    // Getters
    public function getIdPost(): ?int { return $this->idPost; }
    public function getContenu(): ?string { return $this->contenu; }
    public function getTypePost(): ?string { return $this->typePost; }
    public function getDatePublication(): ?string { return $this->datePublication; }
    public function getIdAuteur(): ?int { return $this->idAuteur; }
    public function getIdRoom(): ?int { return $this->idRoom; }

    // Setters
    public function setIdPost(int $idPost): void { $this->idPost = $idPost; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setTypePost(string $typePost): void { $this->typePost = $typePost; }
    public function setDatePublication(string $datePublication): void { $this->datePublication = $datePublication; }
    public function setIdAuteur(int $idAuteur): void { $this->idAuteur = $idAuteur; }
    public function setIdRoom(int $idRoom): void { $this->idRoom = $idRoom; }
}
