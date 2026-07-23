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
        <h2 class="fw-bold mb-1">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</h2>
        <p class="text-muted mb-0">Rôle : <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($_SESSION['role']); ?></span></p>
    </div>

    <h4 class="fw-bold mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Actions rapides</h4>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        
        <!-- Module Calendrier (US06) - NOUVEAU JEUDI -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm dashboard-card">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary text-white me-3">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Disponibilités (Calendrier)</h5>
                            <small class="text-muted">US06 - Jeudi 23/07</small>
                        </div>
                    </div>
                    <p class="text-muted flex-grow-1">Consultez l'emploi du temps et les disponibilités des salles sous forme de calendrier interactif.</p>
                    <a href="/calendrier" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-calendar-event me-1"></i>Consulter le calendrier
                    </a>
                </div>
            </div>
        </div>

        <!-- Module Salles (US04, US05) -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm dashboard-card">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-secondary text-white me-3">
                            <i class="bi bi-door-open"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Gestion des Salles</h5>
                            <small class="text-muted">US04, US05</small>
                        </div>
                    </div>
                    <p class="text-muted flex-grow-1">Consultez la liste des salles, leurs capacités et leurs équipements disponibles.</p>
                    <a href="/salles" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-eye me-1"></i>Consulter les salles
                    </a>
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
                        <a href="/salles/creer" class="btn btn-outline-success w-100 mt-2">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter une salle
                        </a>
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
