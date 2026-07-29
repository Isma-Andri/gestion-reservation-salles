<?php 
// src/Views/dashboard/index.php
$pageTitle = "Tableau de bord - Gestion des Réservations";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container pb-5">
    <?php require __DIR__ . '/../layout/alerts.php'; ?>

    <!-- Message de bienvenue -->
    <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border-start border-4 border-primary">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-1">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</h2>
                <p class="text-muted mb-0">
                    Rôle : <span class="badge bg-primary text-capitalize me-2"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                    <span class="text-secondary small"><i class="bi bi-mortarboard me-1"></i>MIT - MISA</span>
                </p>
            </div>
            <a href="/reservations/creer" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i>Nouvelle Réservation
            </a>
        </div>
    </div>

    <h4 class="fw-bold mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Actions rapides</h4>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        
        <!-- Module Calendrier -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm dashboard-card">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary text-white me-3">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Planning & Calendrier</h5>
                            <small class="text-muted">Consultation des disponibilités</small>
                        </div>
                    </div>
                    <p class="text-muted flex-grow-1">Consultez l'emploi du temps et les disponibilités des salles sous forme de calendrier interactif ou de grille.</p>
                    <a href="/calendrier" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-calendar-event me-1"></i>Consulter le calendrier
                    </a>
                </div>
            </div>
        </div>

        <!-- Mes Réservations -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm dashboard-card">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info text-white me-3">
                            <i class="bi bi-bookmark-star"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Mes Réservations</h5>
                            <small class="text-muted">Suivi de mes demandes</small>
                        </div>
                    </div>
                    <p class="text-muted flex-grow-1">Consultez l'état de vos réservations (validées, en attente ou refusées) et gérez vos annulations.</p>
                    <a href="/reservations" class="btn btn-outline-info w-100 mt-2">
                        <i class="bi bi-list-check me-1"></i>Voir mes réservations
                    </a>
                </div>
            </div>
        </div>

        <!-- Module Salles -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm dashboard-card">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-secondary text-white me-3">
                            <i class="bi bi-door-open"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Liste des Salles</h5>
                            <small class="text-muted">Équipements & capacités</small>
                        </div>
                    </div>
                    <p class="text-muted flex-grow-1">Consultez la liste des salles de l'établissement, leurs capacités et leurs équipements disponibles.</p>
                    <a href="/salles" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-eye me-1"></i>Consulter les salles
                    </a>
                </div>
            </div>
        </div>

        <!-- Module Validations Logistique / Admin -->
        <?php if (in_array($_SESSION['role'], ['admin', 'logistique'])): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-warning text-dark me-3">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Validations</h5>
                                <small class="text-muted">Service Logistique / Admin</small>
                            </div>
                        </div>
                        <p class="text-muted flex-grow-1">Examinez, validez ou refusez les demandes de réservation soumises par les associations.</p>
                        <a href="/reservations/validations" class="btn btn-warning text-dark w-100 mt-2 fw-semibold">
                            <i class="bi bi-clock-history me-1"></i>Voir les validations
                        </a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success text-white me-3">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Nouvelle Salle</h5>
                                <small class="text-muted">Gestion du parc</small>
                            </div>
                        </div>
                        <p class="text-muted flex-grow-1">Ajoutez une nouvelle salle dans l'application avec sa capacité et ses équipements.</p>
                        <a href="/salles/creer" class="btn btn-outline-success w-100 mt-2">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter une salle
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action rapide Enseignant -->
        <?php if ($_SESSION['role'] === 'enseignant'): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success text-white me-3">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Réservation rapide</h5>
                                <small class="text-muted">Validation instantanée</small>
                            </div>
                        </div>
                        <p class="text-muted flex-grow-1">Réservez une salle de cours avec validation automatique immédiate pour vos enseignements.</p>
                        <a href="/reservations/creer" class="btn btn-success w-100 mt-2">
                            <i class="bi bi-calendar-plus me-1"></i>Réserver une salle
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action rapide Association -->
        <?php if ($_SESSION['role'] === 'association'): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-purple text-white me-3" style="background-color: #6f42c1;">
                                <i class="bi bi-send"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Demande de réservation</h5>
                                <small class="text-muted">Soumission pour validation</small>
                            </div>
                        </div>
                        <p class="text-muted flex-grow-1">Soumettez une demande de réservation d'événement au service logistique pour validation.</p>
                        <a href="/reservations/creer" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-send me-1"></i>Demander une salle
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
