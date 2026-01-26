<?php
/**
 * Classe Invitation
 * 
 * Cette classe représente une invitation à rejoindre un groupe.
 * Elle gère les propriétés : idHote, idInvite, idGroupe, dateInvitation, statut
 * 
 * Exemple d'utilisation :
 * $invitation = new Invitation(1, 5, 3, '2024-01-15 10:30:00', 'en_attente');
 * echo $invitation->getStatut(); // Affiche "en_attente"
 */
class Invitation
{
    // ATTRIBUTS
    private int $idHote;      // L'utilisateur qui envoie l'invitation
    private int $idInvite;    // L'utilisateur qui reçoit l'invitation
    private int $idGroupe;
    private string $dateInvitation;
    private string $statut; // 'en_attente', 'accepte', 'refuse'

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Invitation.
     * 
     * @param int $idHote L'identifiant de l'utilisateur qui envoie l'invitation (hôte).
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @param int $idGroupe L'identifiant du groupe.
     * @param string $dateInvitation La date de l'invitation.
     * @param string $statut Le statut de l'invitation (par défaut 'en_attente').
     */
    public function __construct(
        int $idHote,
        int $idInvite,
        int $idGroupe,
        string $dateInvitation,
        string $statut = 'en_attente'
    ) {
        $this->idHote = $idHote;
        $this->idInvite = $idInvite;
        $this->idGroupe = $idGroupe;
        $this->dateInvitation = $dateInvitation;
        $this->statut = $statut;
    }

    // ENCAPSULATION - GETTERS
    /**
     * Récupère l'identifiant de l'utilisateur hôte (émetteur).
     */
    public function getIdHote(): int
    {
        return $this->idHote;
    }

    /**
     * Récupère l'identifiant de l'utilisateur invité.
     */
    public function getIdInvite(): int
    {
        return $this->idInvite;
    }

    /**
     * Récupère l'identifiant du groupe.
     */
    public function getIdGroupe(): int
    {
        return $this->idGroupe;
    }

    /**
     * Récupère la date de l'invitation.
     */
    public function getDateInvitation(): string
    {
        return $this->dateInvitation;
    }

    /**
     * Récupère le statut de l'invitation.
     */
    public function getStatut(): string
    {
        return $this->statut;
    }

    // ENCAPSULATION - SETTERS
    /**
     * Définit le statut de l'invitation.
     */
    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}
?>

