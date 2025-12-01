<?php
    class ObjetDao {

        // ATTRIBUT
        private PDO $conn;

        // CONSTRUCTEUR
        public function __construct() {
            $this->conn = Database::getInstance()->getConnection();
        }

        //DESTRUCTEUR
        public function __destruct() {
            // rien
        }

        // METHODES
        public function find(int $id): ?Objet {
            $stmt = $this->conn->prepare("SELECT idObjet, description, modele3dPath, prix FROM OBJET WHERE idObjet = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                    return new Objet(
                    $row['idObjet'],
                    $row['description'],
                    $row['modele3dPath'],
                    $row['prix'],
                );
                    
            }
            return null;
        }

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
                );
            }
            return $users;
        }

        /*public function findByRoom(int $idRoom): array {
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
        }*/

        public function updateObjet(Objet $objet): bool {
            $stmt = $this->conn->prepare("UPDATE OBJET SET description = :description, modele3dPath = :modele3dPath, prix = :prix WHERE idObjet = :idObjet;");
            $stmt->bindValue(':description', $objet->getDescription());
            $stmt->bindValue(':modele3dPath', $objet->getModele3dPath());
            $stmt->bindValue(':prix', $objet->getPrix(), PDO::PARAM_INT);
            $stmt->bindValue(':idObjet', $objet->getIdObjet(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        public function createObjet(Objet $objet): bool {
            $stmt = $this->conn->prepare("INSERT INTO OBJET (description, modele3dPath, prix) VALUES (:description, :modele3dPath, :prix)");

            $stmt->bindValue(':description', $objet->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':modele3dPath', $objet->getModele3dPath(), PDO::PARAM_STR);
            $stmt->bindValue(':prix', $objet->getPrix(), PDO::PARAM_INT);
            return $stmt->execute();
        }
    
        public function deleteObjet(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM OBJET WHERE idObjet = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }

?>