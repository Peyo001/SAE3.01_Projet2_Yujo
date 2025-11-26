<?php
    /**
     * Classe RoomDao
     * 
     * Cette classe gère les opérations de la base de données pour les objets Room.
     * Elle utilise la classe Database pour obtenir une connexion PDO.
     * 
     * Exemple d'utilisation :
     * $roomDao = new RoomDao();
     * $room = $roomDao->findAll();
     * 
     */
    class RoomDao {

        // ATTRIBUT
        // Propriété représentant la connexion à la base de données via PDO.
        private PDO $conn;

        // CONSTRUCTEUR
        /**
         * Constructeur de la classe RoomDao.
         * 
         * Ce constructeur initialise la connexion à la base de données en utilisant la classe Database.
         */
        public function __construct() {
            $this->conn = Database::getInstance()->getConnection();
        }

        // DESTRUCTEUR
        /**
         * Destructeur de la classe RoomDao.
         * 
         * Ce destructeur est vide, mais peut être utilisé pour libérer des ressources si nécessaire.
         */
        public function __destruct() {
            // Rien à nettoyer ici
        }

        // METHODES
        
        /**
         * Récupère une room spécifique par son identifiant.
         * 
         * Cette méthode permet de récupérer une room spécifique en fonction de son identifiant.
         * 
         * @param int $id Identifiant de la room à récupérer.
         * @return Room|null Retourne un objet `Room` si trouvé, sinon null.
         */
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

        /**
         * Récupère toutes les rooms.
         * 
         * Cette méthode permet de récupérer toutes les rooms présentes dans la base de données.
         * 
         * @return Room[] Tableau d'objets `Room` représentant toutes les rooms.
         */ 
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
        
        /**
         * Récupère toutes les rooms créées par un utilisateur spécifique.
         * 
         * Cette méthode permet de récupérer toutes les rooms créées par un utilisateur en fonction de son identifiant.
         * 
         * @param int $id Identifiant de l'utilisateur (créateur de la room).
         * @return Room[] Tableau des rooms créées par l'utilisateur spécifié.
         */
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
        
        /**
        * Récupère les rooms dont le nom correspond à la recherche.
        * 
        * Cette méthode permet de récupérer toutes les rooms dont le nom contient un terme spécifié.
        * 
        * @param string $nom Le terme de recherche pour le nom de la room.
        * @return Room[] Tableau des rooms dont le nom contient le terme spécifié.
        */
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
        
        /**
         * Récupère toutes les rooms publiques.
         * 
         * Cette méthode permet de récupérer toutes les rooms dont la visibilité est publique.
         * 
         * @return Room[] Tableau des rooms publiques.
         */
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

        /**
         * Met à jour les informations d'une room.
         * 
         * Cette méthode permet de mettre à jour le nom, la visibilité et le nombre de visites d'une room existante.
         * 
         * @param Room $room L'objet `Room` contenant les nouvelles informations.
         * @return bool Retourne true si la mise à jour a réussi, sinon false.
         */
        public function updateRoom(Room $room): bool {
            $stmt = $this->conn->prepare("UPDATE ROOM SET nom = :nom, visibilite = :visibilite, nbVisit = :nbVisit WHERE idRoom = :idRoom");

            $stmt->bindValue(':idRoom', $room->getIdRoom(), PDO::PARAM_INT);
            $stmt->bindValue(':nom', $room->getNom());
            $stmt->bindValue(':visibilite', $room->getVisibilite());
            $stmt->bindValue(':nbVisit', $room->getNbVisit(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Récupère tous les objets associés à une room spécifique.
         * 
         * Cette méthode récupère tous les objets présents dans une room en fonction de son identifiant.
         * 
         * @param int $idRoom Identifiant de la room pour laquelle récupérer les objets.
         * @return Objet[] Tableau des objets présents dans la room.
         */
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

        /**
         * Incrémente le nombre de visites d'une room.
         * 
         * Cette méthode met à jour le nombre de visites d'une room en l'incrémentant de 1.
         * 
         * @param int $idRoom L'identifiant de la room dont le nombre de visites doit être incrémenté.
         */
        public function incrementVisit(int $idRoom): void {
            $stmt = $this->conn->prepare("UPDATE ROOM SET nbVisit = nbVisit + 1 WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            $stmt->execute();
        }

        /**
        * Ajoute un objet à une room.
        * 
        * Cette méthode permet d'ajouter un objet à la table `OBJET` et de l'associer à une room via son identifiant.
        * 
        * @param Objet $objet L'objet à ajouter à la room.
        * @return bool Retourne true si l'ajout a réussi, sinon false.
        */
        public function addObjetToRoom(Objet $objet): bool {
            $stmt = $this->conn->prepare("INSERT INTO OBJET (nom, type, idRoom) VALUES (:nom, :type, :idRoom)");
            $stmt->bindValue(':nom', $objet->getNom());
            $stmt->bindValue(':type', $objet->getType());
            $stmt->bindValue(':idRoom', $objet->getIdRoom(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Supprime tous les objets d'une room.
         * 
         * Cette méthode supprime tous les objets associés à une room en fonction de son identifiant.
         * 
         * @param int $idRoom L'identifiant de la room dont les objets doivent être supprimés.
         * @return bool Retourne true si la suppression a réussi, sinon false.
         */
        public function removeObjectsFromRoom(int $idRoom): bool {
            $stmt = $this->conn->prepare("DELETE FROM OBJET WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            return $stmt->execute();
        }

        /**
         * Crée une nouvelle room dans la base de données.
         * 
         * Cette méthode insère une nouvelle room dans la table `ROOM` en utilisant les informations de l'objet `Room` passé en paramètre.
         * 
         * @param Room $room L'objet `Room` à insérer dans la base de données.
         * @return bool Retourne true si l'insertion a réussi, sinon false.
         */
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
        
        /**
        * Supprime une room de la base de données.
        * 
        * Cette méthode supprime une room en fonction de son identifiant de la table `ROOM`.
        * 
        * @param int $id L'identifiant de la room à supprimer.
        * @return bool Retourne true si la suppression a réussi, sinon false.
        */
        public function deleteRoom(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM ROOM WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }
?>