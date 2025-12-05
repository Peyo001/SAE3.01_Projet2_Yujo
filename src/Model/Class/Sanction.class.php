<?php
/**
 * Classe Sanction
 * 
 * Cette classe représente un signalement fait par un utilisateur sur un post.
 * Elle permet de creer un objet Sanction et de l'utiliser avec les propriétés idSignalement, idUtilisateur, idPost, dateSignalement et status.
 * 
 * Exemple d'utilisation :
 * $sanction = new Sanction(1, 42, 100, '2024-01-01', 'en_attente');
 * echo $sanction->getStatus(); // Affiche 'en_attente'
 * 
 */
class Sanction {
    // Identifiant unique du signalement
    private int $idSignalement;

     // Identifiant de l'utilisateur ayant fait le signalement
    private int $idUtilisateur;

    // Identifiant du post signalé
    private int $idPost;

    // Date à laquelle le signalement a été effectué
    private ?string $dateSignalement;

    // Statut actuel du signalement (par exemple, "en_attente", "résolu", etc.)
    private ?string $status;

    /**
     * Constructeur de la classe Sanction.
     * 
     * Ce constructeur initialise un objet `Sanction` avec les valeurs spécifiées pour ses propriétés.
     * 
     * @param int $idSignalement Identifiant du signalement.
     * @param int $idUtilisateur Identifiant de l'utilisateur ayant effectué le signalement.
     * @param int $idPost Identifiant du post signalé.
     * @param ?string $dateSignalement Date du signalement (peut être null si non défini).
     * @param ?string $status Statut du signalement (peut être null si non défini).
     */
    public function __construct(int $idSignalement, int $idUtilisateur, int $idPost, ?string $dateSignalement, ?string $status) {
        $this->idSignalement = $idSignalement;
        $this->idUtilisateur = $idUtilisateur;
        $this->idPost = $idPost;
        $this->dateSignalement = $dateSignalement;
        $this->status = $status;
    }

    // GETTERS
    /**
     * Récupère l'identifiant du signalement.
     * 
     * @return int L'identifiant du signalement.
     */
    public function getIdSignalement(): int {
        return $this->idSignalement;
    }

    /**
     * Récupère l'identifiant de l'utilisateur ayant fait le signalement.
     * 
     * @return int L'identifiant de l'utilisateur.
     */
    public function setIdSignalement(int $idSignalement): void {
        $this->idSignalement = $idSignalement;
    }

    /**
     * Récupère l'identifiant du post signalé.
     * 
     * @return int L'identifiant du post.
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /**
     * Récupère la date du signalement.
     * 
     * @return ?string La date du signalement, ou null si non définie.
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Récupère le statut du signalement.
     * 
     * @return ?string Le statut du signalement, ou null si non défini.
     */
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