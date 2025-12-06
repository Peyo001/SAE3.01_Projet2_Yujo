<?php
/**
 * Classe Signaler
 * 
 * Cette classe représente un signalement effectué par un utilisateur sur un post.
 * Elle gère la relation entre un utilisateur, un signalement et un post.
 * 
 * Exemple d'utilisation :
 * $signaler = new Signaler(1, 5, 10, '2024-01-15', 'en_attente');
 * echo $signaler->getIdUtilisateur(); // Affiche 1
 */

class Signaler {
    // Attributs
    // Identifiant de l'utilisateur ayant signalé
    private ?int $idUtilisateur;
    // Identifiant du signalement
    private ?int $idSignalement;
    // Identifiant du post signalé
    private ?int $idPost;
    // Date du signalement
    private ?string $dateSignalement;
    // Statut du signalement
    private ?string $statut;

    /**
     * Constructeur de la classe Signaler.
     * 
     * @param ?int $idUtilisateur Identifiant de l'utilisateur ayant signalé.
     * @param ?int $idSignalement Identifiant du signalement.
     * @param ?int $idPost Identifiant du post signalé.
     * @param ?string $dateSignalement Date du signalement.
     * @param ?string $statut Statut du signalement.
     */
    public function __construct(?int $idUtilisateur, ?int $idSignalement, ?int $idPost, ?string $dateSignalement, ?string $statut) {
        $this->idUtilisateur = $idUtilisateur;
        $this->idSignalement = $idSignalement;
        $this->idPost = $idPost;
        $this->dateSignalement = $dateSignalement;
        $this->statut = $statut;
    }

    // GETTERS

    /**
     * Obtient l'identifiant de l'utilisateur.
     * 
     * @return ?int Identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): ?int {
        return $this->idUtilisateur;
    }

    /**
     * Obtient l'identifiant du signalement.
     * 
     * @return ?int Identifiant du signalement.
     */
    public function getIdSignalement(): ?int {
        return $this->idSignalement;
    }

    /**
     * Obtient l'identifiant du post.
     * 
     * @return ?int Identifiant du post.
     */
    public function getIdPost(): ?int {
        return $this->idPost;
    }

    /**
     * Obtient la date du signalement.
     * 
     * @return ?string Date du signalement.
     */
    public function getDateSignalement(): ?string {
        return $this->dateSignalement;
    }

    /**
     * Obtient le statut du signalement.
     * 
     * @return ?string Statut du signalement.
     */
    public function getStatut(): ?string {
        return $this->statut;
    }

    // SETTERS

    /**
     * Définit l'identifiant de l'utilisateur.
     * 
     * @param ?int $idUtilisateur Identifiant de l'utilisateur.
     */
    public function setIdUtilisateur(?int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Définit l'identifiant du signalement.
     * 
     * @param ?int $idSignalement Identifiant du signalement.
     */
    public function setIdSignalement(?int $idSignalement): void {
        $this->idSignalement = $idSignalement;
    }

    /**
     * Définit l'identifiant du post.
     * 
     * @param ?int $idPost Identifiant du post.
     */
    public function setIdPost(?int $idPost): void {
        $this->idPost = $idPost;
    }

    /**
     * Définit la date du signalement.
     * 
     * @param ?string $dateSignalement Date du signalement.
     */
    public function setDateSignalement(?string $dateSignalement): void {
        $this->dateSignalement = $dateSignalement;
    }

    /**
     * Définit le statut du signalement.
     * 
     * @param ?string $statut Statut du signalement.
     */
    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }
}
