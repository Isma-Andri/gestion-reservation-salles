# Liste de tests – Jeudi Sprint 2 (US14, US15, US16, US17, US18)
*À tester sur : `http://localhost:8000`*

---

## 🔐 US18 – Sécurité & Session

### T1. Expiration de session (30 min)
> **Méthode** : Modifier `SESSION_TIMEOUT` à `5` dans `public/index.php`, attendre 5s puis recharger.
- [ ] Redirection automatique vers `/login`
- [ ] Message flash : "Votre session a expiré. Veuillez vous reconnecter."
- [ ] **Remettre à `1800` après test**

### T2. Contrôle d'accès admin
| URL | Compte de test | Résultat attendu |
|-----|----------------|-----------------|
| `GET /admin/stats` | Non connecté | → Redirect `/login` |
| `GET /admin/stats` | `mbolatiana@test.mg` (enseignant) | → Redirect `/dashboard` + message erreur |
| `GET /admin/stats` | `admin@test.mg` (admin) | ✅ Page statistiques affichée |
| `GET /admin/users` | `logistique@test.mg` | → Redirect `/dashboard` + erreur |
| `GET /admin/export-csv` | `admin@test.mg` | ✅ Téléchargement du fichier CSV |

### T3. Protection CSRF
- [ ] Soumettre le formulaire "Changer le rôle" avec un token CSRF invalide → `403 CSRF token invalide`
- [ ] Soumettre normalement → succès + message flash

### T4. Chiffrement des mots de passe
- [ ] Vérifier en DB : `SELECT mot_de_passe FROM utilisateurs LIMIT 1;`
- [ ] Le champ doit commencer par `$2y$` (bcrypt hash)
- [ ] Se connecter avec le mot de passe en clair → ✅

---

## 📊 US14 – Statistiques d'utilisation

### T5. KPI Cards (haut de page)
> Connecté en `admin@test.mg` → `http://localhost:8000/admin/stats`
- [ ] Carte "Total Réservations" : valeur > 0, cohérente avec la DB
- [ ] Carte "Validées" : affiche un % du total
- [ ] Carte "En Attente" : affiche le nombre correct
- [ ] Carte "Utilisateurs" : nombre correct + nb salles actives

### T6. Graphique Chart.js mensuel
- [ ] Le graphique s'affiche (pas d'erreur JS en console)
- [ ] Les barres "Validées" (vert) et "Refusées" (rouge) sont visibles
- [ ] L'axe X montre les 6 derniers mois
- [ ] Le tooltip s'affiche au survol

### T7. Barres de progression par rôle
- [ ] Les 4 rôles (Admin, Enseignant, Logistique, Association) sont affichés
- [ ] Les barres sont proportionnelles aux comptes existants

### T8. Activité récente (tableau)
- [ ] Les 10 dernières réservations sont affichées
- [ ] Colonnes : Demandeur (avec rôle), Salle, Événement, Créneau, Statut (badge coloré), Créée le
- [ ] Les badges de statut sont colorés : vert (validée), orange (en attente), rouge (refusée)

---

## 📈 US15 – Taux d'occupation

### T9. Section "Occupation cette semaine"
> Dans `/admin/stats`, section du bas à droite
- [ ] Toutes les salles actives apparaissent
- [ ] Les barres de progression reflètent les minutes de réservation validée
- [ ] Les salles sans réservation affichent "Libre" (0 min)
- [ ] La salle la plus occupée a une barre à 100%

### T10. Top 5 salles les plus réservées
- [ ] Le classement est affiché (table à gauche)
- [ ] Le #1 a l'icône 🏆 doré
- [ ] Les colonnes affichent : Rang, Nom, Réservations (badge bleu), Capacité

---

## 📥 US16 – Export CSV

### T11. Export CSV complet
> `http://localhost:8000/admin/export-csv`
- [ ] Téléchargement déclenché automatiquement
- [ ] Nom du fichier : `reservations_export_YYYYMMDD_HHMMSS.csv`
- [ ] Ouvrir dans LibreOffice Calc ou Excel → toutes les colonnes lisibles
- [ ] BOM UTF-8 correct : les accents (é, è, à) s'affichent correctement

### T12. Export filtré
- [ ] `GET /admin/export-csv?statut=validee` → seulement les réservations validées
- [ ] `GET /admin/export-csv?salle_id=1` → seulement la salle #1
- [ ] Combiner : `?statut=refusee&salle_id=2`

---

## 👤 US02 – Gestion des Utilisateurs (Admin)

### T13. Liste des utilisateurs
> `http://localhost:8000/admin/users`
- [ ] Tous les utilisateurs sont listés avec : Nom, Email, Rôle (badge), Nb réservations, Date d'inscription
- [ ] L'utilisateur connecté (admin) est mis en surbrillance (ligne bleue) avec le badge "Vous"
- [ ] L'admin ne voit pas d'action sur sa propre ligne

### T14. Changement de rôle
- [ ] Sélectionner un autre rôle dans le dropdown d'un utilisateur → cliquer ⟳
- [ ] Message flash de succès : "Rôle de l'utilisateur #X mis à jour : nouveau_role"
- [ ] Vérifier en DB : `SELECT role FROM utilisateurs WHERE id = X;`
- [ ] L'admin ne peut pas changer son propre rôle (son bouton n'est pas affiché)

### T15. Suppression d'utilisateur
- [ ] Cliquer sur 🗑️ pour un utilisateur → confirmation JavaScript demandée
- [ ] Confirmer → utilisateur supprimé + message flash de succès
- [ ] L'admin ne peut pas supprimer son propre compte (son bouton 🗑️ n'est pas affiché)

---

## 📱 US17 – Responsive Mobile (Xiaomi 12 Pro / Chrome)

### T16. Tableau de bord Admin sur mobile
- [ ] Les 4 cartes KPI s'affichent en 2x2 (`col-6 col-md-3`)
- [ ] Les tableaux ont un scroll horizontal (`table-responsive`)
- [ ] Les boutons d'action (Changer rôle + Supprimer) ne se chevauchent pas

### T17. Navbar sur mobile
- [ ] Le hamburger menu fonctionne
- [ ] Le lien "Admin" apparaît dans le menu déroulant uniquement pour les admins

### T18. Page statistiques sur mobile
- [ ] Le graphique Chart.js est responsive (se redimensionne)
- [ ] Le top 5 salles est scrollable horizontalement
- [ ] Les barres d'occupation sont lisibles

---

## 🧭 Navigation Admin depuis le Dashboard

### T19. Dashboard Admin
> Connecté en admin → `http://localhost:8000/dashboard`
- [ ] Le bouton "Statistiques Admin" (bleu) remplace le bouton "Nouvelle Réservation"
- [ ] Deux nouvelles cartes sont visibles : "Statistiques" (noire) et "Utilisateurs" (grise)
- [ ] Cliquer sur ces cartes navigue vers les bonnes URLs

---

## 🔗 Lien rapide
- Admin : `admin@test.mg` / `Admin@1234`
- Enseignant : `mbolatiana@test.mg` / `Ens@1234`
- Logistique : `logistique@test.mg` / `Logi@1234`
