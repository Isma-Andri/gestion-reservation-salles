<?php
// src/Views/profile/edit.php
$pageTitle = $pageTitle ?? "Modifier mon Profil";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

// Initiales avatar
$initiales = strtoupper(mb_substr($user['prenom'], 0, 1) . mb_substr($user['nom'], 0, 1));
?>

<div class="container pb-5">
    <div class="row g-4 pt-3">

        <!-- Sidebar identité -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="profile-avatar mx-auto mb-3">
                    <?php echo htmlspecialchars($initiales); ?>
                </div>
                <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h5>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                <div class="d-grid gap-2">
                    <a href="/profil" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Retour au profil
                    </a>
                </div>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Votre rôle <strong><?php echo htmlspecialchars($user['role']); ?></strong> ne peut être modifié que par un administrateur.
                </p>
            </div>
        </div>

        <!-- Formulaires -->
        <div class="col-lg-8">
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- === Informations personnelles === -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-fill text-primary me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/profil/modifier" id="form-profile" novalidate>
                        <input type="hidden" name="action" value="update_profile">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom"
                                       value="<?php echo htmlspecialchars($user['prenom']); ?>" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom"
                                       value="<?php echo htmlspecialchars($user['nom']); ?>" required maxlength="100">
                            </div>
                            <div class="col-md-8">
                                <label for="email" class="form-label fw-semibold">Adresse Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required maxlength="150">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                           value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>" maxlength="20"
                                           placeholder="+261 xx xxx xx">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/profil" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="btn-save-profile">
                                <i class="bi bi-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- === Changement de mot de passe === -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Changer le mot de passe</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/profil/modifier" id="form-password" novalidate>
                        <input type="hidden" name="action" value="update_password">

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Mot de passe actuel <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                       placeholder="••••••••" required autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('current_password', this)" title="Afficher/Masquer">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                           placeholder="Min. 6 caractères" required minlength="6" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('new_password', this)" title="Afficher/Masquer">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label fw-semibold">Confirmer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                           placeholder="Répéter le mot de passe" required autocomplete="new-password"
                                           oninput="checkPasswordMatch()">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('confirm_password', this)" title="Afficher/Masquer">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="password-match-msg" class="form-text"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-danger" id="btn-save-password">
                                <i class="bi bi-shield-lock me-1"></i>Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Afficher/Masquer le mot de passe
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Vérification en temps réel des mots de passe
function checkPasswordMatch() {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('confirm_password').value;
    const msg = document.getElementById('password-match-msg');
    if (cp.length === 0) {
        msg.textContent = '';
        return;
    }
    if (np === cp) {
        msg.textContent = '✓ Les mots de passe correspondent';
        msg.className = 'form-text text-success fw-semibold';
    } else {
        msg.textContent = '✗ Les mots de passe ne correspondent pas';
        msg.className = 'form-text text-danger';
    }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
