<!-- Controller pour la classe Quiz -->

<?php
    class ControllerQuiz extends Controller {
        public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
            parent::__construct($loader, $twig);
        }

        public function afficher() {
            $idQuiz = isset($_GET['idQuiz']) ? $_GET['idQuiz'] : null;

            if ($idQuiz === null) {
                die("Erreur : aucun idQuiz fourni.");
            }

            // Récupère les Quiz à l'aide de la méthode find() de QuizDao
            $managerQuiz = new QuizDao($this->getPdo());
            $quiz = $managerQuiz->find($idQuiz);

            if (!$quiz) {
                die("Erreur : le quiz n'existe pas.");
            }

            // Génération de la vue
            echo $this->getTwig()->render('quiz.twig', [
                'quiz' => $quiz
            ]);
        }

        public function lister() {
            $managerQuiz = new QuizDao($this->getPdo());
            $quizs = $managerQuiz->findAll();

            // Généralisation de la vue
            echo $this->getTwig()->render('liste_quizs.twig', [
                'quizs' => $quizs,
                'title' => 'Liste des Quiz'
            ]);
        }

        public function creer() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo "Méthode non autorisée.";
                return;
            }

            // Récupération des données du formulaire
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $choixMultiples = isset($_POST['choix_multiples']) ? (bool)$_POST['choix_multiples'] : false;
            $idQuestion = isset($_POST['id_question']) ? (int)$_POST['id_question'] : 1;
            $idPost = isset($_POST['id_post']) ? (int)$_POST['id_post'] : null;

            if (empty($titre) || $idPost === null) {
                echo "Le titre et l'ID du post sont requis.";
                return;
            }

            // Création de l'objet Quiz
            $quiz = new Quiz(null, $titre, $description, $choixMultiples, $idQuestion, $idPost);

            // Insertion dans la base de données
            $managerQuiz = new QuizDao($this->getPdo());
            $succes = $managerQuiz->insererQuiz($quiz);

            if ($succes) {
                header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());    // à modifier en fonction de ce qu'on met
                exit;
            } else {
                throw new Exception("Erreur lors de la création du quiz.");
            }
        }

        public function ajouterQuestion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo "Méthode non autorisée.";
                return;
            }

            $idQuiz = isset($_POST['id_quiz']) ? (int)$_POST['id_quiz'] : 0;
            $libelleQuestion = trim($_POST['libelle_question'] ?? '');

            if ($idQuiz === 0 || $libelleQuestion === '') {
                echo "L'identifiant du quiz et le libellé de la question sont requis.";
                return;
            }

            $quizDao = new QuizDao($this->getPdo());
            $quiz = $quizDao->find($idQuiz);

            if (!$quiz) {
                echo "Quiz introuvable.";
                return;
            }

            $questionDao = new QuestionDAO($this->getPdo());
            $question = new Question(null, $libelleQuestion);

            $created = $questionDao->createQuestion($question);
            if (!$created || $question->getIdQuestion() === null) {
                throw new Exception("Erreur lors de la création de la question.");
            }

            // Associer la nouvelle question au quiz et sauvegarder
            $quiz->setIdQuestion($question->getIdQuestion());
            $quizDao->mettreAJourQuiz($quiz);

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }

        public function ajouterReponse() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo "Méthode non autorisée.";
                return;
            }

            $idQuiz = isset($_POST['id_quiz']) ? (int)$_POST['id_quiz'] : 0;
            $idQuestion = isset($_POST['id_question']) ? (int)$_POST['id_question'] : 0;
            $libelle = trim($_POST['libelle_reponse'] ?? '');

            $estCorrecte = isset($_POST['est_correcte']) ? (bool)$_POST['est_correcte'] : false;

            if ($idQuiz === 0 || $idQuestion === 0 || $libelle === '') {
                echo "Les champs id_quiz, id_question et libellé sont requis.";
                return;
            }

            $quizDao = new QuizDao($this->getPdo());
            $quiz = $quizDao->find($idQuiz);
            if (!$quiz) {
                echo "Quiz introuvable.";
                return;
            }

            $questionDao = new QuestionDAO($this->getPdo());
            $question = $questionDao->findByIdQuestion($idQuestion);
            if (!$question) {
                echo "Question introuvable.";
                return;
            }

            $reponse = new ReponsePossible(null, $libelle, $estCorrecte);

            $ok = $questionDao->addReponseToQuestion($idQuestion, $reponse);
            if (!$ok) {
                throw new Exception("Erreur lors de l'ajout de la réponse.");
            }

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }

        public function supprimerReponse() {
            $idQuiz = isset($_REQUEST['id_quiz']) ? (int)$_REQUEST['id_quiz'] : 0;
            $idQuestion = isset($_REQUEST['id_question']) ? (int)$_REQUEST['id_question'] : 0;
            $idReponsePossible = isset($_REQUEST['id_reponse_possible']) ? (int)$_REQUEST['id_reponse_possible'] : 0;

            if ($idQuiz === 0 || $idQuestion === 0 || $idReponsePossible === 0) {
                echo "Les identifiants de quiz, question et réponse sont requis.";
                return;
            }

            $quizDao = new QuizDao($this->getPdo());
            $quiz = $quizDao->find($idQuiz);
            if (!$quiz) {
                echo "Quiz introuvable.";
                return;
            }

            $questionDao = new QuestionDAO($this->getPdo());
            $question = $questionDao->findByIdQuestion($idQuestion);
            if (!$question) {
                echo "Question introuvable.";
                return;
            }

            // Supprime l'association LISTER entre la question et la réponse possible
            $listerDao = new ListerDAO($this->getPdo());
            $listerDao->supprimerLister($idReponsePossible, $idQuestion);

            // Si la réponse possible n'est plus associée à aucune question, on la supprime
            $assocsRemaining = $listerDao->findByReponsePossible($idReponsePossible);
            if (count($assocsRemaining) === 0) {
                $questionDao->supprimerReponsePossible($idReponsePossible);
            }

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }

        public function supprimer() {
            $idQuiz = isset($_GET['idQuiz']) ? (int)$_GET['idQuiz'] : 0;

            if ($idQuiz === 0) {
                header('Location: index.php?controleur=quiz&methode=lister');
                exit;
            }

            // Récupérer le quiz pour obtenir l'idPost associé
            $managerQuiz = new QuizDao($this->getPdo());
            $quiz = $managerQuiz->find($idQuiz);

            if (!$quiz) {
                header('Location: index.php?controleur=quiz&methode=lister');
                exit;
            }

            // Supprimer d'abord le quiz
            $managerQuiz->supprimerQuiz($idQuiz);

            // Puis supprimer le post associé
            $managerPost = new PostDao($this->getPdo());
            $managerPost->supprimerPost($quiz->getIdPost());

            header('Location: index.php?controleur=post&methode=lister');
            exit;
        }

        public function supprimerQuestion() {
            // Accepte GET ou POST
            $idQuiz = isset($_REQUEST['id_quiz']) ? (int)$_REQUEST['id_quiz'] : 0;
            $idQuestion = isset($_REQUEST['id_question']) ? (int)$_REQUEST['id_question'] : 0;

            if ($idQuiz === 0 || $idQuestion === 0) {
                echo "Les identifiants de quiz et de question sont requis.";
                return;
            }

            $quizDao = new QuizDao($this->getPdo());
            $quiz = $quizDao->find($idQuiz);
            if (!$quiz) {
                echo "Quiz introuvable.";
                return;
            }

            $questionDao = new QuestionDAO($this->getPdo());
            $question = $questionDao->findByIdQuestion($idQuestion);
            if (!$question) {
                echo "Question introuvable.";
                return;
            }

            // Supprimer les associations LISTER pour cette question
            $listerDao = new ListerDAO($this->getPdo());
            $assocs = $listerDao->findByQuestion($idQuestion);
            foreach ($assocs as $assoc) {
                $listerDao->supprimerLister($assoc->getIdReponsePossible(), $assoc->getIdQuestion());
            }

            // Si le quiz référence cette question, l'enlever (mettre un fallback à 0)
            if ($quiz->getIdQuestion() === $idQuestion) {
                $quiz->setIdQuestion(0);
                $quizDao->mettreAJourQuiz($quiz);
            }

            // Supprimer la question
            $ok = $questionDao->supprimerQuestion($idQuestion);
            if (!$ok) {
                throw new Exception("Erreur lors de la suppression de la question.");
            }

            // Retour à l'affichage du quiz
            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }
    }