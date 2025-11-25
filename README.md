# SAE3.01_Projet2_Yujo

**Yujo** est une application web de réseau social immersive et authentique, éveloppée dans le cadre de la SAE 3.01 (BUT Informatique S3-S4).


## Présentation

Dans un monde où les réseaux sociaux se résument souvent à des images parfaites et des interactions superficielles, Yujo propose une alternative révolutionnaire : un réseau social immersif et authentique, fondé sur des rooms virtuelles personnalisables.
Avec Yujo, fini les profils figés et les fils d’actualité passifs. Ici, vous créez votre propre espace personnel, un lieu unique en 2D ou 3D, façonné selon vos goûts, vos passions et votre personnalité. Choisissez un thème, ajoutez du mobilier, des objets qui vous représentent et façonnez une ambiance qui vous ressemble. Votre room devient une véritable extension de vous-même — un univers qui raconte votre histoire et votre manière d’être.

Les autres utilisateurs peuvent découvrir votre room en visitant votre profil ou en la retrouvant comme un post sur la page d’accueil. Vous pouvez, vous aussi, explorer les rooms d’autres membres, vous inspirer de leurs créations et échanger directement avec eux. Chaque espace devient ainsi une porte ouverte sur la personnalité de son créateur et une opportunité de rencontres sincères.

Mais Yujo ne se limite pas à la simple contemplation : c’est un espace d’interaction et de partage. Les rooms peuvent accueillir des discussions, des défis, des mini-jeux ou des projets collaboratifs. Ces expériences sont conçues pour encourager des échanges naturels, authentiques et créatifs — loin de la superficialité des réseaux classiques.

Contrairement aux plateformes traditionnelles, Yujo met l’accent sur la sincérité, l’inclusivité et le bien-être numérique. Pas de pression sociale, pas de course aux likes : ici, l’objectif est d’être soi-même, sans filtres ni artifices. L’application promeut un usage plus sain et réfléchi des réseaux sociaux, grâce à des contenus éducatifs qui valorisent la bienveillance et les bonnes pratiques en ligne.

Avec Yujo, vous ne subissez plus les réseaux sociaux — vous les façonnez à votre image.

Créez votre espace, exprimez-vous librement et vivez des interactions numériques enfin authentiques, dynamiques et humaines.

Rejoignez Yujo et redécouvrez ce que signifie être vraiment connecté.

## Installation

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/votre-utilisateur/SAE3.01_Projet2_Yujo.git
   cd SAE3.01_Projet2_Yujo
   ```

2. **Installer les dépendances :**
    Si ce n'est pas déjà fait: 
   - PHP >= 8.0
   - Composer
   - Base de données MySQL/MariaDB

    Executez ensuite: 
   ```bash
   composer install
   ```

3. **Configurer la base de données :**
   - Créer le fichier `config/config.json` en le copiant à partir de `configExample.json`.
   - Modifier le fichier `config/config.json` avec vos paramètres.

4. **Lancer le serveur local :**
   Avec WAMP ou autre (si vous utilisez Linux le service php tourne normalement déjà, déplacez le projet dans le dossier servi par Apache ou configurez le)

## Structure du projet

```
SAE3.01_Projet2_Yujo/
│
├── config/           # Fichiers de configuration
├── public/           # Point d’entrée de l’application (index.php)
├── src/              # Code source (DAO, modèles, contrôleurs)
├── view/        # Templates Twig
├── tests/            # Tests unitaires
└── README.md         # Documentation
```

## Conventions de code & bonnes pratiques

Pour contribuer au projet, vous devez suivre certaines conventions et bonnes pratiques.

- **Commits** : Utilisez des messages clairs et descriptifs (ex : `Ajout de la gestion des rooms`).
- **Documentation** : Commentez les méthodes et classes avec des docblocks PHP (`/** ... */`).

Pour chaque fonction on mettra au moins une docstring expliquant ce que fait la fonction et optionnellement la description des parametres (pour les fonctions compliquées).
Exemple complet: 
```php
/**
 * Classe Config
 *
 * Cette classe gère les paramètres de configuration stockés dans un fichier JSON.
 * Elle permet de récupérer des paramètres spécifiques via une clé donnée.
 *
 * Exemple d'utilisation :
 * ```php
 * $dbHost = Config::getParameter('db_host');
 * ```
 */
class exempleDocumentation {



    /**
     * Ceci est un exemple de méthode bien documentée.
     *
     * Cette méthode ne fait rien de particulier, mais elle illustre comment rédiger une docstring claire et informative.
     *
     * @param string $param1 Description du premier paramètre.
     * @param int $param2 Description du deuxième paramètre.
     * @return bool Description de la valeur de retour.
     * @throws Exception Description des exceptions pouvant être levées.
     */
    public function methodeExemple(string $param1, int $param2): bool {
        // Implémentation de la méthode
        return true;
    }
}
```

De manière générale tout le code doit être en français.

- **Nommage des fichiers** : Utilisez le camelCase pour les fichiers PHP (`maClasse.php`), kebab-case pour les templates (`page-accueil.html.twig`).
- **Variables** : Utilisez le camelCase (`$myVar`), noms explicites.
- **Classes** : Utilisez le PascalCase (`MyClass`), noms explicites.
- **Fonctions/Méthodes** : Utilisez le camelCase (`myFunction()`), noms explicites.
- **Indentation** : Respectez une indentation cohérente (4 espaces).
- **Organisation** : Séparez le code métier, la présentation et la configuration dans les dossiers dédiés.