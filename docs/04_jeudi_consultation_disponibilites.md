# Documentation de Développement - Jeudi 23 Juillet 2026

## 📌 Objectif de la journée
Développement de la consultation des disponibilités des salles via un calendrier interactif (User Story US06), amélioration majeure de la lisibilité/pratique (Vue Grille par Salle & Filtres rapides) et centralisation du design minimaliste de l'application web.

---

## 🛠️ Réalisations

### 1. Centralisation du Design & Architecture Layout (`public/css/` & `src/Views/layout/`)
- **Feuille de style centralisée (`public/css/style.css`)** :
  - Définition des variables CSS (couleurs principales, ombres, arrondis).
  - Normalisation des styles de cartes responsives (`.card-salle`, `.dashboard-card`).
  - Composants de badges d'équipements (`.badge-equipment`, `.eq-chip`).
  - Styles personnalisés pour le calendrier FullCalendar et la grille de planning par salle (`.table-planning`, `.slot-card`).
- **Composants Layout Réutilisables (`src/Views/layout/`)** :
  - `header.php` : En-tête HTML centralisé, métadonnées, chargement conditionnel des CSS (Bootstrap 5, Bootstrap Icons, FullCalendar).
  - `navbar.php` : Barre de navigation réutilisable avec gestion dynamique des liens actifs, profil utilisateur connecté et bouton de déconnexion.
  - `alerts.php` : Composant unique d'affichage des messages flash (`success` et `error`).
  - `footer.php` : Pied de page standardisé et scripts JS (Bootstrap Bundle, FullCalendar CDN).

### 2. Modèle Réservation (`src/Models/Reservation.php`)
- `getAllWithDetails($salleId, $statut, $start, $end)` : Récupère les réservations avec jointures SQL (`salles` et `utilisateurs`) et prend en charge le filtrage dynamique par salle, statut d'approbation et plage de dates.
- `getById($id)` : Récupère les détails complets d'une réservation spécifique.
- `create(...)` : Permet l'insertion de demandes de réservations.

### 3. Contrôleur Calendrier (`src/Controllers/CalendrierController.php`)
- **`index()` (US06)** : Prépare la double vue (Calendrier graphique + Grille hebdomadaire par salle), gère la navigation entre semaines (`?week=YYYY-MM-DD`) et extrait les réservations ordonnées.
- **`events()` (API JSON)** : Fournit le flux JSON compatible FullCalendar (`/calendrier/events`), en appliquant les couleurs selon le statut (`validee` => vert, `en_attente` => orange, `refusee` => rouge) et en envoyant les métadonnées de l'événement.

### 4. Amélioration de la Lisibilité du Planning (Double Vue & Onglets) (`src/Views/calendrier/index.php`)
- **Boutons Onglets par Salle (`.salle-pill`)** : Filtrage instantané d'une salle spécifique en un clic, évitant le chevauchement complexe de plusieurs salles en vue semaine.
- **Optimisation FullCalendar (Vue Semaine & Jour)** :
  - Restriction des créneaux aux heures ouvrées (07:30 à 20:30) via `slotMinTime` et `slotMaxTime` pour éliminer les heures de nuit inutiles.
  - Suppression de la ligne "Toute la journée" (`allDaySlot: false`) pour maximiser l'espace vertical.
  - Rendu HTML personnalisé (`eventContent`) affichant le badge de la salle, la plage horaire exacte et le titre de l'événement.
- **Nouvelle Vue "Grille de Planning par Salle" (`table-planning`)** :
  - Matrice hebdomadaire lisible sous forme de tableau comparatif (Salles en lignes, Jours de la semaine en colonnes).
  - Cartes de créneaux colorées et cliquables pour ouvrir les détails.
  - Mise en évidence automatique de la journée courante et affichage du statut "Libre" lorsque la salle est disponible.
- **Fenêtre modale de détails (`#eventModal`)** : Consultation complète au clic sur un événement.

### 5. Routage & Dashboard
- Déclaration des routes `/calendrier` et `/calendrier/events` dans `public/index.php`.
- Ajout du raccourci d'accès au calendrier sur le Tableau de bord (`src/Views/dashboard/index.php`).
- Insertion de jeux de données de test en base (réservations pour la semaine du 20 au 27 juillet 2026).

---

## 🔒 Sécurité & Bonnes pratiques
- **Authentification requise** : Accès au calendrier et à son API JSON strictement réservé aux utilisateurs authentifiés (`requireAuth`).
- **Protection des requêtes** : Requêtes SQL préparées PDO pour l'extraction filtrée des réservations sans risque d'injection SQL.
- **Sécurisation de l'affichage** : Échappement des caractères HTML dans les modales JS et les vues PHP (`htmlspecialchars`).
