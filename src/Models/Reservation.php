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
     * Récupère une réservation par son ID
     */
    public function getById($id) {
        $sql = "SELECT r.*, 
                       s.nom AS salle_nom, 
                       CONCAT(u.prenom, ' ', u.nom) AS demandeur_nom, 
                       u.role AS demandeur_role
                FROM reservations r
                JOIN salles s ON r.salle_id = s.id
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                WHERE r.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Création d'une réservation (utilisé pour l'initialisation et les futures US)
     */
    public function create($utilisateurId, $salleId, $titreEvenement, $dateDebut, $dateFin, $statut = 'en_attente') {
        $sql = "INSERT INTO reservations (utilisateur_id, salle_id, titre_evenement, date_debut, date_fin, statut)
                VALUES (:utilisateur_id, :salle_id, :titre_evenement, :date_debut, :date_fin, :statut)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'salle_id' => $salleId,
            'titre_evenement' => $titreEvenement,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'statut' => $statut
        ]);
    }
}
?>
