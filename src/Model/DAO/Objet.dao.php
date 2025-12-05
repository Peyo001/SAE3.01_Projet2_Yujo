<?php
    /**
     * Classe ObjetDao
     * 
     * Cette classe permet d'accéder aux données des objets dans la base de données.
     * Elle utilise la classe Database pour obtenir une connexion PDO.
     * 
     * Exemple d'utilisation :
     * $objetDao = new ObjetDao();
     * $objet = $objetDao->find(1);
     */
    class ObjetDao extends Dao
    {

        // METHODES
        /**
         * Trouve un objet dans la base de données par son identifiant.
         * 
         * Cette méthode récupère un objet spécifique en fonction de son identifiant et retourne un objet de type `Objet`.
         * 
         * @param int $id Identifiant de l'objet à récupérer.
         * @return Objet|null Retourne un objet `Objet` si trouvé, sinon null.
         */
        public function find(int $id): ?Objet {
            $stmt = $this->conn->prepare("SELECT idObjet, description, modele3dPath, prix, idRoom FROM OBJET WHERE idObjet = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                    return new Objet(
                    $row['idObjet'],
                    $row['description'],
                    $row['modele3dPath'],
                    $row['prix'],
                    $row['idRoom']
                );
                    
            }
            return null;
        }

        /**
         * Récupère tous les objets de la base de données.
         * 
         * Cette méthode récupère tous les objets et retourne un tableau d'objets `Objet`.
         * 
         * @return Objet[] Tableau des objets `Objet`.
         */
        public function findAll(): array {
            $stmt = $this->conn->prepare("SELECT idObjet, description, modele3dPath, prix FROM OBJET");
            $stmt->execute();
            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new Objet(
                    (int)$row['idObjet'],
                    $row['description'],
                    $row['modele3dPath'],
                    (int)$row['prix'],
                    (int)$row['idRoom']
                );
            }
            return $users;
        }

        /**
         * Récupère les objets d'une room spécifique.
         * 
         * Cette méthode récupère tous les objets qui appartiennent à une room donnée en fonction de son identifiant.
         * 
         * @param int $idRoom Identifiant de la room.
         * @return Objet[] Tableau d'objets `Objet` dans la room spécifiée.
         */
        public function findByRoom(int $idRoom): array {
            $stmt = $this->conn->prepare("SELECT * FROM OBJET WHERE idRoom = :idRoom");
            $stmt->bindParam(':idRoom', $idRoom, PDO::PARAM_INT);
            $stmt->execute();
            $objets = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $objets[] = new Objet(
                    $row['idObjet'],
                    $row['description'],
                    $row['modele3dPath'],
                    $row['prix'],
                    $row['idRoom']
                );
            }
            return $objets;
        }

        /**
         * Met à jour un objet dans la base de données.
         * 
         * Cette méthode met à jour les informations d'un objet existant en base de données.
         * 
         * @param Objet $objet L'objet à mettre à jour.
         * @return bool Retourne true si la mise à jour a réussi, sinon false.
         */
        public function updateObjet(Objet $objet): bool {
            $stmt = $this->conn->prepare("UPDATE OBJET SET description = :description, modele3dPath = :modele3dPath, prix = :prix, idRoom = :idRoom WHERE idObjet = :idObjet;");
            $stmt->bindValue(':description', $objet->getDescription());
            $stmt->bindValue(':modele3dPath', $objet->getModele3dPath());
            $stmt->bindValue(':prix', $objet->getPrix(), PDO::PARAM_INT);
            $stmt->bindValue(':idRoom', $objet->getIdRoom(), PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $objet->getIdObjet(), PDO::PARAM_INT);

            return $stmt->execute();
        }

         /**
         * Crée un nouvel objet dans la base de données.
         * 
         * Cette méthode permet d'ajouter un nouvel objet dans la base de données avec les informations de l'objet fourni.
         * 
         * @param Objet $objet L'objet à insérer dans la base de données.
         * @return bool Retourne true si l'insertion a réussi, sinon false.
         */
        public function createObjet(Objet $objet): bool {
            $stmt = $this->conn->prepare("INSERT INTO OBJET (description, modele3dPath, prix, idRoom) VALUES (:description, :modele3dPath, :prix, :idRoom)");

            $stmt->bindValue(':description', $objet->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':modele3dPath', $objet->getModele3dPath(), PDO::PARAM_STR);
            $stmt->bindValue(':prix', $objet->getPrix(), PDO::PARAM_INT);
            $stmt->bindValue(':idRoom', $objet->getIdRoom(), PDO::PARAM_INT);
            return $stmt->execute();
        }
        
         /**
         * Supprime un objet de la base de données.
         * 
         * Cette méthode permet de supprimer un objet de la base de données en fonction de son identifiant.
         * 
         * @param int $id Identifiant de l'objet à supprimer.
         * @return bool Retourne true si la suppression a réussi, sinon false.
         */
        public function deleteObjet(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM OBJET WHERE idObjet = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }
?>