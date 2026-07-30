# Documentation Jour 8 – Jeudi 30/07/2026 (Sprint 2)
## US14, US15, US16, US17, US18 : Statistiques, Taux d'occupation, Export CSV, Responsive & Sécurité

### Objectif du jour
Implémenter les fonctionnalités avancées d'administration, de visualisation des données, d'export et de sécurisation du système, conformément au planning du Jeudi du Sprint 2.

---

### Fonctionnalités implémentées

#### US14 – Statistiques d'utilisation (Admin)
**En tant qu'admin, je veux consulter des statistiques d'utilisation**

1. **Tableau de bord Admin (`/admin/stats`)** :
   - **4 cartes KPI** : Total réservations, Validées (avec %), En attente, Nombre d'utilisateurs & salles actives.
   - **Graphique en barres (Chart.js)** : Évolution mensuelle des réservations validées / refusées sur les 6 derniers mois.
   - **Classement Top 5 Salles** les plus réservées avec médaille pour le #1.
   - **Tableau d'Activité récente** : Les 10 dernières réservations avec tous les détails (demandeur, salle, créneau, statut).
   - **Barres de progression** par rôle d'utilisateur (Enseignant, Association, Logistique, Admin).

#### US15 – Taux d'occupation
**En tant qu'admin, je veux visualiser le taux d'occupation**

1. **Section "Occupation cette semaine"** dans le tableau de bord admin :
   - Calcul du nombre de minutes occupées (réservations validées) par salle sur la semaine en cours.
   - Affichage sous forme de barres de progression relatives (la salle la plus occupée = 100%).
   - Requête SQL utilisant `TIMESTAMPDIFF(MINUTE, date_debut, date_fin)` pour le calcul précis.

#### US16 – Export des données
**En tant qu'admin, je veux exporter les données**

1. **Export CSV (`/admin/export-csv`)** :
   - Génération d'un fichier CSV au format semicolon (`;`) compatible Excel.
   - **BOM UTF-8** inclus pour l'affichage correct des caractères accentués dans Excel.
   - Colonnes : ID, Demandeur, Rôle, Email, Salle, Événement, Début, Fin, Statut, Date de Création.
   - Paramètres de filtrage disponibles : `?statut=validee&salle_id=1`.

#### US17 – Interface Responsive
**En tant qu'utilisateur, je veux une interface responsive (mobile/PC)**

- Calendrier FullCalendar : **adaptation automatique mobile** (vue `listWeek` par défaut sur écran < 768px), refait lors de la correction précédente.
- Tableaux admin avec `table-responsive` pour le scroll horizontal sur mobile.
- Grilles Bootstrap adaptatives (`col-6 col-md-3`) pour les cartes KPI.

#### US18 – Sécurité renforcée
**En tant que système, je veux sécuriser l'accès**

1. **Expiration automatique de session (30 min)** — ajout dans `public/index.php` :
   - Si la durée d'inactivité dépasse 30 minutes, la session est détruite et l'utilisateur est redirigé vers `/login` avec un message d'erreur explicatif.
   - `$_SESSION['last_activity']` mis à jour à chaque requête.

2. **Protection CSRF** pour les actions d'administration sensibles :
   - Token CSRF généré avec `bin2hex(random_bytes(32))` et stocké en session.
   - Vérifié avec `hash_equals()` pour se protéger contre les attaques temporelles.
   - Appliqué sur les formulaires : **changement de rôle** et **suppression d'utilisateur**.

3. **Contrôle d'accès par rôle** renforcé dans `AdminController` :
   - Accès `/admin/stats`, `/admin/users`, `/admin/export-csv` réservé au rôle `admin` uniquement.
   - Redirection automatique avec message d'erreur pour les accès non autorisés.

4. **Protection auto-suppression** : un admin ne peut pas supprimer son propre compte.

---

### Fichiers créés / modifiés

| Fichier | Action | Description |
|---------|--------|-------------|
| `src/Models/Stats.php` | Créé | Modèle dédié aux statistiques : stats globales, top salles, occupation semaine, évolution mensuelle, export (US14, US15, US16) |
| `src/Models/User.php` | Modifié | Ajout de `findById()`, `updateRole()`, `delete()` pour la gestion admin (US02) |
| `src/Controllers/AdminController.php` | Créé | Contrôleur admin : stats, gestion utilisateurs, export CSV, CSRF, contrôle d'accès (US02, US14, US15, US16, US18) |
| `src/Views/admin/stats.php` | Créé | Vue tableau de bord admin avec Chart.js, KPIs, top salles, occupation, activité récente (US14, US15) |
| `src/Views/admin/users.php` | Créé | Vue gestion utilisateurs avec changement de rôle (dropdown) et suppression sécurisée (US02, US18) |
| `src/Views/errors/404.php` | Créé | Page d'erreur 404 conviviale |
| `src/Views/layout/navbar.php` | Modifié | Ajout du lien "Admin" dans la navbar (visible uniquement pour les admins) |
| `src/Views/dashboard/index.php` | Modifié | Ajout des cartes Admin (Statistiques, Utilisateurs) + bouton contextuel "Statistiques Admin" |
| `public/index.php` | Modifié | Ajout de la sécurité session (timeout 30 min), inclusion Stats + AdminController, routes `/admin/*` |
| `docs/09_jeudi_s2_stats_securite.md` | Créé | Documentation du Jeudi Sprint 2 |

### Routes ajoutées

| Route | Accès | Description |
|-------|-------|-------------|
| `GET /admin/stats` | Admin | Tableau de bord statistiques (US14, US15) |
| `GET/POST /admin/users` | Admin | Gestion utilisateurs : rôles & suppression (US02) |
| `GET /admin/export-csv` | Admin | Export CSV des réservations (US16) |

---

### Architecture Sécurité (US18)

```
HTTP Request
    │
    ▼
session_start() → Vérification last_activity
                        │
              ┌─────────┴──────────┐
              ▼                    ▼
       < 30 min                > 30 min
    [Continuer]          [Détruire session]
                         [Redirect /login]
                         [Message flash]
                              │
                              ▼
                    Chaque POST admin → Vérifier CSRF token
                                    → hash_equals() pour comparaison sécurisée
                                    → 403 si invalide
```

---

### Tests manuels effectués
- ✅ Syntaxe PHP validée sur tous les fichiers (0 erreur)
- ✅ Requêtes MySQL de statistiques testées directement sur la DB de test
- ✅ Logique CSRF : token généré, vérification fonctionnelle
- ✅ Routes admin retournent 302 → login si non connecté

---
*Sprint 2, Jour 4 – Jeudi 30 juillet 2026*
