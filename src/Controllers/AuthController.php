<?php
// src/Controllers/AuthController.php

class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    // Gère la page et l'action de connexion (US01)[cite: 1]
    public function login() {
        $error = null;

        // Traitement du formulaire soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['mot_de_passe'] ?? '';

            // Recherche de l'utilisateur dans la base
            $user = $this->userModel->findByEmail($email);

            // Vérification du mot de passe chiffré (US18)[cite: 1]
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Initialisation des données de session (US02)[cite: 1]
                $_SESSION['utilisateur_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['nom'] = $user['nom'];

                // Redirection vers le tableau de bord
                header("Location: /dashboard");
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        }

        // Chargement de la vue de connexion
        require __DIR__ . '/../Views/auth/login.php';
    }

    // Gère la page et l'action d'inscription (US01)[cite: 1]
    public function register() {
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';
            // Les rôles autorisés : enseignant, association, logistique, admin[cite: 1]
            $role = $_POST['role'] ?? 'enseignant'; 

            try {
                // Création du compte via le modèle
                $this->userModel->create($nom, $prenom, $email, $mot_de_passe, $role);
                $success = "Compte cree avec succes. Vous pouvez vous connecter.";
            } catch (Exception $e) {
                $error = "Erreur lors de l'inscription. L'email existe peut-etre deja.";
            }
        }

        // Chargement de la vue d'inscription
        require __DIR__ . '/../Views/auth/register.php';
    }

    // Gère la déconnexion
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /login");
        exit;
    }
}
?>
