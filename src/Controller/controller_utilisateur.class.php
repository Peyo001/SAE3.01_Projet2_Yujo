<?php
/**
 * Contrôleur pour la gestion des utilisateurs (inscription, connexion, déconnexion, profil).
 * Utilise les classes métiers et DAO appropriées pour interagir avec la base de données
 * et afficher les vues correspondantes.
 * 
 * Exemple d'utilisation :
 * $controllerUtilisateur = new ControllerUtilisateur($loader, $twig);
 * $controllerUtilisateur->afficherFormulaireInscription();
 * $controllerUtilisateur->traiterInscription();
 */
class ControllerUtilisateur extends Controller
{
    /**
     * Constructeur du contrôleur des utilisateurs.
     * 
     * Initialise la classe `ControllerUtilisateur` en passant les objets Twig `Environment` et `FilesystemLoader`
     * au constructeur de la classe parente `Controller`.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader L'objet loader pour la gestion des fichiers Twig.
     * @param \Twig\Environment $twig L'objet Twig pour le rendu des templates.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    /**
     * Affiche le formulaire d'inscription.
     * 
     * Cette méthode rend la vue `inscription.twig` pour permettre à un nouvel utilisateur
     * de s'inscrire.
     * 
     * @return void
     */
    public function afficherFormulaireInscription(): void
    {
        echo $this->getTwig()->render('inscription.twig', [
            'menu' => 'inscription'
        ]);
    }

    /**
     * Traite le formulaire d'inscription.
     * 
     * Cette méthode récupère les données soumises via le formulaire d'inscription,
     * crée un nouvel objet `Utilisateur`, le sécurise (hachage du mot de passe),
     * et l'enregistre dans la base de données via le DAO `UtilisateurDao`.
     * 
     * @return void
     */
    public function traiterInscription(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        // 1. Récupération des données
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $mdp = $_POST['password'];
        $genre = $_POST['genre'];
        $dateNaiss = $_POST['date_naissance'];

        // 2. Sécurisation du mot de passe (Indispensable !)
        // On ne stocke jamais un mot de passe en clair.
        $mdpHache = password_hash($mdp, PASSWORD_DEFAULT);

        // 3. Valeurs par défaut pour un nouveau membre
        $dateInscription = date('Y-m-d H:i:s');
        $yuPoints = 0;
        $typeCompte = 'Classique';
        $estPremium = false;
        $personnalisation = null;

        // 4. Création de l'objet (ID à null car auto-increment)
        $user = new Utilisateur(
            null, 
            $nom, $prenom, $dateNaiss, $genre, $pseudo, $email, 
            $mdpHache, // On envoie le mot de passe crypté
            $typeCompte, $estPremium, $dateInscription, $yuPoints, $personnalisation
        );

        // 5. Enregistrement
        $manager = new UtilisateurDao($this->getPdo());
        
        // Idéalement, il faudrait vérifier ici si l'email existe déjà (findByEmail)
        // Pour faire simple, on tente l'insertion directement
        try {
            $succes = $manager->createUser($user);
            
            if ($succes) {
                // Redirection vers la connexion après succès
                header('Location: index.php?controleur=utilisateur&methode=connexion');
                exit;
            }
        } catch (Exception $e) {
            echo "Erreur lors de l'inscription (Email ou Pseudo peut-être déjà pris).";
        }
    }

    
    /**
     * Affiche le formulaire de connexion.
     * 
     * Cette méthode rend la vue `connexion.twig` pour permettre à un utilisateur
     * de se connecter.
     * 
     * @return void
     */
    public function connexion(): void // Affiche le formulaire
    {
        // Si déjà connecté, on renvoie à l'accueil
        if (isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php');
            exit;
        }

        echo $this->getTwig()->render('connexion.twig', [
            'menu' => 'connexion'
        ]);
    }

    /**
     * Traite le formulaire de connexion.
     * 
     * Cette méthode récupère les données soumises via le formulaire de connexion,
     * vérifie les informations d'identification de l'utilisateur, et crée une session
     * si la connexion est réussie.
     * 
     * @return void
     */
    public function traiterConnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $manager = new UtilisateurDao($this->getPdo());
        // C'est ici qu'on utilise la nouvelle méthode findByEmail
        $user = $manager->findByEmail($email);

        // Vérification :
        // 1. Est-ce qu'on a trouvé un utilisateur ?
        // 2. Est-ce que le mot de passe correspond au hash en base ?
        if ($user && password_verify($password, $user->getMotDePasse())) {
            
            // SUCCÈS : ON CRÉE LA SESSION
            $_SESSION['idUtilisateur'] = $user->getIdUtilisateur();
            $_SESSION['pseudo'] = $user->getPseudo();
            $_SESSION['typeCompte'] = $user->getTypeCompte();
            
            // Redirection vers l'accueil ou le fil d'actu
            header('Location: index.php?controleur=post&methode=lister');
            exit;

        } else {
            // ÉCHEC
            echo $this->getTwig()->render('connexion.twig', [
                'error' => 'Email ou mot de passe incorrect.',
                'last_email' => $email // Pour ne pas qu'il ait à retaper l'email
            ]);
        }
    }

    /**
     * Déconnecte l'utilisateur en détruisant la session.
     * 
     * Cette méthode vide la session actuelle et redirige l'utilisateur
     * vers la page de connexion ou l'accueil.
     * 
     * @return void
     */
    public function deconnexion(): void
    {
        // On vide la session
        session_unset();
        session_destroy();

        // On redirige vers l'accueil ou la connexion
        header('Location: index.php?controleur=utilisateur&methode=connexion');
        exit;
    }
    
    /**
     * Affiche le profil de l'utilisateur connecté.
     * 
     * Cette méthode vérifie si un utilisateur est connecté, récupère ses informations
     * depuis la base de données, et rend la vue `profil.twig` avec ces informations.
     * 
     * @return void
     */
    public function afficherProfil(): void
    {
         if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }
        
        $manager = new UtilisateurDao($this->getPdo());
        $user = $manager->find($_SESSION['idUtilisateur']);
        
        echo $this->getTwig()->render('profil.twig', ['user' => $user]);
    }
}