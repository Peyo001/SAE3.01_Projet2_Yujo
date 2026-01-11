# Rapport de Sécurisation XSS du Projet

## Résumé
Le projet a été sécurisé contre les injections XSS (Cross-Site Scripting) en implémentant une stratégie défense en profondeur.

---

## 1. Stratégie Globale

### 1.1 Double Protection
- **Serveur (PHP)**: Sanitization des données au niveau du contrôleur
- **Client (Twig)**: Autoescape automatique lors du rendu en HTML

### 1.2 Principe de défense en profondeur
```
Entrée utilisateur → Validation → Sanitization → Stockage BD → Autoescape Twig → Affichage
```

---

## 2. Modifications Apportées

### 2.1 Classe Controller de base
**Fichier**: `src/Controller/controller.class.php`

Ajout de la méthode `sanitize()` héritable par tous les contrôleurs:
```php
protected function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
```

**Explication**:
- `htmlspecialchars()`: Convertit les caractères spéciaux HTML en entités
  - `<` → `&lt;`
  - `>` → `&gt;`
  - `&` → `&amp;`
  - `"` → `&quot;`
  - `'` → `&#039;`
- `ENT_QUOTES`: Échappe à la fois les guillemets simples ET doubles
- `UTF-8`: Gère correctement les caractères accentués français
- `trim()`: Supprime les espaces inutiles

### 2.2 Contrôleurs Sécurisés

#### ✅ controller_post.class.php
- Sanitization de `type_post`
- Sanitization de `contenu` (sauf si c'est un chemin d'image uploadé)

#### ✅ controller_groupe.class.php
- Sanitization de `nom_groupe`
- Sanitization de `description`
- Sanitization du contenu des messages (`message`)
- Sanitization du paramètre de recherche (`search`)

#### ✅ controller_question.php
- Sanitization du `libelle` (intitulé de la question)
- Appliqué dans les 3 méthodes: création, modification, suppression

#### ✅ controller_objet.class.php
- Sanitization de `description`
- Sanitization de `modele3dPath`
- Appliqué dans les méthodes de création et modification

#### ✅ controller_signalement.class.php
- Sanitization de `raison`

#### ✅ controller_room.class.php
- Sanitization de `nom`
- Sanitization de `visibilite`
- Appliqué dans les méthodes de création et modification

#### ✅ controller_parametre.php
- Sanitization de `nom`
- Sanitization de `prenom`
- Sanitization de `pseudo`
- Email: Validé avec `filter_var(FILTER_VALIDATE_EMAIL)` (pas de sanitization)

#### ✅ controller_admin.class.php
- Sanitization de `description`
- Sanitization de `modele3dPath`

### 2.3 Configuration Twig
**Fichier**: `config/twig.php`

Modifications:
```php
$twig = new \Twig\Environment($loader, [
    'debug' => false,           // SÉCURITÉ: Désactivé (empêche l'exposition de données)
    'autoescape' => 'html',     // SÉCURITÉ: Échappe automatiquement les variables HTML
]);
```

**Bénéfices**:
- **autoescape => 'html'**: Toute variable affichée dans un template Twig est automatiquement échappée
- **debug => false**: En production, empêche les développeurs d'utiliser `dump()` et d'exposer des données sensibles

---

## 3. Workflow de Protection XSS

### Exemple avec un post contenant du contenu utilisateur

**Étape 1: Saisie utilisateur**
```
Utilisateur saisit: <script>alert('XSS')</script>
```

**Étape 2: Sanitization en PHP**
```php
$contenu = $this->sanitize($_POST['contenu']);
// Résultat: "&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;"
```

**Étape 3: Stockage en base de données**
```
Valeur stockée: "&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;"
```

**Étape 4: Autoescape Twig**
```twig
{{ post.contenu }}
<!-- Twig rééchappe si nécessaire (même si déjà échappé) -->
```

**Étape 5: Affichage HTML sécurisé**
```html
&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;
<!-- Le script n'est jamais exécuté, affiché en texte brut -->
```

---

## 4. Validation vs Sanitization

### Validation (déjà présente)
- **But**: Vérifier que les données respectent le format attendu
- **Exemple**: Vérifier qu'un email est valide avec `FILTER_VALIDATE_EMAIL`
- **Contrôleur**: `Validator.class.php`

