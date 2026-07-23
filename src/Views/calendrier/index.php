<?php 
// src/Views/calendrier/index.php
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container-fluid px-lg-5 pb-5">
    <?php require __DIR__ . '/../layout/alerts.php'; ?>

    <!-- En-tête de la page & Commutateur de Vues -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar3 text-primary me-2"></i>Planning & Disponibilités des Salles
            </h2>
            <p class="text-muted mb-0">Consultez l'occupation des salles par calendrier graphique ou par grille d'emploi du temps (US06)</p>
        </div>
        
        <!-- Selecteur de Vue (Calendrier vs Grille) -->
        <div class="btn-group shadow-sm" role="group">
            <button type="button" id="btnViewCalendar" class="btn <?php echo $activeView === 'calendar' ? 'btn-primary' : 'btn-outline-primary'; ?> fw-semibold">
                <i class="bi bi-calendar-week me-1"></i>Vue Calendrier
            </button>
            <button type="button" id="btnViewGrid" class="btn <?php echo $activeView === 'grid' ? 'btn-primary' : 'btn-outline-primary'; ?> fw-semibold">
                <i class="bi bi-table me-1"></i>Vue Grille par Salle
            </button>
        </div>
    </div>

    <!-- Filtres Rapides par Salle (Boutons Onglets) & Filtre Statut -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <!-- Onglets/Pills Rapides des Salles -->
                <div class="col-lg-8">
                    <label class="form-label fw-semibold mb-2 text-muted small uppercase me-2">
                        <i class="bi bi-building me-1"></i>Filtrer par salle :
                    </label>
                    <div class="d-flex flex-wrap gap-2 room-pills" id="sallePillsContainer">
                        <button type="button" class="btn btn-outline-primary btn-sm salle-pill <?php echo $selectedSalleId === 0 ? 'active' : ''; ?>" data-salle-id="0">
                            <i class="bi bi-grid-fill me-1"></i>Toutes les salles
                        </button>
                        <?php foreach ($salles as $salle): ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm salle-pill <?php echo $selectedSalleId == $salle['id'] ? 'active btn-primary text-white' : ''; ?>" data-salle-id="<?php echo $salle['id']; ?>">
                                <i class="bi bi-door-closed me-1"></i><?php echo htmlspecialchars($salle['nom']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Filtre Statut & Selecteur Caché (gardé synchro) -->
                <div class="col-lg-4 d-flex align-items-center justify-content-lg-end gap-3">
                    <div>
                        <select id="filterStatut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="validee" <?php echo $selectedStatut === 'validee' ? 'selected' : ''; ?>>Validées uniquement</option>
                            <option value="en_attente" <?php echo $selectedStatut === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="refusee" <?php echo $selectedStatut === 'refusee' ? 'selected' : ''; ?>>Refusées</option>
                        </select>
                    </div>
                    <input type="hidden" id="filterSalle" value="<?php echo $selectedSalleId; ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- VUE 1 : CALENDRIER INTERACTIF FULLCALENDAR (Optimisé pour la lisibilité) -->
    <div id="containerCalendarView" class="<?php echo $activeView === 'grid' ? 'd-none' : ''; ?>">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light rounded">
                <span class="small text-muted fw-semibold ps-2">
                    <i class="bi bi-info-circle me-1 text-primary"></i>Astuce : Basculez entre les vues <strong>Semaine</strong>, <strong>Jour</strong>, <strong>Mois</strong> ou <strong>Liste</strong>. Cliquez sur une salle ci-dessus pour isoler son planning.
                </span>
                <div class="d-flex gap-2">
                    <span class="badge bg-success px-2 py-1"><span class="legend-indicator bg-white"></span>Validée</span>
                    <span class="badge bg-warning text-dark px-2 py-1"><span class="legend-indicator bg-dark"></span>En attente</span>
                    <span class="badge bg-danger px-2 py-1"><span class="legend-indicator bg-white"></span>Refusée</span>
                </div>
            </div>
        </div>
        <div id="calendar" class="shadow-sm border-0 rounded-3"></div>
    </div>

    <!-- VUE 2 : GRILLE DE PLANNING PAR SALLE (Emploi du temps synthétique) -->
    <div id="containerGridView" class="<?php echo $activeView === 'calendar' ? 'd-none' : ''; ?>">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-table text-primary me-2"></i><?php echo htmlspecialchars($currentWeekLabel); ?>
                </h5>
                <div class="d-flex gap-2">
                    <a href="/calendrier?view=grid&week=<?php echo $prevWeek; ?>&salle_id=<?php echo $selectedSalleId; ?>&statut=<?php echo urlencode($selectedStatut); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-chevron-left me-1"></i>Semaine précédente
                    </a>
                    <a href="/calendrier?view=grid&week=<?php echo date('Y-m-d'); ?>&salle_id=<?php echo $selectedSalleId; ?>&statut=<?php echo urlencode($selectedStatut); ?>" class="btn btn-outline-primary btn-sm">
                        Aujourd'hui
                    </a>
                    <a href="/calendrier?view=grid&week=<?php echo $nextWeek; ?>&salle_id=<?php echo $selectedSalleId; ?>&statut=<?php echo urlencode($selectedStatut); ?>" class="btn btn-outline-secondary btn-sm">
                        Semaine suivante<i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-planning mb-0">
                    <thead>
                        <tr>
                            <th class="salle-col text-center">Salle & Capacité</th>
                            <?php foreach ($weekDays as $day): ?>
                                <th class="text-center <?php echo $day['is_today'] ? 'day-header-today' : ''; ?>">
                                    <?php echo htmlspecialchars($day['label']); ?>
                                    <?php if ($day['is_today']): ?>
                                        <span class="badge bg-primary d-block mt-1" style="font-size:0.65rem;">Aujourd'hui</span>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $filteredSalles = $salles;
                        if ($selectedSalleId > 0) {
                            $filteredSalles = array_filter($salles, function($s) use ($selectedSalleId) {
                                return $s['id'] == $selectedSalleId;
                            });
                        }
                        ?>
                        <?php foreach ($filteredSalles as $salle): ?>
                            <tr>
                                <td class="salle-col">
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($salle['nom']); ?></div>
                                    <div class="text-muted small">
                                        <i class="bi bi-people-fill text-primary me-1"></i><?php echo (int)$salle['capacite']; ?> places
                                    </div>
                                    <a href="/salles/voir?id=<?php echo $salle['id']; ?>" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary mt-1">
                                        <i class="bi bi-info-circle me-1"></i>Détails
                                    </a>
                                </td>

                                <?php foreach ($weekDays as $day): ?>
                                    <?php 
                                        $dStr = $day['date'];
                                        $dayReservations = $gridReservations[$salle['id']][$dStr] ?? [];
                                    ?>
                                    <td>
                                        <?php if (!empty($dayReservations)): ?>
                                            <?php foreach ($dayReservations as $res): ?>
                                                <div class="slot-card statut-<?php echo $res['statut']; ?>" 
                                                     onclick="showModalDetails(<?php echo htmlspecialchars(json_encode([
                                                         'salle_nom' => $res['salle_nom'],
                                                         'titre_evenement' => $res['titre_evenement'],
                                                         'date_debut_formatted' => date('d/m/Y H:i', strtotime($res['date_debut'])),
                                                         'date_fin_formatted' => date('d/m/Y H:i', strtotime($res['date_fin'])),
                                                         'demandeur_nom' => $res['demandeur_nom'],
                                                         'demandeur_role' => ucfirst($res['demandeur_role']),
                                                         'statut' => $res['statut'],
                                                         'statut_label' => ($res['statut'] === 'validee' ? 'Validée' : ($res['statut'] === 'en_attente' ? 'En attente' : 'Refusée'))
                                                     ]), ENT_QUOTES, 'UTF-8'); ?>)">
                                                    <div class="fw-bold text-primary mb-1">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <?php echo date('H:i', strtotime($res['date_debut'])); ?> - <?php echo date('H:i', strtotime($res['date_fin'])); ?>
                                                    </div>
                                                    <div class="fw-semibold text-dark text-truncate" title="<?php echo htmlspecialchars($res['titre_evenement']); ?>">
                                                        <?php echo htmlspecialchars($res['titre_evenement']); ?>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($res['demandeur_nom']); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="slot-free">
                                                <i class="bi bi-check-circle text-success me-1"></i>Libre
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Détails Réservation (Partagé) -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="eventModalLabel">
                    <i class="bi bi-info-circle me-2"></i>Détails de la Réservation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="text-muted small uppercase fw-bold">Salle concernée</label>
                    <div class="fs-5 fw-bold text-primary" id="modalSalle"></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small uppercase fw-bold">Motif / Titre</label>
                    <div class="fs-6 fw-semibold text-dark" id="modalTitre"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small uppercase fw-bold"><i class="bi bi-clock me-1"></i>Début</label>
                        <div class="fw-semibold text-dark" id="modalDebut"></div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small uppercase fw-bold"><i class="bi bi-clock-history me-1"></i>Fin</label>
                        <div class="fw-semibold text-dark" id="modalFin"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small uppercase fw-bold"><i class="bi bi-person me-1"></i>Réservé par</label>
                    <div id="modalDemandeur"></div>
                </div>

                <div>
                    <label class="text-muted small uppercase fw-bold mb-1">Statut actuel</label>
                    <div id="modalStatut"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JS (FullCalendar & Interactions) -->
