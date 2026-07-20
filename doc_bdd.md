# Documentation de la Base de Données : `gestion_salles`

Cette documentation décrit la structure de la base de données utilisée pour l'application de gestion et de réservation de salles.

## Base de données
- **Nom** : `gestion_salles`
- **Encodage** : `utf8mb4` (Collation: `utf8mb4_unicode_ci`)

## Tables

### 1. Table `utilisateurs`
Stocke les informations des utilisateurs de l'application et gère les accès selon les rôles. 

| Colonne | Type | Description | Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | INT | Identifiant unique de l'utilisateur | PRIMARY KEY, AUTO_INCREMENT |
| `nom` | VARCHAR(100) | Nom de famille de l'utilisateur | NOT NULL |
| `prenom` | VARCHAR(100) | Prénom de l'utilisateur | NOT NULL |
| `email` | VARCHAR(150) | Adresse email de connexion | UNIQUE, NOT NULL |
| `mot_de_passe` | VARCHAR(255) | Mot de passe chiffré | NOT NULL |
| `role` | ENUM | Rôle définissant les droits d'accès | NOT NULL ('enseignant', 'association', 'logistique', 'admin') |
| `date_creation` | TIMESTAMP | Date et heure de création du compte | DEFAULT CURRENT_TIMESTAMP |

### 2. Table `salles`
Contient la liste des salles disponibles à la réservation, leurs capacités et équipements.

| Colonne | Type | Description | Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | INT | Identifiant unique de la salle | PRIMARY KEY, AUTO_INCREMENT |
| `nom` | VARCHAR(100) | Nom ou numéro de la salle | NOT NULL |
| `capacite` | INT | Nombre maximum de personnes | NOT NULL |
| `equipements` | TEXT | Liste des caractéristiques/équipements | |
| `est_active` | BOOLEAN | Indique si la salle est disponible | DEFAULT TRUE |

### 3. Table `reservations`
Enregistre toutes les demandes de réservation et leurs statuts d'approbation.

| Colonne | Type | Description | Contraintes |
| :--- | :--- | :--- | :--- |
| `id` | INT | Identifiant unique de la réservation | PRIMARY KEY, AUTO_INCREMENT |
| `utilisateur_id` | INT | Référence à l'utilisateur (créateur) | FOREIGN KEY, NOT NULL, ON DELETE CASCADE |
| `salle_id` | INT | Référence à la salle réservée | FOREIGN KEY, NOT NULL, ON DELETE CASCADE |
| `titre_evenement` | VARCHAR(150) | Motif ou titre de la réservation | NOT NULL |
| `date_debut` | DATETIME | Date et heure de début de la réservation | NOT NULL |
| `date_fin` | DATETIME | Date et heure de fin de la réservation | NOT NULL |
| `statut` | ENUM | État actuel de la réservation | DEFAULT 'en_attente' ('en_attente', 'validee', 'refusee') |
| `date_creation` | TIMESTAMP | Date de la demande | DEFAULT CURRENT_TIMESTAMP |
