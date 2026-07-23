<!-- src/Views/layout/footer.php -->
</main>

<footer class="bg-white border-top py-3 mt-5 text-center text-muted small">
    <div class="container">
        <span>&copy; <?php echo date('Y'); ?> Application de Gestion et Réservation de Salles. Tous droits réservés.</span>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($loadCalendar) && $loadCalendar): ?>
    <!-- FullCalendar JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<?php endif; ?>

</body>
</html>
