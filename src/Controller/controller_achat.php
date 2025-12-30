<?php

/**
 * Contrôleur pour la gestion des achats.
 * 
 * Utilise les classes métiers et DAO appropriées pour interagir avec la base de données
 * et afficher les vues correspondantes.
 * 
 * Exemple d'utilisation :
 * $controllerAchat = new ControllerAchat($loader, $twig);
 * $controllerAchat->lister();
 * $controllerAchat->afficher();
 */
class ControllerAchat extends Controller
{   
    /**
     * Constructeur du contrôleur des achats.
     * 
     * Initialise la classe `ControllerAchat` en passant les objets Twig `Environment` et `FilesystemLoader`
     * au constructeur de la classe parente `Controller`.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader L'objet loader pour la gestion des fichiers Twig.
     * @param \Twig\Environment $twig L'objet Twig pour le rendu des templates.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * Liste tous les achats.
     * 
     * Cette méthode récupère tous les achats de la base de données en utilisant le DAO `AchatDao`
     * et rend la vue `liste_achats.twig` avec les achats à afficher.
     * 
     * @return void
     */
    public function lister(): void
    {
        $manager = new AchatDao($this->getPdo());
        $achats = $manager->findAll();

        echo $this->getTwig()->render('liste_achats.twig', [
            'achats' => $achats,
            'title' => 'Liste des Achats'
        ]);
    }


    /** 
     * Liste les achats d'un utilisateur spécifique
     * 
     * Cette méthode récupère les achats associées a un utilisateur par son identifiant
     * et rend la vue `liste_achats.twig` avec les achats à afficher.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur dont on veut lister les achats.
     * @return void
     * 
     */
    public function listerParUtilisateur(int $idUtilisateur): void
    {
        $manager = new AchatDao($this->getPdo());
        $achats = $manager->findByUtilisateur($idUtilisateur);

        echo $this->getTwig()->render('liste_achats.twig', [
            'achats' => $achats,
            'title' => 'Mes Achats'
        ]);
    }

    /**
     * Affiche un achat spécifique.
     * 
     * Cette méthode affiche un achat spécifique en récupérant son identifiant (`id`) passé dans l'URL.
     * Si l'achat est trouvé, la vue `achat.twig` est rendue avec les détails de l'achat.
     * 
     * @return void
     */
    public function afficher(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            header('Location: index.php?controleur=achat&methode=lister');
            exit;
        }

        $manager = new AchatDao($this->getPdo());
        $achat = $manager->findByIdObjet($id);

        if (!$achat) {
            echo "Cet achat n'existe pas.";
            return;
        }

        echo $this->getTwig()->render('achat.twig', [
            'achat' => $achat,
            'user_connected' => $_SESSION['idUtilisateur'] ?? null
        ]);
    }
}