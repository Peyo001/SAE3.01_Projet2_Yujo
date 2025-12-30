<?php
/**
 * ControllerGroupe gère les actions liées aux groupes comme
 * la liste des groupes, l'affichage d'un groupe, la création d'un groupe
 * et la possibilité de rejoindre un groupe.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controller = new ControllerGroupe($loader, $twig);
 * $controller->lister(); // Affiche la liste des groupes
 * $controller->afficher(); // Affiche un groupe spécifique
 * $controller->afficherFormulaireCreation(); // Affiche le formulaire de création de groupe
 * $controller->traiterFormulaireCreation(); // Traite la création d'un groupe
 * $controller->rejoindre(); // Permet de rejoindre un groupe
 */
class ControllerGroupe extends Controller
{
    /**
     * Constructeur de la classe ControllerGroupe
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }


    /**
     * @brief Affiche la liste de tous les groupes
     * 
     * Utilise la méthode findAll() de la classe GroupeDao pour récupérer tous les groupes
     * et rend la vue 'liste_groupes.twig' avec les données des groupes.
     * 
     * @return void
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
     * @brief Affiche un groupe spécifique
     * 
     * Récupère l'ID du groupe depuis les paramètres GET, vérifie son existence,
     * puis utilise la méthode find() de la classe GroupeDao pour récupérer le groupe.
     * Rend la vue 'groupe.twig' avec les données du groupe.
     * 
     * @return void
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
     * @brief Affiche le formulaire de création d'un nouveau groupe
     * 
     * Vérifie si l'utilisateur est connecté avant d'afficher le formulaire.
     * Sinon, redirige vers la page de connexion.
     * 
     * @return void
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
     * @brief Traite les données du formulaire de création d'un nouveau groupe
     * 
     * Vérifie la méthode de requête, l'authentification de l'utilisateur,
     * puis insère le nouveau groupe dans la base de données.
     * 
     * @return void
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

        $groupe = new Groupe($nom, $description, $dateCreation, [], null);

        $manager = new GroupeDao($this->getPdo());

        $succes = $manager->insererGroupe($groupe);

        if ($succes) {
            if ($groupe->getIdGroupe() !== null) {
                 $manager->ajouterMembre($groupe, $idCreateur, $dateCreation);
            }

            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du groupe.");
        }
    }

    /**
     * @brief Permet de rejoindre un groupe existant
     * 
     * Vérifie si l'utilisateur est connecté, récupère l'ID du groupe depuis les paramètres GET,
     * puis utilise la méthode ajouterMembre() de la classe GroupeDao pour ajouter l'utilisateur au groupe.
     * Redirige ensuite vers la page d'affichage du groupe.
     * 
     * @return void
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
            $manager->ajouterMembre($groupe, $idUtilisateur, $dateAjout);
        }

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }
}