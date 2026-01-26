<?php
/**
 * ControllerGroupe gère les actions liées aux groupes comme
 * la liste des groupes, l'affichage d'un groupe, la création d'un groupe
 * et la possibilité de rejoindre un groupe.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controller = new ControllerGroupe($loader, $twig);
 * $controller->lister(); // Affiche la liste des groupes
 * $controller->afficher(); // Affiche un groupe spécifique
 * $controller->afficherFormulaireCreation(); // Affiche le formulaire de création de groupe
 * $controller->traiterFormulaireCreation(); // Traite la création d'un groupe
 * $controller->rejoindre(); // Permet de rejoindre un groupe
 */
class ControllerGroupe extends Controller
{
    /**
     * Constructeur de la classe ControllerGroupe
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
    }


    /**
     * @brief Affiche la liste de tous les groupes
     * 
     * Utilise la méthode findAll() de la classe GroupeDao pour récupérer tous les groupes
     * et rend la vue 'liste_groupes.twig' avec les données des groupes.
     * Filtre les groupes privés: seuls les groupes publics et les groupes privés dont l'utilisateur est membre sont affichés.
     * 
     * @return void
     */
    public function lister(): void
    {   
        $manager = new GroupeDao($this->getPdo());
        $search = isset($_GET['search']) ? $this->sanitize($_GET['search']) : '';
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        if ($search !== '') {
            $groupes = $manager->search($search);
        } else {
            $groupes = $manager->findAll();
        }

        // Filtrer les groupes privés
        $groupesFiltres = array_filter($groupes, function($groupe) use ($idUtilisateur) {
            // Si le groupe est public, l'afficher
            if (!$groupe->estPrive()) {
                return true;
            }
            // Si le groupe est privé, l'afficher seulement si l'utilisateur en est membre
            if ($idUtilisateur && in_array($idUtilisateur, $groupe->getMembres())) {
                return true;
            }
            return false;
        });

        echo $this->getTwig()->render('liste_groupes.twig', [
            'groupes' => $groupesFiltres,
            'title' => 'Les Groupes',
            'search' => $search
        ]);
    }

    /**
     * @brief Affiche un groupe spécifique
     * 
     * Récupère l'ID du groupe depuis les paramètres GET, vérifie son existence,
     * puis utilise la méthode find() de la classe GroupeDao pour récupérer le groupe.
     * Rend la vue 'groupe.twig' avec les données du groupe.
     * 
     * @return void
     */
    public function afficher(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $manager = new GroupeDao($this->getPdo());
        $messageDao = new MessageDAO($this->getPdo());
        $groupe = $manager->find($id);

        if (!$groupe) {
            echo "Ce groupe n'existe pas.";
            return;
        }

        $messages = $messageDao->findByIdGroupe($id);
        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

        // Préparer une liste des utilisateurs (auteurs des messages) pour l'affichage des noms
        $utilisateurDao = new UtilisateurDao($this->getPdo());
        $utilisateurs = [];
        // Auteurs des messages
        foreach ($messages as $m) {
            $uid = $m->getIdUtilisateur();
            if ($uid && !isset($utilisateurs[$uid])) {
                $u = $utilisateurDao->find($uid);
                if ($u) {
                    $utilisateurs[$uid] = $u;
                }
            }
        }
        // Membres du groupe
        foreach ($groupe->getMembres() as $idMembre) {
            if ($idMembre && !isset($utilisateurs[$idMembre])) {
                $u = $utilisateurDao->find($idMembre);
                if ($u) {
                    $utilisateurs[$idMembre] = $u;
                }
            }
        }

        // Récupérer les amis de l'utilisateur connecté
        $touslesAmis = [];
        if ($idUtilisateur) {
            $amiDao = new AmiDao($this->getPdo());
            $amis = $amiDao->findAmis($idUtilisateur);
            // Hydrater les amis avec les informations utilisateur
            foreach ($amis as $ami) {
                $u = $utilisateurDao->find($ami->getIdUtilisateur2());
                if ($u) {
                    $touslesAmis[] = $u;
                }
            }
        }

        // Récupérer l'ID du créateur directement depuis le groupe
        $idCreateur = $groupe->getIdCreateur();

        echo $this->getTwig()->render('groupe.twig', [
            'groupe' => $groupe,
            'user_connected' => $_SESSION['idUtilisateur'] ?? null,
            'messages' => $messages,
            'utilisateurs' => $utilisateurs,
            'touslesAmis' => $touslesAmis,
            'idCreateur' => $idCreateur
        ]);
    }


