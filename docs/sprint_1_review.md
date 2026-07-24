# Revue du Premier Sprint (20/07 - 23/07)

## Objectif du Sprint
Ce sprint visait à mettre en place les fondations de l'application de gestion et réservation de salles, incluant l'initialisation du projet, la configuration de la base de données, l'authentification et la gestion des rôles, la création du CRUD des salles, ainsi que la consultation des disponibilités via un calendrier.

## Livrables Terminés
| Jour | Date | Fonctionnalité(s) implémentée(s) | Documents associés |
|------|------|----------------------------------|--------------------|
| Lundi | 20/07 | * Installation du projet * Structure de la base de données (*`schema.sql`*) * Architecture MVC du backend | [Setup du projet, BDD & Architecture](01_lundi_setup_projet.md) |
| Mardi | 21/07 | * Authentification (inscription / connexion) * Gestion des rôles (enseignant, association, logistique, administrateur) | [Authentification & Gestion des Rôles](02_mardi_authentification_roles.md) |
| Mercredi | 22/07 | * CRUD des salles (création, lecture, mise à jour, suppression) * Gestion des équipements et capacités des salles | [Gestion des Salles & Équipements](03_mercredi_gestion_salles.md) |
| Jeudi | 23/07 | * Consultation des disponibilités via un calendrier * Validation de conflits de créneaux * Design centralisé du flux de réservation | [Consultation des Disponibilités & Design](04_jeudi_consultation_disponibilites.md) |

## Améliorations & Corrections
- **Sécurité** :
  - Mettre en place le hachage des mots de passe avec `password_hash` (bcrypt) et vérifier avec `password_verify`.
  - Implémenter la validation côté serveur (et côté client) pour éviter les injections SQL – envisager l'usage de requêtes préparées PDO.
  - Configurer les en-têtes HTTP de sécurité (CSP, X‑Content‑Type‑Options, X‑Frame‑Options).
- **Réactivité** :
  - Ajouter du chargement asynchrone (AJAX / Fetch API) pour les opérations CRUD afin d'éviter les rechargements de page complets.
  - Utiliser la pagination ou le lazy‑loading pour les listes de salles lorsqu’elles deviennent volumineuses.
- **Performance** :
  - Indexer les colonnes fréquemment filtrées (`room_id`, `date`, `user_id`) dans MySQL.
  - Optimiser les requêtes de disponibilité avec des jointures appropriées et limiter le nombre de lignes retournées.
  - Mettre en cache les requêtes de lecture statiques (ex. liste des équipements) via APCu ou un simple fichier JSON.
- **Qualité du Code** :
  - Introduire un outil d’analyse statique (PHPStan, Psalm) et des tests unitaires (PHPUnit) pour les composants critiques.
  - Standardiser le formatage du code avec PHP CS Fixer.
- **Expérience Utilisateur** :
  - Ajouter des notifications toast pour les actions réussies/échouées.
  - Améliorer l’accessibilité (ARIA, contraste des couleurs).

---
*Ce document résume les travaux accomplis durant le premier sprint et propose des axes d'amélioration pour les prochains cycles de développement.*
