<?php
/**
 * ControlleurAccueil gère les actions liées à la page d'accueil comme son affichage.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validation pour la validation des données.
 * 
 * Exemple d'utilisation :
 * $controllerAccueil = new ControllerAccueil($loader, $twig);
 * $controllerAccueil->afficher(); // Affiche la page d'accueil
 */
class ControllerAccueil extends Controller
{
    /**
     * @brief Constructeur de la classe ControllerAccueil.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig.
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * @brief Affiche la page d'accueil avec les posts des amis de l'utilisateur connecté.
     * 
     * Récupère les amis de l'utilisateur, puis leurs posts, et les passe à la vue Twig.
     * 
     * @return void
     */
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