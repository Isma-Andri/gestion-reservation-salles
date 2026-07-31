<?php
// src/Views/admin/export.php
$pageTitle = $pageTitle ?? "Export des données";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Export des données</h2>
            <p class="text-muted mb-0">Filtrez et exportez les réservations au format CSV (compatible Excel)</p>
        </div>
        <a href="/admin/stats" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart-line me-1"></i>Statistiques
        </a>
    </div>

    <div class="row g-4">
        <!-- Formulaire de filtres -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-funnel text-primary me-2"></i>Filtres d'export</h5>
                </div>
                <div class="card-body p-4">
                    <form id="export-form" method="GET" action="/admin/export-csv" target="_blank">
                        <!-- Statut -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="statut_all" value="" checked>
                                    <label class="form-check-label" for="statut_all">Tous</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="statut_validee" value="validee">
                                    <label class="form-check-label text-success fw-semibold" for="statut_validee">
                                        <i class="bi bi-check-circle me-1"></i>Validées
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="statut_attente" value="en_attente">
                                    <label class="form-check-label text-warning fw-semibold" for="statut_attente">
                                        <i class="bi bi-hourglass me-1"></i>En attente
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="statut_refusee" value="refusee">
                                    <label class="form-check-label text-danger fw-semibold" for="statut_refusee">
                                        <i class="bi bi-x-circle me-1"></i>Refusées
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Salle -->
                        <div class="mb-3">
                            <label for="salle_id" class="form-label fw-semibold">Salle</label>
                            <select name="salle_id" id="salle_id" class="form-select">
                                <option value="">Toutes les salles</option>
                                <?php foreach ($salles as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Période -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label for="date_debut" class="form-label fw-semibold">Période du</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut">
                            </div>
                            <div class="col-6">
                                <label for="date_fin" class="form-label fw-semibold">Au</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="btn-export">
                            <i class="bi bi-download me-2"></i>Télécharger le fichier CSV
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser les filtres
                        </button>
                    </form>
                </div>
            </div>

            <!-- Infos format -->
            <div class="card border-0 shadow-sm mt-3 bg-light">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-1"></i>À propos du fichier CSV</h6>
                    <ul class="small text-muted mb-0">
                        <li>Séparateur : <code>;</code> (point-virgule)</li>
                        <li>Encodage : <strong>UTF-8 avec BOM</strong> (compatible Excel)</li>
                        <li>Colonnes : ID, Demandeur, Rôle, Email, Salle, Événement, Début, Fin, Statut, Date création</li>
                        <li>Le fichier s'ouvre directement dans LibreOffice Calc</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Prévisualisation format -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-table text-secondary me-2"></i>Format du fichier exporté</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 small">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Demandeur</th>
                                    <th>Rôle</th>
                                    <th>Email</th>
                                    <th>Salle</th>
                                    <th>Événement</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Statut</th>
                                    <th>Créée le</th>
                                </tr>
                            </thead>
                            <tbody class="text-muted">
                                <tr>
                                    <td>1</td>
                                    <td>Miora Rabe.</td>
                                    <td>enseignant</td>
                                    <td>miora@...</td>
                                    <td>Amphi A</td>
                                    <td>Cours L3</td>
                                    <td>28/07 08:00</td>
                                    <td>10:00</td>
                                    <td><span class="badge bg-success">validée</span></td>
                                    <td>27/07 14:22</td>
                                </tr>
                                <tr class="fst-italic text-secondary">
                                    <td colspan="10" class="text-center">… (toutes les lignes selon vos filtres)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Boutons d'export rapide -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-lightning text-warning me-2"></i>Exports rapides</h5>
                </div>
                <div class="card-body p-3 d-flex flex-wrap gap-2">
                    <a href="/admin/export-csv" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i>Tout exporter
                    </a>
                    <a href="/admin/export-csv?statut=validee" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-check-circle me-1"></i>Validées seulement
                    </a>
                    <a href="/admin/export-csv?statut=en_attente" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-hourglass me-1"></i>En attente
                    </a>
                    <a href="/admin/export-csv?statut=refusee" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Refusées
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetFilters() {
    document.getElementById('export-form').reset();
    document.querySelectorAll('input[name="statut"]')[0].checked = true;
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
