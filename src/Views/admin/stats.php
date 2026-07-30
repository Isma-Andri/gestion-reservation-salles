<?php 
// src/Views/admin/stats.php
$pageTitle = $pageTitle ?? "Statistiques Admin";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Préparer les données pour le graphique mensuel (Chart.js)
$chartLabels  = json_encode(array_column($monthlyData, 'mois_label'));
$chartTotal   = json_encode(array_map('intval', array_column($monthlyData, 'total')));
$chartValid   = json_encode(array_map('intval', array_column($monthlyData, 'validees')));
$chartRefus   = json_encode(array_map('intval', array_column($monthlyData, 'refusees')));

$res = $globalStats['reservations'];
$totalRes = max(1, (int)$res['total']); // éviter division par 0
?>

<div class="container-fluid px-lg-5 pb-5">
    <?php if ($flashSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($flashError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-bar-chart-line text-primary me-2"></i>Statistiques & Tableau de Bord
            </h2>
            <p class="text-muted mb-0">Vue d'ensemble de l'utilisation du système — Réservé aux administrateurs</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/users" class="btn btn-outline-secondary">
                <i class="bi bi-people me-1"></i>Gérer les Utilisateurs
            </a>
            <a href="/admin/export-csv" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exporter CSV
            </a>
        </div>
    </div>

    <!-- === KPI Cards === -->
    <div class="row g-3 mb-4">
        <!-- Total Réservations -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Réservations</div>
                        <div class="fs-3 fw-bold text-dark"><?php echo (int)$res['total']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Validées -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Validées</div>
                        <div class="fs-3 fw-bold text-success"><?php echo (int)$res['validees']; ?></div>
                        <div class="small text-muted"><?php echo round((int)$res['validees'] * 100 / $totalRes); ?>% du total</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- En attente -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">En Attente</div>
                        <div class="fs-3 fw-bold text-warning"><?php echo (int)$res['en_attente']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Utilisateurs -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Utilisateurs</div>
                        <div class="fs-3 fw-bold text-dark"><?php echo (int)$globalStats['total_users']; ?></div>
                        <div class="small text-muted"><?php echo (int)$globalStats['salles']['actives']; ?> salles actives</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- === Graphique Mensuel === -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Évolution des réservations (6 derniers mois)</h5>
                </div>
                <div class="card-body p-3">
                    <canvas id="chartMonthly" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- === Utilisateurs par Rôle === -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Utilisateurs par rôle</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center gap-3 p-4">
                    <?php
                    $roleColors = ['admin' => '#0d6efd', 'enseignant' => '#198754', 'logistique' => '#fd7e14', 'association' => '#6f42c1'];
                    $roleIcons  = ['admin' => 'bi-shield-fill-check', 'enseignant' => 'bi-mortarboard-fill', 'logistique' => 'bi-tools', 'association' => 'bi-people-fill'];
                    $roleLabels = ['admin' => 'Administrateur', 'enseignant' => 'Enseignant', 'logistique' => 'Logistique', 'association' => 'Association'];
                    foreach ($roleColors as $roleKey => $color):
                        $count = $globalStats['users_by_role'][$roleKey] ?? 0;
                        $pct   = $globalStats['total_users'] > 0 ? round($count * 100 / $globalStats['total_users']) : 0;
                    ?>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold">
                                <i class="bi <?php echo $roleIcons[$roleKey]; ?> me-1" style="color:<?php echo $color; ?>"></i>
                                <?php echo $roleLabels[$roleKey]; ?>
                            </span>
                            <span class="small fw-bold"><?php echo $count; ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: <?php echo $pct; ?>%; background-color: <?php echo $color; ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <!-- === Top Salles === -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Salles les plus réservées</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Rang</th>
                                    <th>Salle</th>
                                    <th class="text-center">Réservations</th>
                                    <th class="text-center">Capacité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topSalles as $i => $salle): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php if ($i === 0): ?>
                                                <span class="text-warning fw-bold"><i class="bi bi-trophy-fill"></i> #1</span>
                                            <?php elseif ($i === 1): ?>
                                                <span class="text-secondary fw-bold">#2</span>
                                            <?php else: ?>
                                                <span class="text-muted">#<?php echo $i + 1; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($salle['nom']); ?></td>
                                        <td class="text-center"><span class="badge bg-primary"><?php echo (int)$salle['nb_reservations']; ?></span></td>
                                        <td class="text-center text-muted small"><?php echo (int)$salle['capacite']; ?> pl.</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Taux d'occupation semaine === -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-danger me-2"></i>Occupation cette semaine (minutes validées)</h5>
                </div>
                <div class="card-body p-4">
                    <?php 
                    $maxMins = max(array_column($occupationWeek, 'minutes_occupees') ?: [1]);
                    $maxMins = max($maxMins, 1);
                    foreach ($occupationWeek as $row):
                        $mins = (int)$row['minutes_occupees'];
                        $pct  = round($mins * 100 / $maxMins);
                        $hours = floor($mins / 60);
                        $minRem = $mins % 60;
                        $durStr = $hours > 0 ? $hours . 'h' . ($minRem > 0 ? $minRem . 'min' : '') : $minRem . 'min';
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-dark"><?php echo htmlspecialchars($row['nom']); ?></span>
                            <span class="small text-muted"><?php echo $mins > 0 ? $durStr : 'Libre'; ?></span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo $pct; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- === Activité récente === -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-activity text-success me-2"></i>Activité récente (10 dernières)</h5>
            <a href="/admin/export-csv" class="btn btn-sm btn-outline-success">
                <i class="bi bi-download me-1"></i>Exporter tout
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Demandeur</th>
                            <th>Salle</th>
                            <th>Événement</th>
                            <th>Créneau</th>
                            <th class="text-center">Statut</th>
                            <th>Créée le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivity as $res):
                            $badge = $res['statut'] === 'validee' ? 'bg-success' : ($res['statut'] === 'en_attente' ? 'bg-warning text-dark' : 'bg-danger');
                            $label = $res['statut'] === 'validee' ? 'Validée' : ($res['statut'] === 'en_attente' ? 'En attente' : 'Refusée');
                        ?>
                        <tr>
                            <td class="ps-4 text-muted"><?php echo $res['id']; ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($res['demandeur_nom']); ?></div>
                                <span class="badge bg-secondary bg-opacity-50 text-dark small"><?php echo htmlspecialchars($res['demandeur_role']); ?></span>
                            </td>
                            <td class="small"><?php echo htmlspecialchars($res['salle_nom']); ?></td>
                            <td class="small fw-semibold"><?php echo htmlspecialchars($res['titre_evenement']); ?></td>
                            <td class="small text-muted">
                                <?php echo date('d/m H:i', strtotime($res['date_debut'])); ?>
                                → <?php echo date('H:i', strtotime($res['date_fin'])); ?>
                            </td>
                            <td class="text-center"><span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                            <td class="small text-muted"><?php echo date('d/m/y H:i', strtotime($res['date_creation'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('chartMonthly');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $chartLabels; ?>,
                datasets: [
                    {
                        label: 'Validées',
                        data: <?php echo $chartValid; ?>,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Refusées',
                        data: <?php echo $chartRefus; ?>,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { stacked: false, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
