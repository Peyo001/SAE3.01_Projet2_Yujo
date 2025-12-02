<?php
/**
 * Class ControllerPost
 * 
 * Ce contrôleur gère les opérations liées aux posts, telles que l'affichage de la liste des posts,
 * l'affichage d'un post spécifique, l'insertion de nouveaux posts et la suppression de
 * 
 * Exemple d'utilisation :
 * $controllerPost = new ControllerPost($loader, $twig);
 * $controllerPost->lister();
 * $controllerPost->afficher();
 */
class ControllerPost extends Controller
{
    /**
     * Constructeur du contrôleur des posts.
     * 
     * Initialise la classe `ControllerPost` en passant les objets Twig `Environment` et `FilesystemLoader`
     * au constructeur de la classe parente `Controller`.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader L'objet loader pour la gestion des fichiers Twig.
     * @param \Twig\Environment $twig L'objet Twig pour le rendu des templates.
     */
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

    /**
     * Affiche un post spécifique.
     * 
     * Cette méthode affiche un post spécifique en récupérant son identifiant (`id`) passé dans l'URL.
     * Si le post est trouvé, la vue `post.twig` est rendue avec les
     * détails du post.
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
     * Affiche le formulaire d'insertion d'un nouveau post.
     * 
     * Cette méthode affiche le formulaire permettant à l'utilisateur d'ajouter un nouveau post.
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
     * Traite le formulaire d'insertion d'un nouveau post.
     * 
     * Cette méthode traite les données soumises via le formulaire d'insertion de post.
     * Elle crée un nouvel objet `Post`, l'enregistre dans la base de données via le DAO,
     * puis redirige vers la liste des posts.
     * 
     * @return void
     * @throws Exception Si une erreur survient lors de la création du post.
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

        $post = new Post();
        $post->setContenu($contenu);
        $post->setTypePost($typePost);
        $post->setIdAuteur($idAuteur);
        $post->setIdRoom($idRoom);
        
        $post->setDatePublication(date('Y-m-d H:i:s'));

        $manager = new PostDao($this->getPdo());
        $succes = $manager->createPost($post);

        if ($succes) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du post.");
        }
    }


    /**
     * Supprime un post spécifique.
     * 
     * Cette méthode supprime un post spécifique en récupérant son identifiant (`id`) passé dans l'URL.
     * Après la suppression, elle redirige vers la liste des posts.
     * 
     * @return void
     * 
     */
    public function supprimer(): void
    {

        $idPost = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($idPost === 0) {
            header('Location: index.php?controleur=post&methode=lister');
            exit;
        }

        $manager = new PostDao($this->getPdo());
        $manager->deletePost($idPost);


        header('Location: index.php?controleur=post&methode=lister');
        exit;
    }
}