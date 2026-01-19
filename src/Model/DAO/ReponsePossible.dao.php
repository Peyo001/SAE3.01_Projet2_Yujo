<?php
require_once "Dao.class.php";
    /**
     * Classe ReponsePossibleDao
     * 
     * Cette classe permet d'accéder aux données des réponses possibles dans la base de données.
     * Elle utilise la classe Database pour obtenir une connexion PDO.
     * 
     * Exemple d'utilisation :
     * $reponsePossibleDao = new ReponsePossibleDao();
     * $reponse = $reponsePossibleDao->find(1);
     */
    class ReponsePossibleDao extends Dao
    {
        /**
         * Hydrate une ligne de résultat en un objet ReponsePossible.
         * 
         * @param array $row Ligne de résultat de la base de données.
         * @return ReponsePossible Retourne un objet ReponsePossible
         */
        public function hydrate(array $row): ReponsePossible {
            return new ReponsePossible(
                (int)$row['idReponsePossible'],
                $row['libelle'],
                (bool)(int)$row['estCorrecte']
            );
        }

        // METHODES
        /**
         * Trouve une réponse possible dans la base de données par son identifiant.
         * 
         * Cette méthode récupère une réponse spécifique en fonction de son identifiant et retourne un objet de type `ReponsePossible`.
         * 
         * @param int $id Identifiant de la réponse possible à récupérer.
         * @return ReponsePossible|null Retourne un objet `ReponsePossible` si trouvé, sinon null.
         */
        public function find(int $id): ?ReponsePossible {
            $stmt = $this->conn->prepare("SELECT idReponsePossible, libelle, estCorrecte FROM REPONSE_POSSIBLE WHERE idReponsePossible = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        }

        /**
         * Récupère toutes les réponses possibles de la base de données.
         * 
         * Cette méthode récupère toutes les réponses possibles et retourne un tableau d'objets `ReponsePossible`.
         * 
         * @return ReponsePossible[] Tableau des objets `ReponsePossible`.
         */
        public function findAll(): array {
            $stmt = $this->conn->prepare("SELECT idReponsePossible, libelle, estCorrecte FROM REPONSE_POSSIBLE");
            $stmt->execute();
            $reponses = [];

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $reponses = $this->hydrateAll($rows);
            return $reponses;
        }

        /**
         * Récupère toutes les réponses possibles pour une question spécifique.
         * 
         * Cette méthode récupère toutes les réponses possibles associées à une question donnée en fonction de son identifiant.
         * 
         * @param int $idQuestion Identifiant de la question.
         * @return ReponsePossible[] Tableau des objets `ReponsePossible` associés à la question.
         */
        public function findByQuestion(int $idQuestion): array {
            $sql = "SELECT rp.idReponsePossible, rp.libelle, rp.estCorrecte 
                    FROM REPONSE_POSSIBLE rp
                    INNER JOIN QUESTION_REPONSE qr ON rp.idReponsePossible = qr.idReponsePossible
                    WHERE qr.idQuestion = :idQuestion";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idQuestion', $idQuestion, PDO::PARAM_INT);
            $stmt->execute();
            $reponses = [];
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $reponses = $this->hydrateAll($rows);
            return $reponses;
        }

        /**
         * Met à jour une réponse possible dans la base de données.
         * 
         * Cette méthode met à jour les informations d'une réponse existante en base de données.
         * 
         * @param ReponsePossible $reponsePossible la réponse à mettre à jour.
         * @return bool Retourne true si la mise à jour a réussi, sinon false.
         */
        public function mettreAJourReponsePossible(ReponsePossible $reponsePossible): bool {
            $stmt = $this->conn->prepare("UPDATE REPONSE_POSSIBLE SET libelle = :libelle, estCorrecte = :estCorrecte WHERE idReponsePossible = :idReponsePossible");
            $stmt->bindValue(':libelle', $reponsePossible->getLibelle(), PDO::PARAM_STR);
            $stmt->bindValue(':estCorrecte', $reponsePossible->getEstCorrecte(), PDO::PARAM_BOOL);
            $stmt->bindValue(':idReponsePossible', $reponsePossible->getIdReponsePossible(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Crée une nouvelle réponse possible dans la base de données.
         * 
         * Cette méthode permet d'ajouter une nouvelle réponse possible à la base de données avec les informations fournies.
         * 
         * @param ReponsePossible $reponsePossible La réponse possible à insérer dans la base de données.
         * @return bool Retourne true si l'insertion a réussi, sinon false.
         */
        public function insererReponsePossible(ReponsePossible $reponsePossible): bool {
            $stmt = $this->conn->prepare("INSERT INTO REPONSE_POSSIBLE (libelle, estCorrecte) VALUES (:libelle, :estCorrecte)");
            $stmt->bindValue(':libelle', $reponsePossible->getLibelle(), PDO::PARAM_STR);
            $stmt->bindValue(':estCorrecte', $reponsePossible->getEstCorrecte(), PDO::PARAM_BOOL);

            return $stmt->execute();
            if ($result) {
                $reponsePossible->setIdReponsePossible((int)$this->conn->lastInsertId());
            }
            return $result;
        }

        /**
         * Supprime une réponse possible de la base de données.
         * 
         * Cette méthode supprime une réponse possible en fonction de son identifiant.
         * 
         * @param int $id Identifiant de la réponse possible à supprimer.
         * @return bool Retourne true si la suppression a réussi, sinon false.
         */
        public function supprimerReponsePossible(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM REPONSE_POSSIBLE WHERE idReponsePossible = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }