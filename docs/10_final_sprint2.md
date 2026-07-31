# Documentation Jour 9 – Vendredi 31/07/2026 (Sprint 2 – Dernier jour)
## US03, US15, US16 : Profil utilisateur, Taux d'occupation & Export avancé

### Objectif du jour
Finaliser le Sprint 2 avec la gestion du profil utilisateur (US03), l'amélioration du taux d'occupation (US15) déjà vu jeudi, et l'export avancé des données avec filtres visuels (US16).

---

### Fonctionnalités implémentées

#### US03 – Gestion du profil utilisateur
**En tant qu'utilisateur, je veux gérer mon profil**

1. **Vue Profil (`/profil`)** :
   - **Avatar avec initiales** : Généré dynamiquement (ex. "IA" pour Ismaël Andrimalala), fond dégradé bleu-violet.
   - **Fiche identité** : Nom complet, email, téléphone (si renseigné), rôle (badge coloré), date d'inscription.
   - **Statistiques personnelles** : Total réservations, validées, en attente, refusées (compteurs colorés).
   - **Historique des réservations** : Tableau complet avec salle, créneau, statut (badge coloré), date de création.

2. **Formulaire d'édition (`/profil/modifier`)** :
   - **Deux formulaires séparés** sur la même page :
     - **Informations personnelles** : Prénom, Nom, Email, Téléphone (optionnel)
     - **Changement de mot de passe** : Mot de passe actuel requis + nouveau + confirmation
   - **Validation email unique** : Vérification côté serveur qu'un autre compte n'utilise pas déjà le même email.
   - **Validation de mot de passe** :
     - Vérification de l'ancien mot de passe avec `password_verify()`
     - Minimum 6 caractères pour le nouveau
     - Concordance nouveau/confirmation vérifiée côté client en temps réel (JS)
   - **Boutons afficher/masquer** : Sur les 3 champs mot de passe
   - **Mise à jour de la session** : `$_SESSION['nom']` et `$_SESSION['prenom']` mis à jour immédiatement après sauvegarde

3. **Colonnes de base de données ajoutées** :
   - `telephone VARCHAR(20) NULL` — numéro de téléphone optionnel
   - `photo_url VARCHAR(255) NULL` — prévu pour une future fonctionnalité de photo de profil

4. **Navigation** : Le nom de l'utilisateur dans la navbar est maintenant un **lien cliquable** vers `/profil`

#### US16 – Export des données (amélioré)
**En tant qu'admin, je veux exporter les données**

1. **Page d'export visuelle (`/admin/export`)** — Nouvelle page dédiée :
   - **Filtres radio** pour le statut : Tous / Validées / En attente / Refusées
   - **Dropdown** de sélection de salle
   - **Sélecteurs de période** : Date de début et date de fin
   - **Prévisualisation** du format du fichier CSV (tableau exemple)
   - **Exports rapides** : 4 boutons pour les filtres courants
   - Formulaire ouvert dans un nouvel onglet (`target="_blank"`)

2. **Export CSV amélioré (`/admin/export-csv`)** :
   - Filtre par **période** (`date_debut` et `date_fin`) ajouté en plus du statut et de la salle
   - Format : semicolon (`;`), BOM UTF-8, encodage ISO correct pour Excel

3. **Liens mis à jour** : Les boutons "Exporter CSV" dans `/admin/stats` pointent maintenant vers `/admin/export` (page avec filtres)

#### US15 – Taux d'occupation (déjà implémenté jeudi, rappel)
- Barre de progression par salle sur la semaine en cours
- Calculé via `TIMESTAMPDIFF(MINUTE)` sur les réservations validées
- Visible dans le tableau de bord admin (`/admin/stats`)

---

### Fichiers créés / modifiés

| Fichier | Action | Description |
|---------|--------|-------------|
| `src/Models/User.php` | Modifié | Ajout de `updateProfile()` (avec check email unique), `updatePassword()` (avec vérification ancienne) |
| `src/Models/Reservation.php` | Modifié | Ajout de `getAllByUser()` pour l'historique personnel |
| `src/Models/Stats.php` | Modifié | Mise à jour `getAllForExport()` avec filtres de date, ajout `getAllSallesForFilter()` |
| `src/Controllers/ProfileController.php` | Créé | `show()` (profil + historique), `edit()` (deux formulaires : infos + mdp) |
| `src/Controllers/AdminController.php` | Modifié | Ajout `exportPage()` pour la page de filtres visuels ; mise à jour `exportCsv()` avec filtres de date |
| `src/Views/profile/show.php` | Créé | Vue profil : avatar, stats, historique réservations |
| `src/Views/profile/edit.php` | Créé | Vue édition : infos perso + changement mdp avec JS de validation |
| `src/Views/admin/export.php` | Créé | Page d'export avec filtres visuels, prévisualisation, exports rapides |
| `src/Views/layout/navbar.php` | Modifié | Nom d'utilisateur → lien cliquable vers `/profil` |
| `public/css/style.css` | Modifié | Ajout `.profile-avatar`, `.icon-box`, `.dashboard-card`, responsive mobile |
| `public/index.php` | Modifié | Ajout `ProfileController`, routes `/profil`, `/profil/modifier`, `/admin/export` |
| `schema.sql` | Modifié | Ajout colonnes `telephone` et `photo_url` dans `utilisateurs` |
| `docs/10_final_sprint2.md` | Créé | Documentation du Vendredi |

### Nouvelles routes

| Route | Méthode | Accès | Description |
|-------|---------|-------|-------------|
| `/profil` | GET | Connecté | Afficher le profil de l'utilisateur connecté |
| `/profil/modifier` | GET/POST | Connecté | Formulaire d'édition du profil + changement mdp |
| `/admin/export` | GET | Admin | Page de filtres visuels pour l'export |

---

### Sécurité implémentée (rappel US18)
- `ProfileController::requireAuth()` → redirection `/login` si non connecté
- `updatePassword()` → vérifie l'ancien mot de passe avec `password_verify()` avant tout changement
- `updateProfile()` → vérifie l'unicité de l'email côté serveur pour éviter les doublons
- Mise à jour de session immédiate après modification du profil

---

### Bilan Sprint 2 complet
| US | Description | Statut |
|----|-------------|--------|
| US07 | Création de réservation | ✅ Fait (Lundi) |
| US08 | Vérification conflits | ✅ Fait (Lundi) |
| US09 | Auto-validation enseignants | ✅ Fait (Mardi) |
| US10 | Validation manuelle logistique | ✅ Fait (Mardi) |
| US11 | Notification confirmation | ✅ Fait (Mercredi) |
| US12 | Notification refus | ✅ Fait (Mercredi) |
| US13 | Notification changement statut | ✅ Fait (Mercredi) |
| US14 | Statistiques d'utilisation | ✅ Fait (Jeudi) |
| US15 | Taux d'occupation | ✅ Fait (Jeudi) |
| US16 | Export des données CSV | ✅ Fait (Jeudi + Vendredi amélioré) |
| US17 | Interface responsive mobile | ✅ Fait (Mercredi + Jeudi + Vendredi) |
| US18 | Sécurité (CSRF, session timeout) | ✅ Fait (Jeudi) |
| US03 | Gestion du profil utilisateur | ✅ Fait (Vendredi) |

---
*Sprint 2 terminé — Vendredi 31 juillet 2026*
*Développeur : Ismaël Andrimalala — MIT MISA*
