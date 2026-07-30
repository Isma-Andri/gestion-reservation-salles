<?php
// src/Models/Stats.php

class Stats {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Statistiques globales : total reservations, utilisateurs, salles
     * US14 – Statistiques d'utilisation
     */
    public function getGlobalStats() {
        // Réservations
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN statut='validee' THEN 1 ELSE 0 END) AS validees,
                SUM(CASE WHEN statut='en_attente' THEN 1 ELSE 0 END) AS en_attente,
                SUM(CASE WHEN statut='refusee' THEN 1 ELSE 0 END) AS refusees
            FROM reservations
        ");
        $reservations = $stmt->fetch(PDO::FETCH_ASSOC);

        // Utilisateurs par rôle
        $stmt = $this->pdo->query("
            SELECT role, COUNT(*) AS total FROM utilisateurs GROUP BY role
        ");
        $usersByRole = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $usersByRole[$row['role']] = (int)$row['total'];
        }

        // Salles actives / inactives
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) AS total,
                SUM(est_active) AS actives,
                SUM(1 - est_active) AS inactives
            FROM salles
        ");
        $salles = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'reservations' => $reservations,
            'users_by_role' => $usersByRole,
            'total_users' => array_sum($usersByRole),
            'salles' => $salles,
        ];
    }

    /**
     * Salles les plus réservées (validées uniquement) — US14, US15
     */
    public function getTopSalles($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT s.nom, s.capacite,
                   COUNT(r.id) AS nb_reservations,
                   ROUND(
                       COUNT(r.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM reservations WHERE statut='validee'), 0),
                   1) AS pct_of_validated
            FROM salles s
            LEFT JOIN reservations r ON s.id = r.salle_id AND r.statut = 'validee'
            GROUP BY s.id
            ORDER BY nb_reservations DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Taux d'occupation par salle sur la semaine courante — US15
     */
    public function getOccupationRateThisWeek() {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $sunday = date('Y-m-d', strtotime('sunday this week')) . ' 23:59:59';

        $stmt = $this->pdo->prepare("
            SELECT s.nom,
                   COUNT(r.id) AS nb,
                   IFNULL(SUM(TIMESTAMPDIFF(MINUTE, r.date_debut, r.date_fin)), 0) AS minutes_occupees
            FROM salles s
            LEFT JOIN reservations r ON s.id = r.salle_id 
                AND r.statut = 'validee'
                AND r.date_debut >= :monday 
                AND r.date_fin <= :sunday
            WHERE s.est_active = 1
            GROUP BY s.id
            ORDER BY minutes_occupees DESC
        ");
        $stmt->execute([':monday' => $monday, ':sunday' => $sunday]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Réservations par mois sur les 6 derniers mois — US14
     */
    public function getReservationsByMonth($months = 6) {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE_FORMAT(date_debut, '%Y-%m') AS mois,
                DATE_FORMAT(date_debut, '%b %Y') AS mois_label,
                COUNT(*) AS total,
                SUM(CASE WHEN statut='validee' THEN 1 ELSE 0 END) AS validees,
                SUM(CASE WHEN statut='en_attente' THEN 1 ELSE 0 END) AS en_attente,
                SUM(CASE WHEN statut='refusee' THEN 1 ELSE 0 END) AS refusees
            FROM reservations
            WHERE date_debut >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(date_debut, '%Y-%m')
            ORDER BY mois ASC
        ");
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Activité récente (10 dernières réservations) — US14
     */
    public function getRecentActivity($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT r.id, r.titre_evenement, r.statut, r.date_creation,
                   r.date_debut, r.date_fin,
                   s.nom AS salle_nom,
                   CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom,
                   u.role AS demandeur_role
            FROM reservations r
            JOIN salles s ON r.salle_id = s.id
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            ORDER BY r.date_creation DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Export CSV des réservations — US16
     */
    public function getAllForExport($statut = null, $salleId = null) {
        $sql = "
            SELECT r.id, 
                   CONCAT(u.prenom, ' ', u.nom) AS demandeur,
                   u.role AS role_demandeur,
                   u.email AS email_demandeur,
                   s.nom AS salle,
                   r.titre_evenement,
                   r.date_debut, r.date_fin,
                   r.statut,
                   r.date_creation
            FROM reservations r
            JOIN salles s ON r.salle_id = s.id
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($statut)) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $statut;
        }
        if (!empty($salleId)) {
            $sql .= " AND r.salle_id = :salle_id";
            $params['salle_id'] = $salleId;
        }
        $sql .= " ORDER BY r.date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les utilisateurs avec leur nombre de réservations — US14
     */
    public function getUsersWithStats() {
        $stmt = $this->pdo->query("
            SELECT u.id, u.nom, u.prenom, u.email, u.role, u.date_creation,
                   COUNT(r.id) AS nb_reservations,
                   SUM(CASE WHEN r.statut='validee' THEN 1 ELSE 0 END) AS validees
            FROM utilisateurs u
            LEFT JOIN reservations r ON u.id = r.utilisateur_id
            GROUP BY u.id
            ORDER BY nb_reservations DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