    /**
     * @brief Affiche le formulaire de création d'un nouveau groupe
     * 
     * Vérifie si l'utilisateur est connecté avant d'afficher le formulaire.
     * Sinon, redirige vers la page de connexion.
     * 
     * @return void
     */
    public function afficherFormulaireCreation(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        echo $this->getTwig()->render('ajout_groupe.twig', [
            'menu' => 'nouveau_groupe'
        ]);
    }

  
    /**
     * @brief Traite les données du formulaire de création d'un nouveau groupe
     * 
     * Vérifie la méthode de requête, l'authentification de l'utilisateur,
     * puis insère le nouveau groupe dans la base de données.
     * 
     * @return void
     */
    public function traiterFormulaireCreation(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        // Définition des règles de validation
        $reglesValidation = [
            'nom_groupe' => [
                'obligatoire' => true,
                'type' => 'string',
                'longueur_min' => 2,
                'longueur_max' => 150
            ],
            'description' => [
                'obligatoire' => false,
                'type' => 'string',
                'longueur_max' => 1000
            ],
            'confidentialite' => [
                'obligatoire' => true,
                'type' => 'string'
            ]
        ];

        $validator = new Validator($reglesValidation);
        $donneesValides = $validator->valider($_POST);
        $erreurs = $validator->getMessagesErreurs();

        if (!$donneesValides) {
            echo $this->getTwig()->render('ajout_groupe.twig', [
                'menu' => 'nouveau_groupe',
                'erreurs' => $erreurs,
                'donnees' => $_POST
            ]);
            exit;
        }

        $nom = $this->sanitize($_POST['nom_groupe']);
        $description = $this->sanitize($_POST['description'] ?? '');
        $confidentialite = $_POST['confidentialite'] === 'prive';
        $dateCreation = date('Y-m-d H:i:s');
        $idCreateur = $_SESSION['idUtilisateur'];

        $groupe = new Groupe($nom, $description, $dateCreation, [], null, $confidentialite, $idCreateur);

        $manager = new GroupeDao($this->getPdo());

        $succes = $manager->insererGroupe($groupe);

        if ($succes) {
            if ($groupe->getIdGroupe() !== null) {
                 $manager->ajouterMembre($groupe, $idCreateur, $dateCreation);
            }

            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        } else {
            throw new Exception("Erreur lors de la création du groupe.");
        }
    }

