<?php
class ControllerParametre extends Controller
{
    public function __construct($loader, $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * Affiche la page des paramètres utilisateur.
     *
     * Cette méthode récupère les informations de l'utilisateur connecté et les envoie à la vue pour affichage.
     */
    public function afficherParametre(): void
    {
        // 1. Récupération de l'ID utilisateur via la session 
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        if (!$idUtilisateur) {
            header('Location: index.php?controleur=utilisateur&methode=connexion'); 
            exit;
        }

        // 2. Initialisation du DAO Utilisateur
        $userManager = new UtilisateurDao($this->getPdo());

        // 3. Récupération des données
        $utilisateur = $userManager->find($idUtilisateur); 

        // 4. Envoi à la vue
        echo $this->getTwig()->render('pageParametre.twig', [
            'user' => $utilisateur,
            'page_title' => 'Paramètres'
        ]);
    }

    /**
     * Modifie le profil de l'utilisateur connecté.
     * 
     * Cette méthode traite les données du formulaire de modification de profil, met à jour les informations de l'utilisateur dans la base de données, puis affiche la page des paramètres avec les informations mises à jour.
     */
    public function modifierProfil(): void
    {
        // 1. Récupération de l'ID utilisateur via la session 
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        if (!$idUtilisateur) {
            header('Location: index.php?controleur=utilisateur&methode=connexion'); 
            exit;
        }

        // 2. Initialisation du DAO Utilisateur
        $userManager = new UtilisateurDao($this->getPdo());

        // 3. Récupération des données du formulaire
        $nouveauNom = $_POST['nom'] ?? null;
        $nouveauEmail = $_POST['email'] ?? null;

        // 4. Validation et mise à jour
        if ($nouveauNom && $nouveauEmail) {
            $utilisateur = $userManager->find($idUtilisateur);
            if ($utilisateur) {
                $utilisateur->setNom($nouveauNom);
                $utilisateur->setEmail($nouveauEmail);
                $userManager->modifierUtilisateur($utilisateur);
            }
        }
        $utilisateur = $userManager->find($idUtilisateur);
        // 4. Envoi à la vue
        echo $this->getTwig()->render('page_modification_profil.twig', [
            'utilisateur' => $utilisateur,
            'page_title' => 'Paramètres'
        ]);
    }
}
?>