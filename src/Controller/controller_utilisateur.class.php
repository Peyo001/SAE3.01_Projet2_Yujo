<?php
/** 
 * ControllerUtilisateur gère les actions liées aux utilisateurs telles que l'inscription,
 * la connexion, la déconnexion et l'affichage du profil.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controller = new ControllerUtilisateur($loader, $twig); 
 * $controller->afficherFormulaireInscription(); // Affiche le formulaire d'inscription
 * $controller->traiterInscription(); // Traite les données d'inscription
 */
class ControllerUtilisateur extends Controller
{   
    /**
     * @brief Constructeur de la classe ControllerUtilisateur.
     *
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig.
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }

    


    /**
     * @brief Affiche le formulaire d'inscription utilisateur.
     * 
     * Rend la vue 'inscription.twig' avec le menu actif sur 'inscription'.
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
     * @brief Traite les données d'inscription utilisateur.
     * 
     * Valide les données du formulaire, crée un nouvel utilisateur et l'enregistre dans la base de données.
     * En cas de succès, redirige vers la page de connexion.
     * En cas d'erreurs de validation, réaffiche le formulaire avec les messages d'erreur.
     * 
     * @return void
     */
    public function traiterInscription(): void
    {
        // Définition des règles de validation
        $reglesValidation = [
            'nom' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_min' => 2,
                'longueur_max' => 1150,
                 // Lettres, accents, apostrophes et traits d'union
                'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/'
                
            ],
            'prenom' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 2,
                'longueur_max' => 150,
                // Lettres et caractères accentués uniquement
                'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/' 
            ],
            'pseudo' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 3,
                'longueur_max' => 150,
                // Lettres, chiffres et underscores uniquement
                'format' => '/^[a-zA-Z0-9_]+$/' 
            ],
            'email' => [
                'obligatoire' => true,
                'type' => 'email',
                'longueur_max' => 255,
                'format' => FILTER_VALIDATE_EMAIL // Utilisation du filtre PHP pour valider l'email
            ],
            'password' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 8,
                'longueur_max' => 64,
                // Au moins une majuscule, une minuscule, un chiffre et un caractère spécial
                'format' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'date_naissance' => [
                'obligatoire' => true,
                'type' => 'date',
                'format' => 'Y-m-d' // Format de date attendu
            ],
            'genre' => [
                'obligatoire' => false,
                'type' => 'string',
                'valeurs_acceptables' => ['Homme', 'Femme', 'Autre']
            ],
        ];

        // vérification de la méthode pour acquerir les données
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        $validator = new Validator($reglesValidation);
        $donneesValides = $validator->valider($_POST);
        $erreurs = $validator->getMessagesErreurs();

        
        if (!$donneesValides){
            // Il y a des erreurs de validation
            echo $this->getTwig()->render('inscription.twig', [
                'menu' => 'inscription',
                'erreurs' => $erreurs,
                'donnees' => $_POST // Pour pré-remplir le formulaire avec les données saisies
            ]);
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
            $nom, $prenom, $dateNaiss, $genre, $pseudo, $email, 
            $mdpHache, // On envoie le mot de passe crypté
            $typeCompte, $estPremium, $dateInscription, $yuPoints, null, $personnalisation
        );

        // 5. Enregistrement
        $manager = new UtilisateurDao($this->getPdo());
        
        // Vérification si pseudo ou email existe déjà
        $pseudoExiste = $manager->findByPseudo($pseudo) !== null;
        $emailExiste = $manager->findByEmail($email) !== null;
    
        if ($pseudoExiste || $emailExiste) {
            $erreurs = [];
            if ($pseudoExiste) {
                $erreurs[] = "Ce pseudo est déjà utilisé. Veuillez en choisir un autre.";
            }
            if ($emailExiste) {
                $erreurs[] = "Cet email est déjà associé à un compte.";
            }
            
            echo $this->getTwig()->render('inscription.twig', [
                'menu' => 'inscription',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }
        // Si l'email et le pseudo sont uniques, on essaie de créer l'utilisateur
        try {
            $succes = $manager->creerUtilisateur($user);
            
            if ($succes) {
                // Redirection vers la connexion après succès
                header('Location: index.php?controleur=accueil&methode=afficher');
                exit;
            } else {
                throw new Exception("Erreur lors de la création de l'utilisateur.");
                $erreurs = ["Une erreur est survenue lors de l'inscription. Veuillez réessayer."];
            }
        } catch (Exception $e) {
            echo $this->getTwig()->render('inscription.twig', [
                'menu' => 'inscription',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }
    }

    
    /**
     * @brief Affiche le formulaire de connexion utilisateur.
     * 
     * Rend la vue 'connexion.twig' avec le menu actif sur 'connexion'.
     * 
     * @return void
     */
    public function connexion(): void // Affiche le formulaire
    {
<<<<<<< Updated upstream
        // Si déjà connecté, on renvoie à l'accueil
        if (isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=accueil&methode=afficher');
            exit;
        }

=======
        $dejaConnecte = isset($_SESSION['idUtilisateur']);
>>>>>>> Stashed changes
        echo $this->getTwig()->render('connexion.twig', [
            'menu' => 'connexion',
            'deja_connecte' => $dejaConnecte,
            'pseudo' => $dejaConnecte ? ($_SESSION['pseudo'] ?? '') : ''
        ]);
    }

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
            session_regenerate_id(true);
            
            // SUCCÈS : ON CRÉE LA SESSION
            $_SESSION['idUtilisateur'] = $user->getIdUtilisateur();
            $_SESSION['pseudo'] = $user->getPseudo();
            $_SESSION['typeCompte'] = $user->getTypeCompte();
            
            // Redirection vers l'accueil ou le fil d'actu
            header('Location: index.php?controleur=accueil&methode=afficher');
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
     * @brief Déconnecte l'utilisateur en détruisant la session.
     * Redirige ensuite vers la page de connexion.
     * 
     * @return void
     */
    public function deconnexion(): void
    {
        // On vide la session
        session_unset();
        // Invalider le cookie de session côté client
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        // On redirige vers l'accueil ou la connexion
        header('Location: index.php?controleur=utilisateur&methode=connexion');
        exit;
    }
    
    // --- PROFIL (Bonus) ---
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