<script>
var eventModalObj;

function showModalDetails(props) {
    document.getElementById('modalSalle').textContent = props.salle_nom;
    document.getElementById('modalTitre').textContent = props.titre_evenement;
    document.getElementById('modalDebut').textContent = props.date_debut_formatted;
    document.getElementById('modalFin').textContent = props.date_fin_formatted;
    document.getElementById('modalDemandeur').innerHTML = '<strong>' + props.demandeur_nom + '</strong> <span class="badge bg-secondary ms-1">' + props.demandeur_role + '</span>';

    var badgeClass = 'bg-secondary';
    if (props.statut === 'validee') badgeClass = 'bg-success';
    else if (props.statut === 'en_attente') badgeClass = 'bg-warning text-dark';
    else if (props.statut === 'refusee') badgeClass = 'bg-danger';

    document.getElementById('modalStatut').innerHTML = '<span class="badge ' + badgeClass + ' px-3 py-2 fs-6">' + props.statut_label + '</span>';

    if (!eventModalObj) {
        eventModalObj = new bootstrap.Modal(document.getElementById('eventModal'));
    }
    eventModalObj.show();
}

document.addEventListener('DOMContentLoaded', function() {
    eventModalObj = new bootstrap.Modal(document.getElementById('eventModal'));

    var calendarEl = document.getElementById('calendar');
    var filterSalleEl = document.getElementById('filterSalle');
    var filterStatutEl = document.getElementById('filterStatut');

    var btnViewCalendar = document.getElementById('btnViewCalendar');
    var btnViewGrid = document.getElementById('btnViewGrid');
    var containerCalendarView = document.getElementById('containerCalendarView');
    var containerGridView = document.getElementById('containerGridView');

    // Gestion du basculement entre Vue Calendrier et Vue Grille
    btnViewCalendar.addEventListener('click', function() {
        btnViewCalendar.className = 'btn btn-primary fw-semibold';
        btnViewGrid.className = 'btn btn-outline-primary fw-semibold';
        containerCalendarView.classList.remove('d-none');
        containerGridView.classList.add('d-none');
        calendar.render();
    });

    btnViewGrid.addEventListener('click', function() {
        btnViewGrid.className = 'btn btn-primary fw-semibold';
        btnViewCalendar.className = 'btn btn-outline-primary fw-semibold';
        containerGridView.classList.remove('d-none');
        containerCalendarView.classList.add('d-none');
    });

    // Interaction des onglets/pills par salle
    var sallePills = document.querySelectorAll('.salle-pill');
    sallePills.forEach(function(pill) {
        pill.addEventListener('click', function() {
            var salleId = this.getAttribute('data-salle-id');
            filterSalleEl.value = salleId;

            sallePills.forEach(function(p) {
                p.classList.remove('active', 'btn-primary', 'text-white');
                p.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary', 'text-white');

            updateCalendarEvents();
        });
    });

    function getEventsUrl() {
        var salleId = filterSalleEl.value;
        var statut = filterStatutEl.value;
        return '/calendrier/events?salle_id=' + salleId + '&statut=' + encodeURIComponent(statut);
    }

    // Initialisation FullCalendar avec paramètres optimisés pour le mode semaine
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // Par défaut en vue Semaine optimisée
        locale: 'fr',
        slotMinTime: '07:30:00',     // Limite les heures inutiles de la nuit
        slotMaxTime: '20:30:00',     // Heure de fin d'activité
        slotDuration: '00:30:00',    // Créneaux de 30 minutes
        allDaySlot: false,           // Masque la rangée "toute la journée"
        nowIndicator: true,          // Ligne rouge indiquant l'heure actuelle
        expandRows: true,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,dayGridMonth,timeGridDay,listWeek'
        },
        buttonText: {
            today:    "Aujourd'hui",
            month:    'Mois',
            week:     'Semaine',
            day:      'Jour',
            list:     'Liste'
        },
        events: getEventsUrl(),
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        },
        eventContent: function(arg) {
            var props = arg.event.extendedProps;
            var container = document.createElement('div');
            container.className = 'fc-custom-event-content p-1';
            
            var salleBadge = '<div class="fw-bold fs-7 text-uppercase" style="font-size:0.75rem;"><i class="bi bi-door-closed me-1"></i>' + props.salle_nom + '</div>';
            var timeRange = '<div class="small opacity-75" style="font-size:0.72rem;">' + props.heure_debut + ' - ' + props.heure_fin + '</div>';
            var title = '<div class="fw-semibold text-truncate mt-1" style="font-size:0.8rem;">' + props.titre_evenement + '</div>';

            container.innerHTML = salleBadge + timeRange + title;
            return { domNodes: [container] };
        },
        eventClick: function(info) {
            showModalDetails(info.event.extendedProps);
        }
    });

    calendar.render();

    function updateCalendarEvents() {
        var newUrl = getEventsUrl();
        calendar.removeAllEventSources();
        calendar.addEventSource(newUrl);

        // Si on est en vue grille, recharger la page avec les filtres
        if (!containerGridView.classList.contains('d-none')) {
            window.location.href = '/calendrier?view=grid&salle_id=' + filterSalleEl.value + '&statut=' + encodeURIComponent(filterStatutEl.value);
        }
    }

    filterStatutEl.addEventListener('change', updateCalendarEvents);
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
