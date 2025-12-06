<?php

class ControllerSignalement extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * LISTE DES SIGNALEMENTS
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
     * AFFICHER UN SIGNALEMENT
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
     * FORMULAIRE AJOUT SIGNALEMENT
     */
    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_signalement.twig', [
            'title' => 'Créer un signalement'
        ]);
    }

    /**
     * TRAITEMENT DU FORMULAIRE
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

        $signalement = new Signalement(null, $raison);

        $manager = new SignalementDao($this->getPdo());
        $succes = $manager->insererSignalement($signalement);

        if ($succes) {
            header('Location: index.php?controleur=signalement&methode=lister');
            exit;
        } else {
            echo "Erreur lors de la création du signalement.";
        }
    }
}
