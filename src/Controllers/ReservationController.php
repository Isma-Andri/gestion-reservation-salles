<?php
// src/Controllers/ReservationController.php

class ReservationController {
    private $reservationModel;
    private $salleModel;

    public function __construct($reservationModel, $salleModel) {
        $this->reservationModel = $reservationModel;
        $this->salleModel = $salleModel;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    private function requireAuth() {
        if (!isset($_SESSION['utilisateur_id'])) {
            header("Location: /login");
            exit;
        }
    }

    /**
     * Formulaire de création d'une nouvelle réservation (US07)
     * Affiche le formulaire ou traite la soumission
     */
    public function create() {
        $this->requireAuth();

        $salles = $this->salleModel->getAllActive();
        $errors = [];
        $success = false;
        $conflicts = [];
        $formData = [
            'salle_id' => $_GET['salle_id'] ?? '',
            'titre_evenement' => '',
            'date_debut' => '',
            'heure_debut' => '',
            'date_fin' => '',
            'heure_fin' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et assainir les données du formulaire
            $salleId = (int)($_POST['salle_id'] ?? 0);
            $titreEvenement = trim($_POST['titre_evenement'] ?? '');
            $dateDebutStr = trim($_POST['date_debut'] ?? '');
            $heureDebut = trim($_POST['heure_debut'] ?? '');
            $dateFinStr = trim($_POST['date_fin'] ?? '');
            $heureFin = trim($_POST['heure_fin'] ?? '');

            // Garder les valeurs en mémoire pour ré-afficher en cas d'erreur
            $formData = [
                'salle_id' => $salleId,
                'titre_evenement' => $titreEvenement,
                'date_debut' => $dateDebutStr,
                'heure_debut' => $heureDebut,
                'date_fin' => $dateFinStr,
                'heure_fin' => $heureFin
            ];

            // --- Validation côté serveur ---
            if ($salleId <= 0) {
                $errors[] = "Veuillez sélectionner une salle.";
            } else {
                $salle = $this->salleModel->getById($salleId);
                if (!$salle || !$salle['est_active']) {
                    $errors[] = "La salle sélectionnée n'existe pas ou est inactive.";
                }
            }

            if (empty($titreEvenement)) {
                $errors[] = "Le titre/motif de l'événement est obligatoire.";
            } elseif (strlen($titreEvenement) > 150) {
                $errors[] = "Le titre ne doit pas dépasser 150 caractères.";
            }

            if (empty($dateDebutStr) || empty($heureDebut)) {
                $errors[] = "La date et l'heure de début sont obligatoires.";
            }

            if (empty($dateFinStr) || empty($heureFin)) {
                $errors[] = "La date et l'heure de fin sont obligatoires.";
            }

            // Construire les datetime complets
            $dateDebut = $dateDebutStr . ' ' . $heureDebut . ':00';
            $dateFin = $dateFinStr . ' ' . $heureFin . ':00';

            $tsDebut = strtotime($dateDebut);
            $tsFin = strtotime($dateFin);

            if ($tsDebut === false || $tsFin === false) {
                $errors[] = "Format de date ou d'heure invalide.";
            } elseif ($tsDebut >= $tsFin) {
                $errors[] = "La date/heure de fin doit être postérieure à la date/heure de début.";
            } elseif ($tsDebut < time()) {
                $errors[] = "Impossible de réserver dans le passé.";
            } elseif (($tsFin - $tsDebut) > 86400) {
                $errors[] = "Une réservation ne peut pas dépasser 24 heures.";
            }

            // --- Vérification des conflits (US08) ---
            if (empty($errors) && $salleId > 0) {
                $conflicts = $this->reservationModel->checkConflict($salleId, $dateDebut, $dateFin);

                if (!empty($conflicts)) {
                    $errors[] = "Conflit détecté ! La salle est déjà réservée sur ce créneau.";
                }
            }

            // --- Création si aucune erreur ---
            if (empty($errors)) {
                // Déterminer le statut initial selon le rôle (préparation US09)
                $role = $_SESSION['role'] ?? '';
                $statut = 'en_attente'; // Par défaut en attente

                $newId = $this->reservationModel->create(
                    $_SESSION['utilisateur_id'],
                    $salleId,
                    $titreEvenement,
                    $dateDebut,
                    $dateFin,
                    $statut
                );

                if ($newId) {
                    $_SESSION['flash_success'] = "Réservation créée avec succès ! (N° $newId) – Statut : en attente de validation.";
                    header("Location: /reservations");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création de la réservation. Veuillez réessayer.";
                }
            }
        }

        $pageTitle = "Nouvelle Réservation";
        require __DIR__ . '/../Views/reservations/create.php';
    }

    /**
     * Liste des réservations de l'utilisateur connecté (Mes réservations)
     */
    public function index() {
        $this->requireAuth();

        $filterStatut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
        $reservations = $this->reservationModel->getByUserId(
            $_SESSION['utilisateur_id'],
            !empty($filterStatut) ? $filterStatut : null
        );
        $counts = $this->reservationModel->countByStatutForUser($_SESSION['utilisateur_id']);

        $pageTitle = "Mes Réservations";
        require __DIR__ . '/../Views/reservations/index.php';
    }

    /**
     * Détails d'une réservation spécifique
     */
    public function show() {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: /reservations");
            exit;
        }

        $reservation = $this->reservationModel->getById($id);
        if (!$reservation) {
            $_SESSION['flash_error'] = "Réservation introuvable.";
            header("Location: /reservations");
            exit;
        }

        // Vérifier que l'utilisateur a le droit de voir cette réservation
        $isOwner = ($reservation['utilisateur_id'] == $_SESSION['utilisateur_id']);
        $isAdmin = in_array($_SESSION['role'], ['admin', 'logistique']);
        
        if (!$isOwner && !$isAdmin) {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à cette réservation.";
            header("Location: /reservations");
            exit;
        }

        $pageTitle = "Détails Réservation #" . $id;
        require __DIR__ . '/../Views/reservations/show.php';
    }

