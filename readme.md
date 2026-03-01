# Le Monde — Agrégateur de flux RSS

L'objectif de se projet solaire etait d'utilisé les flux RSS du journal **Le Monde** mais la grande difficulté etait surtout de les sauvegarder dans des fichier csv en imitant les base de donnée pour que l'utilisateur puisse garder ses preferences.

---

## 📁 Structure du projet

```
PHP-projet/
├── index.php                        # Page de connexion
├── .env                             # Variables d'environnement (identifiants Gmail)
├── vendor/                          # Dépendances Composer (PHPMailer)
└── asset/
    ├── pages/
    │   ├── inscription2.php         # Page d'inscription
    │   ├── GmailmdpOublie.php       # Page mot de passe oublié
    │   ├── mdp.php                  # Page création / réinitialisation du mot de passe
    │   ├── dashboard.php            # Tableau de bord (flux RSS)
    │   ├── CentreInteret.php        # Gestion des centres d'intérêt
    │   └── deconnexion.php          # Déconnexion
    ├── baseDonne/
    │   ├── Utilisateur.csv          # Base de données des utilisateurs
    │   └── centreInterets.csv       # Centres d'intérêt par utilisateur
    ├── style/
    │   ├── connexion.css
    │   ├── dashboard.css
    │   └── CentreInteret.css
    └── image/
        └── Logo-le-monde.png
```

---

## Prérequis

- **PHP** >= 7.4
- **Composer** (pour PHPMailer)
- **Serveur local** (MAMP, XAMPP, Laragon...)
- Un compte **Gmail** avec un mot de passe d'application

---

## Installation

**1. Cloner le projet**
```bash
git clone https://github.com/RaphT12/Le-Monde-Aggr-gateur-flux-RSS.git
cd PHP-projet
```

**2. Installer les dépendances Composer**
```bash
composer require phpmailer/phpmailer
```

**3. Créer le fichier `.env`** à la racine du projet
```env
GMAIL_USER=votre.email@gmail.com
GMAIL_PASSWORD=votre_mot_de_passe_application
```

> ⚠️ Ne jamais commiter le `.env` — ajoutez-le à votre `.gitignore`

**4. Créer les fichiers CSV** dans `asset/baseDonne/`

`Utilisateur.csv` :
```
nom,email,token,password
```

`centreInterets.csv` :
```
id_user,name,url
```

**5. Lancer le serveur local** et accéder à :
```
http://localhost:8888/PHP/PHP-projet/index.php
```

---

## Fonctionnement général

### 1. Inscription (`inscription2.php`)
- L'utilisateur saisit son **nom** et son **email**
- Le script vérifie que l'email n'est pas déjà utilisé dans le CSV
- Un **token unique** est généré avec `bin2hex(random_bytes(16))`
- Un **email de confirmation** est envoyé via PHPMailer (SMTP Gmail)
- L'utilisateur est enregistré dans `Utilisateur.csv` avec `[nom, email, token]`

### 2. Création du mot de passe (`mdp.php`)
- Accessible depuis le lien reçu par email
- Valide que les deux champs sont remplis, identiques et font au moins 8 caractères
- Le mot de passe est **hashé avec bcrypt** (`password_hash`)
- Le hash est enregistré en colonne 3 du CSV
- Gère deux cas :
  - **Inscription** : recherche la ligne sans mot de passe
  - **Mot de passe oublié** : recherche par email passé en GET

### 3. Connexion (`index.php`)
- Vérifie l'email et le mot de passe avec `password_verify()`
- Démarre une session PHP avec `$_SESSION['email']` et `$_SESSION['nom']`
- Redirige vers le tableau de bord

### 4. Centres d'intérêt (`CentreInteret.php`)
- L'utilisateur coche les thèmes qui l'intéressent parmi 10 catégories
- Les anciens choix de l'utilisateur sont supprimés du CSV
- Les nouveaux choix sont enregistrés avec l'URL RSS correspondante

### 5. Tableau de bord (`dashboard.php`)
- Lit les thèmes de l'utilisateur dans `centreInterets.csv`
- Charge chaque flux RSS du Monde en temps réel avec `simplexml_load_file()`
- Affiche les articles sous forme de cartes avec image, titre, description et temps écoulé

### 6. Mot de passe oublié (`GmailmdpOublie.php`)
- L'utilisateur saisit son email
- Le script vérifie que l'email existe dans le CSV
- Un email avec un lien de réinitialisation est envoyé (`mdp.php?email=...`)

### 7. Déconnexion (`deconnexion.php`)
- Détruit la session avec `session_destroy()`
- Redirige vers la page de connexion

---

## Structure des fichiers CSV

### `Utilisateur.csv`
| Colonne | Contenu |
|---------|---------|
| 0 | Nom d'utilisateur |
| 1 | Email |
| 2 | Token (généré à l'inscription) |
| 3 | Mot de passe hashé (bcrypt) |

### `centreInterets.csv`
| Colonne | Contenu |
|---------|---------|
| 0 | Email de l'utilisateur |
| 1 | Nom du thème (ex: `sport`) |
| 2 | URL du flux RSS (ex: `https://www.lemonde.fr/sport/rss_full.xml`) |

---

## Thèmes disponibles

| Thème | URL RSS |
|-------|---------|
| International | `https://www.lemonde.fr/international/rss_full.xml` |
| Planète | `https://www.lemonde.fr/planete/rss_full.xml` |
| Politique | `https://www.lemonde.fr/politique/rss_full.xml` |
| Société | `https://www.lemonde.fr/societe/rss_full.xml` |
| Culture | `https://www.lemonde.fr/culture/rss_full.xml` |
| Économie | `https://www.lemonde.fr/economie/rss_full.xml` |
| Idées | `https://www.lemonde.fr/idees/rss_full.xml` |
| Sciences | `https://www.lemonde.fr/sciences/rss_full.xml` |
| Sport | `https://www.lemonde.fr/sport/rss_full.xml` |
| Pixels | `https://www.lemonde.fr/pixels/rss_full.xml` |

---

## Sécurité

- Les mots de passe sont hashés avec **bcrypt** (`PASSWORD_DEFAULT`)
- Les entrées utilisateur sont protégées contre les failles **XSS** avec `htmlspecialchars()`
- Les identifiants SMTP sont stockés dans un fichier **`.env`** non versionné
- Toutes les pages protégées vérifient la session au chargement

> ⚠️ **Limitations connues** — Ce projet est un prototype scolaire. En production, il faudrait :
> - Remplacer les CSV par une **base de données** (MySQL/PostgreSQL)
> - Ajouter un **token dans le lien** de réinitialisation (actuellement l'email passe en clair dans l'URL)
> - Utiliser **HTTPS**
> - Ajouter une protection **CSRF** sur les formulaires

---

## Dépendances

| Dépendance | Usage |
|------------|-------|
| [PHPMailer](https://github.com/PHPMailer/PHPMailer) | Envoi d'emails via SMTP Gmail |

---

## 👤 Auteur

Projet réalisé par Raphaël TURCHI et Luffas dans le cadre d'un cours de développement web en PHP.