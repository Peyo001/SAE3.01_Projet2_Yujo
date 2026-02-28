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

    /**
     * Hydrate une ligne de résultat en un objet Message.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Message Retourne un objet Message
     */
    public function hydrate(array $row): Message {
        return new Message(
            $row['idMessage'],
            $row['contenu'],
            $row['dateEnvoi'],
            $row['idGroupe'],
            $row['idUtilisateur']
        );
    }



    // MÉTHODES
    /**
     * Trouve un message par son identifiant.
     * 
     * @param int $id Identifiant du message.
     * @return Message|null L'objet Message correspondant ou null si non trouvé.
     */
    public function findByIdMessage(int $id): ?Message {
        $stmt = $this->conn->prepare("SELECT * FROM message WHERE idMessage = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            return $this->hydrate($row);
        }

        return null;
    }


    /** 
     * Trouve tous les messages d'un groupe donné.
     * 
     * @param int $idGroupe Identifiant du groupe.
     * @return array Tableau d'objets Message.
     */
    public function findByIdGroupe(int $idGroupe): array {
        $stmt = $this->conn->prepare("SELECT * FROM message WHERE idGroupe = :idGroupe");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $messages = [];
        $rows = $stmt->fetchAll();
        $messages = $this->hydrateAll($rows);

        return $messages;
    }

    /** 
     * Trouve tous les messages d'un utilisateur donné.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return array Tableau d'objets Message.
     */
    public function findByUtilisateur(int $idUtilisateur): array {
        $stmt = $this->conn->prepare("SELECT * FROM message WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $messages = $this->hydrateAll($rows);
        return $messages;
    }


    /** 
     * Trouve tous les messages dans la base de données.
     * 
     * @return array Tableau d'objets Message.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT * FROM message");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $messages = $this->hydrateAll($rows);
        return $messages;
    }


    /** 
     * Sauvegarde un message dans la base de données.
     * 
     * Si l'objet Message a un idMessage null, il est inséré comme un nouveau message.
     * Sinon, le message existant est mis à jour.
     * 
     * @param Message $message L'objet Message à sauvegarder.
     * @return void
     */
    public function insererMessage(Message $message): void {
        if ($message->getIdMessage() === null) {
            $stmt = $this->conn->prepare("INSERT INTO message (contenu, dateEnvoi, idGroupe, idUtilisateur) VALUES (:contenu, :dateEnvoi, :idGroupe, :idUtilisateur)");
            $stmt->execute([
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi(),
                'idGroupe' => $message->getIdGroupe(),
                'idUtilisateur' => $message->getIdUtilisateur()
            ]);
            $message->setIdMessage((int)$this->conn->lastInsertId());
        } else {
            $stmt = $this->conn->prepare("UPDATE message SET contenu = :contenu, dateEnvoi = :dateEnvoi, idGroupe = :idGroupe, idUtilisateur = :idUtilisateur WHERE idMessage = :idMessage");
            $stmt->execute([
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi(),
                'idGroupe' => $message->getIdGroupe(),
                'idUtilisateur' => $message->getIdUtilisateur(),
                'idMessage' => $message->getIdMessage()
            ]);
        }
    }

    /** 
     * Supprime un message de la base de données.
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur dont les messages doivent être supprimés.
     * @return void
     */
    public function supprimerMessage(int $idUtilisateur): void {
        $stmt = $this->conn->prepare("DELETE FROM message WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
    }

    
}
    