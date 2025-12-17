<?php
/**
 * Classe Achat
 * 
 * Cette classe représente un achat effectué par un utilisateur.
 * Elle permet de creer un objet Achat et de l'utiliser avec les propriétés idObjet, dateAchat et idUtilisateur.
 * 
 * Exemple d'utilisation :
 * $achat = new Achat(1, '2024-01-01', 42);
 * echo $achat->getIdObjet(); // Affiche 1
 * 
 */

class Achat {
    // Propriété représentant l'identifiant de l'objet acheté.
    private int $idObjet;

    // Propriété représentant la date de l'achat. Elle peut être nulle si non spécifiée.
    private ?string $dateAchat;

    // Propriété représentant l'identifiant de l'utilisateur ayant effectué l'achat.
    private int $idUtilisateur;

    /**
     * Constructeur de la classe Achat.
     * 
     * Ce constructeur initialise un objet Achat avec les valeurs spécifiées pour 
     * l'identifiant de l'objet, la date de l'achat et l'identifiant de l'utilisateur.
     * 
     * @param int $idObjet Identifiant de l'objet acheté.
     * @param ?string $dateAchat Date de l'achat (peut être nulle).
     * @param int $idUtilisateur Identifiant de l'utilisateur ayant effectué l'achat.
     */
    public function __construct(int $idObjet, int $idUtilisateur, ?string $dateAchat) {
        $this->idObjet = $idObjet;
        $this->idUtilisateur = $idUtilisateur;
        $this->dateAchat = $dateAchat;
    }

    /**
     * Récupère l'identifiant de l'objet acheté.
     * 
     * @return int Identifiant de l'objet acheté.
     */
    public function getIdObjet(): int {
        return $this->idObjet;
    }

    /**
     * Définit l'identifiant de l'objet acheté.
     * 
     * @param int $idObjet L'identifiant de l'objet à définir.
     * @return void
     */
    public function setIdObjet(int $idObjet): void {
        $this->idObjet = $idObjet;
    }

    /**
     * Récupère la date de l'achat.
     * 
     * @return ?string La date de l'achat, ou null si non spécifiée.
     */
    public function getDateAchat(): ?string {
        return $this->dateAchat;
    }

    /**
     * Définit la date de l'achat.
     * 
     * @param ?string $dateAchat La date de l'achat à définir. Peut être nulle.
     * @return void
     */
    public function setDateAchat(?string $dateAchat): void {
        $this->dateAchat = $dateAchat;
    }

    /**
     * Récupère l'identifiant de l'utilisateur ayant effectué l'achat.
     * 
     * @return int Identifiant de l'utilisateur.
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /**
     * Définit l'identifiant de l'utilisateur ayant effectué l'achat.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur à définir.
     * @return void
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}
?>