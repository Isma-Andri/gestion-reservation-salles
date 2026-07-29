# Cahier de Recette & Données de Test Malgaches

## 📌 Présentation
Ce document fournit les **données de démonstration** intégrées en base MySQL (avec des identités et lieux malgaches) ainsi que le **guide d'essai exhaustif (Cahier de Recette)** pour tester l'ensemble des fonctionnalités développées (Sprint 1 et Sprint 2 : US01 à US13).

---

## 👥 Comptes de Test (Mot de passe universel : `password123`)

| Nom & Prénom | Email | Rôle | Privilèges & Utilisation recommandées |
|--------------|-------|------|---------------------------------------|
| **Rakotoarisoa Hery** | `hery.rakoto@univ.mg` | `enseignant` | Test validation automatique des réservations (US09) + emails confirmation |
| **Rasoanaivo Mialy** | `mialy.rasoa@univ.mg` | `enseignant` | Test de créations de cours & consultation calendrier (US06, US07) |
| **Ranaivo Andry** | `andry.ranaivo@assoc-etudiants.mg` | `association` | Test soumission de réservations en attente (US07, US08) |
| **Ratsimbazafy Fitia** | `fitia.ratsimba@club-info.mg` | `association` | Test d'annulation par le propriétaire + notifications (US13) |
| **Rabenjamina Voahirana** | `voahirana.rabenja@logistique.mg` | `logistique` | Test du tableau de bord de validation / refus des réservations (US10) |
| **Andriamihaja Tahina** | `tahina.admin@univ.mg` | `admin` | Accès complet : gestion salles, réservations, validation & rôles |

---

## 🏛️ Salles de Démonstration

| ID | Nom de la Salle | Capacité | Équipements |
|----|-----------------|----------|-------------|
| 10 | **Amphi Ankatso - Ravinala** | 200 places | Vidéoprojecteur 4K, Sonorisation HD, Wi-Fi 6, Micro HF, Climatisation |
| 11 | **Salle Baobab - Labo Info** | 35 places | 35 PC i7, Écran Interactif, Fibre Optique, Wi-Fi |
| 12 | **Salle Ylang-Ylang - Visioconférence** | 50 places | Écran TV 85", Système Polycom, Tableau Blanc, Wi-Fi |

---

## 📋 Liste Complète des Scénarios de Test (Cahier de Recette)

### 1. Module Authentification & Sécurité (US01, US02, US18)
- [ ] **TC-01 : Connexion Réussie**
  - **Action** : Se connecter sur `/login` avec `hery.rakoto@univ.mg` / `password123`.
  - **Résultat attendu** : Redirection vers le tableau de bord (`/dashboard`), affichage du nom et badge `enseignant`.
- [ ] **TC-02 : Connexion Échouée**
  - **Action** : Saisir un mot de passe erroné.
  - **Résultat attendu** : Message d'erreur clair "Identifiants incorrects".
- [ ] **TC-03 : Inscription Nouvel Utilisateur**
  - **Action** : Aller sur `/register`, remplir le formulaire et choisir le rôle `association`.
  - **Résultat attendu** : Compte créé, mot de passe chiffré en BDD via bcrypt, redirection vers login avec message de succès.
- [ ] **TC-04 : Déconnexion**
  - **Action** : Cliquer sur "Déconnexion" dans la navbar.
  - **Résultat attendu** : Destruction de la session, redirection vers `/login`.

---

### 2. Module Gestion des Salles (US04, US05)
- [ ] **TC-05 : Consultation & Recherche de Salles**
  - **Action** : Visiter `/salles`, rechercher "Baobab" ou filtrer par capacité minimum (30).
  - **Résultat attendu** : Affichage dynamique des cartes des salles correspondantes.
- [ ] **TC-06 : Consultation des Fiches Détails**
  - **Action** : Cliquer sur "Détails" de l'Amphi Ankatso.
  - **Résultat attendu** : Fiche complète affichant capacité, badges d'équipements et bouton de réservation rapide.
- [ ] **TC-07 : CRUD Salles (Administrateur/Logistique)**
  - **Action** : Se connecter en `tahina.admin@univ.mg`, ajouter/éditer une salle.
  - **Résultat attendu** : Ajout/Modification enregistrée, mise à jour dans la liste.

---

