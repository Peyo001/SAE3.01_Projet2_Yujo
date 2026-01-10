<?php
/**
 * ControllerAvatar gère les actions liées aux avatars des utilisateurs comme
 * la personnalisation, l'affichage et la suppression.
 * 
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controllerAvatar = new ControllerAvatar($loader, $twig);
 * $controllerAvatar->showCustomizer(); // Affiche l'interface de personnalisation
 * $controllerAvatar->saveAvatar(); // Sauvegarde l'avatar
 * $controllerAvatar->afficherAvatar(); // Affiche l'avatar
 * $controllerAvatar->supprimerAvatar(); // Supprime l'avatar
 */
class ControllerAvatar extends Controller {
    private AvatarDao $avatarDao;

    /**
     * @brief Constructeur de ControllerAvatar
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig
     * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
        parent::__construct($loader, $twig);
        $this->avatarDao = new AvatarDao($this->getPdo());
    }

    /**
     * @brief Affiche l'interface de personnalisation de l'avatar
     * 
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
     * Récupère l'avatar existant de l'utilisateur s'il y en a un.
     * Passe les données nécessaires à la vue Twig pour le rendu.
     * 
     * @return void
     */
    public function showCustomizer(): void {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit();
        }

        $idUtilisateur = $_SESSION['idUtilisateur'];
        $avatar = $this->avatarDao->findByUtilisateur($idUtilisateur);

        $params = [
            'avatar' => $avatar,
            'title' => 'Personnaliser mon Avatar'
        ];

        // Ajouter les messages de session
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            $params['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        echo $this->getTwig()->render('avatar_customizer.twig', $params);
    }

    /**
     * @brief Sauvegarde l'avatar
     * 
     * Vérifie que l'utilisateur est connecté.
     * Valide et nettoie les données reçues.
     * Met à jour ou crée un nouvel avatar selon le cas.
     * Redirige vers l'interface de personnalisation avec un message de succès ou d'erreur.
     * 
     * @return void
     */
    public function saveAvatar(): void {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controleur=avatar&methode=showCustomizer');
            exit();
        }

        try {
            $idUtilisateur = $_SESSION['idUtilisateur'];
            
            // Récupérer et nettoyer les données
            $nom = $this->sanitize($_POST['nom'] ?? '');
            $genre = $this->sanitize($_POST['genre'] ?? 'Neutre');
            $couleurPeau = $this->sanitize($_POST['couleurPeau'] ?? 'Clair');
            $couleurCheveux = $this->sanitize($_POST['couleurCheveux'] ?? 'Brun');
            $vetements = $this->sanitize($_POST['vetements'] ?? 'T-shirt');
            $accessoires = $this->sanitize($_POST['accessoires'] ?? 'Aucun');

            // Validation
            if (strlen($nom) < 2 || strlen($nom) > 50) {
                $_SESSION['error'] = 'Le nom doit contenir entre 2 et 50 caractères';
                header('Location: index.php?controleur=avatar&methode=showCustomizer');
                exit();
            }

            // Vérifier si un avatar existe déjà
            $existingAvatar = $this->avatarDao->findByUtilisateur($idUtilisateur);
            
            $dateCreation = date('Y-m-d H:i:s');
            
            if ($existingAvatar) {
                // Mise à jour
                $avatar = new Avatar(
                    $nom,
                    $genre,
                    $existingAvatar->getDateCreation(),
                    $couleurPeau,
                    $couleurCheveux,
                    $vetements,
                    $accessoires,
                    $idUtilisateur,
                    $existingAvatar->getIdAvatar()
                );
                
                $success = $this->avatarDao->mettreAJourAvatar($avatar);
                $_SESSION['message'] = $success ? 'Avatar mis à jour avec succès ! 🎉' : 'Erreur lors de la mise à jour';
            } else {
                // Création
                $avatar = new Avatar(
                    $nom,
                    $genre,
                    $dateCreation,
                    $couleurPeau,
                    $couleurCheveux,
                    $vetements,
                    $accessoires,
                    $idUtilisateur
                );
                
                $success = $this->avatarDao->creerAvatar($avatar);
                $_SESSION['message'] = $success ? 'Avatar créé avec succès ! 🎉' : 'Erreur lors de la création';
            }

            header('Location: index.php?controleur=avatar&methode=showCustomizer');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Une erreur est survenue : ' . $e->getMessage();
            header('Location: index.php?controleur=avatar&methode=showCustomizer');
            exit();
        }
    }

    /**
     * @brief Affiche l'avatar d'un utilisateur
     * 
     * Vérifie que l'utilisateur est connecté.
     * Récupère l'avatar de l'utilisateur.
     * Redirige vers la personnalisation si aucun avatar n'existe.
     * Passe les données nécessaires à la vue Twig pour le rendu.
     * 
     * @return void
     */
    public function afficherAvatar(): void {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit();
        }

        $idUtilisateur = $_SESSION['idUtilisateur'];
        $avatar = $this->avatarDao->findByUtilisateur($idUtilisateur);

        if (!$avatar) {
            header('Location: index.php?controleur=avatar&methode=showCustomizer');
            exit();
        }

        echo $this->getTwig()->render('avatar_display.twig', [
            'avatar' => $avatar,
            'title' => 'Mon Avatar'
        ]);
    }

    /**
     * @brief Supprime l'avatar
     * 
     * Vérifie que l'utilisateur est connecté.
     * Récupère l'avatar de l'utilisateur.
     * Supprime l'avatar s'il existe.
     * Redirige vers l'interface de personnalisation avec un message de succès.
     * 
     * @return void
     */
    public function supprimerAvatar(): void {
        if (!isset($_SESSION['idUtilisateur'])) {
            header('Location: index.php?controleur=utilisateur&methode=connexion');
            exit();
        }

        $idUtilisateur = $_SESSION['idUtilisateur'];
        $avatar = $this->avatarDao->findByUtilisateur($idUtilisateur);

        if ($avatar) {
            $this->avatarDao->supprimerAvatar($avatar->getIdAvatar());
            $_SESSION['message'] = 'Avatar supprimé avec succès';
        }

        header('Location: index.php?controleur=avatar&methode=showCustomizer');
        exit();
    }

    /**
     * @brief Nettoie une entrée utilisateur
     * 
     * Utilise htmlspecialchars pour éviter les injections XSS.
     * 
     * @param string $input L'entrée utilisateur à nettoyer
     * @return string L'entrée nettoyée
     */
    private function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
