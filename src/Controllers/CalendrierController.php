<?php
// src/Controllers/CalendrierController.php

class CalendrierController {
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
     * Page principale du calendrier de consultation des disponibilités (US06)
     * Propose à la fois un calendrier graphique FullCalendar optimisé et une vue Grille de Planning par Salle
     */
    public function index() {
        $this->requireAuth();

        // Récupérer la liste des salles actives
        $salles = $this->salleModel->getAllActive();

        $selectedSalleId = isset($_GET['salle_id']) ? (int)$_GET['salle_id'] : 0;
        $selectedStatut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
        $activeView = isset($_GET['view']) && $_GET['view'] === 'grid' ? 'grid' : 'calendar';

        // Calcul de la semaine sélectionnée pour la Vue Grille
        $requestedDate = isset($_GET['week']) ? trim($_GET['week']) : date('Y-m-d');
        $timestamp = strtotime($requestedDate);
        if (!$timestamp) {
            $timestamp = time();
        }

        // Lundi de la semaine
        $dayOfWeek = date('N', $timestamp); // 1 (lundi) à 7 (dimanche)
        $mondayTimestamp = strtotime('-' . ($dayOfWeek - 1) . ' days', $timestamp);
        $sundayTimestamp = strtotime('+' . (7 - $dayOfWeek) . ' days', $timestamp);

        $prevWeek = date('Y-m-d', strtotime('-7 days', $mondayTimestamp));
        $nextWeek = date('Y-m-d', strtotime('+7 days', $mondayTimestamp));
        $currentWeekLabel = "Semaine du " . date('d/m/Y', $mondayTimestamp) . " au " . date('d/m/Y', $sundayTimestamp);

        // Préparation des 7 jours de la semaine
        $weekDays = [];
        $dayNamesFr = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];
        
        for ($i = 0; $i < 7; $i++) {
            $curTimestamp = strtotime("+$i days", $mondayTimestamp);
            $dateStr = date('Y-m-d', $curTimestamp);
            $dayNum = (int)date('N', $curTimestamp);
            $weekDays[] = [
                'date' => $dateStr,
                'label' => $dayNamesFr[$dayNum] . ' ' . date('d/m', $curTimestamp),
                'is_today' => ($dateStr === date('Y-m-d'))
            ];
        }

        // Récupération des réservations de la semaine pour la vue grille
        $startRange = date('Y-m-d 00:00:00', $mondayTimestamp);
        $endRange = date('Y-m-d 23:59:59', $sundayTimestamp);
        $rawReservations = $this->reservationModel->getAllWithDetails(
            $selectedSalleId > 0 ? $selectedSalleId : null,
            $selectedStatut !== '' ? $selectedStatut : null,
            $startRange,
            $endRange
        );

        // Organiser les réservations par [salle_id][Y-m-d][]
        $gridReservations = [];
        foreach ($rawReservations as $res) {
            $sId = $res['salle_id'];
            $dayStr = date('Y-m-d', strtotime($res['date_debut']));
            $gridReservations[$sId][$dayStr][] = $res;
        }

        $pageTitle = "Consultation des Disponibilités (Calendrier & Planning)";
        $loadCalendar = true;

        require __DIR__ . '/../Views/calendrier/index.php';
    }

    /**
     * API JSON renvoyant les événements de réservation pour FullCalendar
     */
    public function events() {
        $this->requireAuth();

        $salleId = isset($_GET['salle_id']) && $_GET['salle_id'] > 0 ? (int)$_GET['salle_id'] : null;
        $statut = isset($_GET['statut']) && !empty($_GET['statut']) ? trim($_GET['statut']) : null;
        $start = isset($_GET['start']) ? trim($_GET['start']) : null;
        $end = isset($_GET['end']) ? trim($_GET['end']) : null;

        $reservations = $this->reservationModel->getAllWithDetails($salleId, $statut, $start, $end);

        $events = [];
        foreach ($reservations as $res) {
            // Colors per status
            $color = '#6c757d';
            $statutLabel = 'Inconnu';

            switch ($res['statut']) {
                case 'validee':
                    $color = '#198754';
                    $statutLabel = 'Validée';
                    break;
                case 'en_attente':
                    $color = '#fd7e14';
                    $statutLabel = 'En attente';
                    break;
                case 'refusee':
                    $color = '#dc3545';
                    $statutLabel = 'Refusée';
                    break;
            }

            $events[] = [
                'id' => (int)$res['id'],
                'title' => $res['salle_nom'] . ' : ' . $res['titre_evenement'],
                'start' => $res['date_debut'],
                'end' => $res['date_fin'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'salle_nom' => $res['salle_nom'],
                    'titre_evenement' => $res['titre_evenement'],
                    'demandeur_nom' => $res['demandeur_nom'],
                    'demandeur_role' => ucfirst($res['demandeur_role']),
                    'statut' => $res['statut'],
                    'statut_label' => $statutLabel,
                    'date_debut_formatted' => date('d/m/Y H:i', strtotime($res['date_debut'])),
                    'date_fin_formatted' => date('d/m/Y H:i', strtotime($res['date_fin'])),
                    'heure_debut' => date('H:i', strtotime($res['date_debut'])),
                    'heure_fin' => date('H:i', strtotime($res['date_fin']))
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($events);
        exit;
    }
}
?>
