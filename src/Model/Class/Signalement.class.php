<?php
/**
 * Classe Signalement
 * 
 * Cette classe représente un signalement effectué par un utilisateur.
 * Elle permet de créer un objet Signalement et de l'utiliser avec les propriétés id et raison.
 * 
 * Exemple d'utilisation :
 * $signalement = new Signalement(1, 'Contenu inapproprié');
 * echo $signalement->getRaison(); // Affiche 'Contenu inapproprié'
 */
class Signalement {
    // ATTRIBUTS
    private ?int $id; // Identifiant unique du signalement
    private ?string $raison; // Raison du signalement

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Signalement.
     * 
     * Ce constructeur initialise un objet Signalement avec les propriétés spécifiées.
     * 
     * @param ?int $id Identifiant unique du signalement (peut être nul si non défini).
     * @param ?string $raison Raison du signalement (peut être nulle si non définie).
     */
    public function __construct(?int $id = null, ?string $raison = null) {
        $this->id = $id;
        $this->raison = $raison;
    }

    // DESTRUCTEUR
    /**
     * Destructeur de la classe Signalement.
     * 
     * Ce destructeur est vide mais peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct() { }

    // Getters and Setters
    /**
     * Récupère l'identifiant du signalement.
     * 
     * @return ?int L'identifiant du signalement, ou null si non défini.
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Définit l'identifiant du signalement.
     * 
     * @param ?int $id L'identifiant du signalement (peut être nul).
     */
    public function setId(?int $id): void {
        $this->id = $id;
    }

    /**
     * Récupère la raison du signalement.
     * 
     * @return ?string La raison du signalement, ou null si non définie.
     */
    public function getRaison(): ?string {
        return $this->raison;
    }

    /**
     * Définit la raison du signalement.
     * 
     * @param ?string $raison La raison du signalement (peut être nulle).
     */
    public function setRaison(?string $raison): void {
        $this->raison = $raison;
    }
}
?>