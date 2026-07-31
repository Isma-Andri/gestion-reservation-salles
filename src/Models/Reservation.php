<?php
// src/Models/Reservation.php

class Reservation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les réservations avec les détails de la salle et de l'utilisateur.
     * Permet le filtrage par salle, statut et intervalle de dates (US06)
     */
    public function getAllWithDetails($salleId = null, $statut = null, $start = null, $end = null) {
        $sql = "SELECT r.*, 
                       s.nom AS salle_nom, 
                       s.capacite AS salle_capacite, 
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom, 
                       u.role AS demandeur_role, 
                       u.email AS demandeur_email
                FROM reservations r
                JOIN salles s ON r.salle_id = s.id
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE 1=1";
        
        $params = [];

        if ($salleId !== null && $salleId > 0) {
            $sql .= " AND r.salle_id = :salle_id";
            $params['salle_id'] = $salleId;
        }

        if (!empty($statut)) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $statut;
        }

        if (!empty($start)) {
            $sql .= " AND r.date_fin >= :start";
            $params['start'] = $start;
        }

        if (!empty($end)) {
            $sql .= " AND r.date_debut <= :end";
            $params['end'] = $end;
        }

        $sql .= " ORDER BY r.date_debut ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une réservation par son ID avec tous les détails
     */
    public function getById($id) {
        $sql = "SELECT r.*, 
                       s.nom AS salle_nom, 
                       s.capacite AS salle_capacite,
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom, 
                       u.role AS demandeur_role,
                       u.email AS demandeur_email
                FROM reservations r
                JOIN salles s ON r.salle_id = s.id
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE r.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réservations d'un utilisateur spécifique (US07 - Mes réservations)
     */
    public function getByUserId($utilisateurId, $statut = null) {
        $sql = "SELECT r.*, 
                       s.nom AS salle_nom, 
                       s.capacite AS salle_capacite,
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom, 
                       u.role AS demandeur_role
                FROM reservations r
                JOIN salles s ON r.salle_id = s.id
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE r.utilisateur_id = :utilisateur_id";
        
        $params = ['utilisateur_id' => $utilisateurId];

        if (!empty($statut)) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $statut;
        }

        $sql .= " ORDER BY r.date_debut DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie s'il existe un conflit de créneau pour une salle donnée (US08)
     * Un conflit existe si une réservation validée ou en attente chevauche le créneau demandé.
     * On exclut optionnellement une réservation (pour la modification).
     * 
     * Logique de chevauchement: deux intervalles [A_start, A_end] et [B_start, B_end]
     * se chevauchent si A_start < B_end ET A_end > B_start
     */
    public function checkConflict($salleId, $dateDebut, $dateFin, $excludeReservationId = null) {
        $sql = "SELECT r.id, r.titre_evenement, r.date_debut, r.date_fin, r.statut,
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom
                FROM reservations r
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE r.salle_id = :salle_id
                  AND r.statut IN ('validee', 'en_attente')
                  AND r.date_debut < :date_fin
                  AND r.date_fin > :date_debut";
        
        $params = [
            'salle_id' => $salleId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];

        if ($excludeReservationId !== null) {
            $sql .= " AND r.id != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }

        $sql .= " ORDER BY r.date_debut ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Création d'une réservation (US07)
     * Retourne l'ID de la réservation créée ou false en cas d'échec
     */
    public function create($utilisateurId, $salleId, $titreEvenement, $dateDebut, $dateFin, $statut = 'en_attente') {
        $sql = "INSERT INTO reservations (utilisateur_id, salle_id, titre_evenement, date_debut, date_fin, statut)
                VALUES (:utilisateur_id, :salle_id, :titre_evenement, :date_debut, :date_fin, :statut)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'salle_id' => $salleId,
            'titre_evenement' => $titreEvenement,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'statut' => $statut
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Met à jour le statut d'une réservation (US09, US10 - Validation)
     */
    public function updateStatut($id, $statut) {
        $sql = "UPDATE reservations SET statut = :statut WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }

    /**
     * Annule une réservation (mise à statut 'refusee') - uniquement par le propriétaire
     */
    public function cancel($id, $utilisateurId) {
        $sql = "UPDATE reservations SET statut = 'refusee' 
                WHERE id = :id AND utilisateur_id = :utilisateur_id AND statut = 'en_attente'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'utilisateur_id' => $utilisateurId
        ]);
    }

    /**
     * Compte les réservations par statut pour un utilisateur
     */
    public function countByStatutForUser($utilisateurId) {
        $sql = "SELECT statut, COUNT(*) as total 
                FROM reservations 
                WHERE utilisateur_id = :utilisateur_id 
                GROUP BY statut";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['utilisateur_id' => $utilisateurId]);
        
        $counts = ['en_attente' => 0, 'validee' => 0, 'refusee' => 0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[$row['statut']] = (int)$row['total'];
        }
        return $counts;
    }

    /**
     * Récupère toutes les réservations en attente de validation (pour le service logistique)
     */
    public function getPendingForValidation() {
        $sql = "SELECT r.*, 
                       s.nom AS salle_nom,
                       s.capacite AS salle_capacite,
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom,
                       u.role AS demandeur_role,
                       u.email AS demandeur_email
                FROM reservations r
                JOIN salles s ON r.salle_id = s.id
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE r.statut = 'en_attente'
                ORDER BY r.date_creation ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les réservations d'un utilisateur spécifique (US03 – Profil)
     */
    public function getAllByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, 
                   s.nom AS salle_nom,
                   s.capacite AS salle_capacite
            FROM reservations r
            JOIN salles s ON r.salle_id = s.id
            WHERE r.utilisateur_id = :uid
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
