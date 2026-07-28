<?php 
// src/Views/reservations/validations.php
$pageTitle = $pageTitle ?? "Validation des Réservations";
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
                <i class="bi bi-shield-check text-primary me-2"></i>Validation des Réservations
            </h2>
            <p class="text-muted mb-0">Gérez les demandes de réservation en attente (Service Logistique)</p>
        </div>
    </div>

    <!-- Liste des réservations en attente -->
    <?php if (empty($pendingReservations)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-check2-all text-success" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Aucune demande en attente</h5>
                <p class="text-muted">Toutes les réservations ont été traitées.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Demandeur</th>
                                <th>Salle</th>
                                <th>Événement</th>
                                <th>Créneau</th>
                                <th>Créée le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingReservations as $res): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($res['demandeur_nom']); ?></div>
                                        <span class="badge bg-secondary text-capitalize"><?php echo htmlspecialchars($res['demandeur_role']); ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($res['salle_nom']); ?></div>
                                        <small class="text-muted"><?php echo (int)$res['salle_capacite']; ?> places</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($res['titre_evenement']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo date('d/m/Y', strtotime($res['date_debut'])); ?></div>
                                        <div class="small">
                                            <span class="text-success"><?php echo date('H:i', strtotime($res['date_debut'])); ?></span> - 
                                            <span class="text-danger"><?php echo date('H:i', strtotime($res['date_fin'])); ?></span>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($res['date_creation'])); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <form method="POST" action="/reservations/validate" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $res['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm" title="Valider">
                                                    <i class="bi bi-check-lg me-1"></i>Valider
                                                </button>
                                            </form>
                                            <form method="POST" action="/reservations/reject" class="d-inline" onsubmit="return confirm('Refuser cette réservation ?');">
                                                <input type="hidden" name="id" value="<?php echo $res['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Refuser">
                                                    <i class="bi bi-x-lg me-1"></i>Refuser
                                                </button>
                                            </form>
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
