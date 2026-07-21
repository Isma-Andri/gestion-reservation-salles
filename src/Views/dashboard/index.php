<!-- src/Views/dashboard/index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></h1>
    <p>Connecte en tant que : <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>

    <nav>
        <ul>
            <!-- Affichage conditionnel selon le role (US02)[cite: 1] -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="#">Gerer les utilisateurs</a></li>
                <li><a href="#">Voir les statistiques globales</a></li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'logistique'): ?>
                <li><a href="#">Valider les reservations (Associations)</a></li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'enseignant'): ?>
                <li><a href="#">Nouvelle reservation rapide</a></li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'association'): ?>
                <li><a href="#">Demander une reservation</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <a href="/logout">Se deconnecter</a>
</body>
</html>
