# Documentation de Développement - Mardi 21 Juillet 2026

## 📌 Objectif de la journée
Développement des fonctionnalités d'authentification sécurisée et de la gestion des rôles (User Stories US01, US02 & US18).

---

## 🛠️ Réalisations

### 1. Modèle Utilisateur (`src/Models/User.php`)
- Implémentation des méthodes d'accès aux données :
  - `findByEmail($email)` : Recherche d'un utilisateur par son adresse email.
  - `create($nom, $prenom, $email, $mot_de_passe, $role)` : Inscription d'un nouvel utilisateur.

### 2. Contrôleur d'Authentification (`src/Controllers/AuthController.php`)
- **Action Inscription (`register`)** : Valide les informations saisies et enregistre l'utilisateur avec son rôle spécifique (`enseignant`, `association`, `logistique`, `admin`).
- **Action Connexion (`login`)** : Authentifie l'utilisateur via son email et son mot de passe, puis initialise les données de session `$_SESSION`.
- **Action Déconnexion (`logout`)** : Détruit la session en cours et redirige vers la page d'accueil/connexion.

### 3. Vues d'Authentification (`src/Views/auth/` & `src/Views/dashboard/`)
- `login.php` : Formulaire de connexion propre avec gestion de l'affichage des erreurs d'authentification.
- `register.php` : Formulaire d'inscription avec sélection dynamique du rôle utilisateur.
- `dashboard/index.php` : Tableau de bord principal affichant des options de navigation et des fonctionnalités selon le rôle de l'utilisateur connecté (Enseignant, Association, Logistique, Admin).

---

## 🔒 Sécurité & Bonnes pratiques
- **Hachage des mots de passe (US18)** : Utilisation de l'algorithme standard `password_hash()` avec `PASSWORD_DEFAULT` lors de la création de compte, et vérification via `password_verify()`.
- **Protection contre le vol de session** : Utilisation de `session_start()` et contrôle strict des accès basés sur les variables de session.
- **Protection contre les attaques XSS** : Nettoyage systématique de tous les affichages dynamiques dans le HTML avec `htmlspecialchars()`.
- **Protection contre les injections SQL** : Requêtes SQL préparées avec PDO pour l'ensemble des opérations en base de données.
