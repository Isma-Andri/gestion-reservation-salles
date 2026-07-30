<?php
// src/Models/User.php

class User {
    private $pdo;

    // Injection de la dépendance PDO via le constructeur
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Cherche un utilisateur par son adresse email
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crée un nouvel utilisateur avec un mot de passe chiffré (US18)[cite: 1]
    public function create($nom, $prenom, $email, $mot_de_passe, $role) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$nom, $prenom, $email, $hash, $role]);
    }

    // Récupère un utilisateur par son ID
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Met à jour le rôle d'un utilisateur (US02 – Admin)
    public function updateRole($id, $role) {
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    // Supprime un utilisateur (US02 – Admin)
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
