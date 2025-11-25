<?php
/**
 * Classe Signalement
 * 
 * Cette classe représente un signalement fait par un utilisateur.
 * Elle permet de creer un objet Signalement et de l'utiliser avec les propriétés id et raison.
 * 
 * Exemple d'utilisation :
 * $signalement = new Signalement(1, 'Contenu inapproprié');
 * echo $signalement->getRaison(); // Affiche 'Contenu inapproprié'
 * 
 */
class Signalement {
    private int $id;
    private ?string $raison;

    public function __construct(int $id, ?string $raison) {
        $this->id = $id;
        $this->raison = $raison;
    }

    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    // Getters and Setters
    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getRaison(): ?string {
        return $this->raison    ;
    }

    public function setRaison(?string $raison): void {


        $this->raison = $raison;
    }
}
?>