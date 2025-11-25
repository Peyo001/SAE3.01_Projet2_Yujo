<?php

class Signalement {
<<<<<<< HEAD
    private int $id;
    private ?string $raison;

    public function __construct(int $id, ?string $raison) {
=======
    // On met ?int pour dire que l'id peut être null (avant insertion)
    private ?int $id; 
    private ?string $raison;

    // Constructeur : l'ID est null par défaut s'il n'est pas fourni
    public function __construct(?int $id = null, ?string $raison = null) {
>>>>>>> Signalement
        $this->id = $id;
        $this->raison = $raison;
    }

<<<<<<< HEAD
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    // Getters and Setters
    public function getId(): int {
=======
    public function __destruct() { }

    // Getters and Setters
    public function getId(): ?int {
>>>>>>> Signalement
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
<<<<<<< HEAD
    }

    public function getRaison(): ?string {
        return $this->raison    ;
    }

    public function setRaison(?string $raison): void {


=======
    }

    public function getRaison(): ?string {
        return $this->raison;
    }

    public function setRaison(?string $raison): void {
>>>>>>> Signalement
        $this->raison = $raison;
    }
}
?>