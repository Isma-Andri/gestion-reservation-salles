# Documentation de Développement - Lundi 20 Juillet 2026

## 📌 Objectif de la journée
Mise en place des fondations du projet : initialisation du dépôt Git, configuration du système de fichiers, création de la base de données MySQL et structuration de l'architecture MVC.

---

## 🛠️ Réalisations

### 1. Initialisation du projet et contrôle de version
- Création et configuration du dépôt Git local et distant (`git clone git@github.com:Isma-Andri/gestion-reservation-salles.git`).
- Ajout des fichiers de configuration environnementaux `.env` et `.env.example`.
- Création du fichier `.gitignore` (pour exclure les fichiers sensibles et dépendances).

### 2. Base de données MySQL (`gestion_salles`)
- Création de la base de données relationnelle MySQL avec encodage `utf8mb4` et collation `utf8mb4_unicode_ci`.
- Rédaction et exécution du fichier d'initialisation SQL (`schema.sql`) contenant la structure des tables :
  - `utilisateurs` : Stockage des utilisateurs et de leurs rôles (`enseignant`, `association`, `logistique`, `admin`).
  - `salles` : Liste des salles, leur capacité, équipements et statut (`est_active`).
  - `reservations` : Suivi des demandes de réservation et de leurs statuts (`en_attente`, `validee`, `refusee`).
- Création d'un utilisateur dédié MySQL (`app_user`) avec les privilèges appropriés sur la base de données `gestion_salles`.
- Rédaction de la documentation de la base de données (`doc_bdd.md`).

### 3. Architecture Logicielle (Pattern MVC)
- Arborescence des dossiers mise en place :
  - `config/` : Configuration globale et connexion PDO (`database.php`).
  - `public/` : Point d'entrée de l'application web (`index.php`) et gestion du routage simple.
  - `src/Controllers/` : Contrôleurs métier.
  - `src/Models/` : Modèles de données avec requêtes préparées PDO.
  - `src/Views/` : Vues HTML / Template.

### 4. Rédaction du Cahier des Charges & Planning
- Élaboration du Cahier des Charges (`andrimalala_ismael_cdc_app_res_salle.pdf`).
- Création du Product Backlog et du calendrier de développement sur 2 semaines (`product_backlog_calendrier_app_res_salles.pdf`).
- Rédaction de la présentation générale du projet dans `README.md`.

---

## 🔒 Sécurité & Bonnes pratiques
- Utilisation de **variables d'environnement (`.env`)** pour séparer la configuration du code source.
- Connexion sécurisée à la base de données via **PDO** avec gestion des erreurs (`ERRMODE_EXCEPTION`).
