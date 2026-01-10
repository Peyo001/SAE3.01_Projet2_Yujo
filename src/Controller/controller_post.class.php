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

        // Définition des règles de validation
        $reglesValidation = [
            'contenu' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 1,
                'longueur_max' => 5000
            ],
            'type_post' => [
                'obligatoire' => false,
                'type' => 'string',
                'valeurs_acceptables' => ['texte', 'image', 'lien']
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

        $contenu = trim($_POST['contenu']);
        $typePost = $_POST['type_post'] ?? 'texte'; 
        $idRoom = (int)($_POST['id_room'] ?? 1); 
        
        if (isset($_SESSION['idUtilisateur'])) {
            $idAuteur = (int) $_SESSION['idUtilisateur'];
        } else {
            $idAuteur = 1; 
        }

        $post = new Post(null, $contenu, $typePost, date('Y-m-d H:i:s'), $idAuteur, $idRoom);

        $manager = new PostDao($this->getPdo());
        $succes = $manager->insererPost($post);

        if ($succes) {
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