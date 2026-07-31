<?php
// src/Controllers/ProfileController.php

class ProfileController {
    private $userModel;
    private $reservationModel;

    public function __construct($userModel, $reservationModel) {
        $this->userModel        = $userModel;
        $this->reservationModel = $reservationModel;
    }

    /**
     * Vérifie que l'utilisateur est connecté (US18)
     */
    private function requireAuth() {
        if (!isset($_SESSION['utilisateur_id'])) {
            header("Location: /login");
            exit;
        }
    }

    /**
     * Affiche le profil de l'utilisateur connecté (US03)
     */
    public function show() {
        $this->requireAuth();

        $userId = (int)$_SESSION['utilisateur_id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            header("Location: /logout");
            exit;
        }

        // Historique des réservations personnelles
        $reservations = $this->reservationModel->getAllByUser($userId);

        $pageTitle = "Mon Profil";
        require __DIR__ . '/../Views/profile/show.php';
    }

    /**
     * Formulaire + traitement : modification du profil (US03)
     */
    public function edit() {
        $this->requireAuth();

        $userId = (int)$_SESSION['utilisateur_id'];
        $user   = $this->userModel->findById($userId);
        $error  = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'update_profile';

            if ($action === 'update_profile') {
                // Mise à jour des informations personnelles
                $nom       = trim($_POST['nom'] ?? '');
                $prenom    = trim($_POST['prenom'] ?? '');
                $email     = trim($_POST['email'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');

                if (empty($nom) || empty($prenom) || empty($email)) {
                    $error = "Le nom, prénom et email sont obligatoires.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "L'adresse email n'est pas valide.";
                } else {
                    try {
                        $this->userModel->updateProfile($userId, $nom, $prenom, $email, $telephone);

                        // Mettre à jour la session
                        $_SESSION['nom']    = $nom;
                        $_SESSION['prenom'] = $prenom;

                        // Recharger les données utilisateur
                        $user = $this->userModel->findById($userId);
                        $success = "Vos informations ont été mises à jour avec succès.";
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                }

            } elseif ($action === 'update_password') {
                // Changement de mot de passe
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword     = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $error = "Tous les champs du mot de passe sont obligatoires.";
                } elseif ($newPassword !== $confirmPassword) {
                    $error = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
                } else {
                    try {
                        $this->userModel->updatePassword($userId, $currentPassword, $newPassword);
                        $success = "Mot de passe modifié avec succès.";
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                }
            }
        }

        $reservations = $this->reservationModel->getAllByUser($userId);
        $pageTitle = "Modifier mon Profil";
        require __DIR__ . '/../Views/profile/edit.php';
    }
}
?>
