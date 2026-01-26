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
        $newsletterManager = new NewsletterDAO($this->getPdo());
        $quizDao = new QuizDao($this->getPdo());
        $reponseDao = new ReponseDao($this->getPdo());

        // --- Récupérer les amis ---
        $amis = $amiManager->findAmis($idUtilisateur); // tableau d'objets Ami

        // --- Construire tableau d'IDs pour récupérer les posts des amis + les vôtres ---
        $idsAmis = [];
        foreach ($amis as $ami) {
            $idsAmis[] = $ami->getIdUtilisateur2(); // ou la propriété correspondant à l'id de l'ami
        }
        if ($idUtilisateur) {
            $idsAmis[] = (int)$idUtilisateur; // inclure les posts de l'utilisateur connecté
        }

        // --- Récupérer uniquement les posts des amis ---
        $posts = $postManager->findByAuteurs($idsAmis);
        
        // Récupérer les quiz associés aux posts de type quiz
        $quizParPost = [];
        foreach ($posts as $post) {
            if ($post->getTypePost() === 'quiz') {
                $quiz = $quizDao->findByPost($post->getIdPost());
                if ($quiz) {
                    $quizParPost[$post->getIdPost()] = $quiz;
                }
            }
        }

        // --- Tableau id => Utilisateur pour Twig (clé = idUtilisateur) ---
        $utilisateursList = $userManager->findAll();
        $utilisateurs = [];
        foreach ($utilisateursList as $u) {
            // suppose getIdUtilisateur() existe dans le modèle Utilisateur
            $utilisateurs[$u->getIdUtilisateur()] = $u;
        }

        // --- Réponses par post pour les commentaires sur la page d'accueil ---
        $reponsesParPost = [];
        foreach ($posts as $p) {
            $reponsesParPost[$p->getIdPost()] = $reponseDao->findResponsesByPost($p->getIdPost());
        }
        
        // Vérifier si l'utilisateur est inscrit à la newsletter
        $utilisateurConnecte = $userManager->find($idUtilisateur);
        $estInscritNewsletter = false;
        if ($utilisateurConnecte) {
            $estInscritNewsletter = $newsletterManager->emailExiste($utilisateurConnecte->getEmail());
        }

        echo $this->getTwig()->render('pageAccueil.html.twig', [
            'amis' => $amis,
            'posts' => $posts,
            'utilisateurs' => $utilisateurs,
            'quizParPost' => $quizParPost,
            'reponsesParPost' => $reponsesParPost,
            'estInscritNewsletter' => $estInscritNewsletter
        ]);
    }
}
?>