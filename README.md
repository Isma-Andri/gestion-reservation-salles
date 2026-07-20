# Gestion et Réservation de Salles

## ▸ Présentation du Projet

Cette application web permet la gestion centralisée des réservations de salles pour les enseignants, les responsables d'association, le service logistique et l'administration. Elle remplace les processus manuels sur tableur afin d'éliminer les doublons de réservation et d'automatiser les flux de validation.

## ▸ Stack Technique

- **Backend :** PHP (Architecture MVC)
- **Frontend :** HTML5, CSS3, JavaScript, Bootstrap
- **Base de données :** MySQL 8.0
- **Notifications :** SMTP Mailer

## ▸ Fonctionnalités Principales

- **Authentification & Rôles :** Inscription/connexion sécurisée, gestion des droits (enseignant, association, logistique, administrateur).
- **Gestion des Salles :** Consultation de la liste, des capacités et des équipements.
- **Réservations & Calendrier :** Consultation des disponibilités, vérification automatique des conflits de créneau.
- **Workflows de Validation :** Validation automatique pour les enseignants, validation manuelle par le service logistique pour les associations.
- **Notifications Email :** Envoi automatique des confirmations et des refus.

## ▸ Installation & Configuration Locale

### Prérequis

- PHP 8.x
- Serveur Web (Apache / Nginx) ou serveur intégré PHP
- MySQL 8.0

### Étapes d'installation

1. **Cloner le dépôt :**

   ```bash
   git clone git@github.com:Isma-Andri/gestion-reservation-salles.git
   cd gestion-reservation-salles
   ```
