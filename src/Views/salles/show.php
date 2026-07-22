<!-- src/Views/salles/show.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la salle <?php echo htmlspecialchars($salle['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .eq-chip { background-color: #eef2ff; color: #3730a3; font-weight: 500; font-size: 0.95rem; padding: 8px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px; margin: 4px; border: 1px solid #c7d2fe; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/dashboard"><i class="bi bi-calendar-event me-2"></i>Gestion Salles</a>
            <div class="d-flex align-items-center text-white">
                <a href="/salles" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour aux salles</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
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

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
