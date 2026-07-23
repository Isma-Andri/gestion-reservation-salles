<?php
// src/Views/auth/login.php
$pageTitle = "Connexion - Gestion Salles";
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="icon-box bg-primary text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h3 class="fw-bold">Connexion</h3>
                        <p class="text-muted small">Espace de gestion des réservations de salles</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nom@domaine.fr" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <span class="text-muted small">Pas encore de compte ?</span>
                        <a href="/register" class="text-primary text-decoration-none fw-semibold ms-1">S'inscrire</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
