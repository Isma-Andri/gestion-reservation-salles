<?php 
// src/Views/admin/users.php
$pageTitle = $pageTitle ?? "Gestion des Utilisateurs";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$csrfToken = AdminController::getCSRFToken();
$roleColors = ['admin' => 'bg-dark', 'enseignant' => 'bg-success', 'logistique' => 'bg-warning text-dark', 'association' => 'bg-secondary'];
$roleLabels = ['admin' => 'Admin', 'enseignant' => 'Enseignant', 'logistique' => 'Logistique', 'association' => 'Association'];
?>

<div class="container pb-5">
    <?php if ($flashSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($flashError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>Gestion des Utilisateurs
            </h2>
            <p class="text-muted mb-0">Gérez les comptes, rôles et droits d'accès des utilisateurs</p>
        </div>
        <a href="/admin/stats" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart-line me-1"></i>Statistiques
        </a>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Email</th>
                            <th class="text-center">Rôle actuel</th>
                            <th class="text-center">Réservations</th>
                            <th>Inscrit le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user):
                            $isMe = ($user['id'] == $_SESSION['utilisateur_id']);
                            $roleClass = $roleColors[$user['role']] ?? 'bg-secondary';
                            $roleLabel = $roleLabels[$user['role']] ?? $user['role'];
                        ?>
                        <tr class="<?php echo $isMe ? 'table-primary' : ''; ?>">
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></div>
                                <?php if ($isMe): ?><span class="badge bg-primary">Vous</span><?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo $roleClass; ?> px-2 py-1"><?php echo $roleLabel; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?php echo (int)$user['nb_reservations']; ?></span>
                            </td>
                            <td class="small text-muted"><?php echo date('d/m/Y', strtotime($user['date_creation'])); ?></td>
                            <td class="text-center">
                                <?php if (!$isMe): ?>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <!-- Formulaire changement de rôle -->
                                    <form method="POST" action="/admin/users" class="d-flex gap-1 align-items-center">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="new_role" class="form-select form-select-sm" style="width:auto;">
                                            <?php foreach ($roleLabels as $r => $l): ?>
                                                <option value="<?php echo $r; ?>" <?php echo $user['role'] === $r ? 'selected' : ''; ?>><?php echo $l; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Changer le rôle">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    <!-- Supprimer -->
                                    <form method="POST" action="/admin/users" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
