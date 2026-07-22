<?php
// src/Models/Salle.php

class Salle {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les salles (toutes ou filtrées par recherche)
     */
    public function getAll($search = '', $minCapacite = 0) {
        $sql = "SELECT * FROM salles WHERE capacite >= ?";
        $params = [(int)$minCapacite];

        if (!empty($search)) {
            $sql .= " AND (nom LIKE ? OR equipements LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère uniquement les salles actives (pour la consultation publique/réservations)
     */
    public function getAllActive($search = '', $minCapacite = 0) {
        $sql = "SELECT * FROM salles WHERE est_active = 1 AND capacite >= ?";
        $params = [(int)$minCapacite];

        if (!empty($search)) {
            $sql .= " AND (nom LIKE ? OR equipements LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une salle par son ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM salles WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle salle (US04, US05)
     */
    public function create($nom, $capacite, $equipements, $est_active = 1) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO salles (nom, capacite, equipements, est_active) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            trim($nom),
            (int)$capacite,
            trim($equipements),
            $est_active ? 1 : 0
        ]);
    }

    /**
     * Met à jour les informations d'une salle
     */
    public function update($id, $nom, $capacite, $equipements, $est_active) {
        $stmt = $this->pdo->prepare(
            "UPDATE salles SET nom = ?, capacite = ?, equipements = ?, est_active = ? WHERE id = ?"
        );
        return $stmt->execute([
            trim($nom),
            (int)$capacite,
            trim($equipements),
            $est_active ? 1 : 0,
            (int)$id
        ]);
    }

    /**
     * Bascule le statut d'activation d'une salle
     */
    public function toggleStatus($id) {
        $stmt = $this->pdo->prepare("UPDATE salles SET est_active = NOT est_active WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    /**
     * Supprime définitivement une salle
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM salles WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}
?>
