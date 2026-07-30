<?php 
// src/Views/errors/404.php
http_response_code(404);
$pageTitle = "Page introuvable – 404";
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/navbar.php';
?>
<div class="container py-5 text-center">
    <div class="display-1 fw-bold text-primary opacity-25">404</div>
    <h2 class="fw-bold mt-2">Page introuvable</h2>
    <p class="text-muted mb-4">La page que vous cherchez n'existe pas ou a été déplacée.</p>
    <a href="/dashboard" class="btn btn-primary me-2"><i class="bi bi-house me-1"></i>Tableau de bord</a>
    <a href="/calendrier" class="btn btn-outline-secondary"><i class="bi bi-calendar3 me-1"></i>Calendrier</a>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
