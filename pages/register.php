<?php
// ============================
// Fichier : pages/register.php
// Rôle : Permet à un visiteur de créer un compte utilisateur
// ============================
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Créer un compte</h1>
</header>

<main>
<section>
    <?php if (isset($_SESSION['message'])): ?>
        <p class="error-message"><?= $_SESSION['message'] ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form action="../controllers/userController.php" method="post" class="form-create-user">
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <small>🔐 Le mot de passe doit contenir au moins 9 caractères, une majuscule, une minuscule et un chiffre.</small>

        <button type="submit">Créer mon compte</button>
    </form>

    <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
</section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>