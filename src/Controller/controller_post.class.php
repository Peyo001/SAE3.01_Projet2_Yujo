<?php

/**
 * ControllerPost gère les actions liées aux posts comme
 * la liste des posts, l'affichage d'un post, la création et la suppression de posts.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controllerPost = new ControllerPost($loader, $twig);
 * $controllerPost->lister(); // Affiche la liste des posts
 * $controllerPost->afficher(); // Affiche un post spécifique
 * $controllerPost->afficherFormulaireInsertion(); // Affiche le formulaire de création de post
 * $controllerPost->traiterFormulaireInsertion(); // Traite la création d'un post
 * $controllerPost->supprimer(); // Supprime un post
 */
class ControllerPost extends Controller
{
    /**
     * Constructeur de la classe ControllerPost
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * @brief Liste tous les posts ou les posts d'un auteur spécifique
     * 
     * Utilise la méthode findAll() ou findPostsByAuteur() de la classe PostDao pour récupérer les posts
     * et rend la vue 'liste_posts.twig' avec les données des posts.
     * 
     * @return void
     */
    public function lister(): void
    {
        $manager = new PostDao($this->getPdo());
        $userManager = new UtilisateurDao($this->getPdo());
        $quizDao = new QuizDao($this->getPdo());

        if (isset($_GET['id_auteur'])) {
            $posts = $manager->findPostsByAuteur($_GET['id_auteur']);
        } else {
            $posts = $manager->findAll();
        }
        
        // Récupérer les quiz associés aux posts de type quiz
        $quizParPost = [];
        foreach ($posts as $post) {
            if ($post->getTypePost() === 'quiz') {
                $quiz = $quizDao->findByPost($post->getIdPost());
                if ($quiz) {
                    $quizParPost[$post->getIdPost()] = $quiz;
                }
            }
        }

        // Récupérer tous les utilisateurs pour afficher les noms des auteurs
        $utilisateursList = $userManager->findAll();
        $utilisateurs = [];
        foreach ($utilisateursList as $u) {
            $utilisateurs[$u->getIdUtilisateur()] = $u;
        }

        echo $this->getTwig()->render('liste_posts.twig', [
            'posts' => $posts,
            'utilisateurs' => $utilisateurs,
            'quizParPost' => $quizParPost,
            'title' => 'Fil d\'actualité'
        ]);
    }

    /**
     * @brief Affiche un post spécifique
     * 
     * Récupère l'ID du post depuis les paramètres GET, vérifie son existence,
     * puis utilise la méthode find() de la classe PostDao pour récupérer le post.
     * Rend la vue 'post.twig' avec les données du post.
     * 
     * @return void
     */
    public function afficher(): void
    {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        }

        $manager = new PostDao($this->getPdo());
        $post = $manager->find($_GET['id']);

        if (!$post) {
            echo "Post introuvable."; 
            return;
        }

