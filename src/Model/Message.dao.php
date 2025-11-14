<?php
class MessageDao {
    private PDO $conn;

    public function __construct(PDO $connexion)
    {
        $this->conn = DATABASE::getInstance()->getConnection();
    }

    // DESTRUCTEUR
    public function __destruct() {
        // Rien à nettoyer ici
    }

    // MÉTHODES
    // Trouver un message par son ID
    public function find(int $idMessage): ?Message {
        $stmt = $this->conn->prepare("SELECT * FROM Message WHERE idMessage = :idMessage");
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Message(
                $row['idMessage'],
                $row['contenuMessage'],
                $row['dateEnvoi'],
                $row['idUtilisateur'],
                $row['idGroupe']
            );
        }
        return null;
    }

    // Trouver tous les messages d’un groupe
    public function findByGroupe(int $idGroupe): array {
        $stmt = $this->conn->prepare("SELECT * FROM Message WHERE idGroupe = :idGroupe ORDER BY dateEnvoi ASC");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $messages = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message(
                $row['idMessage'],
                $row['contenuMessage'],
                $row['dateEnvoi'],
                $row['idUtilisateur'],
                $row['idGroupe']
            );
        }
        return $messages;
    }

    // Récupérer tous les messages
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT * FROM Message");
        $stmt->execute();
        $messages = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message(
                $row['idMessage'],
                $row['contenuMessage'],
                $row['dateEnvoi'],
                $row['idUtilisateur'],
                $row['idGroupe']
            );
        }
        return $messages;
    }

    // Insérer un nouveau message
    public function update(Message $message): bool {
        $stmt = $this->conn->prepare("UPDATE Message SET contenuMessage = :contenuMessage, dateEnvoi = :dateEnvoi, idUtilisateur = :idUtilisateur, idGroupe = :idGroupe WHERE idMessage = :idMessage");
        $stmt->bindValue(':contenuMessage', $message->getContenuMessage(), PDO::PARAM_STR);
        $stmt->bindValue(':dateEnvoi', $message->getDateEnvoi(), PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $message->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $message->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':idMessage', $message->getIdMessage(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Supprimer un message
    public function delete(int $idMessage): bool {
        $stmt = $this->conn->prepare("DELETE FROM Message WHERE idMessage = :idMessage");
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Crée un nouveau message
    public function createMessage(string $contenuMessage, int $idUtilisateur, int $idGroupe): int {
        $stmt = $this->conn->prepare("INSERT INTO Message (contenuMessage, dateEnvoi, idUtilisateur, idGroupe) VALUES (:contenuMessage, NOW(), :idUtilisateur, :idGroupe)");
        $stmt->bindValue(':contenuMessage', $contenuMessage, PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }
}
?>