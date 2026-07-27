<?php 
// src/Views/reservations/show.php
$pageTitle = $pageTitle ?? "Détails Réservation";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

// Messages flash
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Préparer les variables d'affichage
$statutBadge = 'bg-secondary';
$statutLabel = 'Inconnu';
$statutIcon = 'bi-question-circle';
switch ($reservation['statut']) {
    case 'en_attente':
        $statutBadge = 'bg-warning text-dark';
        $statutLabel = 'En attente de validation';
        $statutIcon = 'bi-hourglass-split';
        break;
    case 'validee':
        $statutBadge = 'bg-success';
        $statutLabel = 'Validée';
        $statutIcon = 'bi-check-circle-fill';
        break;
    case 'refusee':
        $statutBadge = 'bg-danger';
        $statutLabel = 'Refusée / Annulée';
        $statutIcon = 'bi-x-circle-fill';
        break;
}

$isPast = strtotime($reservation['date_fin']) < time();
$isOwner = ($reservation['utilisateur_id'] == $_SESSION['utilisateur_id']);

// Calculer la durée
$tsDebut = strtotime($reservation['date_debut']);
$tsFin = strtotime($reservation['date_fin']);
$dureeMinutes = ($tsFin - $tsDebut) / 60;
$dureeH = floor($dureeMinutes / 60);
$dureeM = $dureeMinutes % 60;
$dureeStr = $dureeH > 0 ? $dureeH . 'h' : '';
$dureeStr .= $dureeM > 0 ? ($dureeH > 0 ? ' ' : '') . $dureeM . 'min' : '';
?>

<div class="container pb-5">
    <!-- Messages flash -->
    <?php if ($flashSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>

    <!-- Navigation retour -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-bookmark-check text-primary me-2"></i>Réservation #<?php echo $reservation['id']; ?>
            </h2>
            <p class="text-muted mb-0">Détails complets de la demande de réservation</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/reservations" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
            <a href="/calendrier" class="btn btn-outline-primary">
                <i class="bi bi-calendar-week me-1"></i>Voir sur le calendrier
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i>Informations
                    </h5>
                    <span class="badge <?php echo $statutBadge; ?> px-3 py-2 fs-6">
                        <i class="bi <?php echo $statutIcon; ?> me-1"></i><?php echo $statutLabel; ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    <!-- Titre de l'événement -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                            <i class="bi bi-tag me-1"></i>Titre / Motif
                        </label>
                        <div class="fs-5 fw-semibold text-dark"><?php echo htmlspecialchars($reservation['titre_evenement']); ?></div>
                    </div>

                    <!-- Salle -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                            <i class="bi bi-door-open me-1"></i>Salle
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5 fw-bold text-primary"><?php echo htmlspecialchars($reservation['salle_nom']); ?></span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-people me-1"></i><?php echo (int)$reservation['salle_capacite']; ?> places
                            </span>
                            <a href="/salles/voir?id=<?php echo $reservation['salle_id']; ?>" class="btn btn-link btn-sm text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Voir la salle
                            </a>
                        </div>
                    </div>

                    <!-- Créneau horaire -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                                <i class="bi bi-calendar-event text-success me-1"></i>Début
                            </label>
                            <div class="fw-semibold text-dark"><?php echo date('d/m/Y', $tsDebut); ?></div>
                            <div class="fs-5 fw-bold text-success"><?php echo date('H:i', $tsDebut); ?></div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                                <i class="bi bi-calendar-event text-danger me-1"></i>Fin
                            </label>
                            <div class="fw-semibold text-dark"><?php echo date('d/m/Y', $tsFin); ?></div>
                            <div class="fs-5 fw-bold text-danger"><?php echo date('H:i', $tsFin); ?></div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">
                                <i class="bi bi-stopwatch me-1"></i>Durée
                            </label>
                            <div class="fs-5 fw-bold text-primary"><?php echo $dureeStr; ?></div>
                        </div>
                    </div>

                    <?php if ($isPast): ?>
                        <div class="alert alert-secondary py-2 mb-0">
                            <i class="bi bi-clock-history me-2"></i>Cet événement est <strong>passé</strong>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Demandeur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Demandeur</h6>
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($reservation['demandeur_nom']); ?></div>
                            <span class="badge bg-secondary text-capitalize"><?php echo htmlspecialchars($reservation['demandeur_role']); ?></span>
                        </div>
                    </div>
                    <?php if (isset($reservation['demandeur_email'])): ?>
                        <div class="mt-3 small text-muted">
                            <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($reservation['demandeur_email']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-clock-history me-2 text-info"></i>Métadonnées</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">N° Réservation</span>
                            <span class="fw-bold">#<?php echo $reservation['id']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Créée le</span>
                            <span class="fw-semibold"><?php echo date('d/m/Y H:i', strtotime($reservation['date_creation'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Statut actuel</span>
                            <span class="badge <?php echo $statutBadge; ?>"><?php echo $statutLabel; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <?php if ($reservation['statut'] === 'en_attente' && !$isPast && $isOwner): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-gear me-2"></i>Actions</h6>
                        <form method="POST" action="/reservations/annuler" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                            <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-2"></i>Annuler cette réservation
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
