<?php
/** 
 * Class    ControllerReponse
 * 
 * Ce contrôleur gère les opérations liées aux réponses, telles que l'affichage de la liste des réponses,
 * l'affichage d'une réponse spécifique, l'insertion de nouvelles réponses.
 * 
 * Exemple d'utilisation :
 * $controllerReponse = new ControllerReponse($loader, $twig);  
 * $controllerReponse->lister();
 * $controllerReponse->afficher();
 */
class ControllerReponse extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * Cette méthode liste toutes les réponses.
     * 
     * Elle récupère toutes les réponses de la base de données en utilisant le DAO `ReponseDao`
     * et rend la vue `liste_reponse.twig` avec les réponses à afficher.
     * 
     * @return void
     */
    public function lister(): void
    {
        $dao = new ReponseDao();
        $reponses = $dao->findAll();

        echo $this->getTwig()->render('liste_reponse.twig', [
            'reponses' => $reponses,
            'title' => 'Liste des réponses'
        ]);
    }

    /**
     * Affiche une réponse spécifique.
     * 
     * Cette méthode affiche une réponse spécifique en récupérant son identifiant (`idReponse`) passé dans l'URL.
     * Si la réponse est trouvée, la vue `reponse.twig` est rendue avec les détails de la réponse.
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
     * Affiche le formulaire d'insertion d'une nouvelle réponse.
     * 
     * Cette méthode affiche le formulaire permettant à l'utilisateur d'ajouter une nouvelle réponse.
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
     * Traite le formulaire d'insertion d'une nouvelle réponse.
     * 
     * Cette méthode traite les données soumises via le formulaire d'insertion de réponse.
     * Elle crée un nouvel objet `Reponse`, l'enregistre dans la base de données via le DAO,
     * puis redirige vers la liste des réponses.
     * 
     * @return void
     */
    public function traiterFormulaireInsertion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'REPONSE') {
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
        $success = $manager->insert($reponse);

        if ($success) {
            header('Location: index.php?controleur=reponse&methode=lister');
            exit;
        } else {
            echo "Erreur lors de l'insertion de la réponse.";
        }
    }
}