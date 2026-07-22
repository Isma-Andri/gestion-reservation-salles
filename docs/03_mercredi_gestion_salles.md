# Documentation de Développement - Mercredi 22 Juillet 2026

## 📌 Objectif de la journée
Développement de la gestion complète des salles de classe / réunion, de leurs caractéristiques et équipements (User Stories US04 & US05), conformément au calendrier de développement.

---

## 🛠️ Réalisations

### 1. Modèle Salle (`src/Models/Salle.php`)
- **Méthodes de consultation :**
  - `getAll($search, $minCapacite)` : Récupère la liste de toutes les salles (actives ou inactives) avec filtres optionnels par mot-clé et capacité.
  - `getAllActive($search, $minCapacite)` : Récupère uniquement les salles actives disponibles pour les enseignants et associations.
  - `getById($id)` : Récupère les caractéristiques détaillées d'une salle spécifique.
- **Méthodes CRUD (Admin / Logistique) :**
  - `create($nom, $capacite, $equipements, $est_active)` : Ajoute une nouvelle salle en BDD.
  - `update($id, $nom, $capacite, $equipements, $est_active)` : Modifie les caractéristiques d'une salle existante.
  - `toggleStatus($id)` : Active ou désactive la disponibilité d'une salle.
  - `delete($id)` : Supprime une salle si aucune contrainte de clé étrangère ne l'en empêche.

### 2. Contrôleur Salle (`src/Controllers/SalleController.php`)
- **`index()` (US04)** : Gère l'affichage de la grille des salles avec moteur de recherche et filtre de capacité.
- **`show()` (US05)** : Affiche les informations complètes d'une salle et convertit la chaîne des équipements en badges/chips interactifs.
- **`create()` / `edit()` / `toggleStatus()` / `delete()`** : Contrôle l'accès administrateur/logistique et applique les modifications de données.

### 3. Vues Modernes & Responsives (`src/Views/salles/`)
- `salles/index.php` : Grille dynamique avec cartes responsives Bootstrap 5, badges d'état et filtres instantanés.
- `salles/show.php` : Fiche détaillée de la salle avec aperçu de la capacité et liste sous forme de badges pour chaque équipement.
- `salles/create.php` : Formulaire de création de salle avec validation et commutateur de statut.
- `salles/edit.php` : Formulaire de modification pré-rempli avec gestion des erreurs.

### 4. Routage & Dashboard
- Mise à jour du routeur dans `public/index.php` (`/salles`, `/salles/voir`, `/salles/creer`, `/salles/editer`, `/salles/toggle-statut`, `/salles/supprimer`).
- Intégration des liens de gestion des salles dans le tableau de bord principal (`src/Views/dashboard/index.php`).
- Insertion de données initiales de démonstration (Amphithéâtre A, Salle 101, Salle 204, Salle de Réunion B).

---

## 🔒 Sécurité & Bonnes pratiques
- **Contrôle d'accès (RBAC)** : Les actions de modification (`create`, `edit`, `toggleStatus`, `delete`) sont strictement restreintes aux utilisateurs ayant le rôle `admin` ou `logistique`.
- **Validation des entrées** : Vérification des champs requis (nom de salle non vide, capacité > 0).
- **Protection XSS & SQLi** : Échappement HTML via `htmlspecialchars()` dans l'ensemble des vues, et requêtes préparées avec paramètres typés PDO dans le modèle `Salle`.