        echo $this->getTwig()->render('post.twig', [
            'post' => $post
        ]);
    }

    /**
     * @brief Affiche le formulaire d'insertion d'un nouveau post
     * 
     * Rends la vue 'ajout_post.twig' pour permettre à l'utilisateur de créer un nouveau post.
     * 
     * @return void
     */
    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_post.twig', [
            'menu' => 'nouveau_post'
        ]);
    }

    /**
     * @brief Traite le formulaire d'insertion d'un nouveau post
     * 
     * Récupère les données du formulaire, crée un objet Post,
     * utilise la méthode insererPost() de la classe PostDao pour insérer le post dans la base de données,
     * puis redirige vers la liste des posts.
     * 
     * @return void
     * @throws Exception Si une erreur survient lors de la création du post
     */
    public function traiterFormulaireInsertion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=post&methode=afficherFormulaireInsertion');
            exit;
        }

        // Définition des règles de validation
        $reglesValidation = [
            'contenu' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_min' => 0,
                'longueur_max' => 5000
            ],
            'type_post' => [
                'obligatoire' => true,
                'type' => 'string',
                'valeurs_acceptables' => ['post', 'quiz']
            ],
            'id_room' => [
                'obligatoire' => false,
                'type' => 'integer'
            ]
        ];

        $validator = new Validator($reglesValidation);
        $donneesValides = $validator->valider($_POST);
        $erreurs = $validator->getMessagesErreurs();

        if (!$donneesValides) {
            echo $this->getTwig()->render('ajout_post.twig', [
                'menu' => 'nouveau_post',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }

        $typePost = $this->sanitize($_POST['type_post'] ?? 'post'); 
        $contenu = trim($_POST['contenu'] ?? '');
        // Sanitize le contenu seulement s'il n'est pas une image
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $contenu = $this->sanitize($contenu);
        }
        $idRoom = (int)($_POST['id_room'] ?? 1); 
        
        if (isset($_SESSION['idUtilisateur'])) {
            $idAuteur = (int) $_SESSION['idUtilisateur'];
        } else {
            $idAuteur = 1; 
        }

        // Stocker le contenu texte original avant de le remplacer potentiellement par une image
        $contenuTexte = $contenu;

        // Gestion spécifique selon type
        if ($typePost === 'post') {
            // Facultatif: texte OU image, au moins l'un des deux
            if ((isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK)) {
                $tmp = $_FILES['image']['tmp_name'];
                $name = $_FILES['image']['name'];
                $size = (int)$_FILES['image']['size'];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (!in_array($ext, $allowed)) {
                    $erreurs[] = "Format d'image non supporté (jpg, png, gif, webp).";
                } elseif ($size > 5 * 1024 * 1024) {
                    $erreurs[] = "Image trop lourde (max 5MB).";
                } else {
                    $targetDir = __DIR__ . '/../../public/uploads/posts';
                    $unique = uniqid('post_', true) . '.' . $ext;
                    $targetPath = $targetDir . '/' . $unique;
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0775, true);
                    }
                    if (move_uploaded_file($tmp, $targetPath)) {
                        // chemin web relatif
                        $contenu = 'uploads/posts/' . $unique;
                    } else {
                        $erreurs[] = "Échec de l'envoi de l'image.";
                    }
                }
            }
            if ($contenu === '' && !(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK)) {
                $erreurs[] = "Veuillez écrire un contenu ou ajouter une image.";
            }
        }

        // Si erreurs, afficher le formulaire
        if (!empty($erreurs)) {
            echo $this->getTwig()->render('ajout_post.twig', [
                'menu' => 'nouveau_post',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }

        if ($typePost === 'post'){
            // Créer le post (contenu est soit texte/lien, soit chemin d'image)
            $post = new Post(null, $contenu, $typePost, date('Y-m-d H:i:s'), $idAuteur, $idRoom);
            $manager = new PostDao($this->getPdo());
            $succes = $manager->insererPost($post);
            
            if ($succes) {
                header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
                exit;
            } else {
                throw new Exception("Erreur lors de la création du post.");
            }
        }

        elseif ($typePost === 'quiz'){
            // Si le type de post est "quiz", créer la question et ses réponses puis le quiz associé (SANS post)
            
            $managerQuiz = new QuizDao($this->getPdo());

            $titreQuiz = trim($_POST['titre_quiz'] ?? 'Quiz sans titre');
            $descriptionQuiz = trim($_POST['description_quiz'] ?? '');
            $choixMultiples = isset($_POST['choix_multiples']) ? (bool)$_POST['choix_multiples'] : false;

            $idQuestion = 0;
            $questionLibelle = trim($_POST['question_libelle'] ?? '');
            $reponses = isset($_POST['reponses']) && is_array($_POST['reponses']) ? $_POST['reponses'] : [];

            // Validation minimale côté serveur pour le quiz
            $validAnswers = [];
            $correctCount = 0;
            foreach ($reponses as $r) {
                $lib = isset($r['libelle']) ? trim($r['libelle']) : '';
                if ($lib !== '') {
                    $isCorrect = !empty($r['correct']);
                    $validAnswers[] = [ 'libelle' => $lib, 'correct' => $isCorrect ];
                    if ($isCorrect) { $correctCount++; }
                }
            }
            if ($questionLibelle === '') {
                $erreurs[] = "La question du quiz est obligatoire.";
            }
            if (count($validAnswers) < 2) {
                $erreurs[] = "Indique au moins 2 réponses pour le quiz.";
            }
            if (!$choixMultiples && $correctCount !== 1) {
                $erreurs[] = "Sélectionne exactement une bonne réponse (désactive choix multiples).";
            }

            if (!empty($erreurs)) {
                echo $this->getTwig()->render('ajout_post.twig', [
                    'menu' => 'nouveau_post',
                    'erreurs' => $erreurs,
                    'donnees' => $_POST
                ]);
                exit;
            }

            // Créer la question
            $managerQuestion = new QuestionDAO($this->getPdo());
            $question = new Question(null, $questionLibelle);
            if ($managerQuestion->createQuestion($question)) {
                $idQuestion = (int)$question->getIdQuestion();
            }

            if ($idQuestion === 0) {
                $erreurs[] = "Impossible de créer la question du quiz.";
                echo $this->getTwig()->render('ajout_post.twig', [
                    'menu' => 'nouveau_post',
                    'erreurs' => $erreurs,
                    'donnees' => $_POST
                ]);
                exit;
            }

            // Ajouter les réponses possibles
            if ($idQuestion > 0) {
                foreach ($validAnswers as $ans) {
                    $rp = new ReponsePossible(null, $ans['libelle'], (bool)$ans['correct']);
                    $managerQuestion->addReponseToQuestion($idQuestion, $rp);
                }
            }

            // Créer le quiz SANS post (idPost = null)
            $quiz = new Quiz(null, $titreQuiz, $descriptionQuiz, $choixMultiples, $idQuestion, null);
            $managerQuiz->insererQuiz($quiz);
            
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }
    }

    /**
     * Affiche le quiz associé à un post (jouer au quiz).
     */
    public function afficherQuiz(): void
    {
        $idPost = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($idPost === 0) {
            header('Location: index.php?controleur=accueil&methode=afficher');
            exit;
        }

        $quizDao = new QuizDao($this->getPdo());
        $quiz = $quizDao->findByPost($idPost);
        if (!$quiz) {
            header('Location: index.php?controleur=post&methode=afficher&id=' . $idPost);
            exit;
        }

        $question = null;
        $reponses = [];
        if ($quiz->getIdQuestion()) {
            $questionDao = new QuestionDAO($this->getPdo());
            $question = $questionDao->findByIdQuestion($quiz->getIdQuestion());
            if ($question) {
                $reponses = $question->getReponses();
            }
        }

        echo $this->getTwig()->render('quiz_play.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'reponses' => $reponses
        ]);
    }

    /**
     * @brief Supprime un post spécifique
     * 
     * Récupère l'ID du post depuis les paramètres GET, vérifie son existence,
     * utilise la méthode supprimerPost() de la classe PostDao pour supprimer le post de la base de données,
     * puis redirige vers la liste des posts.
     * 
     * @return void
     */
    public function supprimer(): void
    {

        $idPost = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($idPost === 0) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        }

        $manager = new PostDao($this->getPdo());
        $manager->supprimerPost($idPost);

        // Déterminer la redirection : profil ou fil d'actualité
        $redirect = $_GET['redirect'] ?? 'lister';
        if ($redirect === 'profil') {
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
        } else {
            header('Location: index.php?controleur=post&methode=lister');
        }
        exit;
    }
}