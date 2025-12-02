<?php
/**
 * Contrôleur pour la gestion des signalements.
 * 
 * Cette classe gère les actions liées aux signalements, telles que l'affichage,
 * la création, la modification et la suppression des signalements.
 * 
 */
class ControllerSignalement extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * Liste tous les signalements.
     * 
     * Cette méthode récupère tous les signalements de la base de données en utilisant le DAO `SignalementDao`
     * et rend la vue `liste_signalement.twig` avec les signalements à afficher.    
     * 
     * @return void
     */
    public function lister(): void
    {
        $manager = new SignalementDao($this->getPdo());
        $signalements = $manager->findAll();

        echo $this->getTwig()->render('liste_signalement.twig', [
            'signalements' => $signalements,
            'title' => 'Liste des signalements'
        ]);
    }

    /**
     * Affiche un signalement spécifique.
     * 
     * Cette méthode affiche un signalement spécifique en récupérant son identifiant (`id`) passé dans l'URL.
     * Si le signalement est trouvé, la vue `signalement.twig` est rendue avec les détails du signalement.
     * 
     * @return void
     */
    public function afficher(): void
    {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controleur=signalement&methode=lister');
            exit;
        }

        $manager = new SignalementDao($this->getPdo());
        $signalement = $manager->find($_GET['id']);

        if (!$signalement) {
            echo "Signalement introuvable.";
            return;
        }

        echo $this->getTwig()->render('signalement.twig', [
            'signalement' => $signalement,
            'title' => 'Détails du signalement'
        ]);
    }

    /**
     * Affiche le formulaire d'insertion d'un nouveau signalement.
     * 
     * Cette méthode affiche le formulaire permettant à l'utilisateur d'ajouter un nouveau signalement.
     * 
     * @return void
     */
    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_signalement.twig', [
            'title' => 'Créer un signalement'
        ]);
    }

    /**
     * Traitement du formulaire d'insertion d'un nouveau signalement.
     * 
     * Cette méthode traite les données soumises via le formulaire d'insertion de signalement.
     * Elle crée un nouvel objet `Signalement`, l'enregistre dans la base de données
     * 
     * @return void
     */
    public function traiterFormulaireInsertion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=signalement&methode=afficherFormulaireInsertion');
            exit;
        }

        $raison = trim($_POST['raison'] ?? '');
        $idPost = $_POST['id_post'] ?? null;

        if (empty($raison)) {
            echo "La raison ne peut pas être vide.";
            return;
        }

        $signalement = new Signalement($idPost, $raison);

        $manager = new SignalementDao($this->getPdo());
        $succes = $manager->insert($signalement);

        if ($succes) {
            header('Location: index.php?controleur=signalement&methode=lister');
            exit;
        } else {
            echo "Erreur lors de la création du signalement.";
        }
    }
}