### 3. Module Consultation des Disponibilités & Calendrier (US06)
- [ ] **TC-08 : Vue Calendrier Graphique (FullCalendar)**
  - **Action** : Aller sur `/calendrier`, basculer entre vues *Semaine*, *Mois*, *Jour* et *Liste*.
  - **Résultat attendu** : Affichage fluide des créneaux de 07:30 à 20:30 avec codes couleurs (Vert = Validée, Orange = En attente, Rouge = Refusée).
- [ ] **TC-09 : Vue Grille de Planning par Salle**
  - **Action** : Sur `/calendrier`, cliquer sur "Vue Grille par Salle".
  - **Résultat attendu** : Matrice claire avec les salles en lignes et les 7 jours de la semaine en colonnes.
- [ ] **TC-10 : Filtrage par Salle**
  - **Action** : Cliquer sur le pill de la salle "Amphi Ankatso - Ravinala".
  - **Résultat attendu** : Le calendrier et la grille se mettent à jour pour n'afficher que cette salle.

---

### 4. Module Création de Réservation & Conflits (US07, US08)
- [ ] **TC-11 : Création de Réservation Valide**
  - **Action** : Sur `/reservations/creer`, choisir la *Salle Baobab*, saisir un créneau futur disponible et valider.
  - **Résultat attendu** : Réservation créée avec succès.
- [ ] **TC-12 : Détection de Conflit en Temps Réel (AJAX - US08)**
  - **Action** : Choisir une salle et saisir un créneau qui chevauche un cours existant (ex: 30/07/2026 de 09:00 à 10:00 sur Amphi Ankatso).
  - **Résultat attendu** : Alerte rouge instantanée indiquant le conflit, bouton de soumission désactivé.
- [ ] **TC-13 : Blocage de Conflit Côté Serveur (US08)**
  - **Action** : Tenter d'envoyer le formulaire malgré un créneau occupé.
  - **Résultat attendu** : Rejet par le contrôleur avec message d'erreur et récapitulatif des réservations en conflit.

---

### 5. Module Validation Automatique & Manuelle (US09, US10)
- [ ] **TC-14 : Validation Automatique Enseignant (US09)**
  - **Action** : Se connecter en `hery.rakoto@univ.mg` (Enseignant) et créer une réservation libre.
  - **Résultat attendu** : Le statut passe immédiatement à **Validée** (`validee`) sans action manuelle.
- [ ] **TC-15 : Soumission Association (En Attente - US10)**
  - **Action** : Se connecter en `andry.ranaivo@assoc-etudiants.mg` (Association) et réserver une salle.
  - **Résultat attendu** : La réservation est enregistrée avec le statut **En attente** (`en_attente`).
- [ ] **TC-16 : Espace de Validation Logistique (US10)**
  - **Action** : Se connecter en `voahirana.rabenja@logistique.mg`, aller sur `/reservations/validations`.
  - **Résultat attendu** : Liste de toutes les demandes en attente avec boutons "Valider" et "Refuser".
- [ ] **TC-17 : Action de Validation / Refus**
  - **Action** : Cliquer sur "Valider" ou "Refuser" sur une demande.
  - **Résultat attendu** : Statut mis à jour en BDD, message de confirmation flash affiché.

---

### 6. Module Notifications Email (US11, US12, US13)
- [ ] **TC-18 : Verification du Log d'Emails (`logs/emails.log`)**
  - **Action** : Après avoir créé/validé/refusé une réservation, consulter `logs/emails.log`.
  - **Résultat attendu** : Entrées d'emails enregistrées avec horodatage, sujet et destinataire.
- [ ] **TC-19 : Notification de Confirmation (US11)**
  - **Action** : Valider une demande d'association.
  - **Résultat attendu** : Email HTML de confirmation (template vert) généré pour le demandeur.
- [ ] **TC-20 : Notification de Refus (US12)**
  - **Action** : Refuser une demande.
  - **Résultat attendu** : Email HTML de refus (template rouge) généré avec le motif.
- [ ] **TC-21 : Notification de Changement de Statut (US13)**
  - **Action** : Annuler ou modifier une réservation.
  - **Résultat attendu** : Email HTML retraçant l'ancien et le nouveau statut.

---
*Fichier généré automatiquement pour les tests d'acceptation du projet.*