    /**
     * @brief Permet de rejoindre un groupe existant
     * 
     * Vérifie si l'utilisateur est connecté, récupère l'ID du groupe depuis les paramètres GET,
     * puis utilise la méthode ajouterMembre() de la classe GroupeDao pour ajouter l'utilisateur au groupe.
     * Redirige ensuite vers la page d'affichage du groupe.
     * 
     * @return void
     */
    public function rejoindre(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idGroupe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $idUtilisateur = $_SESSION['idUtilisateur'];
        $dateAjout = date('Y-m-d H:i:s');

        if ($idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $manager = new GroupeDao($this->getPdo());
        $groupe = $manager->find($idGroupe);

        if ($groupe) {
            $manager->ajouterMembre($groupe, $idUtilisateur, $dateAjout);
        }

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }

    /**
     * @brief Poster un message dans un groupe
     *
     * Vérifie que l'utilisateur est membre du groupe, puis insère le message.
     */
    public function posterMessage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;
        $idGroupe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $contenu = isset($_POST['message']) ? $this->sanitize($_POST['message']) : '';

        if (!$idUtilisateur || $idGroupe === 0 || $contenu === '') {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        $groupeDao = new GroupeDao($this->getPdo());
        $groupe = $groupeDao->find($idGroupe);
        if (!$groupe) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        // Vérifier que l'utilisateur est membre du groupe
        if (!in_array($idUtilisateur, $groupe->getMembres())) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Insérer le message
        $messageDao = new MessageDAO($this->getPdo());
        $message = new Message(
            null,
            $contenu,
            date('Y-m-d H:i:s'),
            $idGroupe,
            $idUtilisateur
        );
        $messageDao->insererMessage($message);

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }

    /**
     * @brief Modifie la confidentialité d'un groupe
     *
     * Permet uniquement au créateur du groupe de modifier sa confidentialité (privé/public).
     */
    public function modifierConfidentialite(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idGroupe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $confidentialite = isset($_POST['confidentialite']) ? $_POST['confidentialite'] : 'public';

        if ($idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $estPrive = ($confidentialite === 'prive');
        $groupeDao = new GroupeDao($this->getPdo());
        $groupe = $groupeDao->find($idGroupe);

        if (!$groupe) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        // Vérifier que l'utilisateur connecté est le créateur du groupe
        if ($_SESSION['idUtilisateur'] !== $groupe->getIdCreateur()) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Mettre à jour la confidentialité
        $groupeDao->modifierConfidentialite($idGroupe, $estPrive);

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }

    /**
     * @brief Affiche les invitations en attente de l'utilisateur connecté
     */
    public function afficherInvitations(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idUtilisateur = $_SESSION['idUtilisateur'];
        $invitationDao = new InvitationDao($this->getPdo());
        $invitations = $invitationDao->findByInvite($idUtilisateur);

        // Récupérer les informations des groupes et des utilisateurs
        $groupeDao = new GroupeDao($this->getPdo());
        $utilisateurDao = new UtilisateurDao($this->getPdo());
        
        $groupes = [];
        $utilisateurs = [];
        
        foreach ($invitations as $inv) {
            if (!isset($groupes[$inv->getIdGroupe()])) {
                $groupes[$inv->getIdGroupe()] = $groupeDao->find($inv->getIdGroupe());
            }
            if (!isset($utilisateurs[$inv->getIdHote()])) {
                $utilisateurs[$inv->getIdHote()] = $utilisateurDao->find($inv->getIdHote());
            }
        }

        echo $this->getTwig()->render('invitations.twig', [
            'invitations' => $invitations,
            'groupes' => $groupes,
            'utilisateurs' => $utilisateurs,
            'title' => 'Mes invitations'
        ]);
    }

    /**
     * @brief Envoie une invitation à rejoindre un groupe
     */
    public function envoyerInvitation(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idGroupe = isset($_POST['idGroupe']) ? (int)$_POST['idGroupe'] : 0;
        $idInvite = isset($_POST['idUtilisateur']) ? (int)$_POST['idUtilisateur'] : 0;
        $idHote = $_SESSION['idUtilisateur'];

        if ($idGroupe === 0 || $idInvite === 0) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        $groupeDao = new GroupeDao($this->getPdo());
        $invitationDao = new InvitationDao($this->getPdo());
        $utilisateurDao = new UtilisateurDao($this->getPdo());

        $groupe = $groupeDao->find($idGroupe);
        $utilisateurInvite = $utilisateurDao->find($idInvite);

        if (!$groupe || !$utilisateurInvite) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Vérifier que l'hôte est membre du groupe
        if (!in_array($idHote, $groupe->getMembres())) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Vérifier que l'utilisateur invité n'est pas déjà membre
        if (in_array($idInvite, $groupe->getMembres())) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Vérifier qu'il n'existe pas déjà une invitation en attente
        if ($invitationDao->existeInvitationEnAttente($idGroupe, $idHote, $idInvite)) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Créer l'invitation
        $invitation = new Invitation(
            $idHote,
            $idInvite,
            $idGroupe,
            date('Y-m-d H:i:s'),
            'en_attente'
        );

        $invitationDao->creerInvitation($invitation);

        header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
        exit;
    }

    /**
     * @brief Accepte une invitation à rejoindre un groupe
     */
    public function accepterInvitation(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idHote = isset($_GET['idHote']) ? (int)$_GET['idHote'] : 0;
        $idGroupe = isset($_GET['idGroupe']) ? (int)$_GET['idGroupe'] : 0;
        $idInvite = $_SESSION['idUtilisateur'];

        if ($idHote === 0 || $idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=afficherInvitations');
            exit;
        }

        $invitationDao = new InvitationDao($this->getPdo());
        $invitation = $invitationDao->find($idHote, $idInvite, $idGroupe);

        if (!$invitation || $invitation->getStatut() !== 'en_attente') {
            header('Location: index.php?controleur=groupe&methode=afficherInvitations');
            exit;
        }

        // Ajouter l'utilisateur au groupe
        $groupeDao = new GroupeDao($this->getPdo());
        $groupe = $groupeDao->find($idGroupe);

        if ($groupe) {
            $groupeDao->ajouterMembre($groupe, $idInvite, date('Y-m-d H:i:s'));
        }

        // Supprimer l'invitation
        $invitationDao->supprimerInvitation($idHote, $idInvite, $idGroupe, $invitation->getDateInvitation());

        header('Location: index.php?controleur=groupe&methode=afficherInvitations');
        exit;
    }

    /**
     * @brief Refuse une invitation à rejoindre un groupe
     */
    public function refuserInvitation(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idHote = isset($_GET['idHote']) ? (int)$_GET['idHote'] : 0;
        $idGroupe = isset($_GET['idGroupe']) ? (int)$_GET['idGroupe'] : 0;
        $idInvite = $_SESSION['idUtilisateur'];

        if ($idHote === 0 || $idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=afficherInvitations');
            exit;
        }

        $invitationDao = new InvitationDao($this->getPdo());
        $invitation = $invitationDao->find($idHote, $idInvite, $idGroupe);

        if (!$invitation || $invitation->getStatut() !== 'en_attente') {
            header('Location: index.php?controleur=groupe&methode=afficherInvitations');
            exit;
        }

        // Supprimer l'invitation
        $invitationDao->supprimerInvitation($idHote, $idInvite, $idGroupe, $invitation->getDateInvitation());

        header('Location: index.php?controleur=groupe&methode=afficherInvitations');
        exit;
    }

    /**
     * @brief Permet à un utilisateur de quitter un groupe
     */
    public function quitterGroupe(): void
    {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit;
        }

        $idGroupe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $idUtilisateur = $_SESSION['idUtilisateur'];

        if ($idGroupe === 0) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        $groupeDao = new GroupeDao($this->getPdo());
        $groupe = $groupeDao->find($idGroupe);

        if (!$groupe) {
            header('Location: index.php?controleur=groupe&methode=lister');
            exit;
        }

        // Vérifier que l'utilisateur est bien membre du groupe
        if (!in_array($idUtilisateur, $groupe->getMembres())) {
            header('Location: index.php?controleur=groupe&methode=afficher&id=' . $idGroupe);
            exit;
        }

        // Supprimer l'utilisateur du groupe
        $groupeDao->supprimerMembre($idGroupe, $idUtilisateur);

        header('Location: index.php?controleur=groupe&methode=lister');
        exit;
    }
}
