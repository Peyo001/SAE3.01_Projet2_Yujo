<?php

class ControllerReponse extends Controller
{
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    public function lister(): void
    {
        $manager = new ReponseDao();

        if (isset($_GET['idReponse'])) {
            $reponses = $manager->find($_GET['idReponse']);
        } else {
            $reponses = $manager->findAll();
        }

        echo $this->getTwig()->render('liste_reponses.twig', [
            'reponses' => $reponses,
            'title' => 'Liste des réponses'
        ]);
    }

    public function afficher(): void
    {
        if (!isset($_GET['idReponse'])) {
            header('Location: index.php?controleur=reponse&methode=lister');
            exit;
        }

        $manager = new ReponseDao();
        $reponse = $manager->find($_GET['idReponse']);

        if (!$reponse) {
            echo "Réponse introuvable."; 
            return;
        }

        echo $this->getTwig()->render('reponse.twig', [
            'reponse' => $reponse
        ]);
    }
}