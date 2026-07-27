<?php 
// src/Views/reservations/create.php
$pageTitle = $pageTitle ?? "Nouvelle Réservation";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container pb-5">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-plus text-primary me-2"></i>Nouvelle Réservation
            </h2>
            <p class="text-muted mb-0">Réservez une salle pour votre événement – les conflits sont vérifiés automatiquement (US07, US08)</p>
        </div>
        <a href="/reservations" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Mes Réservations
        </a>
    </div>

    <!-- Erreurs de validation -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger shadow-sm border-0" role="alert">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur(s) détectée(s)</h6>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Conflits détectés (US08) -->
    <?php if (!empty($conflicts)): ?>
        <div class="alert alert-warning shadow-sm border-0" role="alert">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-diamond-fill me-2"></i>Conflits de créneau détectés</h6>
            <p class="mb-2">Les réservations suivantes occupent déjà ce créneau :</p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0">
                    <thead class="table-warning">
                        <tr>
                            <th>Événement</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th>Demandeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conflicts as $c): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($c['titre_evenement']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($c['date_debut'])); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($c['date_fin'])); ?></td>
                                <td>
                                    <?php 
                                    $bClass = $c['statut'] === 'validee' ? 'bg-success' : 'bg-warning text-dark';
                                    $label = $c['statut'] === 'validee' ? 'Validée' : 'En attente';
                                    ?>
                                    <span class="badge <?php echo $bClass; ?>"><?php echo $label; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($c['demandeur_nom']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulaire -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Informations de la réservation
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/reservations/creer" id="formReservation">
                        <!-- Sélection de la salle -->
                        <div class="mb-4">
                            <label for="salle_id" class="form-label fw-semibold">
                                <i class="bi bi-door-open me-1 text-primary"></i>Salle <span class="text-danger">*</span>
                            </label>
                            <select id="salle_id" name="salle_id" class="form-select form-select-lg" required>
                                <option value="">-- Choisir une salle --</option>
                                <?php foreach ($salles as $salle): ?>
                                    <option value="<?php echo $salle['id']; ?>" 
                                            data-capacite="<?php echo (int)$salle['capacite']; ?>"
                                            data-equipements="<?php echo htmlspecialchars($salle['equipements'] ?? ''); ?>"
                                            <?php echo ($formData['salle_id'] == $salle['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($salle['nom']); ?> (<?php echo (int)$salle['capacite']; ?> places)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <!-- Aperçu rapide de la salle sélectionnée -->
                            <div id="sallePreview" class="mt-2 d-none">
                                <div class="alert alert-info py-2 px-3 mb-0 small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong id="previewNom"></strong> — 
                                    <span id="previewCapacite"></span> places — 
                                    Équipements : <span id="previewEquipements" class="fst-italic"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Titre / Motif -->
                        <div class="mb-4">
                            <label for="titre_evenement" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1 text-primary"></i>Titre / Motif de l'événement <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="titre_evenement" name="titre_evenement" 
                                   class="form-control form-control-lg" 
                                   placeholder="Ex: Cours de Mathématiques, Réunion du BDE..."
                                   maxlength="150" required
                                   value="<?php echo htmlspecialchars($formData['titre_evenement']); ?>">
                            <div class="form-text">Maximum 150 caractères.</div>
                        </div>

                        <!-- Dates et Heures -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-success"></i>Date de début <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_debut" name="date_debut" 
                                       class="form-control" required
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($formData['date_debut']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="heure_debut" class="form-label fw-semibold">
                                    <i class="bi bi-clock me-1 text-success"></i>Heure de début <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="heure_debut" name="heure_debut" 
                                       class="form-control" required
                                       min="07:00" max="20:00"
                                       value="<?php echo htmlspecialchars($formData['heure_debut']); ?>">
                                <div class="form-text">Entre 07:00 et 20:00</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="date_fin" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-danger"></i>Date de fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_fin" name="date_fin" 
                                       class="form-control" required
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($formData['date_fin']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="heure_fin" class="form-label fw-semibold">
                                    <i class="bi bi-clock-history me-1 text-danger"></i>Heure de fin <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="heure_fin" name="heure_fin" 
                                       class="form-control" required
                                       min="07:00" max="21:00"
                                       value="<?php echo htmlspecialchars($formData['heure_fin']); ?>">
                                <div class="form-text">Entre 07:00 et 21:00</div>
                            </div>
                        </div>

                        <!-- Zone d'alerte de conflit en temps réel (US08) -->
                        <div id="conflictAlert" class="d-none mb-4">
                            <div class="alert alert-danger py-2 mb-0">
                                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                <strong>Conflit détecté !</strong> 
                                <span id="conflictDetails"></span>
                            </div>
                        </div>

                        <!-- Zone de confirmation pas de conflit -->
                        <div id="noConflictAlert" class="d-none mb-4">
                            <div class="alert alert-success py-2 mb-0">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Créneau disponible !</strong> Aucun conflit détecté pour cette salle et ce créneau.
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSubmit">
                                <i class="bi bi-check-lg me-2"></i>Soumettre la réservation
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="btnCheckConflict">
                                <i class="bi bi-shield-check me-2"></i>Vérifier les conflits
                            </button>
                            <a href="/reservations" class="btn btn-outline-danger btn-lg ms-auto">
                                <i class="bi bi-x-lg me-1"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau latéral : Aide & Info -->
        <div class="col-lg-4">
            <!-- Guide rapide -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb me-2"></i>Comment ça marche ?</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li class="mb-2">Sélectionnez la <strong>salle</strong> souhaitée</li>
                        <li class="mb-2">Décrivez le <strong>motif</strong> de votre événement</li>
                        <li class="mb-2">Choisissez les <strong>date et heure</strong> de début et de fin</li>
                        <li class="mb-2">Le système <strong>vérifie automatiquement</strong> les conflits de créneau</li>
                        <li class="mb-2">Si le créneau est libre, votre réservation est <strong>soumise</strong></li>
                        <li>Vous recevrez une <strong>notification</strong> une fois la réservation validée</li>
                    </ol>
                </div>
            </div>

            <!-- Règles de réservation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-triangle me-2"></i>Règles de réservation</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-2">Pas de réservation dans le <strong>passé</strong></li>
                        <li class="mb-2">Durée maximale : <strong>24 heures</strong></li>
                        <li class="mb-2">Horaires recommandés : <strong>07:00 – 21:00</strong></li>
                        <li class="mb-2">Les <strong>conflits</strong> sont bloquants</li>
                        <li>Seules les salles <strong>actives</strong> sont disponibles</li>
                    </ul>
                </div>
            </div>

            <!-- Info sur le statut -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-info-circle me-2"></i>Processus de validation</h6>
                    <div class="small text-muted">
                        <p class="mb-2"><span class="badge bg-warning text-dark me-1">En attente</span> Votre réservation est soumise et attend la validation.</p>
                        <p class="mb-2"><span class="badge bg-success me-1">Validée</span> Le créneau vous est confirmé.</p>
                        <p class="mb-0"><span class="badge bg-danger me-1">Refusée</span> La réservation a été refusée ou annulée.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de vérification des conflits en temps réel (US08) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var salleSelect = document.getElementById('salle_id');
    var dateDebut = document.getElementById('date_debut');
    var heureDebut = document.getElementById('heure_debut');
    var dateFin = document.getElementById('date_fin');
    var heureFin = document.getElementById('heure_fin');
    var conflictAlert = document.getElementById('conflictAlert');
    var conflictDetails = document.getElementById('conflictDetails');
    var noConflictAlert = document.getElementById('noConflictAlert');
    var btnSubmit = document.getElementById('btnSubmit');
    var btnCheckConflict = document.getElementById('btnCheckConflict');
    var sallePreview = document.getElementById('sallePreview');

    // Aperçu de la salle sélectionnée
    salleSelect.addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('previewNom').textContent = selected.text.split(' (')[0];
            document.getElementById('previewCapacite').textContent = selected.getAttribute('data-capacite');
            document.getElementById('previewEquipements').textContent = selected.getAttribute('data-equipements') || 'Non spécifié';
            sallePreview.classList.remove('d-none');
        } else {
            sallePreview.classList.add('d-none');
        }
        checkConflictsRealtime();
    });

    // Synchroniser date_fin avec date_debut si vide
    dateDebut.addEventListener('change', function() {
        if (!dateFin.value) {
            dateFin.value = dateDebut.value;
        }
        dateFin.min = dateDebut.value;
        checkConflictsRealtime();
    });

    // Vérification automatique à chaque changement
    [heureDebut, heureFin, dateFin].forEach(function(el) {
        el.addEventListener('change', checkConflictsRealtime);
    });

    btnCheckConflict.addEventListener('click', checkConflictsRealtime);

    var debounceTimer;
    function checkConflictsRealtime() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doCheckConflict, 400);
    }

    function doCheckConflict() {
        var sId = salleSelect.value;
        var dDebut = dateDebut.value;
        var hDebut = heureDebut.value;
        var dFin = dateFin.value;
        var hFin = heureFin.value;

        // Masquer les alertes si données incomplètes
        if (!sId || !dDebut || !hDebut || !dFin || !hFin) {
            conflictAlert.classList.add('d-none');
            noConflictAlert.classList.add('d-none');
            return;
        }

        var fullDebut = dDebut + ' ' + hDebut + ':00';
        var fullFin = dFin + ' ' + hFin + ':00';

        var url = '/reservations/check-conflict?salle_id=' + sId 
                + '&date_debut=' + encodeURIComponent(fullDebut) 
                + '&date_fin=' + encodeURIComponent(fullFin);

        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.hasConflict) {
                    var details = data.conflicts.map(function(c) {
                        return c.titre + ' (' + c.debut + ' – ' + c.fin + ') par ' + c.demandeur;
                    }).join('<br>');
                    conflictDetails.innerHTML = '<br>' + details;
                    conflictAlert.classList.remove('d-none');
                    noConflictAlert.classList.add('d-none');
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('btn-secondary');
                    btnSubmit.classList.remove('btn-primary');
                } else {
                    conflictAlert.classList.add('d-none');
                    noConflictAlert.classList.remove('d-none');
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('btn-secondary');
                    btnSubmit.classList.add('btn-primary');
                }
            })
            .catch(function(err) {
                console.error('Erreur vérification conflit:', err);
            });
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
