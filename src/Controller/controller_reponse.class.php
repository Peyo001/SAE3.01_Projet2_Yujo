<?php

/**
 * ControllerReponse gère les actions liées aux réponses comme
 * lister, afficher, ajouter une réponse.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controller = new ControllerReponse($loader, $twig);
 * $controller->lister(); // Affiche la liste des réponses
 * $controller->afficher(); // Affiche une réponse spécifique
 * $controller->afficherFormulaireInsertion(); // Affiche le formulaire d'ajout de réponse
 * $controller->traiterFormulaireInsertion(); // Traite l'ajout d'une réponse
 */
class ControllerReponse extends Controller
{
    /**
     * Constructeur de ControllerReponse
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates Twig
     * @param \Twig\Environment $twig Environnement Twig pour le rendu des vues
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * @brief Affiche la liste des réponses
     * 
     * Récupère toutes les réponses via le DAO et les passe au template Twig pour affichage.
     * 
     * @return void
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
     * @brief Affiche une réponse spécifique
     * 
     * Vérifie la présence de l'ID de la réponse dans les paramètres GET,
     * récupère la réponse via le DAO et la passe au template Twig pour affichage.
     * 
     * @return void
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
     * @brief Affiche le formulaire d'ajout d'une réponse
     * 
     * Rend le template Twig contenant le formulaire d'insertion.
     * 
     * @return void
     */

    public function afficherFormulaireInsertion(): void
    {
        echo $this->getTwig()->render('ajout_reponse.twig', [
            'title' => 'Nouvelle réponse'
        ]);
    }

    /**
     * @brief Traite le formulaire d'ajout d'une réponse
     * 
     * Valide les données reçues via POST, crée une nouvelle réponse,
     * l'insère via le DAO et redirige ou affiche un message d'erreur.
     * 
     * @return void
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