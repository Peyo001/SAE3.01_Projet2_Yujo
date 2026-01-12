<?php
/**
 * Classe MailService
 * 
 * Service centralisé pour l'envoi d'emails via PHPMailer.
 * Gère tous les types d'emails : newsletter, changement de mot de passe, notifications de sécurité, etc.
 * 
 * Utilise SMTP avec authentification pour un envoi fiable.
 * Configuration chargée depuis config/config.json
 * 
 * Exemples d'utilisation :
 * $mailService = new MailService();
 * $mailService->envoyerEmailChangementMotDePasse($utilisateur, $lienConfirmation);
 * $mailService->envoyerEmailMotDePasseOublie($utilisateur, $lienReinitialisation);
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $config;
    private $mail;
    
    /**
     * Constructeur : charge la configuration SMTP et initialise PHPMailer
     */
    public function __construct()
    {
        // Charger la configuration
        $configPath = __DIR__ . '/../../config/config.json';
        if (!file_exists($configPath)) {
            throw new Exception("Fichier de configuration introuvable : $configPath");
        }
        
        $configJson = file_get_contents($configPath);
        $this->config = json_decode($configJson, true);
        
        if (!isset($this->config['smtp'])) {
            throw new Exception("Configuration SMTP manquante dans config.json");
        }
        
        // Initialiser PHPMailer
        $this->mail = new PHPMailer(true);
        $this->configurerSMTP();
    }
    
    /**
     * Configure les paramètres SMTP de PHPMailer
     */
    private function configurerSMTP(): void
    {
        try {
            // Configuration du serveur SMTP
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp']['host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp']['username'];
            $this->mail->Password = $this->config['smtp']['password'];
            $this->mail->SMTPSecure = $this->config['smtp']['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $this->config['smtp']['port'];
            $this->mail->CharSet = 'UTF-8';
            
            // Désactiver la vérification SSL en développement (à activer en production)
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Expéditeur par défaut
            $this->mail->setFrom(
                $this->config['smtp']['from_email'] ?? 'noreply@yujo.fr',
                $this->config['smtp']['from_name'] ?? 'Yujo'
            );
            
        } catch (Exception $e) {
            throw new Exception("Erreur de configuration SMTP : " . $e->getMessage());
        }
    }
    
    /**
     * Réinitialise PHPMailer pour un nouvel envoi
     */
    private function reinitialiser(): void
    {
        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
        $this->mail->clearCustomHeaders();
        $this->mail->Body = '';
        $this->mail->AltBody = '';
        $this->mail->Subject = '';
    }
    
    /**
     * Envoie un email de changement de mot de passe avec lien de confirmation
     * 
     * @param Utilisateur $utilisateur L'utilisateur qui change son mot de passe
     * @param string $lienConfirmation Le lien de confirmation sécurisé
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailChangementMotDePasse(Utilisateur $utilisateur, string $lienConfirmation): bool
    {
        try {
            $this->reinitialiser();
            
            $this->mail->addAddress($utilisateur->getEmail(), $utilisateur->getPseudo());
            $this->mail->Subject = 'Confirmation du changement de mot de passe - Yujo';
            
            // Version HTML
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                'Confirmation du changement de mot de passe',
                $utilisateur->getPseudo(),
                "Vous avez demandé à changer votre mot de passe Yujo.",
                "Cliquez sur le bouton ci-dessous pour confirmer ce changement :",
                $lienConfirmation,
                "Confirmer le changement",
                "Ce lien est valide pendant 1 heure.<br>Si vous n'avez pas demandé ce changement, ignorez cet email."
            );
            
            // Version texte brut (fallback)
            $this->mail->AltBody = "Bonjour {$utilisateur->getPseudo()},\n\n"
                . "Vous avez demandé à changer votre mot de passe Yujo.\n\n"
                . "Cliquez sur le lien ci-dessous pour confirmer ce changement :\n"
                . $lienConfirmation . "\n\n"
                . "Ce lien est valide pendant 1 heure.\n\n"
                . "Si vous n'avez pas demandé ce changement, ignorez cet email.\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email changement MDP : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de confirmation après changement de mot de passe
     * 
     * @param Utilisateur $utilisateur L'utilisateur concerné
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailConfirmationChangementMotDePasse(Utilisateur $utilisateur): bool
    {
        try {
            $this->reinitialiser();
            
            $this->mail->addAddress($utilisateur->getEmail(), $utilisateur->getPseudo());
            $this->mail->Subject = 'Confirmation : Votre mot de passe a été changé - Yujo';
            
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                'Mot de passe changé avec succès',
                $utilisateur->getPseudo(),
                "Votre mot de passe Yujo a été changé avec succès.",
                "Date : " . date('d/m/Y à H:i'),
                null,
                null,
                "Si vous ne reconnaissez pas cette action, veuillez contacter notre équipe de sécurité immédiatement à <a href='mailto:security@yujo.fr'>security@yujo.fr</a>."
            );
            
            $this->mail->AltBody = "Bonjour {$utilisateur->getPseudo()},\n\n"
                . "Votre mot de passe Yujo a été changé avec succès.\n"
                . "Date : " . date('d/m/Y H:i') . "\n\n"
                . "Si vous ne reconnaissez pas cette action, contactez-nous : security@yujo.fr\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email confirmation changement MDP : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de réinitialisation de mot de passe oublié
     * 
     * @param Utilisateur $utilisateur L'utilisateur qui a oublié son mot de passe
     * @param string $lienReinitialisation Le lien de réinitialisation sécurisé
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailMotDePasseOublie(Utilisateur $utilisateur, string $lienReinitialisation): bool
    {
        try {
            $this->reinitialiser();
            
            $this->mail->addAddress($utilisateur->getEmail(), $utilisateur->getPseudo());
            $this->mail->Subject = 'Réinitialisation de votre mot de passe - Yujo';
            
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                'Réinitialisation de votre mot de passe',
                $utilisateur->getPseudo(),
                "Vous avez demandé à réinitialiser votre mot de passe Yujo.",
                "Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :",
                $lienReinitialisation,
                "Réinitialiser mon mot de passe",
                "Ce lien est valide pendant 30 minutes.<br>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre compte reste sécurisé."
            );
            
            $this->mail->AltBody = "Bonjour {$utilisateur->getPseudo()},\n\n"
                . "Vous avez demandé à réinitialiser votre mot de passe Yujo.\n\n"
                . "Cliquez sur le lien ci-dessous pour créer un nouveau mot de passe :\n"
                . $lienReinitialisation . "\n\n"
                . "Ce lien est valide pendant 30 minutes.\n\n"
                . "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email mot de passe oublié : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de notification après réinitialisation de mot de passe
     * 
     * @param Utilisateur $utilisateur L'utilisateur concerné
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailNotificationReinitialisation(Utilisateur $utilisateur): bool
    {
        try {
            $this->reinitialiser();
            
            $this->mail->addAddress($utilisateur->getEmail(), $utilisateur->getPseudo());
            $this->mail->Subject = 'Notification : Votre mot de passe a été réinitialisé - Yujo';
            
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                'Mot de passe réinitialisé',
                $utilisateur->getPseudo(),
                "Votre mot de passe Yujo a été réinitialisé avec succès.",
                "Date : " . date('d/m/Y à H:i'),
                null,
                null,
                "Si vous ne reconnaissez pas cette action, veuillez contacter notre équipe de sécurité immédiatement : <a href='mailto:security@yujo.fr'>security@yujo.fr</a>"
            );
            
            $this->mail->AltBody = "Bonjour {$utilisateur->getPseudo()},\n\n"
                . "Votre mot de passe Yujo a été réinitialisé avec succès.\n"
                . "Date : " . date('d/m/Y H:i') . "\n\n"
                . "Si vous ne reconnaissez pas cette action, contactez-nous immédiatement : security@yujo.fr\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email notification réinitialisation : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email d'alerte sécurité pour tentatives de connexion échouées
     * 
     * @param Utilisateur $utilisateur L'utilisateur concerné
     * @param string $ip L'adresse IP de la tentative
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailAlerteSecurite(Utilisateur $utilisateur, string $ip): bool
    {
        try {
            $this->reinitialiser();
            
            $this->mail->addAddress($utilisateur->getEmail(), $utilisateur->getPseudo());
            $this->mail->Subject = '⚠️ Alerte sécurité : tentatives de connexion échouées - Yujo';
            
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                '⚠️ Alerte sécurité',
                $utilisateur->getPseudo(),
                "Nous avons détecté plusieurs tentatives de connexion échouées sur votre compte Yujo.",
                "<strong>Détails :</strong><br>" .
                "• Email : {$utilisateur->getEmail()}<br>" .
                "• Adresse IP : $ip<br>" .
                "• Date : " . date('d/m/Y à H:i') . "<br><br>" .
                "Par sécurité, les nouvelles connexions sont temporairement bloquées pendant 2 minutes.",
                "index.php?controleur=utilisateur&methode=afficherChangementMotDePasse",
                "Changer mon mot de passe",
                "Si ce n'était pas vous, nous vous recommandons de changer votre mot de passe immédiatement."
            );
            
            $this->mail->AltBody = "Bonjour {$utilisateur->getPseudo()},\n\n"
                . "Alerte sécurité : tentatives de connexion échouées.\n"
                . "Email : {$utilisateur->getEmail()}\n"
                . "IP : $ip\n"
                . "Date : " . date('d/m/Y H:i') . "\n\n"
                . "Connexions bloquées pendant 2 minutes.\n"
                . "Si ce n'était pas vous, changez votre mot de passe.\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email alerte sécurité : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de bienvenue pour la newsletter
     * 
     * @param string $email L'email du destinataire
     * @param string $prenom Le prénom du destinataire (optionnel)
     * @return bool True si l'email a été envoyé avec succès
     */
    public function envoyerEmailBienvenueNewsletter(string $email, string $prenom = null): bool
    {
        try {
            $this->reinitialiser();
            
            $nom = $prenom ?? "nouveau membre";
            $this->mail->addAddress($email, $prenom);
            $this->mail->Subject = '🎉 Bienvenue dans la newsletter Yujo !';
            
            $this->mail->isHTML(true);
            $this->mail->Body = $this->genererTemplateHTML(
                'Bienvenue dans la newsletter Yujo ! 🎉',
                $nom,
                "Merci de vous être inscrit à notre newsletter !",
                "Vous recevrez désormais nos actualités, nouveautés et contenus exclusifs directement dans votre boîte mail.",
                "index.php?controleur=accueil&methode=afficher",
                "Découvrir Yujo",
                "Vous pouvez vous désinscrire à tout moment depuis vos paramètres."
            );
            
            $this->mail->AltBody = "Bonjour $nom,\n\n"
                . "Bienvenue dans la newsletter Yujo !\n\n"
                . "Vous recevrez nos actualités et contenus exclusifs.\n"
                . "Désinscription possible à tout moment.\n\n"
                . "— Équipe Yujo";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email newsletter : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Génère un template HTML élégant pour les emails
     * 
     * @param string $titre Titre principal de l'email
     * @param string $nomDestinataire Nom du destinataire
     * @param string $messageIntro Message d'introduction
     * @param string $messageCorps Corps du message
     * @param string|null $lienBouton Lien du bouton d'action (optionnel)
     * @param string|null $texteBouton Texte du bouton (optionnel)
     * @param string|null $messagePied Note en pied de page (optionnel)
     * @return string Le HTML complet de l'email
     */
    private function genererTemplateHTML(
        string $titre,
        string $nomDestinataire,
        string $messageIntro,
        string $messageCorps,
        ?string $lienBouton = null,
        ?string $texteBouton = null,
        ?string $messagePied = null
    ): string {
        $boutonHTML = '';
        if ($lienBouton && $texteBouton) {
            $boutonHTML = "
                <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='margin: 30px 0;'>
                    <tr>
                        <td style='border-radius: 8px; background: linear-gradient(135deg, #eb3f73 0%, #c12458 45%, #2c3696 100%);'>
                            <a href='$lienBouton' target='_blank' style='display: inline-block; padding: 16px 36px; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                                $texteBouton
                            </a>
                        </td>
                    </tr>
                </table>
            ";
        }
        
        $piedHTML = $messagePied ? "<p style='color: #666; font-size: 14px; margin-top: 20px;'>$messagePied</p>" : '';
        
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$titre</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 40px 20px;'>
                <tr>
                    <td align='center'>
                        <table role='presentation' width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden;'>
                            <!-- Header -->
                            <tr>
                                <td style='background: linear-gradient(135deg, #eb3f73 0%, #c12458 45%, #2c3696 100%); padding: 40px; text-align: center;'>
                                    <h1 style='margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;'>Yujo</h1>
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style='padding: 40px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333; font-size: 24px;'>$titre</h2>
                                    <p style='margin: 0 0 15px 0; color: #333; font-size: 16px; line-height: 1.6;'>
                                        Bonjour <strong>$nomDestinataire</strong>,
                                    </p>
                                    <p style='margin: 0 0 15px 0; color: #333; font-size: 16px; line-height: 1.6;'>
                                        $messageIntro
                                    </p>
                                    <p style='margin: 0 0 15px 0; color: #333; font-size: 16px; line-height: 1.6;'>
                                        $messageCorps
                                    </p>
                                    
                                    $boutonHTML
                                    
                                    $piedHTML
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f8f8; padding: 30px; text-align: center; border-top: 1px solid #eee;'>
                                    <p style='margin: 0 0 10px 0; color: #999; font-size: 14px;'>
                                        © " . date('Y') . " Yujo - Tous droits réservés
                                    </p>
                                    <p style='margin: 0; color: #999; font-size: 12px;'>
                                        <a href='index.php?controleur=parametre&methode=afficherPolitiqueConfidentialite' style='color: #666; text-decoration: none;'>Politique de confidentialité</a> | 
                                        <a href='index.php?controleur=parametre&methode=afficherMentionsLegales' style='color: #666; text-decoration: none;'>Mentions légales</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
}
