<?php
require_once "Dao.class.php";
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
class MessageDAO extends Dao{


    // MÉTHODES
    /**
     * Trouve un message par son identifiant.
     * 
     * @param int $id Identifiant du message.
     * @return Message|null L'objet Message correspondant ou null si non trouvé.
     */
    public function findByIdMessage(int $id): ?Message {
        $stmt = $this->conn->prepare("SELECT * FROM MESSAGE WHERE idMessage = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
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
    public function findByIdGroupe(int $idGroupe): array {
        $stmt = $this->conn->prepare("SELECT * FROM MESSAGE WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
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

    /** Trouve tous les messages d'un utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return array Tableau d'objets Message.
     */
    public function findByUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT * FROM MESSAGE WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
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
    public function findAll(): array {
        $stmt = $this->conn->query("SELECT * FROM MESSAGE");
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
    public function insererMessage(Message $message): void {
        if ($message->getIdMessage() === null) {
            $stmt = $this->conn->prepare("INSERT INTO MESSAGE (contenu, dateEnvoi, idGroupe, idUtilisateur) VALUES (:contenu, :dateEnvoi, :idGroupe, :idUtilisateur)");
            $stmt->execute([
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi(),
                'idGroupe' => $message->getIdGroupe(),
                'idUtilisateur' => $message->getIdUtilisateur()
            ]);
            $message->setIdMessage((int)$this->conn->lastInsertId());
        } else {
            $stmt = $this->conn->prepare("UPDATE MESSAGE SET contenu = :contenu, dateEnvoi = :dateEnvoi, idGroupe = :idGroupe, idUtilisateur = :idUtilisateur WHERE idMessage = :idMessage");
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
    public function supprimerMessage(int $idUtilisateur): void {
        $stmt = $this->conn->prepare("DELETE FROM MESSAGE WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
    }

    
}
    