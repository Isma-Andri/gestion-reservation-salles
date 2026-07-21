<!-- src/Views/auth/register.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Gestion Salles</title>
</head>
<body>
    <h2>Inscription</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="/register">
        <div>
            <label>Nom :</label>
            <input type="text" name="nom" required>
        </div>
        <div>
            <label>Prenom :</label>
            <input type="text" name="prenom" required>
        </div>
        <div>
            <label>Email :</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" required>
        </div>
        <div>
            <label>Role :</label>
            <select name="role">
                <option value="enseignant">Enseignant</option>
                <option value="association">Association</option>
                <option value="logistique">Logistique</option>
                <option value="admin">Administrateur</option>
            </select>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Deja un compte ? <a href="/login">Se connecter</a></p>
</body>
</html>
