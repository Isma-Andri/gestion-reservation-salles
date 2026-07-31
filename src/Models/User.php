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

    // Met à jour les informations du profil (US03)
    public function updateProfile($id, $nom, $prenom, $email, $telephone) {
        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        $check = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
        if ($check->fetch()) {
            throw new Exception("Cet email est déjà utilisé par un autre compte.");
        }
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?"
        );
        return $stmt->execute([$nom, $prenom, $email, $telephone ?: null, $id]);
    }

    // Met à jour le mot de passe après vérification de l'ancien (US03)
    public function updatePassword($id, $currentPassword, $newPassword) {
        // Récupérer le hash actuel
        $user = $this->findById($id);
        if (!$user || !password_verify($currentPassword, $user['mot_de_passe'])) {
            throw new Exception("Mot de passe actuel incorrect.");
        }
        if (strlen($newPassword) < 6) {
            throw new Exception("Le nouveau mot de passe doit contenir au moins 6 caractères.");
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }
}
?>
