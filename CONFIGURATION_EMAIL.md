# Configuration de l'envoi d'emails avec PHPMailer

## 📧 Service d'emails configuré avec succès !

Votre application Yujo utilise maintenant **PHPMailer** pour envoyer tous les emails de manière professionnelle et fiable.

---

## ✅ Ce qui a été fait

### 1. Installation de PHPMailer
- ✅ PHPMailer déjà installé via Composer

### 2. Classe MailService créée
- 📁 Emplacement : `src/Service/MailService.php`
- 🎨 Templates HTML élégants avec le design Yujo
- 📧 Types d'emails disponibles :
  - Changement de mot de passe (confirmation + notification)
  - Mot de passe oublié (réinitialisation + notification)
  - Alertes de sécurité (tentatives de connexion échouées)
  - Newsletter (bienvenue)

### 3. Configuration SMTP ajoutée
- 📁 Fichier : `config/config.json`
- ⚙️ Paramètres prêts pour Gmail/Outlook/autre SMTP

### 4. Intégration complète
- ✅ `controller_utilisateur.class.php` : Changement de mot de passe
- ✅ `controller_utilisateur.class.php` : Mot de passe oublié
- ✅ `controller_utilisateur.class.php` : Alertes de sécurité
- ✅ `controller_newsletter.class.php` : Newsletter

---

## 🔧 Configuration nécessaire

### Option 1 : Gmail (Recommandé pour développement)

1. **Créer un mot de passe d'application Gmail** :
   - Allez sur https://myaccount.google.com/security
   - Activez la validation en 2 étapes si ce n'est pas fait
   - Recherchez "Mots de passe des applications"
   - Créez un nouveau mot de passe pour "Mail"
   - Copiez le mot de passe généré (16 caractères)

2. **Modifiez `config/config.json`** :
```json
{
    "smtp": {
        "host": "smtp.gmail.com",
        "port": 587,
        "encryption": "tls",
        "username": "votre.email@gmail.com",
        "password": "xxxx xxxx xxxx xxxx",
        "from_email": "votre.email@gmail.com",
        "from_name": "Yujo"
    }
}
```

### Option 2 : Outlook / Hotmail

```json
{
    "smtp": {
        "host": "smtp-mail.outlook.com",
        "port": 587,
        "encryption": "tls",
        "username": "votre.email@outlook.com",
        "password": "votre_mot_de_passe",
        "from_email": "votre.email@outlook.com",
        "from_name": "Yujo"
    }
}
```

### Option 3 : Serveur SMTP personnalisé

```json
{
    "smtp": {
        "host": "smtp.votre-serveur.com",
        "port": 465,
        "encryption": "ssl",
        "username": "votre-username",
        "password": "votre-password",
        "from_email": "noreply@yujo.fr",
        "from_name": "Yujo"
    }
}
```

---

## 🧪 Test de l'envoi d'emails

### Tester la newsletter :
1. Lancez votre application WAMP
2. Allez dans les paramètres utilisateur
3. Inscrivez-vous à la newsletter
4. Vérifiez votre boîte mail !

### Tester le changement de mot de passe :
1. Connectez-vous à votre compte
2. Allez dans Paramètres > Mot de passe
3. Changez votre mot de passe
4. Vous recevrez un email de confirmation

### Tester le mot de passe oublié :
1. Sur la page de connexion, cliquez sur "Mot de passe oublié"
2. Entrez votre email
3. Vous recevrez un lien de réinitialisation

---

## 🐛 Débogage

### Les emails ne s'envoient pas ?

1. **Vérifiez les logs d'erreur** :
   - PHP : `C:\wamp64\logs\php_error.log`
   - Apache : `C:\wamp64\logs\apache_error.log`

2. **Vérifiez la configuration SMTP** :
   - Username et password corrects ?
   - Mot de passe d'application Gmail activé ?
   - Port firewall ouvert ?

3. **Testez manuellement** :
   Créez un fichier `test_email.php` à la racine :
   ```php
   <?php
   require_once 'include.php';
   
   try {
       $mailService = new MailService();
       $result = $mailService->envoyerEmailBienvenueNewsletter(
           'votre.email@test.com',
           'Test'
       );
       
       echo $result ? "✅ Email envoyé !" : "❌ Échec de l'envoi";
   } catch (Exception $e) {
       echo "❌ Erreur : " . $e->getMessage();
   }
   ```

4. **Désactiver la vérification SSL** (déjà fait pour développement) :
   Dans `MailService.php`, les options SSL sont déjà configurées pour WAMP.

---

## 🎨 Personnalisation des emails

### Modifier le template HTML :
Éditez la méthode `genererTemplateHTML()` dans `src/Service/MailService.php`

### Ajouter un nouveau type d'email :
1. Créez une nouvelle méthode dans `MailService.php`
2. Utilisez le même pattern que les autres méthodes
3. Appelez-la depuis votre contrôleur

Exemple :
```php
public function envoyerEmailBienvenue(Utilisateur $utilisateur): bool
{
    try {
        $this->reinitialiser();
        $this->mail->addAddress($utilisateur->getEmail());
        $this->mail->Subject = 'Bienvenue sur Yujo !';
        // ... votre contenu
        $this->mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur : " . $e->getMessage());
        return false;
    }
}
```

---

## 📦 Production

Pour la production, pensez à :

1. ✅ Activer la vérification SSL :
```php
'ssl' => [
    'verify_peer' => true,
    'verify_peer_name' => true,
    'allow_self_signed' => false
]
```

2. ✅ Utiliser un vrai serveur SMTP professionnel :
   - SendGrid
   - Mailgun
   - Amazon SES
   - OVH Mail

3. ✅ Protéger vos credentials :
   - Ne jamais commiter `config.json` avec des vrais mots de passe
   - Utiliser des variables d'environnement

---

## 📚 Documentation

- **PHPMailer** : https://github.com/PHPMailer/PHPMailer
- **Gmail SMTP** : https://support.google.com/mail/answer/7126229
- **Outlook SMTP** : https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings

---

## ✨ Fonctionnalités des emails

### Design responsive
- Compatible mobile, desktop, Outlook
- Gradient Yujo dans le header
- Boutons d'action stylisés
- Footer professionnel

### Sécurité
- Liens avec tokens sécurisés
- Expiration des liens
- Notifications de changements
- Alertes tentatives échouées

### Templates disponibles
- ✅ Confirmation changement de mot de passe
- ✅ Notification changement effectué
- ✅ Réinitialisation mot de passe oublié
- ✅ Notification réinitialisation effectuée
- ✅ Alerte sécurité (tentatives échouées)
- ✅ Bienvenue newsletter

---

Bon développement ! 🚀
