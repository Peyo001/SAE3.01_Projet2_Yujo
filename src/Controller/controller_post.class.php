<?php

class ControllerPost extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    public function lister(): void
    {
        $manager = new PostDao();

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

        $manager = new PostDao();
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
        
        $idAuteur = $_SESSION['user_id']; 
        if (empty($contenu)) {
             echo "Le contenu ne peut pas être vide."; 
             return;
        }

        $post = new Post();
        $post->setContenu($contenu);
        $post->setTypePost($typePost);
        $post->setIdAuteur($idAuteur);
        $post->setIdRoom($idRoom);
        
        $post->setDatePublication(date('Y-m-d H:i:s'));

        $manager = new PostDao();
        // Note : On n'a pas mis d'ID, c'est la BDD qui va le créer (Auto Increment)
        $succes = $manager->createPost($post);

        if ($succes) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du post.");
        }
    }
}