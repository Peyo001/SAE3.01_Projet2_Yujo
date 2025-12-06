<?php
/**
 * Message Model Class
 * 
 * Cette classe représente un message dans le système de messagerie.
 * Elle inclut des propriétés pour l'identifiant du message, le contenu,
 * 
 * Exemple d'utilisation :
 * $message = new Message($idMessage, $contenu, $dateEnvoi, $idGroupe, $idUtilisateur);
 */
class Message {
    // ATTRIBUTS
    // Identifiant unique du message
    private $idMessage;
    // Contenu du message
    private $contenu;
    // Date d'envoi du message
    private $dateEnvoi;
    // Identifiant du groupe associé au message
    private $idGroupe;
    // Identifiant de l'utilisateur ayant envoyé le message
    private $idUtilisateur;

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Message.
     * 
     * @param int $id Identifiant unique du message.
     * @param string $contenu Contenu du message.
     * @param string $dateEnvoi Date d'envoi du message.
     * @param int $idGroupe Identifiant du groupe associé au message.
     * @param int $idUtilisateur Identifiant de l'utilisateur ayant envoyé le message.
     */
    public function __construct($idMessage, $contenu, $dateEnvoi, $idGroupe, $idUtilisateur) {
        $this->idMessage = $idMessage;
        $this->contenu = $contenu;
        $this->dateEnvoi = $dateEnvoi;
        $this->idGroupe = $idGroupe;
        $this->idUtilisateur = $idUtilisateur;
    }

    // DESTRUCTEUR
    /**
     * Destructeur de la classe Message.
     */
    public function __destruct() {
        // Code de nettoyage si nécessaire
    }

    // ENCAPSULATION 
    //GETTERS
    
    /**
     * Obtient l'identifiant du message.
     * 
     * @return int Identifiant du message.
     */
    public function getIdMessage() {
        return $this->idMessage;
    }

    /**
     * Obtient le contenu du message.
     * 
     * @return string Contenu du message.
     */
    public function getContenu() {
        return $this->contenu;
    }

    /**
     * Obtient la date d'envoi du message.
     * 
     * @return string Date d'envoi du message.
     */
    public function getDateEnvoi() {
        return $this->dateEnvoi;
    }

    /**
     * Obtient l'identifiant du groupe associé au message.
     * 
     * @return int Identifiant du groupe associé au message.
     */
    public function getIdGroupe() {
        return $this->idGroupe;
    }

    /**
     * Obtient l'identifiant de l'utilisateur ayant envoyé le message.
     * 
     * @return int Identifiant de l'utilisateur ayant envoyé le message.
     */
    public function getIdUtilisateur() {
        return $this->idUtilisateur;
    }




}

