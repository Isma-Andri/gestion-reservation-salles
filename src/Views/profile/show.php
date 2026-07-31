<?php
// src/Views/profile/show.php
$pageTitle = $pageTitle ?? "Mon Profil";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';

$roleLabels = ['admin' => 'Administrateur', 'enseignant' => 'Enseignant', 'logistique' => 'Logistique', 'association' => 'Association'];
$roleIcons  = ['admin' => 'bi-shield-fill-check', 'enseignant' => 'bi-mortarboard-fill', 'logistique' => 'bi-tools', 'association' => 'bi-people-fill'];
$roleColors = ['admin' => 'bg-dark', 'enseignant' => 'bg-success', 'logistique' => 'bg-warning text-dark', 'association' => 'bg-secondary'];
$roleLabel  = $roleLabels[$user['role']] ?? $user['role'];
$roleIcon   = $roleIcons[$user['role']]  ?? 'bi-person';
$roleClass  = $roleColors[$user['role']] ?? 'bg-secondary';

// Initiales pour l'avatar
$initiales = strtoupper(mb_substr($user['prenom'], 0, 1) . mb_substr($user['nom'], 0, 1));

// Stats réservations
$totalRes  = count($reservations);
$validees  = count(array_filter($reservations, fn($r) => $r['statut'] === 'validee'));
$attente   = count(array_filter($reservations, fn($r) => $r['statut'] === 'en_attente'));
$refusees  = count(array_filter($reservations, fn($r) => $r['statut'] === 'refusee'));
?>

<div class="container pb-5">
    <div class="row g-4 pt-3">

        <!-- === Carte Profil Gauche === -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <!-- Avatar avec initiales -->
                    <div class="profile-avatar mx-auto mb-3">
                        <?php echo htmlspecialchars($initiales); ?>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h4>
                    <p class="text-muted mb-2 small"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge <?php echo $roleClass; ?> px-3 py-2 fs-6 mb-3">
                        <i class="bi <?php echo $roleIcon; ?> me-1"></i><?php echo $roleLabel; ?>
                    </span>

                    <?php if (!empty($user['telephone'])): ?>
                    <p class="text-muted small mb-0"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($user['telephone']); ?></p>
                    <?php endif; ?>

                    <hr class="my-3">

                    <div class="text-start small">
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-calendar-plus me-1"></i>Inscrit le</span>
                            <span class="fw-semibold text-dark"><?php echo date('d/m/Y', strtotime($user['date_creation'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-bookmark me-1"></i>Réservations</span>
                            <span class="fw-semibold text-dark"><?php echo $totalRes; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-check-circle me-1 text-success"></i>Validées</span>
                            <span class="fw-semibold text-success"><?php echo $validees; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-hourglass me-1 text-warning"></i>En attente</span>
                            <span class="fw-semibold text-warning"><?php echo $attente; ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span><i class="bi bi-x-circle me-1 text-danger"></i>Refusées</span>
                            <span class="fw-semibold text-danger"><?php echo $refusees; ?></span>
                        </div>
                    </div>

                    <a href="/profil/modifier" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-pencil-square me-1"></i>Modifier mon profil
                    </a>
                </div>
            </div>
        </div>

        <!-- === Historique Réservations Droite === -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Historique de mes réservations</h5>
                    <a href="/reservations/creer" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i>Nouvelle réservation
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($reservations)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-5 d-block mb-2 opacity-25"></i>
                            <p class="mb-0">Aucune réservation pour le moment.</p>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Événement</th>
                                    <th>Salle</th>
                                    <th>Créneau</th>
                                    <th class="text-center">Statut</th>
                                    <th>Créée le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res):
                                    $badgeCls = $res['statut'] === 'validee' ? 'bg-success' : ($res['statut'] === 'en_attente' ? 'bg-warning text-dark' : 'bg-danger');
                                    $badgeLbl = $res['statut'] === 'validee' ? 'Validée' : ($res['statut'] === 'en_attente' ? 'En attente' : 'Refusée');
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?php echo htmlspecialchars($res['titre_evenement']); ?></td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($res['salle_nom']); ?></td>
                                    <td class="small text-muted">
                                        <?php echo date('d/m/y', strtotime($res['date_debut'])); ?><br>
                                        <span class="text-dark"><?php echo date('H:i', strtotime($res['date_debut'])); ?> – <?php echo date('H:i', strtotime($res['date_fin'])); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeCls; ?> px-2"><?php echo $badgeLbl; ?></span>
                                    </td>
                                    <td class="small text-muted"><?php echo date('d/m/y H:i', strtotime($res['date_creation'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
