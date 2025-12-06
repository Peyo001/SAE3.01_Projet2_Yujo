<?php

class ControllerReponse extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * LISTE DES RÉPONSES
     */
    public function lister(): void
    {
        $dao = new ReponseDao($this->getPdo());
        $reponses = $dao->findAll();

        echo $this->getTwig()->render('liste_reponse.twig', [
            'reponses' => $reponses,
            'title' => 'Liste des réponses'
        ]);
    }

    /**
     * AFFICHER UNE RÉPONSE
     */
    public function afficher(): void
    {
        if (!isset($_GET['idReponse'])) {
            header('Location: index.php?controleur=reponse&methode=lister');
            exit;
        }

        $manager = new ReponseDao($this->getPdo());
        $reponse = $manager->find($_GET['idReponse']);

        if (!$reponse) {
            echo "Réponse introuvable."; 
            return;
        }

        echo $this->getTwig()->render('reponse.twig', [
            'reponse' => $reponse
        ]);
    }

    /**
     * FORMULAIRE AJOUT RÉPONSE
     */

    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_reponse.twig', [
            'title' => 'Nouvelle réponse'
        ]);
    }

    /**
     * TRAITEMENT DU FORMULAIRE
     */
    public function traiterFormulaireInsertion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=reponse&methode=afficherFormulaireInsertion');
            exit;
        }

        $dateReponse = $_POST['dateReponse'] ?? null;
        $contenu = $_POST['contenu'] ?? null;
        $idAuteur = $_POST['idAuteur'] ?? null;
        $idPost = $_POST['idPost'] ?? null;

        if (!$dateReponse || !$contenu || !$idAuteur || !$idPost) {
            echo "Tous les champs sont requis.";
            return;
        }

        $reponse = new Reponse(null, $dateReponse, $contenu, $idAuteur, $idPost);
        $manager = new ReponseDao($this->getPdo());
        $success = $manager->insererReponse($reponse);

        if ($success) {
            header('Location: index.php?controleur=reponse&methode=lister');
            exit;
        } else {
            echo "Erreur lors de l'insertion de la réponse.";
        }
    }
}