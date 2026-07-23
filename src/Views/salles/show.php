<?php
// src/Views/salles/show.php
$pageTitle = "Détails de la salle " . htmlspecialchars($salle['nom']);
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container pb-5">
    <?php require __DIR__ . '/../layout/alerts.php'; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-door-open text-primary me-2"></i><?php echo htmlspecialchars($salle['nom']); ?></h3>
                    <?php if ($salle['est_active']): ?>
                        <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i>Disponible</span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2 fs-6"><i class="bi bi-slash-circle me-1"></i>Indisponible</span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small text-uppercase fw-bold"><i class="bi bi-people me-1"></i>Capacité d'accueil</div>
                                <div class="fs-3 fw-bold text-primary"><?php echo (int)$salle['capacite']; ?> places</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small text-uppercase fw-bold"><i class="bi bi-shield-check me-1"></i>Statut de réservation</div>
                                <div class="fs-5 fw-semibold mt-1 <?php echo $salle['est_active'] ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $salle['est_active'] ? 'Ouverte aux réservations' : 'Fermée aux réservations'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Équipements (US05) -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-tools text-primary me-2"></i>Équipements & Caractéristiques</h5>
                        <?php if (!empty($equipementsList)): ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($equipementsList as $eq): ?>
                                    <div class="eq-chip">
                                        <i class="bi bi-check2-circle text-primary"></i>
                                        <span><?php echo htmlspecialchars($eq); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted fst-italic">Aucun équipement enregistré pour cette salle.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Boutons d'actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="/salles" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
                        </a>

                        <div class="d-flex gap-2">
                            <a href="/calendrier?salle_id=<?php echo $salle['id']; ?>" class="btn btn-primary">
                                <i class="bi bi-calendar-week me-1"></i>Voir le planning
                            </a>
                            <?php if ($canManage): ?>
                                <a href="/salles/editer?id=<?php echo $salle['id']; ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil me-1"></i>Modifier la salle
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
