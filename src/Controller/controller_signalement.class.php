<?php

/**
 * ControllerSignalement gère les actions liées aux signalements comme
 * l'affichage, la création et la liste des signalements.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controllerSignalement = new ControllerSignalement($loader, $twig);
 * $controllerSignalement->lister(); // affiche la liste des signalements
 * $controllerSignalement->afficher(); // affiche un signalement spécifique
 * $controllerSignalement->afficherFormulaireInsertion(); // affiche le formulaire d'ajout
 * $controllerSignalement->traiterFormulaireInsertion(); // traite le formulaire d'ajout
 */
class ControllerSignalement extends Controller
{
    /**
     * @brief Constructeur de la classe ControllerSignalement.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig.
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * @brief Liste des signalements.
     * 
     * Utilise SignalementDao pour récupérer tous les signalements.
     * Rend la vue 'liste_signalement.twig' avec les données des signalements.
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
     * @brief Affiche un signalement spécifique.
     * 
     * Récupère l'id du signalement depuis les paramètres GET.
     * Utilise SignalementDao pour récupérer les informations du signalement.
     * Rend la vue 'signalement.twig' avec les données du signalement.
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
     * @brief Affiche le formulaire d'ajout d'un signalement.
     * 
     * Rend la vue 'ajout_signalement.twig'.
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
     * @brief Traite le formulaire d'ajout d'un signalement.
     * 
     * Récupère les données du formulaire depuis $_POST.
     * Valide les données et crée un nouveau signalement via SignalementDao.
     * Redirige vers la liste des signalements après création.
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
