<?php
class ControllerAccueil extends Controller
{
    public function __construct($loader, $twig)
    {
        parent::__construct($loader, $twig);
    }

    public function afficher(): void
    {
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? 0;

        $userManager = new UtilisateurDao($this->getPdo());
        $amiManager  = new AmiDao($this->getPdo());
        $postManager = new PostDao($this->getPdo());

        // --- Récupérer les amis ---
        $amis = $amiManager->findAmis($idUtilisateur); // tableau d'objets Ami

        // --- Construire tableau d'IDs pour récupérer uniquement les posts des amis ---
        $idsAmis = [];
        foreach ($amis as $ami) {
            $idsAmis[] = $ami->getIdUtilisateur2(); // ou la propriété correspondant à l'id de l'ami
        }

        // --- Récupérer uniquement les posts des amis ---
        $posts = $postManager->findByAuteurs($idsAmis);

        // --- Tableau id => pseudo pour Twig ---
        $utilisateurs = $userManager->findAll(); 

        echo $this->getTwig()->render('pageAccueil.html.twig', [
            'amis' => $amis,
            'posts' => $posts,
            'utilisateurs' => $utilisateurs
        ]);
    }
}
?>