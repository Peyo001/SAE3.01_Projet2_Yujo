<?php
require_once "Dao.class.php";
/**
 * Classe QuestionDAO
 * 
 * Cette classe gère les opérations de base de données pour les objets Question.
 * Elle permet de créer, lire, mettre à jour et supprimer des questions dans la base de données.
 * 
 * Exemple d'utilisation :
 * $pdo = Database::getInstance()->getConnection();
 * $questionDAO = new QuestionDAO($pdo);
 * $question = $questionDAO->findById(1);
 */
class QuestionDAO extends Dao{

    // MÉTHODES
    /**
     * Trouve une question par son identifiant.
     * 
     * @param int $id Identifiant de la question.
     * @return Question|null L'objet Question correspondant ou null si non trouvé.
     */
    public function findByIdQuestion(int $id): ?Question {
        $stmt = $this->conn->prepare("SELECT * FROM QUESTION WHERE idQuestion = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $question = new Question(
                (int) $row['idQuestion'],
                $row['libelle']
            );

            $question->setReponses($this->findReponseByQuestion($question->getIdQuestion()));
            return $question;
        }
        return null;
    }

    /**
     * Récupère toutes les questions.
     * 
     * Cette méthode permet de récupérer toutes les questions stockées dans la table QUESTION.
     * 
     * @return Question[] Tableau d'objets Question représentant toutes les questions.
     */
    public function findAll(): array {
        $stmt = $this->conn->prepare("SELECT idQuestion, libelle FROM QUESTION");
        $stmt->execute();
        $questions = [];

        while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            $question = new Question(
                (int) $row['idQuestion'],
                $row['libelle']
            );

            $question->setReponses($this->findReponseByQuestion($question->getIdQuestion()));
            $questions[] = $question;
        }
        return $questions;
    }

    /**
     * Met à jour les informations d'une question
     * 
     * Cette méthode permet de mettre à jour le libellé d'une question existante.
     * 
     * @param Question $question L'objet Question contenant les nouvelles informations.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function update(Question $question): bool {
        $stmt = $this->conn->prepare("UPDATE QUESTION SET libelle = :libelle WHERE idQuestion = :id");

        $stmt->bindValue(':id', $question->getIdQuestion(), PDO::PARAM_INT);
        $stmt->bindValue(':libelle', $question->getLibelle(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Récupère toutes les réponses associées à une question spécifique.
     * 
     * Cette méthode récupère toutes les réponses associées à une question en fonction de son identifiant.
     * 
     * @param int $idQuestion Identifiant de la question pour laquelle on souhaite récupérer les réponses.
     * @return Reponse[] Tableau des réponses associés à la question.
     */
    public function findReponseByQuestion(int $idQuestion): array {
        $sql = "SELECT r.idReponsePossible, r.libelle, r.estCorrecte, l.idQuestion
                FROM REPONSEPOSSIBLE r
                INNER JOIN LISTER l ON l.idReponsePossible = r.idReponsePossible
                WHERE l.idQuestion = :idQuestion";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idQuestion', $idQuestion, PDO::PARAM_INT);
        $stmt->execute();

        $reponses = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reponses[] = new Reponse(
                (int) $row['idReponsePossible'],
                $row['libelle'],
                (bool) $row['estCorrecte'],
                (int) $row['idQuestion']
            );
        }
        return $reponses;
    }

    /**
     * Ajoute une réponse à une question
     * 
     * Cette méthode permet d'ajouter une réponse à la table REPONSEPOSSIBLE et de l'associer à une question via son identifiant.
     * 
     * @param ReponsePossible $reponse la réponse à ajouter à la question.
     * @return bool Retourne true si l'ajout a réussi, false sinon.
     */
    public function addReponseToQuestion(ReponsePossible $reponse): bool {
        // Insérer la réponse dans la table REPONSEPOSSIBLE
        $stmt = $this->conn->prepare("INSERT INTO REPONSEPOSSIBLE (libelle, estCorrecte) VALUES (:libelle, :estCorrecte)");
        $stmt->bindValue(':libelle', $reponse->getLibelle(), PDO::PARAM_STR);
        $stmt->bindValue(':estCorrecte', $reponse->isEstCorrecte(), PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            // Récupérer l'ID de la réponse insérée
            $reponseId = (int)$this->conn->lastInsertId();

            // Associer la réponse à la question dans la table LISTER
            $stmtAssoc = $this->conn->prepare("INSERT INTO LISTER (idQuestion, idReponsePossible) VALUES (:idQuestion, :idReponsePossible)");
            $stmtAssoc->bindValue(':idQuestion', $reponse->getIdQuestion(), PDO::PARAM_INT);
            $stmtAssoc->bindValue(':idReponsePossible', $reponseId, PDO::PARAM_INT);

            return $stmtAssoc->execute();
        }
        return false;
    }

    /**
     * Crée une nouvelle question dans la base de données.
     * 
     * Cette méthode insère une nouvelle question dans la table QUESTION en utilisant les informations de l'objet 'Question' passé en paramètre.
     * 
     * @param Question $question L'objet 'Question' à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, false sinon.
     */
    public function createQuestion(Question $question): bool {
        $stmt = $this->conn->prepare("INSERT INTO QUESTION (libelle) VALUES (:libelle)");
        $stmt->bindValue(':libelle', $question->getLibelle(), PDO::PARAM_STR);

        return $stmt->execute();
        if ($result) {
            $question->setIdQuestion((int)$this->conn->lastInsertId());
        }

        return $result;
    }

    /**
     * Supprimer une question de la base de données.
     * 
     * Cette méthode supprimer une question en fonction de son identifiant de la table QUESTION.
     * 
     * @param int $id Identifiant de la question à supprimer.
     * @return bool Retourne true si la suppression a réussi, false sinon.
     */
    public function supprimerQuestion(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM QUESTION WHERE idQuestion = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

}