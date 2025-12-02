<?php
/**
 * Classe ControllerGroupe
 * 
 * Cette classe gère les actions liées aux groupes, telles que la création, l'affichage et la gestion des membres.
 * Elle étend la classe de base Controller pour bénéficier des fonctionnalités communes.
 * 
 * Exemple d'utilisation :
 * $controllerGroupe = new ControllerGroupe($loader, $twig);
 * $controllerGroupe->lister();
 * 
 */
class ControllerGroupe extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /** 
     * Liste tous les groupes.
     * 
     * Cette méthode récupère tous les groupes de la base de données en utilisant le DAO `GroupeDao`
     * et rend la vue `liste_groupes.twig` avec les groupes à afficher.
     */
    public function lister(): void
    {   
        $manager = new GroupeDao($this->getPdo());
        $groupes = $manager->findAll();

        echo $this->getTwig()->render('liste_groupes.twig', [
            'groupes' => $groupes,
            'title' => 'Les Groupes'
        ]);
    }

    /** 
     * Affiche les détails d'un groupe spécifique.
     * 
     * Cette méthode récupère un groupe par son identifiant et rend la vue `groupe.twig` avec les détails du groupe.
     */
    public function afficher(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $manager = new GroupeDao($this->getPdo());
        $groupe = $manager->find($id);

        if (!$groupe) {
            echo "Ce groupe n'existe pas.";
            return;
        }

        echo $this->getTwig()->render('groupe.twig', [
            'groupe' => $groupe,
            'user_connected' => $_SESSION['idUtilisateur'] ?? null
        ]);
    }

    /** 
     * Affiche le formulaire de création d'un nouveau groupe.
     * 
     * Cette méthode rend la vue `ajout_groupe.twig` pour permettre à l'utilisateur de créer un nouveau groupe.
     */
    public function afficherFormulaireCreation(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        echo $this->getTwig()->render('ajout_groupe.twig', [
            'menu' => 'nouveau_groupe'
        ]);
    }


    /** 
     * Traite le formulaire de création d'un nouveau groupe.
     * 
     * Cette méthode récupère les données du formulaire, crée un nouvel objet Groupe,
     * l'enregistre dans la base de données et redirige vers la liste des groupes.
     */
    public function traiterFormulaireCreation(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $nom = trim($_POST['nom_groupe'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $dateCreation = date('Y-m-d H:i:s');
        $idCreateur = $_SESSION['idUtilisateur'];

        if (empty($nom)) {
            echo "Le nom du groupe ne peut pas être vide.";
            return;
        }

        $groupe = new Groupe(null, $nom, $description, $dateCreation, []);

        $manager = new GroupeDao($this->getPdo());

        $succes = $manager->EnregistrerGroupe($groupe);

        if ($succes) {
            if ($groupe->getIdGroupe() !== null) {
                 $manager->AjouterMembre($groupe, $idCreateur, $dateCreation);
            }

            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du groupe.");
        }
    }

    /**
     * Permet à un utilisateur de rejoindre un groupe.
     * 
     * Cette méthode ajoute l'utilisateur connecté en tant que membre du groupe spécifié
     * et redirige vers la page du groupe.
     */
    public function rejoindre(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idGroupe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $idUtilisateur = $_SESSION['idUtilisateur'];
        $dateAjout = date('Y-m-d H:i:s');

        if ($idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $manager = new GroupeDao($this->getPdo());
        $groupe = $manager->find($idGroupe);

        if ($groupe) {
            $manager->AjouterMembre($groupe, $idUtilisateur, $dateAjout);
        }

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }
}