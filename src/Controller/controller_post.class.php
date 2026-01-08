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


        header('Location: index.php?controleur=post&methode=lister');
        exit;
    }
}