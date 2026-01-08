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
            $idPost = isset($_POST['id_post']) ? (int)$_POST['id_post'] : null;
            $libelleQuestion = trim($_POST['libelle_question'] ?? '');

            if (empty($titre) || $idPost === null || $libelleQuestion === '') {
                echo "Le titre, l'ID du post et le libellé de la question sont requis.";
                return;
            }

            // Création de l'objet Quiz (la question sera associée après sa création)
            $quiz = new Quiz(null, $titre, $description, $choixMultiples, 0, $idPost);

            // Insertion du quiz
            $managerQuiz = new QuizDao($this->getPdo());
            $succesQuiz = $managerQuiz->insererQuiz($quiz);
            if (!$succesQuiz) {
                throw new Exception("Erreur lors de la création du quiz.");
            }

            // Création et association de la question au quiz
            $questionDao = new QuestionDAO($this->getPdo());
            $question = new Question(null, $libelleQuestion);
            $creationQuestion = $questionDao->createQuestion($question);
            if (!$creationQuestion || $question->getIdQuestion() === null) {
                throw new Exception("Erreur lors de la création de la question.");
            }

            // Associer la question au quiz et sauvegarder
            $quiz->setIdQuestion($question->getIdQuestion());
            $managerQuiz->mettreAJourQuiz($quiz);

            // Redirection vers l'affichage du quiz
            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }

        //Je ne pense pas qu'elle serve à quelque chose puisque que la question est ajouté lors de la création du quiz et ne peut pas être supprimée
        /*public function ajouterQuestion() {
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

            $creationQuestion = $questionDao->createQuestion($question);
            if (!$creationQuestion || $question->getIdQuestion() === null) {
                throw new Exception("Erreur lors de la création de la question.");
            }

            // Associer la nouvelle question au quiz et sauvegarder
            $quiz->setIdQuestion($question->getIdQuestion());
            $quizDao->mettreAJourQuiz($quiz);

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }*/

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

            $ajoutReponse = $questionDao->addReponseToQuestion($idQuestion, $reponse);
            if (!$ajoutReponse) {
                throw new Exception("Erreur lors de l'ajout de la réponse.");
            }

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }

        // Je pense que cette fonction n'est pas utile car après l'ajout des réponses et la mise en ligne du quiz, on ne peut plus modifier les réponses, sauf pendant l'ajout des réponses dans le formulaire de créatiuon du quiz
        /*public function supprimerReponse() {
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
            $listeReponses = $listerDao->findByReponsePossible($idReponsePossible);
            if (count($listeReponses) === 0) {
                $questionDao->supprimerReponsePossible($idReponsePossible);
            }

            header('Location: index.php?controleur=quiz&methode=afficher&idQuiz=' . $quiz->getIdQuiz());
            exit;
        }*/

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

        // Je pense que c'est une méthode qui doit être présente QuestionDao et non dans le controller_quiz
        /*public function supprimerQuestion() {
            // Règle métier: une question ne peut pas être supprimée seule.
            // Inviter à supprimer le quiz pour supprimer la question.
            echo "Suppression de question interdite. Supprimez le quiz associé.";
        }*/
    }