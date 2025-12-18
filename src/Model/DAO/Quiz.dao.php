<?php
require_once "Dao.class.php";
    /**
     * Classe QuizDao
     * 
     * Cette classe permet d'accéder aux données des quiz dans la base de données.
     * Elle utilise la classe Database pour obtenir une connexion PDO.
     * 
     * Exemple d'utilisation :
     * $quizDao = new QuizDao();
     * $quiz = $quizDao->find(1);
     */
    class QuizDao extends Dao
    {
        // METHODES
        /**
         * Trouve un quiz dans la base de données par son identifiant.
         * 
         * Cette méthode récupère un quiz spécifique en fonction de son identifiant et retourne un objet de type `Quiz`.
         * 
         * @param int $id Identifiant du quiz à récupérer.
         * @return Quiz|null Retourne un objet `Quiz` si trouvé, sinon null.
         */
        public function find(int $id): ?Quiz {
            $stmt = $this->conn->prepare("SELECT idQuiz, titre, description, choixMultiples, idQuestion, idPost FROM QUIZ WHERE idQuiz = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return new Quiz(
                    (int)$row['idQuiz'],
                    $row['titre'],
                    $row['description'],
                    (bool)$row['choixMultiples'],
                    (int)$row['idQuestion'],
                    (int)$row['idPost']
                );
            }
            return null;
        }

        /**
         * Récupère tous les quiz de la base de données.
         * 
         * Cette méthode récupère tous les quiz et retourne un tableau d'objets `Quiz`.
         * 
         * @return Quiz[] Tableau des objets `Quiz`.
         */
        public function findAll(): array {
            $stmt = $this->conn->prepare("SELECT idQuiz, titre, description, choixMultiples, idQuestion, idPost FROM QUIZ");
            $stmt->execute();
            $quizs = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $quizs[] = new Quiz(
                    (int)$row['idQuiz'],
                    $row['titre'],
                    $row['description'],
                    (bool)$row['choixMultiples'],
                    (int)$row['idQuestion'],
                    (int)$row['idPost']
                );
            }
            return $quizs;
        }

        /**
         * Met à jour un quiz dans la base de données.
         * 
         * Cette méthode met à jour les informations d'un quiz existant dans la base de données.
         * 
         * @param Quiz $quiz Objet `Quiz` à mettre à jour.
         * @return bool Retourne true si la mise à jour a réussi, sinon false.
         */
        public function mettreAJourQuiz(Quiz $quiz): bool {
            $stmt = $this->conn->prepare("UPDATE QUIZ SET titre = :titre, description = :description, choixMultiples = :choixMultiples, idQuestion = :idQuestion, idPost = :idPost WHERE idQuiz = :idQuiz;");
            $stmt->bindValue(':titre', $quiz->getTitre());
            $stmt->bindValue(':description', $quiz->getDescription());
            $stmt->bindValue(':choixMultiples', $quiz->getChoixMultiples(), PDO::PARAM_BOOL);
            $stmt->bindValue(':idQuestion', $quiz->getIdQuestion(), PDO::PARAM_INT);
            $stmt->bindValue(':idPost', $quiz->getIdPost(), PDO::PARAM_INT);
            $stmt->bindValue(':idQuiz', $quiz->getIdQuiz(), PDO::PARAM_INT);

            return $stmt->execute();
        }

        /**
         * Crée un nouveau quiz dans la base de données.
         * 
         * Cette méthode permet d'ajouter un nouveau quiz à la base de données avec les informations du quiz fourni.
         * 
         * @param Quiz $quiz Objet `Quiz` à insérer dans la base de données.
         * @return bool Retourne true si l'insertion a réussi, sinon false.
         */
        public function insererQuiz(Quiz $quiz): bool {
            $stmt = $this->conn->prepare("INSERT INTO QUIZ (titre, description, choixMultiples, idQuestion, idPost) VALUES (:titre, :description, :choixMultiples, :idQuestion, :idPost)");

            $stmt->bindValue(':titre', $quiz->getTitre(), PDO::PARAM_STR);
            $stmt->bindValue(':description', $quiz->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':choixMultiples', $quiz->getChoixMultiples(), PDO::PARAM_BOOL);
            $stmt->bindValue(':idQuestion', $quiz->getIdQuestion(), PDO::PARAM_INT);
            $stmt->bindValue(':idPost', $quiz->getIdPost(), PDO::PARAM_INT);

            $result = $stmt->execute();
            if ($result) {
                $quiz->setIdQuiz((int)$this->conn->lastInsertId());
            }
            return $result;
        }

        /**
         * Supprime un quiz de la base de données.
         * 
         * Cette méthode permet de supprimer un quiz de la base de données en fonction de son identifiant.
         * 
         * @param int $id Identifiant du quiz à supprimer.
         * @return bool Retourne true si la suppression a réussi, sinon false.
         */
        public function supprimerQuiz(int $id): bool {
            $stmt = $this->conn->prepare("DELETE FROM QUIZ WHERE idQuiz = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }

    }
?>