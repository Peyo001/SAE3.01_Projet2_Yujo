<?php
require_once "Dao.class.php";
require_once __DIR__ . "/../Class/ObjetRoom.class.php";
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
    class RoomDao extends Dao
    {
        /**
         * Hydrate une ligne de résultat en un objet Room.
         * 
         * @param array $row Ligne de résultat de la base de données.
         * @return Room Retourne un objet Room
         */
        public function hydrate(array $row): Room {
            return new Room(
                (int)$row['idRoom'],
                $row['nom'],
                $row['visibilite'],
                $row['dateCreation'],
                (int)$row['nbVisit'],
                (int)$row['idCreateur'],
                $row['personnalisation'] ?? null
            );
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
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, personnalisation, dateCreation, nbVisit, idCreateur FROM room WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $room = $this->hydrate($row);
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
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, personnalisation, dateCreation, nbVisit, idCreateur FROM room");
            $stmt->execute();
            $rooms = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rooms = $this->hydrateAll($rows);
            foreach ($rooms as $room) {
                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
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
            $stmt = $this->conn->prepare("SELECT idRoom, nom, visibilite, personnalisation, dateCreation, nbVisit, idCreateur FROM room WHERE idCreateur = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $rooms = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rooms = $this->hydrateAll($rows);
            foreach ($rooms as $room) {
                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
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
            $stmt = $this->conn->prepare("SELECT * FROM room WHERE nom LIKE :nom");
            $stmt->bindValue(':nom', "%$nom%");
            $stmt->execute();

            $rooms = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rooms = $this->hydrateAll($rows);
            foreach ($rooms as $room) {
                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
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
            $stmt = $this->conn->prepare("SELECT * FROM room WHERE visibilite = 'public'");
            $stmt->execute();
            $rooms = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rooms = $this->hydrateAll($rows);
            foreach ($rooms as $room) {
                $room->setObjets($this->findObjetsByRoom($room->getIdRoom()));
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
        public function update(Room $room): bool {
            $stmt = $this->conn->prepare("UPDATE room SET nom = :nom, visibilite = :visibilite, personnalisation = :personnalisation, nbVisit = :nbVisit WHERE idRoom = :idRoom");

            $stmt->bindValue(':idRoom', $room->getIdRoom(), PDO::PARAM_INT);
            $stmt->bindValue(':nom', $room->getNom());
            $stmt->bindValue(':visibilite', $room->getVisibilite());
            $stmt->bindValue(':personnalisation', $room->getPersonnalisation());
            $stmt->bindValue(':nbVisit', $room->getNbVisit(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        // Compatibilité : ancien nom
        public function updateRoom(Room $room): bool { return $this->update($room); }

        /**
         * Récupère tous les objets associés à une room spécifique.
         * 
         * Cette méthode récupère tous les objets présents dans une room en fonction de son identifiant.
         * 
         * @param int $idRoom Identifiant de la room pour laquelle récupérer les objets.
         * @return Objet[] Tableau des objets présents dans la room.
         */
        public function findObjetsByRoom(int $idRoom): array {
            $sql = "SELECT o.idObjet, o.description, o.modele3dPath, o.prix, p.idRoom
                    FROM objet o
                    INNER JOIN posseder p ON p.idObjet = o.idObjet
                    WHERE p.idRoom = :idRoom";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Chaque ligne correspond à un Objet, pas à une Room : on hydrate manuellement
            $objets = [];
            foreach ($rows as $row) {
                $objets[] = new Objet(
                    (int)$row['idObjet'],
                    $row['description'] ?? null,
                    $row['modele3dPath'] ?? null,
                    (int)$row['prix'],
                    isset($row['idRoom']) ? (int)$row['idRoom'] : null,
                    $row['image'] ?? null
                );
            }

            return $objets;
        }

        /**
         * Récupère tous les objets d'une room avec leurs positions 3D.
         * 
         * Cette méthode récupère les objets avec leurs coordonnées de position, rotation et échelle.
         * 
         * @param int $idRoom Identifiant de la room
         * @return ObjetRoom[] Tableau d'objets ObjetRoom avec positions 3D
         */
        public function findObjetsRoomAvecPositions(int $idRoom): array {
            $sql = "SELECT o.idObjet, o.description, o.modele3dPath, o.prix, 
                           p.idRoom, p.positionX, p.positionY, p.positionZ,
                           p.rotationX, p.rotationY, p.rotationZ, p.scale, p.dateAjout
                    FROM objet o
                    INNER JOIN posseder p ON p.idObjet = o.idObjet
                    WHERE p.idRoom = :idRoom";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $objets = [];
            foreach ($rows as $row) {
                $objets[] = new ObjetRoom(
                    (int)$row['idObjet'],
                    $row['description'] ?? null,
                    $row['modele3dPath'] ?? null,
                    (int)$row['prix'],
                    (int)$row['idRoom'],
                    (float)($row['positionX'] ?? 0.0),
                    (float)($row['positionY'] ?? 0.0),
                    (float)($row['positionZ'] ?? 0.0),
                    (float)($row['rotationX'] ?? 0.0),
                    (float)($row['rotationY'] ?? 0.0),
                    (float)($row['rotationZ'] ?? 0.0),
                    (float)($row['scale'] ?? 1.0)
                );
            }

            return $objets;
        }

        /**
         * Ajoute un objet acheté à une room avec sa position 3D.
         * 
         * @param int $idRoom Identifiant de la room
         * @param int $idObjet Identifiant de l'objet
         * @param float $positionX Position X (défaut: 0)
         * @param float $positionY Position Y (défaut: 0)
         * @param float $positionZ Position Z (défaut: 0)
         * @param float $rotationX Rotation X (défaut: 0)
         * @param float $rotationY Rotation Y (défaut: 0)
         * @param float $rotationZ Rotation Z (défaut: 0)
         * @param float $scale Échelle (défaut: 1.0)
         * @return bool True si l'ajout a réussi
         */
        public function ajouterObjetAvecPosition(
            int $idRoom, 
            int $idObjet, 
            float $positionX = 0.0, 
            float $positionY = 0.0, 
            float $positionZ = 0.0,
            float $rotationX = 0.0,
            float $rotationY = 0.0,
            float $rotationZ = 0.0,
            float $scale = 1.0
        ): bool {
                $sql = "INSERT INTO posseder (idRoom, idObjet, positionX, positionY, positionZ, rotationX, rotationY, rotationZ, scale) 
                    VALUES (:idRoom, :idObjet, :positionX, :positionY, :positionZ, :rotationX, :rotationY, :rotationZ, :scale)
                    ON DUPLICATE KEY UPDATE 
                        positionX = :positionX, positionY = :positionY, positionZ = :positionZ,
                        rotationX = :rotationX, rotationY = :rotationY, rotationZ = :rotationZ,
                        scale = :scale";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
            $stmt->bindValue(':positionX', $positionX);
            $stmt->bindValue(':positionY', $positionY);
            $stmt->bindValue(':positionZ', $positionZ);
            $stmt->bindValue(':rotationX', $rotationX);
            $stmt->bindValue(':rotationY', $rotationY);
            $stmt->bindValue(':rotationZ', $rotationZ);
            $stmt->bindValue(':scale', $scale);
            
            return $stmt->execute();
        }

        /**
         * Met à jour la position d'un objet dans une room.
         * 
         * @param int $idRoom Identifiant de la room
         * @param int $idObjet Identifiant de l'objet
         * @param float $positionX Position X
         * @param float $positionY Position Y
         * @param float $positionZ Position Z
         * @param float $rotationX Rotation X
         * @param float $rotationY Rotation Y
         * @param float $rotationZ Rotation Z
         * @param float $scale Échelle
         * @return bool True si la mise à jour a réussi
         */
        public function updateObjetPosition(
            int $idRoom,
            int $idObjet,
            float $positionX,
            float $positionY,
            float $positionZ,
            float $rotationX,
            float $rotationY,
            float $rotationZ,
            float $scale
        ): bool {
            $sql = "UPDATE posseder 
                    SET positionX = :positionX, positionY = :positionY, positionZ = :positionZ,
                        rotationX = :rotationX, rotationY = :rotationY, rotationZ = :rotationZ,
                        scale = :scale
                    WHERE idRoom = :idRoom AND idObjet = :idObjet";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
            $stmt->bindValue(':positionX', $positionX);
            $stmt->bindValue(':positionY', $positionY);
            $stmt->bindValue(':positionZ', $positionZ);
            $stmt->bindValue(':rotationX', $rotationX);
            $stmt->bindValue(':rotationY', $rotationY);
            $stmt->bindValue(':rotationZ', $rotationZ);
            $stmt->bindValue(':scale', $scale);
            
            return $stmt->execute();
        }

        /**
         * Retire un objet d'une room.
         * 
         * @param int $idRoom Identifiant de la room
         * @param int $idObjet Identifiant de l'objet
         * @return bool True si la suppression a réussi
         */
        public function retirerObjetDeRoom(int $idRoom, int $idObjet): bool {
            $stmt = $this->conn->prepare("DELETE FROM posseder WHERE idRoom = :idRoom AND idObjet = :idObjet");
            $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $idObjet, PDO::PARAM_INT);
            return $stmt->execute();
        }

        /**
         * Incrémente le nombre de visites d'une room.
         * 
         * Cette méthode met à jour le nombre de visites d'une room en l'incrémentant de 1.
         * 
         * @param int $idRoom L'identifiant de la room dont le nombre de visites doit être incrémenté.
         * @return void
         */
        public function incrementerVisite(int $idRoom): void {
            $stmt = $this->conn->prepare("UPDATE room SET nbVisit = nbVisit + 1 WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom);
            $stmt->execute();
        }

        
        /**
        * Ajoute un objet à une room.
        * 
        * Cette méthode permet d'ajouter un objet à la table `OBJET` et de l'associer à une room via son identifiant.
        * 
        * @param Objet $objet L'objet à ajouter à la room.
        * @param int $idRoom L'identifiant de la room à laquelle ajouter l'objet.
        * @return bool Retourne true si l'ajout a réussi, sinon false.
        */
        public function addObjetToRoom(Objet $objet, int $idRoom): bool {
            // Crée l'objet s'il n'existe pas encore
            if ($objet->getIdObjet() === null) {
                $stmtObjet = $this->conn->prepare("INSERT INTO objet (description, modele3dPath, prix) VALUES (:description, :modele3dPath, :prix)");
                $stmtObjet->bindValue(':description', $objet->getDescription());
                $stmtObjet->bindValue(':modele3dPath', $objet->getModele3dPath());
                $stmtObjet->bindValue(':prix', $objet->getPrix());
                if (!$stmtObjet->execute()) {
                    return false;
                }
                $objet->setIdObjet((int)$this->conn->lastInsertId());
            }

            $stmt = $this->conn->prepare("INSERT INTO posseder (idRoom, idObjet) VALUES (:idRoom, :idObjet)");
            $stmt->bindValue(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $objet->getIdObjet(), PDO::PARAM_INT);

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
        public function supprimerObjetsDeRoom(int $idRoom): bool {
            $stmt = $this->conn->prepare("DELETE FROM posseder WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom, PDO::PARAM_INT);
            return $stmt->execute();
        }

        // Compatibilité : ancien nom
        public function removeObjectsFromRoom(int $idRoom): bool { return $this->supprimerObjetsDeRoom($idRoom); }

        /**
         * Crée une nouvelle room dans la base de données.
         * 
         * Cette méthode insère une nouvelle room dans la table `ROOM` en utilisant les informations de l'objet `Room` passé en paramètre.
         * 
         * @param Room $room L'objet `Room` à insérer dans la base de données.
         * @return bool Retourne true si l'insertion a réussi, sinon false.
         */
        public function creerRoom(Room $room): bool {
            $stmt = $this->conn->prepare("INSERT INTO room (nom, visibilite, personnalisation, idCreateur) VALUES (:nom, :visibilite, :personnalisation, :idCreateur)");
            $stmt->bindValue(':nom', $room->getNom(), PDO::PARAM_STR);
            $stmt->bindValue(':visibilite', $room->getVisibilite(), PDO::PARAM_STR);
            $stmt->bindValue(':personnalisation', $room->getPersonnalisation());
            $stmt->bindValue(':idCreateur', $room->getIdCreateur(), PDO::PARAM_INT);

            $result = $stmt->execute();
            if ($result) {
                $room->setIdRoom((int)$this->conn->lastInsertId());
            }

            return $result;
        }

        // Compatibilité : ancien nom
        public function createRoom(Room $room): bool { return $this->creerRoom($room); }
        
        /**
        * Supprime une room de la base de données.
        * 
        * Cette méthode supprime une room en fonction de son identifiant de la table `ROOM`.
        * 
        * @param int $id L'identifiant de la room à supprimer.
        * @return bool Retourne true si la suppression a réussi, sinon false.
        */
        public function supprimerRoom(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM room WHERE idRoom = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        // Compatibilité : ancien nom
        public function deleteRoom(int $id): bool { return $this->supprimerRoom($id); }
    }
?>