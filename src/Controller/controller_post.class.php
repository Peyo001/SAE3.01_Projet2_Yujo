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

}