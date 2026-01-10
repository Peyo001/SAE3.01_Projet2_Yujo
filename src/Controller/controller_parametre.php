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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Afficher le formulaire
            $utilisateur = $userManager->find($idUtilisateur);
            echo $this->getTwig()->render('page_modification_profil.twig', [
                'utilisateur' => $utilisateur,
                'page_title' => 'Modification du profil'
            ]);
            return;
        }

        // Traitement du formulaire POST
        // Définition des règles de validation
        $reglesValidation = [
            'nom' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_min' => 2,
                'longueur_max' => 150,
                'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/'
            ],
            'prenom' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_min' => 2,
                'longueur_max' => 150,
                'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/'
            ],
            'pseudo' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_min' => 3,
                'longueur_max' => 150,
                'format' => '/^[a-zA-Z0-9_]+$/'
            ],
            'email' => [
                'obligatoire' => false,
                'type' => 'email',
                'longueur_max' => 255,
                'format' => FILTER_VALIDATE_EMAIL
            ]
        ];

        $validator = new Validator($reglesValidation);
        $donneesValides = $validator->valider($_POST);
        $erreurs = $validator->getMessagesErreurs();

        $utilisateur = $userManager->find($idUtilisateur);

        if (!$donneesValides) {
            echo $this->getTwig()->render('page_modification_profil.twig', [
                'utilisateur' => $utilisateur,
                'page_title' => 'Modification du profil',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }

        // Récupération des données validées
        $nouveauNom = trim($_POST['nom'] ?? '');
        $nouveauPrenom = trim($_POST['prenom'] ?? '');
        $nouveauPseudo = trim($_POST['pseudo'] ?? '');
        $nouvelEmail = trim($_POST['email'] ?? '');

        // Validation supplémentaire: vérifier l'unicité de l'email et du pseudo
        $erreurUnicite = [];
        if (!empty($nouvelEmail) && $nouvelEmail !== $utilisateur->getEmail()) {
            if ($userManager->findByEmail($nouvelEmail) !== null) {
                $erreurUnicite[] = "Cet email est déjà utilisé.";
            }
        }
        if (!empty($nouveauPseudo) && $nouveauPseudo !== $utilisateur->getPseudo()) {
            if ($userManager->findByPseudo($nouveauPseudo) !== null) {
                $erreurUnicite[] = "Ce pseudo est déjà utilisé.";
            }
        }

        if (!empty($erreurUnicite)) {
            echo $this->getTwig()->render('page_modification_profil.twig', [
                'utilisateur' => $utilisateur,
                'page_title' => 'Modification du profil',
                'erreurs' => $erreurUnicite,
                'donnees' => $_POST
            ]);
            exit;
        }

        // Mise à jour des données
        if (!empty($nouveauNom)) {
            $utilisateur->setNom($nouveauNom);
        }
        if (!empty($nouveauPrenom)) {
            $utilisateur->setPrenom($nouveauPrenom);
        }
        if (!empty($nouveauPseudo)) {
            $utilisateur->setPseudo($nouveauPseudo);
        }
        if (!empty($nouvelEmail)) {
            $utilisateur->setEmail($nouvelEmail);
        }

        $userManager->modifierUtilisateur($utilisateur);

        // Redirection avec succès
        header('Location: index.php?controleur=parametre&methode=afficherParametre');
        exit;
    }
}