<?php
// src/Views/auth/register.php
$pageTitle = "Inscription - Gestion Salles";
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="icon-box bg-success text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h3 class="fw-bold">Créer un compte</h3>
                        <p class="text-muted small">Inscrivez-vous pour réserver et gérer les salles</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/register">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom" class="form-control" required value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nom@domaine.fr" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Rôle utilisateur</label>
                            <select name="role" class="form-select">
                                <option value="enseignant" <?php echo (($_POST['role'] ?? '') === 'enseignant') ? 'selected' : ''; ?>>Enseignant</option>
                                <option value="association" <?php echo (($_POST['role'] ?? '') === 'association') ? 'selected' : ''; ?>>Association</option>
                                <option value="logistique" <?php echo (($_POST['role'] ?? '') === 'logistique') ? 'selected' : ''; ?>>Logistique</option>
                                <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="bi bi-person-check me-2"></i>S'inscrire
                        </button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        <span class="text-muted small">Déjà un compte ?</span>
                        <a href="/login" class="text-success text-decoration-none fw-semibold ms-1">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
