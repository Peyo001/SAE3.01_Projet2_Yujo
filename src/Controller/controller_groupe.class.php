<?php

class ControllerGroupe extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }


    public function lister(): void
    {
        $manager = new GroupeDao();
        $groupes = $manager->findAll();

        echo $this->getTwig()->render('liste_groupes.twig', [
            'groupes' => $groupes,
            'title' => 'Les Groupes'
        ]);
    }

    public function afficher(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $manager = new GroupeDao();
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

        $manager = new GroupeDao();

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
     * Permet de rejoindre un groupe existant
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

        $manager = new GroupeDao();
        $groupe = $manager->find($idGroupe);

        if ($groupe) {
            $manager->AjouterMembre($groupe, $idUtilisateur, $dateAjout);
        }

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }
}