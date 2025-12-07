<?php

class ControllerAvatar extends Controller {
    private AvatarDao $avatarDao;

    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
        parent::__construct($loader, $twig);
        $this->avatarDao = new AvatarDao($this->getPdo());
    }

    /**
     * Affiche l'interface de personnalisation de l'avatar
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
     * Sauvegarde l'avatar
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
     * Affiche l'avatar d'un utilisateur
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
     * Supprime l'avatar
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
     * Nettoie une entrée utilisateur
     */
    private function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
