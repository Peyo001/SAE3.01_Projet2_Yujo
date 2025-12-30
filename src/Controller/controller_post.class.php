<?php

class ControllerPost extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    public function lister(): void
    {
        $manager = new PostDao($this->getPdo());

        if (isset($_GET['id_auteur'])) {
            $posts = $manager->findPostsByAuteur($_GET['id_auteur']);
        } else {
            $posts = $manager->findAll();
        }

        echo $this->getTwig()->render('liste_posts.twig', [
            'posts' => $posts,
            'title' => 'Fil d\'actualité'
        ]);
    }

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


    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_post.twig', [
            'menu' => 'nouveau_post'
        ]);
    }

    public function traiterFormulaireInsertion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=post&methode=afficherFormulaireInsertion');
            exit;
        }

        $contenu = trim($_POST['contenu'] ?? '');
        $typePost = $_POST['type_post'] ?? 'texte'; 
        $idRoom = (int)($_POST['id_room'] ?? 1); 
        
        if (isset($_SESSION['idUtilisateur'])) {
            $idAuteur = (int) $_SESSION['idUtilisateur'];
        } else {
            $idAuteur = 1; 
        }       
        if (empty($contenu)) {
            echo "Le contenu ne peut pas être vide."; 
            return;
        }

        $post = new Post(null, $contenu, $typePost, date('Y-m-d H:i:s'), $idAuteur, $idRoom);

        $manager = new PostDao($this->getPdo());
        $succes = $manager->insererPost($post);

        if ($succes) {
            // Si le type de post est "quiz", créer automatiquement le quiz associé
            if ($typePost === 'quiz') {
                $idPost = $post->getIdPost();
                
                // Récupérer les données du quiz depuis le formulaire
                $titreQuiz = trim($_POST['titre_quiz'] ?? 'Quiz sans titre');
                $descriptionQuiz = trim($_POST['description_quiz'] ?? '');
                $choixMultiples = isset($_POST['choix_multiples']) ? (bool)$_POST['choix_multiples'] : false;
                $idQuestion = isset($_POST['id_question']) ? (int)$_POST['id_question'] : 1;
                
                // Créer l'objet Quiz
                $quiz = new Quiz(null, $titreQuiz, $descriptionQuiz, $choixMultiples, $idQuestion, $idPost);
                
                // Insérer le quiz dans la base de données
                $managerQuiz = new QuizDao($this->getPdo());
                $managerQuiz->insererQuiz($quiz);
            }
            
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du post.");
        }
    }

    public function supprimer(): void
    {

        $idPost = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($idPost === 0) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        }

        $manager = new PostDao($this->getPdo());
        $manager->supprimerPost($idPost);


        header('Location: index.php?controleur=post&methode=lister');
        exit;
    }
}