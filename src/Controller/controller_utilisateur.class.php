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
        $dejaConnecte = isset($_SESSION['idUtilisateur']);
        echo $this->getTwig()->render('connexion.twig', [
            'menu' => 'connexion',
            'deja_connecte' => $dejaConnecte,
            'pseudo' => $dejaConnecte ? ($_SESSION['pseudo'] ?? '') : ''
        ]);
    }

    /** 
     * @brief Traite les données de connexion utilisateur.
     * 
     * Valide les informations de connexion, crée une session utilisateur en cas de succès.
     * Gère les tentatives de connexion échouées avec un système de blocage temporaire.
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

        // Anti brute-force : après 5 tentatives échouées, blocage 2 minutes
        $cleTentatives = 'tentatives_connexion';
        $maintenant = time();
        if (!isset($_SESSION[$cleTentatives])) { $_SESSION[$cleTentatives] = []; }
        if (!isset($_SESSION[$cleTentatives][$email])) {
            $_SESSION[$cleTentatives][$email] = [
                'compteur' => 0,
                'bloque_jusqua' => 0,
                'notif_envoyee' => false
            ];
        }
        $etatTentative = &$_SESSION[$cleTentatives][$email];

        // Si le blocage a expiré, on réinitialise l'état
        if ($etatTentative['bloque_jusqua'] > 0 && $etatTentative['bloque_jusqua'] <= $maintenant) {
            $etatTentative = ['compteur' => 0, 'bloque_jusqua' => 0, 'notif_envoyee' => false];
        }

        // Si l'utilisateur est actuellement bloqué, on empêche la tentative
        if ($etatTentative['bloque_jusqua'] > $maintenant) {
            $reste = $etatTentative['bloque_jusqua'] - $maintenant;
            echo $this->getTwig()->render('connexion.twig', [
                'error' => "Trop de tentatives. Réessayez dans $reste secondes.",
                'last_email' => $email
            ]);
            exit;
        }

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
            // Réinitialiser les tentatives pour cet email
            $_SESSION[$cleTentatives][$email] = ['compteur' => 0, 'bloque_jusqua' => 0, 'notif_envoyee' => false];
            
            // Redirection vers l'accueil ou le fil d'actu
            header('Location: index.php?controleur=accueil&methode=afficher');
            exit;

        } else {
            // ÉCHEC : on incrémente le compteur et on bloque si besoin
            $etatTentative['compteur'] = ($etatTentative['compteur'] ?? 0) + 1;
            if ($etatTentative['compteur'] >= 5) {
                $etatTentative['bloque_jusqua'] = time() + 500; // Blocage de 500 secondes
                // Envoi d'un mail de sécurité (une seule fois) si l'utilisateur existe
                if ($user && empty($etatTentative['notif_envoyee'])) {
                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
                    $mailService = new MailService();
                    $mailService->envoyerEmailAlerteSecurite($user, $ip);
                    $etatTentative['notif_envoyee'] = true;
                }
            }

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
    
    /**
     * @brief Affiche le profil de l'utilisateur connecté.
     * 
     * Récupère les informations de l'utilisateur depuis la base de données
     * et rend la vue 'profil.twig' avec les données utilisateur.
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
        $postDao = new PostDao($this->getPdo());
        $amiDao = new AmiDao($this->getPdo());
        $roomDao = new RoomDao($this->getPdo());
        $achatDao = new AchatDao($this->getPdo());
        $objetDao = new ObjetDao($this->getPdo());
        $quizDao = new QuizDao($this->getPdo());

        $amis = $amiDao->findAmis($_SESSION['idUtilisateur']);
        $user = $manager->find($_SESSION['idUtilisateur']);
        $posts = $postDao->findPostsByAuteur($user->getIdUtilisateur());
        $rooms = $roomDao->findByCreateur($user->getIdUtilisateur());
        $reponseDao = new ReponseDao($this->getPdo());
        // Mapping global des utilisateurs pour afficher les pseudos dans les commentaires
        $utilisateursAll = [];
        foreach ($manager->findAll() as $u) {
            $utilisateursAll[$u->getIdUtilisateur()] = $u;
        }
        
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
        
        // Récupérer les objets possédés par l'utilisateur
        $achats = $achatDao->findByUtilisateur($user->getIdUtilisateur());
        $objetsPossedes = [];
        foreach ($achats as $achat) {
            $objet = $objetDao->find($achat->getIdObjet());
            if ($objet) {
                $objetsPossedes[] = $objet;
            }
        }

        // Préparer un mapping des amis -> Utilisateur pour afficher les noms
        $utilisateursAmis = [];
        foreach ($amis as $ami) {
            $idAmi = $ami->getIdUtilisateur2();
            if (!isset($utilisateursAmis[$idAmi])) {
                $u = $manager->find($idAmi);
                if ($u) { $utilisateursAmis[$idAmi] = $u; }
            }
        }

        // Récupérer et consommer les messages flash
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Récupérer les réponses par post pour affichage des commentaires
        $reponsesParPost = [];
        foreach ($posts as $p) {
            $reponsesParPost[$p->getIdPost()] = $reponseDao->findResponsesByPost($p->getIdPost());
        }
        
        echo $this->getTwig()->render('profil.twig', [
            'utilisateur' => $user,
            'posts' => $posts,
            'amis' => $amis,
            'rooms' => $rooms,
            'objetsPossedes' => $objetsPossedes,
            'utilisateursAmis' => $utilisateursAmis,
            'utilisateurs' => $utilisateursAll,
            'quizParPost' => $quizParPost,
            'reponsesParPost' => $reponsesParPost,
            'flash_success' => $flashSuccess,
            'flash_error' => $flashError
        ]);
    }

    /**
     * @brief Ajoute un ami par pseudo depuis le profil
     */
    public function ajouterAmi(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }

        $pseudoAmi = trim($_POST['pseudo_ami'] ?? '');
        if ($pseudoAmi === '') {
            $_SESSION['flash_error'] = "Veuillez entrer le pseudo de l'ami à ajouter.";
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }

        $utilisateurDao = new UtilisateurDao($this->getPdo());
        $amiDao = new AmiDao($this->getPdo());

        $cible = $utilisateurDao->findByPseudo($pseudoAmi);
        if (!$cible) {
            $_SESSION['flash_error'] = "Aucun utilisateur trouvé avec ce pseudo.";
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }

        $idMoi = (int)$_SESSION['idUtilisateur'];
        $idCible = (int)$cible->getIdUtilisateur();
        if ($idMoi === $idCible) {
            $_SESSION['flash_error'] = "Vous ne pouvez pas vous ajouter vous-même.";
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }

        // Vérifier si la relation existe déjà (dans un sens ou l'autre)
        $existe1 = $amiDao->find($idMoi, $idCible);
        $existe2 = $amiDao->find($idCible, $idMoi);
        if ($existe1 || $existe2) {
            $_SESSION['flash_error'] = "Cet utilisateur est déjà dans vos amis.";
            header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
            exit;
        }

        $ami = new Ami($idMoi, $idCible, date('Y-m-d H:i:s'));
        if ($amiDao->insererAmi($ami)) {
            $_SESSION['flash_success'] = "Ami ajouté avec succès.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'ajout de l'ami.";
        }

        header('Location: index.php?controleur=utilisateur&methode=afficherProfil');
        exit;
    }
    
    public function afficherCompte(): void
    {
         if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }
        
        $manager = new UtilisateurDao($this->getPdo());
        $user = $manager->find($_SESSION['idUtilisateur']);
        
        echo $this->getTwig()->render('compte.twig', ['utilisateur' => $user]);
    }

    public function modifierProfil(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }
        
        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->find($_SESSION['idUtilisateur']);

        // Récupérer les nouvelles données du formulaire
        $nouveauNom = $_POST['nom'] ?? $utilisateur->getNom();
        $nouveauPrenom = $_POST['prenom'] ?? $utilisateur->getPrenom();
        $nouveauPseudo = $_POST['pseudo'] ?? $utilisateur->getPseudo();

        // Mettre à jour l'objet utilisateur
        $utilisateur->setNom($nouveauNom);
        $utilisateur->setPrenom($nouveauPrenom);
        $utilisateur->setPseudo($nouveauPseudo);

        // Gestion de l'upload de photo de profil (fichier image < 2 Mo)
        if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
            $cheminTemporaire = $_FILES['photo_profil']['tmp_name'];
            $typeMime = mime_content_type($cheminTemporaire);
            $typesAutorises = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

            if (isset($typesAutorises[$typeMime]) && $_FILES['photo_profil']['size'] <= 2 * 1024 * 1024) {
                $extension = $typesAutorises[$typeMime];
                $dossierUpload = __DIR__ . '/../../public/uploads/avatars/';
                if (!is_dir($dossierUpload)) {
                    @mkdir($dossierUpload, 0775, true);
                }
                $nomFichier = 'avatar_' . $_SESSION['idUtilisateur'] . '_' . uniqid() . '.' . $extension;
                $cheminDestination = $dossierUpload . $nomFichier;
                if (move_uploaded_file($cheminTemporaire, $cheminDestination)) {
                    // Stocker le chemin relatif pour l'affichage
                    $utilisateur->setPersonnalisation('uploads/avatars/' . $nomFichier);
                }
            }
        }

        // Enregistrer les modifications
        $manager->modifierUtilisateur($utilisateur);

        // Rediriger vers le profil après modification
        echo $this->getTwig()->render('compte.twig', ['utilisateur' => $utilisateur]);
        exit;
    }

    /**
     * @brief Affiche le formulaire de changement de mot de passe.
     * 
     * @return void
     */
    public function afficherChangementMotDePasse(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->find($_SESSION['idUtilisateur']);

        echo $this->getTwig()->render('changement_mot_de_passe.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    /**
     * @brief Traite le changement de mot de passe.
     * 
     * Valide l'ancien mot de passe, puis met à jour vers le nouveau.
     * 
     * @return void
     */
    public function traiterChangementMotDePasse(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=utilisateur&methode=afficherChangementMotDePasse');
            exit;
        }

        $ancienMotDePasse = $_POST['ancien_mot_de_passe'] ?? '';
        $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? '';
        $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'] ?? '';

        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->find($_SESSION['idUtilisateur']);

        $erreurs = [];

        // Vérifier que l'ancien mot de passe est correct
        if (!password_verify($ancienMotDePasse, $utilisateur->getMotDePasse())) {
            $erreurs[] = "L'ancien mot de passe est incorrect.";
        }

        // Vérifier que le nouveau mot de passe respecte les critères
        if (strlen($nouveauMotDePasse) < 8) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        }
        if (!preg_match('/[a-z]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins une minuscule.";
        }
        if (!preg_match('/[A-Z]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins une majuscule.";
        }
        if (!preg_match('/[0-9]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins un chiffre.";
        }
        if (!preg_match('/[@$!%*?&]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins un caractère spécial (@$!%*?&).";
        }

        // Vérifier que les deux nouveaux mots de passe correspondent
        if ($nouveauMotDePasse !== $confirmationMotDePasse) {
            $erreurs[] = "Les deux nouveaux mots de passe ne correspondent pas.";
        }

        if (!empty($erreurs)) {
            echo $this->getTwig()->render('changement_mot_de_passe.twig', [
                'utilisateur' => $utilisateur,
                'erreurs' => $erreurs
            ]);
            exit;
        }

        // Générer un token de confirmation sécurisé
        $tokenConfirmation = bin2hex(random_bytes(32));
        $dateExpiration = date('Y-m-d H:i:s', time() + 3600); // Valide 1 heure

        // Stocker le token en session avec les nouvelles données
        $_SESSION['changement_mdp_attente'] = [
            'token' => $tokenConfirmation,
            'nouveau_mdp' => password_hash($nouveauMotDePasse, PASSWORD_DEFAULT),
            'date_expiration' => $dateExpiration
        ];

        // Envoyer un mail de confirmation
        $lienConfirmation = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/index.php?controleur=utilisateur&methode=confirmerChangementMotDePasse&token=' . $tokenConfirmation;
        
        $mailService = new MailService();
        $mailService->envoyerEmailChangementMotDePasse($utilisateur, $lienConfirmation);

        // Rediriger avec message
        $_SESSION['flash_success'] = "Un lien de confirmation a été envoyé à votre adresse email. Veuillez confirmer dans l'heure.";
        header('Location: index.php?controleur=parametre&methode=afficherParametre');
        exit;
    }

    /**
     * @brief Confirme le changement de mot de passe via un token.
     * 
     * @return void
     */
    public function confirmerChangementMotDePasse(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $token = $_GET['token'] ?? '';
        if (empty($token) || !isset($_SESSION['changement_mdp_attente'])) {
            $_SESSION['flash_error'] = "Token invalide ou expiré.";
            header('Location: index.php?controleur=parametre&methode=afficherParametre');
            exit;
        }

        $attente = $_SESSION['changement_mdp_attente'];
        if ($attente['token'] !== $token || strtotime($attente['date_expiration']) < time()) {
            unset($_SESSION['changement_mdp_attente']);
            $_SESSION['flash_error'] = "Le lien de confirmation a expiré. Veuillez réessayer.";
            header('Location: index.php?controleur=parametre&methode=afficherParametre');
            exit;
        }

        // Appliquer le changement de mot de passe
        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->find($_SESSION['idUtilisateur']);
        $utilisateur->setMotDePasse($attente['nouveau_mdp']);
        $manager->modifierUtilisateur($utilisateur);

        // Nettoyer la session
        unset($_SESSION['changement_mdp_attente']);

        // Email de notification
        $mailService = new MailService();
        $mailService->envoyerEmailConfirmationChangementMotDePasse($utilisateur);

        $_SESSION['flash_success'] = "Votre mot de passe a été changé avec succès. Un email de confirmation a été envoyé.";
        header('Location: index.php?controleur=parametre&methode=afficherParametre');
        exit;
    }

    /**
     * @brief Affiche le formulaire de réinitialisation de mot de passe oublié.
     * 
     * @return void
     */
    public function afficherMotDePasseOublie(): void
    {
        echo $this->getTwig()->render('mot_de_passe_oublie.twig');
    }

    /**
     * @brief Traite la demande de réinitialisation de mot de passe.
     * 
     * Envoie un mail avec un lien de réinitialisation sécurisé.
     * 
     * @return void
     */
    public function traiterMotDePasseOublie(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->findByEmail($email);

        // Message générique pour éviter de révéler si un email existe
        if (!$utilisateur) {
            $_SESSION['flash_info'] = "Si cet email existe, vous recevrez un lien de réinitialisation.";
            header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
            exit;
        }

        // Générer un token de réinitialisation
        $tokenReinit = bin2hex(random_bytes(32));
        $dateExpiration = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

        // Stocker le token (dans la vraie app, ce serait en BD avec une colonne token_reinit)
        $_SESSION['reinit_mdp_' . $utilisateur->getIdUtilisateur()] = [
            'token' => $tokenReinit,
            'date_expiration' => $dateExpiration
        ];

        // Envoyer le mail
        $lienReinit = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/index.php?controleur=utilisateur&methode=afficherReinitialisationMotDePasse&token=' . $tokenReinit . '&id=' . $utilisateur->getIdUtilisateur();
        
        $mailService = new MailService();
        $mailService->envoyerEmailMotDePasseOublie($utilisateur, $lienReinit);

        $_SESSION['flash_info'] = "Si cet email existe, vous recevrez un lien de réinitialisation.";
        header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
        exit;
    }

    /**
     * @brief Affiche le formulaire de nouvelle saisie de mot de passe.
     * 
     * @return void
     */
    public function afficherReinitialisationMotDePasse(): void
    {
        $token = $_GET['token'] ?? '';
        $idUtilisateur = $_GET['id'] ?? '';

        if (empty($token) || empty($idUtilisateur)) {
            $_SESSION['flash_error'] = "Lien invalide.";
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        // Vérifier le token
        $cleSession = 'reinit_mdp_' . $idUtilisateur;
        if (!isset($_SESSION[$cleSession]) || $_SESSION[$cleSession]['token'] !== $token) {
            $_SESSION['flash_error'] = "Lien invalide ou expiré.";
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        if (strtotime($_SESSION[$cleSession]['date_expiration']) < time()) {
            unset($_SESSION[$cleSession]);
            $_SESSION['flash_error'] = "Le lien a expiré. Veuillez demander une nouvelle réinitialisation.";
            header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
            exit;
        }

        echo $this->getTwig()->render('reinitialisation_mot_de_passe.twig', [
            'token' => $token,
            'idUtilisateur' => $idUtilisateur
        ]);
    }

    /**
     * @brief Traite la réinitialisation du mot de passe.
     * 
     * @return void
     */
    public function traiterReinitialisationMotDePasse(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $idUtilisateur = $_POST['idUtilisateur'] ?? '';
        $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? '';
        $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'] ?? '';

        // Vérifier le token
        $cleSession = 'reinit_mdp_' . $idUtilisateur;
        if (!isset($_SESSION[$cleSession]) || $_SESSION[$cleSession]['token'] !== $token || strtotime($_SESSION[$cleSession]['date_expiration']) < time()) {
            $_SESSION['flash_error'] = "Lien invalide ou expiré.";
            header('Location: index.php?controleur=utilisateur&methode=afficherMotDePasseOublie');
            exit;
        }

        $erreurs = [];

        // Vérifier que le nouveau mot de passe respecte les critères
        if (strlen($nouveauMotDePasse) < 8) {
            $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (!preg_match('/[a-z]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
        }
        if (!preg_match('/[A-Z]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
        }
        if (!preg_match('/[0-9]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        if (!preg_match('/[@$!%*?&]/', $nouveauMotDePasse)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial (@$!%*?&).";
        }

        // Vérifier que les deux mots de passe correspondent
        if ($nouveauMotDePasse !== $confirmationMotDePasse) {
            $erreurs[] = "Les deux mots de passe ne correspondent pas.";
        }

        if (!empty($erreurs)) {
            echo $this->getTwig()->render('reinitialisation_mot_de_passe.twig', [
                'token' => $token,
                'idUtilisateur' => $idUtilisateur,
                'erreurs' => $erreurs
            ]);
            exit;
        }

        // Appliquer la réinitialisation
        $manager = new UtilisateurDao($this->getPdo());
        $utilisateur = $manager->find($idUtilisateur);
        $utilisateur->setMotDePasse(password_hash($nouveauMotDePasse, PASSWORD_DEFAULT));
        $manager->modifierUtilisateur($utilisateur);

        // Nettoyer la session
        unset($_SESSION[$cleSession]);

        // Email de notification
        $mailService = new MailService();
        $mailService->envoyerEmailNotificationReinitialisation($utilisateur);

        $_SESSION['flash_success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
        header('Location: index.php?controleur=utilisateur&methode=connexion');
        exit;
    }
}