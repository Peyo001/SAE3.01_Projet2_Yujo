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

        // Définition des règles de validation
        $reglesValidation = [
            'contenu' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 1,
                'longueur_max' => 5000
            ],
            'id_post' => [
                'obligatoire' => false,
                'type' => 'integer'
            ]
        ];

        $validator = new Validator($reglesValidation);
        $donneesValides = $validator->valider($_POST);
        $erreurs = $validator->getMessagesErreurs();

        if (!$donneesValides) {
            echo $this->getTwig()->render('ajout_reponse.twig', [
                'title' => 'Nouvelle réponse',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }

        $contenu = trim($_POST['contenu']);
        $idPost = $_POST['id_post'] ?? null;

        $reponse = new Reponse(null, $contenu, null, $idPost);

        $manager = new ReponseDao($this->getPdo());
        $succes = $manager->insererReponse($reponse);

        if ($succes) {
            header('Location: index.php?controleur=reponse&methode=lister');
            exit;
        } else {
            echo "Erreur lors de l'insertion de la réponse.";
        }
    }
}