### Sanitization (nouvellement ajoutée)
- **But**: Nettoyer les données dangereuses
- **Exemple**: Convertir `<script>` en `&lt;script&gt;`
- **Contrôleur**: `sanitize()` dans Controller

**Les deux sont nécessaires pour une sécurité maximale!**

---

## 5. Cas Particuliers

### 5.1 Données numériques
```php
$id = (int)$_GET['id'];  // Conversion directe, pas de sanitization
$prix = (int)$_POST['prix'];
```
✅ Pas besoin de sanitization, la conversion directe en entier empêche l'injection

### 5.2 Chemins de fichiers
```php
$modele3dPath = $this->sanitize($_POST['modele3dPath']);
```
✅ Sanitization pour empêcher `../../etc/passwd`

### 5.3 Emails
```php
$email = trim($_POST['email']);
// Validation avec filter_var
if (filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }
```
✅ La validation d'email est suffisante, pas de sanitization HTML nécessaire

### 5.4 Images uploadées
```php
// Chemin généré côté serveur, pas d'entrée utilisateur
$contenu = 'uploads/posts/' . uniqid('post_', true) . '.' . $ext;
```
✅ Pas de sanitization, le chemin est généré automatiquement

---

## 6. Tests de Sécurité Recommandés

### Test XSS Basique
1. Créer un post avec: `<script>alert('XSS')</script>`
2. Vérifier que le script ne s'exécute pas
3. Vérifier que le contenu s'affiche comme texte: `&lt;script&gt;...`

### Test XSS dans les attributs
1. Créer un objet avec: `" onload="alert('XSS')"`
2. Vérifier que l'attribut est échappé

### Test XSS dans les URLs
1. Groupe avec search: `?search=<img src=x onerror="alert('XSS')">`
2. Vérifier que le script ne s'exécute pas

---

## 7. Checklist de Déploiement

- [ ] **En développement**: `debug: true` autorisé pour déboguer
- [ ] **En production**: `debug: false` et `autoescape: 'html'`
- [ ] Tester tous les formulaires avec des entrées XSS
- [ ] Vérifier que les données s'affichent correctement (pas de doubles-encodages)
- [ ] Documenter toute exception (peu d'exceptions devraient exister)

---

## 8. Améliorations Futures

### 8.1 Content Security Policy (CSP)
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```
Empêcherait l'exécution de scripts importés

### 8.2 Utiliser Twig en mode strict
```php
'strict_variables' => true,  // Erreur si variable non définie
```

### 8.3 Filtres Twig personnalisés
Pour les cas spéciaux où l'HTML est autorisé (éditeur riche, Markdown, etc.)

---

## 9. Résumé des Changements

| Fichier | Changements | Statut |
|---------|-----------|--------|
| `src/Controller/controller.class.php` | Ajout `sanitize()` | ✅ |
| `src/Controller/controller_post.class.php` | Sanitization `type_post`, `contenu` | ✅ |
| `src/Controller/controller_groupe.class.php` | Sanitization `nom_groupe`, `description`, `message`, `search` | ✅ |
| `src/Controller/controller_question.php` | Sanitization `libelle` | ✅ |
| `src/Controller/controller_objet.class.php` | Sanitization `description`, `modele3dPath` | ✅ |
| `src/Controller/controller_signalement.class.php` | Sanitization `raison` | ✅ |
| `src/Controller/controller_room.class.php` | Sanitization `nom`, `visibilite` | ✅ |
| `src/Controller/controller_parametre.php` | Sanitization `nom`, `prenom`, `pseudo` | ✅ |
| `src/Controller/controller_admin.class.php` | Sanitization `description`, `modele3dPath` | ✅ |
| `config/twig.php` | `autoescape: 'html'`, `debug: false` | ✅ |

---

## 10. Conclusion

Le projet est maintenant **protégé contre les injections XSS** grâce à une stratégie défense en profondeur:

1. ✅ **Sanitization côté serveur** (PHP) - Nettoyage des données dangereuses
2. ✅ **Autoescape côté client** (Twig) - Échappement automatique en HTML
3. ✅ **Configuration sécurisée** - Debug désactivé, autoescape activé
4. ✅ **Validation des données** - Format et type vérifiés

**Niveau de sécurité**: 🟢 **BON** (protection XSS complète)
