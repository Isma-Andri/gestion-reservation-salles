# Documentation Jour 6 – Mardi 28/07/2026 (Sprint 2)
## US09 & US10 : Validation automatique et manuelle des réservations

### Objectif du jour
L'objectif de cette journée était d'implémenter les systèmes de validation des réservations selon le rôle de l'utilisateur, comme spécifié dans le product backlog.

---

### Fonctionnalités implémentées

#### US09 – Validation automatique pour les enseignants
**En tant que système, je veux valider automatiquement les réservations des enseignants**

1. **Logique de création modifiée** (`ReservationController::create()`)
   - Lors de la création d'une réservation, le système vérifie le rôle de l'utilisateur.
   - Si l'utilisateur est un `enseignant` (ou un `admin`), le statut initial de la réservation est directement défini sur `validee`.
   - Pour les autres rôles (comme `association`), le statut reste `en_attente`.
   - La vérification des conflits (US08) reste active et prioritaire : un enseignant ne peut pas valider automatiquement une réservation sur un créneau déjà occupé.

#### US10 – Validation manuelle par le service logistique
**En tant que service logistique, je veux valider manuellement les réservations des associations**

1. **Tableau de bord de validation** (`/reservations/validations`)
   - Création d'une nouvelle vue réservée aux administrateurs et au personnel logistique.
   - Affichage sous forme de tableau de toutes les réservations actuellement `en_attente`.
   - Détails affichés : Nom et rôle du demandeur, salle demandée, titre de l'événement, créneau horaire, et date de création.
   - Affichage d'un message spécifique si aucune réservation n'est en attente.

2. **Actions de validation et de refus**
   - Implémentation de deux nouvelles méthodes dans le contrôleur : `validate()` et `reject()`.
   - Ces méthodes vérifient strictement les autorisations (seul `logistique` ou `admin` peut exécuter l'action).
   - `validate()` change le statut en `validee`.
   - `reject()` change le statut en `refusee`.
   - Ajout de confirmations côté client (JavaScript) avant le refus d'une réservation pour éviter les erreurs de manipulation.
   - Messages flash (vert pour le succès, rouge pour l'erreur) pour confirmer le résultat de l'action à l'utilisateur.

3. **Intégration dans la navigation**
   - Mise à jour de la barre de navigation (`navbar.php`) pour inclure un lien vers le tableau de bord des validations.
   - Ce lien n'est visible que pour les utilisateurs ayant le rôle `admin` ou `logistique`.

---

### Fichiers créés / modifiés

| Fichier | Action | Description |
|---------|--------|-------------|
| `src/Controllers/ReservationController.php` | Modifié | Mise à jour de `create()` pour US09. Ajout de `validations()`, `validate()`, et `reject()` pour US10. |
| `src/Views/reservations/validations.php` | Créé | Vue du tableau de bord de validation pour le service logistique/admin. |
| `src/Views/layout/navbar.php` | Modifié | Ajout du lien de navigation contextuel "Validations". |
| `public/index.php` | Modifié | Ajout des routes `/reservations/validations`, `/reservations/validate`, et `/reservations/reject`. |
| `docs/06_mardi_s2_validation.md` | Créé | Documentation du travail réalisé lors de cette journée. |

### Architecture des requêtes

1. **Création (Enseignant)**
   - Requête HTTP POST vers `/reservations/creer`
   - Le système crée la réservation et fixe `statut = 'validee'` dans la BDD.

2. **Création (Association)**
   - Requête HTTP POST vers `/reservations/creer`
   - Le système crée la réservation et fixe `statut = 'en_attente'` dans la BDD.

3. **Validation (Logistique/Admin)**
   - L'utilisateur visite `/reservations/validations` (GET)
   - L'utilisateur clique sur "Valider" -> Requête POST vers `/reservations/validate` avec l'ID de la réservation.
   - Le système met à jour la BDD (`statut = 'validee'`) et redirige vers la liste des validations.

---
*Sprint 2, Jour 2 – Mardi 28 juillet 2026*
