<?php
// src/Views/salles/index.php
$pageTitle = "Gestion des Salles - Réservation Salles";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container pb-5">
    <?php require __DIR__ . '/../layout/alerts.php'; ?>

    <!-- En-tête et bouton d'action -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-door-open text-primary me-2"></i>Liste des Salles</h2>
            <p class="text-muted mb-0">Consultez les caractéristiques, équipements et disponibilités des salles</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/calendrier" class="btn btn-outline-primary"><i class="bi bi-calendar-week me-1"></i>Calendrier des disponibilités</a>
            <?php if ($canManage): ?>
                <a href="/salles/creer" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Ajouter une salle</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/salles" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="q" class="form-label fw-semibold mb-1"><i class="bi bi-search me-1"></i>Recherche par nom ou équipement</label>
                    <input type="text" class="form-control" id="q" name="q" placeholder="Ex: Vidéoprojecteur, Amphi, Bâtiment A..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label for="capacite" class="form-label fw-semibold mb-1"><i class="bi bi-people me-1"></i>Capacité minimale</label>
                    <input type="number" class="form-control" id="capacite" name="capacite" min="0" placeholder="Ex: 30" value="<?php echo $minCapacite > 0 ? $minCapacite : ''; ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Filtrer</button>
                    <a href="/salles" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Grille des salles -->
    <?php if (empty($salles)): ?>
        <div class="card border-0 shadow-sm py-5 text-center">
            <div class="card-body">
                <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Aucune salle ne correspond à vos critères.</h5>
            </div>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($salles as $salle): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm card-salle">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold text-dark mb-0">
                                    <a href="/salles/voir?id=<?php echo $salle['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($salle['nom']); ?>
                                    </a>
                                </h5>
                                <?php if ($salle['est_active']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disponible</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-slash-circle me-1"></i>Indisponible</span>
                                <?php endif; ?>
                            </div>

                            <div class="text-muted mb-3 fs-6">
                                <i class="bi bi-people-fill me-1 text-primary"></i>
                                Capacité : <strong><?php echo (int)$salle['capacite']; ?> places</strong>
                            </div>

                            <div class="mb-4 flex-grow-1">
                                <div class="small fw-semibold text-muted mb-1"><i class="bi bi-tools me-1"></i>Équipements :</div>
                                <?php if (!empty($salle['equipements'])): ?>
                                    <?php 
                                        $eqs = array_map('trim', explode(',', $salle['equipements']));
                                        $displayEqs = array_slice($eqs, 0, 4);
                                    ?>
                                    <?php foreach ($displayEqs as $eq): ?>
                                        <span class="badge-equipment"><i class="bi bi-gear me-1"></i><?php echo htmlspecialchars($eq); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($eqs) > 4): ?>
                                        <span class="badge-equipment bg-light text-primary">+<?php echo count($eqs) - 4; ?> autres</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Aucun équipement spécifié</span>
                                <?php endif; ?>
                            </div>

                            <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-1">
                                    <a href="/salles/voir?id=<?php echo $salle['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Détails
                                    </a>
                                    <a href="/calendrier?salle_id=<?php echo $salle['id']; ?>" class="btn btn-outline-info btn-sm" title="Voir planning de cette salle">
                                        <i class="bi bi-calendar-week"></i>
                                    </a>
                                </div>

                                <?php if ($canManage): ?>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/salles/editer?id=<?php echo $salle['id']; ?>" class="btn btn-outline-secondary" title="Éditer">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/salles/toggle-statut?id=<?php echo $salle['id']; ?>" class="btn btn-outline-<?php echo $salle['est_active'] ? 'warning' : 'success'; ?>" title="<?php echo $salle['est_active'] ? 'Désactiver' : 'Activer'; ?>">
                                            <i class="bi bi-power"></i>
                                        </a>
                                        <a href="/salles/supprimer?id=<?php echo $salle['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?');" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
