# Documentation Jour 7 – Mercredi 29/07/2026 (Sprint 2)
## US11, US12 & US13 : Notifications Email (Confirmation, Refus & Changement de statut)

### Objectif du jour
L'objectif de cette journée était de développer le module de notification par email automatisé pour tenir les utilisateurs informés à chaque étape clé du cycle de vie d'une réservation (US11, US12, US13).

---

### Fonctionnalités implémentées

#### 1. Service de Notification Email (`NotificationService.php`)
- **Création d'un composant de messagerie réutilisable** (`src/Services/NotificationService.php`)
- **Gestion hybride d'envoi & journalisation (Dev/Prod)** :
  - Tentative d'envoi natif via la fonction PHP `mail()` avec en-têtes HTML (`Content-type: text/html; charset=utf-8`).
  - Journalisation systématique de chaque notification dans `logs/emails.log` (date, type de notification, destinataire, sujet, statut).
- **Templates HTML responsifs et soignés** :
  - **US11 (Email de confirmation)** : Template vert avec badge "Validée", récapitulatif du créneau, de la salle et du motif.
  - **US12 (Email de refus)** : Template rouge avec motif explicatif du refus, rappel des détails de la demande et invitation à effectuer une autre réservation.
  - **US13 (Email de changement de statut)** : Template bleu retraçant l'ancien statut et le nouveau statut attribué.

#### 2. US11 – Envoi de l'email de confirmation
**En tant qu'utilisateur, je veux recevoir un email de confirmation**
- **Déclenchement automatique** :
  - Lors de la création d'une réservation par un **enseignant** ou un **administrateur** (validation automatique US09).
  - Lors de l'approbation manuelle d'une réservation d'association par le **service logistique** ou l'**admin** (US10).
- **Contenu** : Confirmation du créneau, rappels des règles d'utilisation de la salle et lien vers l'espace personnel.

#### 3. US12 – Envoi de l'email de refus
**En tant qu'utilisateur, je veux recevoir un email de refus**
- **Déclenchement automatique** :
  - Lorsqu'une demande de réservation est refusée par le **service logistique** ou un **admin**.
- **Contenu** : Notification du refus avec affichage de la raison donnée par la logistique (ex: créneau indisponible, maintenance) et orientation vers d'autres choix.

#### 4. US13 – Notification de changement de statut
**En tant qu'utilisateur, je veux être notifié d'un changement de statut**
- **Déclenchement systématique** à chaque transition d'état d'une réservation :
  - `non_creee` ➔ `en_attente` (Soumission d'une nouvelle demande)
  - `en_attente` ➔ `validee` (Validation de la demande)
  - `en_attente` ➔ `refusee` (Refus par la logistique ou annulation par le demandeur)
- **Traçabilité** : Permet au demandeur de suivre précisément l'évolution de son dossier.

---

### Fichiers créés / modifiés

| Fichier | Action | Description |
|---------|--------|-------------|
| `src/Services/NotificationService.php` | Créé | Service de génération de templates HTML et d'envoi/log des emails (US11, US12, US13). |
| `src/Controllers/ReservationController.php` | Modifié | Injection du `NotificationService` et déclenchement des emails lors de la création, la validation, le refus et l'annulation. |
| `public/index.php` | Modifié | Inclusion du service `NotificationService` et passage du paramètre au contrôleur de réservation. |
| `docs/07_mercredi_s2_notifications.md` | Créé | Documentation complète du sprint du Mercredi. |

---

### Workflow de notification

```
[Utilisateur / Association] ──> Soumet réservation
                                      │
                                      ▼
                        Vérification Rôle & Conflits
                                      │
             ┌────────────────────────┴────────────────────────┐
             ▼                                                 ▼
     Role = Enseignant                                 Role = Association
  (Validation Auto US09)                            (En Attente Logistique)
             │                                                 │
             ├─► US11: Email Confirmation                      ├─► US13: Notif Statut "En attente"
             └─► US13: Notif Statut "Validée"                  │
                                                               ▼
                                                  Logistique valide/refuse (US10)
                                                               │
                                       ┌───────────────────────┴───────────────────────┐
                                       ▼                                               ▼
                                   [Valider]                                       [Refuser]
                                       │                                               │
                       ├─► US11: Email Confirmation                     ├─► US12: Email Refus
                       └─► US13: Notif Statut "Validée"                 └─► US13: Notif Statut "Refusée"
```

---

### Tests manuels effectués
- ✅ Validation de la syntaxe PHP (`No syntax errors detected`).
- ✅ Génération automatique du fichier de logs `logs/emails.log`.
- ✅ Intégration dans le flux complet MVC sans régression sur les fonctionnalités existantes.

---
*Sprint 2, Jour 3 – Mercredi 29 juillet 2026*
