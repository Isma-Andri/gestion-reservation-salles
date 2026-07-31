<?php
// src/Controllers/AdminController.php

class AdminController {
    private $statsModel;
    private $userModel;

    public function __construct($statsModel, $userModel) {
        $this->statsModel = $statsModel;
        $this->userModel  = $userModel;
    }

    /**
     * Vérifie que l'utilisateur est un admin connecté (US18)
     */
    private function requireAdmin() {
        if (!isset($_SESSION['utilisateur_id'])) {
            header("Location: /login");
            exit;
        }
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['flash_error'] = "Accès réservé aux administrateurs.";
            header("Location: /dashboard");
            exit;
        }
    }

    /**
     * Tableau de bord statistiques admin — US14, US15
     */
    public function stats() {
        $this->requireAdmin();

        $globalStats    = $this->statsModel->getGlobalStats();
        $topSalles      = $this->statsModel->getTopSalles(5);
        $recentActivity = $this->statsModel->getRecentActivity(10);
        $monthlyData    = $this->statsModel->getReservationsByMonth(6);
        $occupationWeek = $this->statsModel->getOccupationRateThisWeek();

        $pageTitle = "Statistiques & Tableau de Bord Admin";
        require __DIR__ . '/../Views/admin/stats.php';
    }

    /**
     * Gestion des utilisateurs par l'admin — US02
     */
    public function users() {
        $this->requireAdmin();

        $users = $this->statsModel->getUsersWithStats();

        // Traitement CSRF + changement de rôle
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->verifyCSRF();
            $userId = (int)($_POST['user_id'] ?? 0);
            $action = $_POST['action'];

            if ($action === 'change_role' && $userId > 0) {
                $newRole = trim($_POST['new_role'] ?? '');
                $allowed = ['enseignant', 'association', 'logistique', 'admin'];
                if (in_array($newRole, $allowed) && $userId !== (int)$_SESSION['utilisateur_id']) {
                    $this->userModel->updateRole($userId, $newRole);
                    $_SESSION['flash_success'] = "Rôle de l'utilisateur #$userId mis à jour : $newRole";
                } else {
                    $_SESSION['flash_error'] = "Action non autorisée ou rôle invalide.";
                }
                header("Location: /admin/users");
                exit;
            }

            if ($action === 'delete_user' && $userId > 0) {
                if ($userId === (int)$_SESSION['utilisateur_id']) {
                    $_SESSION['flash_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
                } else {
                    $this->userModel->delete($userId);
                    $_SESSION['flash_success'] = "Utilisateur #$userId supprimé.";
                }
                header("Location: /admin/users");
                exit;
            }
        }

        $pageTitle = "Gestion des Utilisateurs";
        require __DIR__ . '/../Views/admin/users.php';
    }

    /**
     * Page d'export avec formulaire de filtrage visuel — US16
     */
    public function exportPage() {
        $this->requireAdmin();

        // Récupérer la liste des salles pour le filtre
        $salles   = $this->statsModel->getAllSallesForFilter();
        $pageTitle = "Export des données";
        require __DIR__ . '/../Views/admin/export.php';
    }

    /**
     * Export CSV des réservations — US16
     */
    public function exportCsv() {
        $this->requireAdmin();

        $statut    = trim($_GET['statut'] ?? '');
        $salleId   = (int)($_GET['salle_id'] ?? 0);
        $dateDebut = trim($_GET['date_debut'] ?? '');
        $dateFin   = trim($_GET['date_fin'] ?? '');
        $data      = $this->statsModel->getAllForExport(
            $statut ?: null, $salleId ?: null,
            $dateDebut ?: null, $dateFin ?: null
        );

        $filename = 'reservations_export_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM UTF-8 pour Excel
        fputs($output, "\xEF\xBB\xBF");

        // En-tête CSV
        fputcsv($output, ['ID', 'Demandeur', 'Rôle', 'Email', 'Salle', 'Événement', 'Début', 'Fin', 'Statut', 'Date Création'], ';');

        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['demandeur'],
                $row['role_demandeur'],
                $row['email_demandeur'],
                $row['salle'],
                $row['titre_evenement'],
                date('d/m/Y H:i', strtotime($row['date_debut'])),
                date('d/m/Y H:i', strtotime($row['date_fin'])),
                $row['statut'],
                date('d/m/Y H:i', strtotime($row['date_creation'])),
            ], ';');
        }
        fclose($output);
        exit;
    }

    /**
     * Génère et retourne un token CSRF, stocké en session (US18)
     */
    public static function getCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le token CSRF envoyé avec le formulaire (US18)
     */
    private function verifyCSRF() {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die("CSRF token invalide. Action refusée pour des raisons de sécurité.");
        }
    }
}
?>