    /**
     * Annulation d'une réservation en attente par son propriétaire
     */
    public function cancel() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /reservations");
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: /reservations");
            exit;
        }

        $reservation = $this->reservationModel->getById($id);
        if (!$reservation) {
            $_SESSION['flash_error'] = "Réservation introuvable.";
            header("Location: /reservations");
            exit;
        }

        // Seul le propriétaire ou un admin peut annuler
        $isOwner = ($reservation['utilisateur_id'] == $_SESSION['utilisateur_id']);
        $isAdmin = in_array($_SESSION['role'], ['admin', 'logistique']);

        if (!$isOwner && !$isAdmin) {
            $_SESSION['flash_error'] = "Action non autorisée.";
            header("Location: /reservations");
            exit;
        }

        if ($reservation['statut'] !== 'en_attente') {
            $_SESSION['flash_error'] = "Seules les réservations en attente peuvent être annulées.";
            header("Location: /reservations");
            exit;
        }

        $result = $this->reservationModel->updateStatut($id, 'refusee');
        if ($result) {
            $_SESSION['flash_success'] = "Réservation #$id annulée avec succès.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'annulation.";
        }

        header("Location: /reservations");
        exit;
    }

    /**
     * API JSON pour vérifier les conflits en temps réel (appelé en AJAX depuis le formulaire)
     */
    public function checkConflictApi() {
        $this->requireAuth();

        header('Content-Type: application/json');

        $salleId = (int)($_GET['salle_id'] ?? 0);
        $dateDebut = trim($_GET['date_debut'] ?? '');
        $dateFin = trim($_GET['date_fin'] ?? '');

        if ($salleId <= 0 || empty($dateDebut) || empty($dateFin)) {
            echo json_encode(['hasConflict' => false, 'conflicts' => [], 'error' => 'Paramètres manquants']);
            exit;
        }

        $conflicts = $this->reservationModel->checkConflict($salleId, $dateDebut, $dateFin);

        $formatted = [];
        foreach ($conflicts as $c) {
            $formatted[] = [
                'id' => $c['id'],
                'titre' => $c['titre_evenement'],
                'debut' => date('d/m/Y H:i', strtotime($c['date_debut'])),
                'fin' => date('d/m/Y H:i', strtotime($c['date_fin'])),
                'statut' => $c['statut'],
                'demandeur' => $c['demandeur_nom']
            ];
        }

        echo json_encode([
            'hasConflict' => !empty($conflicts),
            'conflicts' => $formatted
        ]);
        exit;
    }
}
?>
