<!-- src/Views/layout/navbar.php -->
<?php
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/dashboard">
            <i class="bi bi-calendar-event me-2"></i>Gestion Salles
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentUri === '/dashboard' ? 'active fw-bold' : 'text-white'; ?>" href="/dashboard">
                            <i class="bi bi-speedometer2 me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentUri, '/salles') === 0 ? 'active fw-bold' : 'text-white'; ?>" href="/salles">
                            <i class="bi bi-door-open me-1"></i>Salles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentUri, '/calendrier') === 0 ? 'active fw-bold' : 'text-white'; ?>" href="/calendrier">
                            <i class="bi bi-calendar-week me-1"></i>Calendrier
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center text-white me-3">
                    <i class="bi bi-person-circle me-2 fs-5"></i>
                    <span>
                        <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> 
                        <span class="badge bg-light text-primary text-capitalize ms-1"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                    </span>
                </div>
                <a href="/logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/login"><i class="bi bi-box-arrow-in-right me-1"></i>Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/register"><i class="bi bi-person-plus me-1"></i>Inscription</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
