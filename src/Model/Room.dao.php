<?php
    class RoomDao {

        // ATTRIBUT
        private PDO $conn;

        // CONSTRUCTEUR
        public function __construct() {
            $this->conn = Database::getInstance()->getConnection();
        }

        //DESTRUCTEUR
        public function __destruct() {
            Database::getInstance()->__destruct();
        }

        // METHODES
        public function findRoomById(int $id): ?Room {
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, dateCreation, nbVisit, idCreateur FROM ROOM WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                    return new Room(
                    $row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    $row['nbVisit'],
                    $row['idCreateur']);
            }
            return null;
        }

        public function findAllRoom(): array {
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, dateCreation, nbVisit, idCreation FROM ROOM");
            $stmt->execute();
            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new Room(
                    (int)$row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    (int)$row['nbVisit'],
                    (bool)$row['idCreation']);
            }
            
            return $users;
        }

        public function createRoom(Room $room): bool {
            $stmt = $this->conn->prepare("INSERT INTO ROOM (nom, visibilite, dateCreation, nbVisit, idCreateur) VALUES (:nom, :visibilite, :dateCreation, :nbVisit, :idCreateur)");
            $stmt->bindValue(':nom', $room->getNom(), PDO::PARAM_STR);
            $stmt->bindValue(':visibilite', $room->getVisibilite(), PDO::PARAM_STR);
            $stmt->bindValue(':dateCreation', $room->getDateCreation(), PDO::PARAM_STR);
            $stmt->bindValue(':nbVisit', $room->getNbVisit(), PDO::PARAM_INT);
            $stmt->bindValue(':idCreateur', $room->getIdCreateur(), PDO::PARAM_INT);

            return $stmt->execute();
        }
    
        public function deleteRoom(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM ROOM WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }
?>