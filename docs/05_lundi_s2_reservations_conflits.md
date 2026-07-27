# Documentation Jour 5 – Lundi 27/07/2026 (Sprint 2)
## US07 : Création de réservation & US08 : Vérification automatique des conflits

### Objectif du jour
Implémenter le système de réservation de salles avec un formulaire interactif et la vérification automatique des conflits de créneaux horaires, conformément aux User Stories US07 et US08 du product backlog.

---

### Fonctionnalités implémentées

#### US07 – Création de réservation
**En tant qu'utilisateur, je veux créer une réservation**

1. **Formulaire de réservation** (`/reservations/creer`)
   - Sélection de salle avec aperçu en temps réel (nom, capacité, équipements)
   - Champs : titre/motif de l'événement, date et heure de début, date et heure de fin
   - Validation côté serveur complète :
     - Salle existante et active
     - Titre obligatoire (max 150 caractères)
     - Dates/heures cohérentes (fin > début)
     - Interdiction de réserver dans le passé
     - Durée maximale de 24 heures
   - Synchronisation automatique de la date de fin avec la date de début
   - Panneau latéral avec guide d'utilisation et règles de réservation

2. **Liste des réservations** (`/reservations`)
   - Tableau récapitulatif avec toutes les réservations de l'utilisateur connecté
   - Cartes de compteurs par statut (en attente, validée, refusée)
   - Filtrage par statut via boutons
   - Actions : voir les détails, annuler (réservations en attente uniquement)
   - Messages flash pour les confirmations/erreurs
   - Marquage visuel des réservations passées

3. **Détails d'une réservation** (`/reservations/voir?id=X`)
   - Affichage complet : titre, salle, créneau, durée calculée, demandeur, statut
   - Lien vers la fiche de la salle et le calendrier
   - Bouton d'annulation pour les réservations en attente
   - Contrôle d'accès : seul le propriétaire ou un admin/logistique peut voir

4. **Annulation** (`/reservations/annuler` - POST)
   - Seul le propriétaire ou un admin peut annuler
   - Uniquement les réservations en statut "en attente"
   - Confirmation JavaScript avant soumission

#### US08 – Vérification automatique des conflits
**En tant que système, je veux vérifier automatiquement les conflits**

1. **Vérification côté serveur**
   - Méthode `checkConflict()` dans le modèle Reservation
   - Algorithme de chevauchement : deux intervalles [A_start, A_end] et [B_start, B_end] se chevauchent si `A_start < B_end ET A_end > B_start`
   - Ne considère que les réservations validées ou en attente (pas les refusées)
   - Support d'exclusion d'une réservation (pour future modification)
   - Blocage de la création si conflit détecté
   - Affichage des détails des conflits (événement, horaires, demandeur)

2. **Vérification en temps réel (AJAX)**
   - API JSON `/reservations/check-conflict` pour vérification asynchrone
   - Vérification automatique à chaque changement de champ (salle, dates, heures)
   - Debounce de 400ms pour éviter les requêtes excessives
   - Feedback visuel immédiat :
     - Alerte rouge si conflit détecté (détails des réservations conflictuelles)
     - Alerte verte si créneau disponible
     - Bouton de soumission désactivé en cas de conflit
   - Bouton "Vérifier les conflits" pour vérification manuelle

---

### Fichiers créés / modifiés

| Fichier | Action | Description |
|---------|--------|-------------|
| `src/Models/Reservation.php` | Modifié | Ajout des méthodes : `getByUserId()`, `checkConflict()`, `updateStatut()`, `cancel()`, `countByStatutForUser()`, `getPendingForValidation()` |
| `src/Controllers/ReservationController.php` | Créé | Contrôleur complet avec : `create()`, `index()`, `show()`, `cancel()`, `checkConflictApi()` |
| `src/Views/reservations/create.php` | Créé | Formulaire de réservation avec vérification de conflits en temps réel |
| `src/Views/reservations/index.php` | Créé | Liste des réservations avec compteurs et filtres par statut |
| `src/Views/reservations/show.php` | Créé | Page de détails d'une réservation |
| `src/Views/layout/navbar.php` | Modifié | Ajout du lien "Réservations" dans la navigation |
| `public/index.php` | Modifié | Ajout des routes pour les réservations et l'API de vérification de conflits |
| `docs/05_lundi_s2_reservations_conflits.md` | Créé | Documentation de cette journée |

### Routes ajoutées

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/reservations` | Liste des réservations de l'utilisateur |
| GET/POST | `/reservations/creer` | Formulaire de création de réservation |
| GET | `/reservations/voir?id=X` | Détails d'une réservation |
| POST | `/reservations/annuler` | Annulation d'une réservation en attente |
| GET | `/reservations/check-conflict` | API JSON de vérification de conflits (AJAX) |

### Architecture technique

```
src/
├── Controllers/
│   └── ReservationController.php    # Logique métier US07, US08
├── Models/
│   └── Reservation.php              # Accès BDD, vérification conflits
└── Views/
    └── reservations/
        ├── create.php               # Formulaire de création
        ├── index.php                # Liste (Mes réservations)
        └── show.php                 # Détails
```

### Algorithme de détection de conflits (US08)

```sql
-- Requête SQL de détection de chevauchement
SELECT r.id, r.titre_evenement, r.date_debut, r.date_fin
FROM reservations r
WHERE r.salle_id = :salle_id
  AND r.statut IN ('validee', 'en_attente')
  AND r.date_debut < :date_fin_demandee
  AND r.date_fin > :date_debut_demandee
```

Ce pattern couvre tous les cas de chevauchement :
- La nouvelle réservation commence pendant une existante
- La nouvelle réservation finit pendant une existante
- La nouvelle réservation englobe entièrement une existante
- La nouvelle réservation est entièrement contenue dans une existante

---

### Tests manuels effectués
- ✅ Vérification syntaxique PHP de tous les fichiers (0 erreur)
- ✅ Routes fonctionnelles (retour 302 → redirection vers login si non connecté)
- ✅ Intégrité du routeur (toutes les anciennes routes préservées)

---
*Sprint 2, Jour 1 – Lundi 27 juillet 2026*
