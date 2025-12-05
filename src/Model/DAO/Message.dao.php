<?php

/**
 * Classe MessageDAO
 * 
 * Cette classe gère les opérations de base de données pour les objets Message.
 * Elle permet de créer, lire, mettre à jour et supprimer des messages dans la base de
 * données.
 * 
 * Exemple d'utilisation :
 * $pdo = Database::getInstance()->getConnection();
 * $messageDAO = new MessageDAO($pdo);
 * $message = $messageDAO->findById(1);
 */
class MessageDAO {

    // ATTRIBUTS
    // Instance PDO pour la connexion à la base de données
    private $pdo;

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe MessageDAO.
     * 
     * Ce constructeur initialise un objet MessageDAO avec une connexion PDO.
     * 
     * @param PDO $pdo Instance PDO pour la connexion à la base de données.
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // MÉTHODES
    /**
     * Trouve un message par son identifiant.
     * 
     * @param int $id Identifiant du message.
     * @return Message|null L'objet Message correspondant ou null si non trouvé.
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM message WHERE idMessage = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            return new Message(
                $row['idMessage'],
                $row['contenu'],
                $row['dateEnvoi'],
                $row['idGroupe'],
                $row['idUtilisateur']
            );
        }

        return null;
    }


    /** Trouve tous les messages d'un groupe donné.
     * 
     * @param int $idGroupe Identifiant du groupe.
     * @return array Tableau d'objets Message.
     */
    public function findByIdGroupe($idGroupe) {
        $stmt = $this->pdo->prepare("SELECT * FROM message WHERE idGroupe = :idGroupe");
        $stmt->execute(['idGroupe' => $idGroupe]);
        $messages = [];

        while ($row = $stmt->fetch()) {
            $messages[] = new Message(
                $row['idMessage'],
                $row['contenu'],
                $row['dateEnvoi'],
                $row['idGroupe'],
                $row['idUtilisateur']
            );
        }

        return $messages;
    }


    /** Trouve tous les messages dans la base de données.
     * 
     * @return array Tableau d'objets Message.
     */
    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM message");
        $messages = [];

        while ($row = $stmt->fetch()) {
            $messages[] = new Message(
                $row['idMessage'],
                $row['contenu'],
                $row['dateEnvoi'],
                $row['idGroupe'],
                $row['idUtilisateur']
            );
        }

        return $messages;
    }


    /** Sauvegarde un message dans la base de données.
     * 
     * @param Message $message L'objet Message à sauvegarder.
     */
    public function sauvegarderMessage(Message $message) {
        if ($message->getIdMessage() === null) {
            $stmt = $this->pdo->prepare("INSERT INTO message (contenu, dateEnvoi, idGroupe, idUtilisateur) VALUES (:contenu, :dateEnvoi, :idGroupe, :idUtilisateur)");
            $stmt->execute([
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi(),
                'idGroupe' => $message->getIdGroupe(),
                'idUtilisateur' => $message->getIdUtilisateur()
            ]);
            $message->setIdMessage($this->pdo->lastInsertId());
        } else {
            $stmt = $this->pdo->prepare("UPDATE message SET contenu = :contenu, dateEnvoi = :dateEnvoi, idGroupe = :idGroupe, idUtilisateur = :idUtilisateur WHERE idMessage = :idMessage");
            $stmt->execute([
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi(),
                'idGroupe' => $message->getIdGroupe(),
                'idUtilisateur' => $message->getIdUtilisateur(),
                'idMessage' => $message->getIdMessage()
            ]);
        }
    }

    /** Supprime un message de la base de données.
     * 
     * @param Message $message L'objet Message à supprimer.
     */
    public function supprimerMessage(Message $message) {
        $stmt = $this->pdo->prepare("DELETE FROM message WHERE idMessage = :idMessage");
        $stmt->execute(['idMessage' => $message->getIdMessage()]);
    }

    
}
    