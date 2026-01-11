<?php
/**
 * Classe ControllerNewsletter
 * 
 * Cette classe gère les opérations liées à la newsletter.
 * 
 * Exemple d'utilisation :
 * $controller = new ControllerNewsletter($loader, $twig);
 * $controller->inscrire();
 */
class ControllerNewsletter extends Controller
{
    private NewsletterDAO $newsletterDAO;

    /**
     * Constructeur du contrôleur Newsletter.
     * 
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
     * @param \Twig\Environment $twig Environnement Twig.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        parent::__construct($loader, $twig);
        $this->newsletterDAO = new NewsletterDAO($this->getPdo());
    }

    /**
     * Inscrit un email à la newsletter via POST.
     */
    public function inscrire(): void
    {
        $message = '';
        $type = 'danger';

        if ($this->getPost() !== null && isset($this->getPost()['email'])) {
            $email = trim($this->getPost()['email']);

            // Validation de l'email
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($this->newsletterDAO->emailExiste($email)) {
                    $message = 'Cet email est déjà inscrit à la newsletter.';
                    $type = 'warning';
                } else {
                    if ($this->newsletterDAO->inscrire($email)) {
                        $message = 'Merci ! Vous êtes maintenant inscrit à la newsletter.';
                        $type = 'success';
                    } else {
                        $message = 'Une erreur est survenue. Veuillez réessayer.';
                    }
                }
            } else {
                $message = 'Veuillez entrer une adresse email valide.';
            }
        }

        // Stocke le message dans la session pour l'afficher après redirection
        $_SESSION['newsletter_message'] = $message;
        $_SESSION['newsletter_type'] = $type;

        // Redirige vers la page d'origine ou l'accueil
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?controleur=accueil&methode=afficher';
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Désinscrit un email de la newsletter.
     */
    public function desinscrire(): void
    {
        $message = '';
        $type = 'danger';

        if ($this->getPost() !== null && isset($this->getPost()['email'])) {
            $email = trim($this->getPost()['email']);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($this->newsletterDAO->desinscrire($email)) {
                    $message = 'Vous avez été désinscrit de la newsletter.';
                    $type = 'success';
                } else {
                    $message = 'Une erreur est survenue. Veuillez réessayer.';
                }
            } else {
                $message = 'Veuillez entrer une adresse email valide.';
            }
        }

        $_SESSION['newsletter_message'] = $message;
        $_SESSION['newsletter_type'] = $type;

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?controleur=accueil&methode=afficher';
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Affiche la liste des inscrits (admin uniquement).
     */
    public function listerInscrits(): void
    {
        // Vérification des droits admin (à adapter selon votre système)
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']->getTypeCompte() !== 'Admin') {
            header('Location: index.php');
            exit;
        }

        $inscrits = $this->newsletterDAO->getInscritsActifs();
        $total = $this->newsletterDAO->compterInscritsActifs();

        echo $this->getTwig()->render('admin/liste_newsletter.twig', [
            'inscrits' => $inscrits,
            'total' => $total
        ]);
    }
}
