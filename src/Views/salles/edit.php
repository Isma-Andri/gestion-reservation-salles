<!-- src/Views/salles/edit.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Salle <?php echo htmlspecialchars($salle['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/dashboard"><i class="bi bi-calendar-event me-2"></i>Gestion Salles</a>
            <div class="d-flex align-items-center text-white">
                <a href="/salles" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Modifier la salle : <?php echo htmlspecialchars($salle['nom']); ?></h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/salles/editer?id=<?php echo $salle['id']; ?>">
                            <div class="mb-3">
                                <label for="nom" class="form-label fw-semibold">Nom de la salle <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required placeholder="Ex: Amphi A, Salle 102..." value="<?php echo htmlspecialchars($_POST['nom'] ?? $salle['nom']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="capacite" class="form-label fw-semibold">Capacité d'accueil (personnes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="capacite" name="capacite" min="1" required placeholder="Ex: 50" value="<?php echo htmlspecialchars($_POST['capacite'] ?? $salle['capacite']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="equipements" class="form-label fw-semibold">Équipements (séparés par des virgules)</label>
                                <textarea class="form-control" id="equipements" name="equipements" rows="3" placeholder="Ex: Vidéoprojecteur, Tableau blanc, Wi-Fi, Climatisation, Micro"><?php echo htmlspecialchars($_POST['equipements'] ?? $salle['equipements']); ?></textarea>
                                <div class="form-text">Indiquez la liste des équipements disponibles séparés par des virgules.</div>
                            </div>

                            <div class="mb-4 form-check form-switch">
                                <?php 
                                    $isActive = $_SERVER['REQUEST_METHOD'] === 'POST' ? isset($_POST['est_active']) : (bool)$salle['est_active'];
                                ?>
                                <input class="form-check-input" type="checkbox" id="est_active" name="est_active" value="1" <?php echo $isActive ? 'checked' : ''; ?>>
                                <label class="form-check-label fw-semibold" for="est_active">Salle active / disponible à la réservation</label>
                            </div>

                            <div class="d-flex justify-content-between pt-2 border-top">
                                <a href="/salles" class="btn btn-outline-secondary">Annuler</a>
                                <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
