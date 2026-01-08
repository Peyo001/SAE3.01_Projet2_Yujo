<?php
/**
 * Classe ControllerParametre gère les actions liées aux paramètres utilisateur comme
 * l'affichage et la modification du profil.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controllerParametre = new ControllerParametre($loader, $twig);
 * $controllerParametre->afficherParametre(); // Affiche la page des paramètres
 * $controllerParametre->modifierProfil(); // Modifie le profil de l'utilisateur
 */
class ControllerParametre extends Controller
{
    public function __construct($loader, $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * @brief Affiche la page des paramètres utilisateur.
     *
     * Cette méthode récupère les informations de l'utilisateur connecté et les envoie à la vue pour affichage.
     * 
     * @return void
     */
    public function afficherParametre(): void
    {
        // Récupération de l'ID utilisateur via la session 
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        if (!$idUtilisateur) {
            header('Location: index.php?controleur=utilisateur&methode=connexion'); 
            exit;
        }

        // Initialisation du DAO Utilisateur
        $userManager = new UtilisateurDao($this->getPdo());

        // Récupération des données
        $utilisateur = $userManager->find($idUtilisateur); 

        // Envoi à la vue
        echo $this->getTwig()->render('pageParametre.twig', [
            'user' => $utilisateur,
            'page_title' => 'Paramètres'
        ]);
    }

    /**
     * @brief Modifie le profil de l'utilisateur connecté.
     * 
     * Cette méthode traite les données du formulaire de modification de profil, met à jour les informations de l'utilisateur dans la base de données, puis affiche la page des paramètres avec les informations mises à jour.
     * 
     * @return void
     */
    public function modifierProfil(): void
    {
        // Récupération de l'ID utilisateur via la session 
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        if (!$idUtilisateur) {
            header('Location: index.php?controleur=utilisateur&methode=connexion'); 
            exit;
        }

        // Initialisation du DAO Utilisateur
        $userManager = new UtilisateurDao($this->getPdo());

        // Récupération des données du formulaire
        $nouveauNom = $_POST['nom'] ?? null;
        $nouveauEmail = $_POST['email'] ?? null;

        // Validation et mise à jour
        if ($nouveauNom && $nouveauEmail) {
            $utilisateur = $userManager->find($idUtilisateur);
            if ($utilisateur) {
                $utilisateur->setNom($nouveauNom);
                $utilisateur->setEmail($nouveauEmail);
                $userManager->modifierUtilisateur($utilisateur);
            }
        }
        $utilisateur = $userManager->find($idUtilisateur);
        // Envoi à la vue
        echo $this->getTwig()->render('page_modification_profil.twig', [
            'utilisateur' => $utilisateur,
            'page_title' => 'Paramètres'
        ]);
    }
}
?>