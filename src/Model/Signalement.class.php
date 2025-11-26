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
    // Identifiant unique du signalement
    private int $id;
    // Identifiant unique du signalement
    private ?string $raison;

    //CONSTRUCTEUR
    /**
     * Constructeur de la classe Signalement.
     * 
     * Ce constructeur initialise un objet `Signalement` avec un identifiant et une raison pour le signalement.
     * 
     * @param int $id Identifiant du signalement.
     * @param ?string $raison La raison pour laquelle le signalement a été fait.
     */
    public function __construct(int $id, ?string $raison) {
        $this->id = $id;
        $this->raison = $raison;
    }

    /**
     * Destructeur de la classe Signalement.
     * 
     * Ce destructeur est vide pour le moment, mais peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct()
    {
        // Rien à nettoyer ici
    }
    

    // Getters and Setters
    /**
     * Récupère l'identifiant du signalement.
     * 
     * @return int L'identifiant du signalement.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Définit l'identifiant du signalement.
     * 
     * @param int $id L'identifiant du signalement à définir.
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Récupère la raison du signalement.
     * 
     * @return ?string La raison du signalement, ou null si non définie.
     */
    public function getRaison(): ?string {
        return $this->raison    ;
    }

    /**
     * Définit la raison du signalement.
     * 
     * @param ?string $raison La raison du signalement à définir.
     */
    public function setRaison(?string $raison): void {


        $this->raison = $raison;
    }
}
?>