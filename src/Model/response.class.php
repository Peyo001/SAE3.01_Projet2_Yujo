<?php

class Reponse
{
    private int $idReponse;
    private ?DateTime $dateReponse;
    private ?string $contenu;
    private ?int $idAuteur;
    private ?int $idPost;

    public function getId(): ?int {
        return $this->idReponse;
    }

    public function setId(?int $id): void {
        $this->idReponse = $id;
    }

    public function getDateReponse(): ?DateTime {
        return $this->dateReponse;
    }

    public function setDateReponse(?DateTime $dateReponse): void {
        $this->dateReponse = $dateReponse;
    }

    public function getContenu(): ?string {
        return $this->idReponse;
    }

    public function setContenu(?string $contenu): void{
        $this->contenu = $contenu;
    }

    public function getIdAuteur(): ?int {
        return $this->idAuteur;
    }

    public function setIdAuteur(?int $idAuteur): void {
        $this->idAuteur = $idAuteur;
    }

    public function getIdPost(): ?int {
        return $this->idPost;
    }

    public function setIdPost(?int $idPost): void {
        $this->idPost = $idPost;
    }

    public function __construct(?int $idReponse = null, ?DateTime $dateReponse = null, ?string $contenu = null, ?int $idAuteur = null, ?int $idPost = null)
    {
        $this->setId($idReponse);
        $this->setDateReponse($dateReponse);
        $this->setContenu($contenu);
        $this->setIdAuteur($idAuteur);
        $this->setIdPost($idPost);
    }

    public function afficher()
    {
        echo "ID de la reponse : " . $this->getId() . "<br/>";
        echo "Date de la reponse : " . $this->getDateReponse() . "<br/>";
        echo "Contenu de la reponse : " . $this->getContenu() . "<br/>";
        echo "ID de l'auteur de la reponse : " . $this->getIdAuteur() . "<br/>";
        echo "ID du post : " . $this->getIdPost() . "<br/>";
    }
}