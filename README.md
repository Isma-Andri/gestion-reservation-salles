# Gestion et Réservation de Salles

## ▸ Présentation du Projet

Cette application web permet la gestion centralisée des réservations de salles pour les enseignants, les responsables d'association, le service logistique et l'administration. Elle remplace les processus manuels sur tableur afin d'éliminer les doublons de réservation et d'automatiser les flux de validation.

## ▸ Stack Technique

- **Backend :** PHP (Architecture MVC)
- **Frontend :** HTML5, CSS3, JavaScript, Bootstrap 5
- **Base de données :** MySQL 8.0
- **Notifications :** SMTP Mailer

## ▸ Fonctionnalités Principales

- **Authentification & Rôles :** Inscription/connexion sécurisée, gestion des droits (enseignant, association, logistique, administrateur).
- **Gestion des Salles :** Consultation de la liste, des capacités et des équipements avec filtres et administration CRUD.
- **Réservations & Calendrier :** Consultation des disponibilités, vérification automatique des conflits de créneau.
- **Workflows de Validation :** Validation automatique pour les enseignants, validation manuelle par le service logistique pour les associations.
- **Notifications Email :** Envoi automatique des confirmations et des refus.

## ▸ Installation & Configuration Locale

### Prérequis

- PHP 8.x
- Serveur Web (Apache / Nginx) ou serveur intégré PHP (`php -S localhost:8000 -t public`)
- MySQL 8.0

### Étapes d'installation

1. **Cloner le dépôt :**
   ```bash
   git clone git@github.com:Isma-Andri/gestion-reservation-salles.git
   cd gestion-reservation-salles
   ```

2. **Configurer la base de données MySQL :**
   ```bash
   mysql -u root -p < schema.sql
   ```

3. **Lancer l'application web :**
   ```bash
   php -S localhost:8000 -t public
   ```

## ▸ Documentation par jour de développement

- 📄 [Jour 1 - Lundi 20/07 : Setup du projet, BDD & Architecture](file:///home/isma/Project_mgmt/gestion-reservation-salles/docs/01_lundi_setup_projet.md)
- 📄 [Jour 2 - Mardi 21/07 : Authentification & Gestion des Rôles (US01, US02)](file:///home/isma/Project_mgmt/gestion-reservation-salles/docs/02_mardi_authentification_roles.md)
- 📄 [Jour 3 - Mercredi 22/07 : Gestion des Salles & Équipements CRUD (US04, US05)](file:///home/isma/Project_mgmt/gestion-reservation-salles/docs/03_mercredi_gestion_salles.md)
