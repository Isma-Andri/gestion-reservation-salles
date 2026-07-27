<?php 
// src/Views/reservations/index.php
$pageTitle = $pageTitle ?? "Mes Réservations";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

// Récupérer les messages flash
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="container pb-5">
    <!-- Messages flash -->
    <?php if ($flashSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($flashError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-bookmark-star text-primary me-2"></i>Mes Réservations
            </h2>
            <p class="text-muted mb-0">Consultez et gérez vos demandes de réservation de salles</p>
        </div>
        <a href="/reservations/creer" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Nouvelle Réservation
        </a>
    </div>

    <!-- Cartes de compteurs par statut -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">En attente</div>
                        <div class="fs-4 fw-bold text-dark"><?php echo $counts['en_attente']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Validées</div>
                        <div class="fs-4 fw-bold text-dark"><?php echo $counts['validee']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Refusées</div>
                        <div class="fs-4 fw-bold text-dark"><?php echo $counts['refusee']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres par statut -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 d-flex align-items-center gap-3 flex-wrap">
            <span class="fw-semibold text-muted small"><i class="bi bi-funnel me-1"></i>Filtrer :</span>
            <a href="/reservations" class="btn btn-sm <?php echo empty($filterStatut) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Toutes (<?php echo array_sum($counts); ?>)
            </a>
            <a href="/reservations?statut=en_attente" class="btn btn-sm <?php echo $filterStatut === 'en_attente' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                <i class="bi bi-hourglass-split me-1"></i>En attente (<?php echo $counts['en_attente']; ?>)
            </a>
            <a href="/reservations?statut=validee" class="btn btn-sm <?php echo $filterStatut === 'validee' ? 'btn-success' : 'btn-outline-success'; ?>">
                <i class="bi bi-check-circle me-1"></i>Validées (<?php echo $counts['validee']; ?>)
            </a>
            <a href="/reservations?statut=refusee" class="btn btn-sm <?php echo $filterStatut === 'refusee' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                <i class="bi bi-x-circle me-1"></i>Refusées (<?php echo $counts['refusee']; ?>)
            </a>
        </div>
    </div>

    <!-- Liste des réservations -->
    <?php if (empty($reservations)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Aucune réservation trouvée</h5>
                <p class="text-muted">Vous n'avez pas encore de réservation<?php echo !empty($filterStatut) ? ' avec ce statut' : ''; ?>.</p>
                <a href="/reservations/creer" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-2"></i>Créer ma première réservation
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Salle</th>
                                <th>Événement</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th class="text-center">Statut</th>
                                <th>Créée le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $res): ?>
                                <?php
                                    $statutBadge = 'bg-secondary';
                                    $statutLabel = 'Inconnu';
                                    $statutIcon = 'bi-question-circle';
                                    switch ($res['statut']) {
                                        case 'en_attente':
                                            $statutBadge = 'bg-warning text-dark';
                                            $statutLabel = 'En attente';
                                            $statutIcon = 'bi-hourglass-split';
                                            break;
                                        case 'validee':
                                            $statutBadge = 'bg-success';
                                            $statutLabel = 'Validée';
                                            $statutIcon = 'bi-check-circle-fill';
                                            break;
                                        case 'refusee':
                                            $statutBadge = 'bg-danger';
                                            $statutLabel = 'Refusée';
                                            $statutIcon = 'bi-x-circle-fill';
                                            break;
                                    }
                                    
                                    $isPast = strtotime($res['date_fin']) < time();
                                ?>
                                <tr class="<?php echo $isPast ? 'opacity-50' : ''; ?>">
                                    <td class="ps-4 fw-bold text-muted"><?php echo $res['id']; ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($res['salle_nom']); ?></div>
                                        <small class="text-muted"><i class="bi bi-people me-1"></i><?php echo (int)$res['salle_capacite']; ?> places</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($res['titre_evenement']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo date('d/m/Y', strtotime($res['date_debut'])); ?></div>
                                        <div class="small text-primary"><?php echo date('H:i', strtotime($res['date_debut'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo date('d/m/Y', strtotime($res['date_fin'])); ?></div>
                                        <div class="small text-danger"><?php echo date('H:i', strtotime($res['date_fin'])); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $statutBadge; ?> px-3 py-2">
                                            <i class="bi <?php echo $statutIcon; ?> me-1"></i><?php echo $statutLabel; ?>
                                        </span>
                                        <?php if ($isPast): ?>
                                            <div class="small text-muted mt-1">Passé</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($res['date_creation'])); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="/reservations/voir?id=<?php echo $res['id']; ?>" class="btn btn-outline-primary btn-sm" title="Détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($res['statut'] === 'en_attente' && !$isPast): ?>
                                                <form method="POST" action="/reservations/annuler" class="d-inline" 
                                                      onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                                    <input type="hidden" name="id" value="<?php echo $res['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Annuler">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
