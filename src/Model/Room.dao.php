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
        public function find(int $id): ?Room {
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, dateCreation, nbVisit, idCreateur FROM ROOM WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $room = new Room(
                    (int)$row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    (int)$row['nbVisit'],
                    (int)$row['idCreateur']
                );

                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
                return $room;
            }
            return null;
        }

        public function findAll(): array {
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, dateCreation, nbVisit, idCreateur FROM ROOM");
            $stmt->execute();
            $rooms = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room(
                    (int)$row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    (int)$row['nbVisit'],
                    (int)$row['idCreateur']
                );
                
                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
                $rooms[] = $room;
            }
            
            return $rooms;
        }

        public function findByCreateur(int $id): array {
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, dateCreation, nbVisit, idCreateur FROM ROOM WHERE idCreateur = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $rooms = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room(
                    (int)$row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    (int)$row['nbVisit'],
                    (int)$row['idCreateur']
                );

                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
                $rooms[] = $room;
            }

            return $rooms;
        }

        public function findByName(string $nom): array {
            $stmt = $this->conn->prepare("SELECT * FROM ROOM WHERE nom LIKE :nom");
            $stmt->bindValue(':nom', "%$nom%");
            $stmt->execute();

            $rooms = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room(
                    $row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    $row['nbVisit'],
                    $row['idCreateur']
                );

                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));

                $rooms[] = $room;
            }

            return $rooms;
        }

        public function findPublicRooms(): array {
            $stmt = $this->conn->prepare("SELECT * FROM ROOM WHERE visibilite = 'public'");
            $stmt->execute();

            $rooms = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room(
                    $row['idRoom'],
                    $row['nom'],
                    $row['visibilite'],
                    $row['dateCreation'],
                    $row['nbVisit'],
                    $row['idCreateur']
                );

                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
                $rooms[] = $room;
            }

            return $rooms;
        }

        public function updateRoom(Room $room): bool {
            $stmt = $this->conn->prepare("UPDATE ROOM SET nom = :nom, visibilite = :visibilite, nbVisit = :nbVisit WHERE idRoom = :idRoom");

            $stmt->bindValue(':idRoom', $room->getIdRoom(), PDO::PARAM_INT);
            $stmt->bindValue(':nom', $room->getNom());
            $stmt->bindValue(':visibilite', $room->getVisibilite());
            $stmt->bindValue(':nbVisit', $room->getNbVisit(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        public function findObjetsByRoom(int $idRoom): array {
            $stmt = $this->conn->prepare("SELECT * FROM OBJET WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            $stmt->execute();

            $objets = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $objets[] = new Objet(
                    $row['idObjet'],
                    $row['nom'],
                    $row['type'],
                    $row['idRoom']
                );
            }

            return $objets;
        }

        public function incrementVisit(int $idRoom): void {
            $stmt = $this->conn->prepare("UPDATE ROOM SET nbVisit = nbVisit + 1 WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            $stmt->execute();
        }

        public function addObjetToRoom(Objet $objet): bool {
            $stmt = $this->conn->prepare("INSERT INTO OBJET (nom, type, idRoom) VALUES (:nom, :type, :idRoom)");
            $stmt->bindValue(':nom', $objet->getNom());
            $stmt->bindValue(':type', $objet->getType());
            $stmt->bindValue(':idRoom', $objet->getIdRoom(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        public function removeObjectsFromRoom(int $idRoom): bool {
            $stmt = $this->conn->prepare("DELETE FROM OBJET WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            return $stmt->execute();
        }

        public function createRoom(Room $room): bool {
            $stmt = $this->conn->prepare("INSERT INTO ROOM (idRoom, nom, visibilite, dateCreation, nbVisit, idCreateur) VALUES (:idRoom, :nom, :visibilite, :dateCreation, :nbVisit, :idCreateur)");
            $stmt->bindValue(':idRoom', $room->getIdRoom(), PDO::PARAM_INT);
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