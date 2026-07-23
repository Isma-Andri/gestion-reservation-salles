<!-- src/Views/layout/header.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Gestion des Réservations'); ?></title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FullCalendar CSS (si nécessaire) -->
    <?php if (isset($loadCalendar) && $loadCalendar): ?>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Style centralisé -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="content-wrapper">
