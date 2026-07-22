<!-- src/Views/dashboard/index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Gestion des Réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .dashboard-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border-radius: 12px; }
        .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/dashboard"><i class="bi bi-calendar-event me-2"></i>Gestion Salles</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active fw-bold" href="/dashboard">Tableau de bord</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/salles">Salles</a></li>
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
        <!-- Message de bienvenue -->
        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border-start border-4 border-primary">
            <h2 class="fw-bold mb-1">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</h2>
            <p class="text-muted mb-0">Rôle : <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($_SESSION['role']); ?></span></p>
        </div>

        <h4 class="fw-bold mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Actions rapides</h4>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            
            <!-- Module Salles (Accessible à tous) -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary text-white me-3">
                                <i class="bi bi-door-open"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Gestion des Salles</h5>
                                <small class="text-muted">US04, US05</small>
                            </div>
                        </div>
                        <p class="text-muted flex-grow-1">Consultez la liste des salles, leurs capacités et leurs équipements disponibles.</p>
                        <a href="/salles" class="btn btn-outline-primary w-100 mt-2"><i class="bi bi-eye me-1"></i>Consulter les salles</a>
                    </div>
                </div>
            </div>

            <?php if (in_array($_SESSION['role'], ['admin', 'logistique'])): ?>
                <!-- Ajouter une salle -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-success text-white me-3">
                                    <i class="bi bi-plus-circle"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Nouvelle Salle</h5>
                                    <small class="text-muted">Admin / Logistique</small>
                                </div>
                            </div>
                            <p class="text-muted flex-grow-1">Ajoutez une nouvelle salle dans le système avec sa capacité et ses équipements.</p>
                            <a href="/salles/creer" class="btn btn-outline-success w-100 mt-2"><i class="bi bi-plus-lg me-1"></i>Ajouter une salle</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-dark text-white me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Utilisateurs</h5>
                                    <small class="text-muted">Administration</small>
                                </div>
                            </div>
                            <p class="text-muted flex-grow-1">Gérez les comptes d'utilisateurs et leurs attributions de rôles.</p>
                            <button class="btn btn-outline-secondary w-100 mt-2" disabled><i class="bi bi-gear me-1"></i>Gérer les utilisateurs</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'logistique'): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-warning text-dark me-3">
                                    <i class="bi bi-check2-square"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Validations</h5>
                                    <small class="text-muted">Logistique</small>
                                </div>
                            </div>
                            <p class="text-muted flex-grow-1">Validez ou refusez les demandes de réservation des associations.</p>
                            <button class="btn btn-outline-warning text-dark w-100 mt-2" disabled><i class="bi bi-clock-history me-1"></i>Voir les validations</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'enseignant'): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-info text-white me-3">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Réservation rapide</h5>
                                    <small class="text-muted">Enseignant</small>
                                </div>
                            </div>
                            <p class="text-muted flex-grow-1">Effectuez une réservation avec validation automatique immédiate.</p>
                            <button class="btn btn-outline-info w-100 mt-2" disabled><i class="bi bi-calendar-plus me-1"></i>Réserver (Sprint 2)</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'association'): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-purple bg-secondary text-white me-3">
                                    <i class="bi bi-send"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Demande de réservation</h5>
                                    <small class="text-muted">Association</small>
                                </div>
                            </div>
                            <p class="text-muted flex-grow-1">Soumettez une demande de réservation de salle au service logistique.</p>
                            <button class="btn btn-outline-secondary w-100 mt-2" disabled><i class="bi bi-send me-1"></i>Demander (Sprint 2)</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
