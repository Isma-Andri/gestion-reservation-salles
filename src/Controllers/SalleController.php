<?php
// src/Controllers/SalleController.php

class SalleController {
    private $salleModel;

    public function __construct($salleModel) {
        $this->salleModel = $salleModel;
    }

    /**
     * Vérifie si l'utilisateur est connecté, sinon redirige vers la connexion
     */
    private function requireAuth() {
        if (!isset($_SESSION['utilisateur_id'])) {
            header("Location: /login");
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur a le rôle admin ou logistique
     */
    private function isAdminOrLogistique() {
        return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'logistique']);
    }

    /**
     * Liste des salles (US04)
     */
    public function index() {
        $this->requireAuth();

        $search = trim($_GET['q'] ?? '');
        $minCapacite = (int)($_GET['capacite'] ?? 0);

        // Si l'utilisateur est admin ou logistique, il peut voir toutes les salles (actives et inactives)
        // Sinon, il ne voit que les salles actives
        if ($this->isAdminOrLogistique()) {
            $salles = $this->salleModel->getAll($search, $minCapacite);
        } else {
            $salles = $this->salleModel->getAllActive($search, $minCapacite);
        }

        $canManage = $this->isAdminOrLogistique();
        require __DIR__ . '/../Views/salles/index.php';
    }

    /**
     * Détails d'une salle et de ses équipements (US05)
     */
    public function show() {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $salle = $this->salleModel->getById($id);

        if (!$salle) {
            header("Location: /salles?error=Salle+introuvable");
            exit;
        }

        // Si la salle est inactive et que l'utilisateur n'est pas admin/logistique
        if (!$salle['est_active'] && !$this->isAdminOrLogistique()) {
            header("Location: /salles?error=Salle+non+disponible");
            exit;
        }

        $canManage = $this->isAdminOrLogistique();

        // Convertir la chaîne des équipements en tableau d'équipements
        $equipementsList = [];
        if (!empty($salle['equipements'])) {
            $equipementsList = array_map('trim', explode(',', $salle['equipements']));
        }

        require __DIR__ . '/../Views/salles/show.php';
    }

    /**
     * Formulaire et action de création d'une salle (Admin / Logistique)
     */
    public function create() {
        $this->requireAuth();

        if (!$this->isAdminOrLogistique()) {
            header("Location: /salles?error=Accès+refusé");
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $capacite = (int)($_POST['capacite'] ?? 0);
            $equipements = trim($_POST['equipements'] ?? '');
            $est_active = isset($_POST['est_active']) ? 1 : 0;

            if (empty($nom)) {
                $error = "Le nom de la salle est obligatoire.";
            } elseif ($capacite <= 0) {
                $error = "La capacité doit être un nombre positif.";
            } else {
                try {
                    $this->salleModel->create($nom, $capacite, $equipements, $est_active);
                    header("Location: /salles?success=Salle+créée+avec+succès");
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de la création de la salle : " . $e->getMessage();
                }
            }
        }

        require __DIR__ . '/../Views/salles/create.php';
    }

    /**
     * Formulaire et action de modification d'une salle (Admin / Logistique)
     */
    public function edit() {
        $this->requireAuth();

        if (!$this->isAdminOrLogistique()) {
            header("Location: /salles?error=Accès+refusé");
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $salle = $this->salleModel->getById($id);

        if (!$salle) {
            header("Location: /salles?error=Salle+introuvable");
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $capacite = (int)($_POST['capacite'] ?? 0);
            $equipements = trim($_POST['equipements'] ?? '');
            $est_active = isset($_POST['est_active']) ? 1 : 0;

            if (empty($nom)) {
                $error = "Le nom de la salle est obligatoire.";
            } elseif ($capacite <= 0) {
                $error = "La capacité doit être un nombre positif.";
            } else {
                try {
                    $this->salleModel->update($id, $nom, $capacite, $equipements, $est_active);
                    header("Location: /salles?success=Salle+mise+à+jour+avec+succès");
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur lors de la mise à jour : " . $e->getMessage();
                }
            }
        }

        require __DIR__ . '/../Views/salles/edit.php';
    }

    /**
     * Activer / Désactiver une salle (Admin / Logistique)
     */
    public function toggleStatus() {
        $this->requireAuth();

        if (!$this->isAdminOrLogistique()) {
            header("Location: /salles?error=Accès+refusé");
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->salleModel->toggleStatus($id);
        }

        header("Location: /salles?success=Statut+de+la+salle+modifié");
        exit;
    }

    /**
     * Suppression d'une salle (Admin / Logistique)
     */
    public function delete() {
        $this->requireAuth();

        if (!$this->isAdminOrLogistique()) {
            header("Location: /salles?error=Accès+refusé");
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                $this->salleModel->delete($id);
                header("Location: /salles?success=Salle+supprimée+avec+succès");
                exit;
            } catch (Exception $e) {
                header("Location: /salles?error=Impossible+de+supprimer+la+salle+(réservations+liées)");
                exit;
            }
        }

        header("Location: /salles");
        exit;
    }
}
?>
