<?php

class Signalement {
    // On met ?int pour dire que l'id peut être null (avant insertion)
    private ?int $id; 
    private ?string $raison;

    // Constructeur : l'ID est null par défaut s'il n'est pas fourni
    public function __construct(?int $id = null, ?string $raison = null) {
        $this->id = $id;
        $this->raison = $raison;
    }

    public function __destruct() { }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getRaison(): ?string {
        return $this->raison;
    }

    public function setRaison(?string $raison): void {
        $this->raison = $raison;
    }
}
?>