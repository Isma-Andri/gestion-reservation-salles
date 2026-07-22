<!-- src/Views/salles/index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles - Réservation Salles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: bold; }
        .card-salle { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-salle:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .badge-equipment { background-color: #e9ecef; color: #495057; font-weight: 500; font-size: 0.8rem; margin-right: 4px; margin-bottom: 4px; display: inline-block; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>

    <!-- Barre de Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="/dashboard"><i class="bi bi-calendar-event me-2"></i>Gestion Salles</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-navbar nav me-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="/dashboard">Tableau de bord</a></li>
                    <li class="nav-item"><a class="nav-link text-white fw-bold active" href="/salles">Salles</a></li>
                </ul>
                <div class="d-flex align-items-center text-white me-3">
                    <i class="bi bi-person-circle me-2"></i>
                    <span><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                </div>
                <a href="/logout" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <!-- Messages d'alerte -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- En-tête et bouton d'action -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-door-open text-primary me-2"></i>Liste des Salles</h2>
                <p class="text-muted mb-0">Consultez les caractéristiques, équipements et disponibilités des salles</p>
            </div>
            <?php if ($canManage): ?>
                <a href="/salles/creer" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Ajouter une salle</a>
            <?php endif; ?>
        </div>

        <!-- Filtres de recherche -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="/salles" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="q" class="form-label fw-medium"><i class="bi bi-search me-1"></i>Recherche par nom ou équipement</label>
                        <input type="text" class="form-control" id="q" name="q" placeholder="Ex: Vidéoprojecteur, Amphi, Bâtiment A..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="capacite" class="form-label fw-medium"><i class="bi bi-people me-1"></i>Capacité minimale</label>
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
                                    <a href="/salles/voir?id=<?php echo $salle['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Détails
                                    </a>

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

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
