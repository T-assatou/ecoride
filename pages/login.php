<?php
// ============================
// Fichier : pages/login.php
// Rôle : Affiche le formulaire de connexion pour les utilisateurs
// Partie connexion, avant la gestion PHP réelle avec sessions
// ============================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Connexion à EcoRide</h1>
</header>

<main>
    <!-- Formulaire de connexion -->
    <section>
    <?php
if (isset($_GET['message']) && $_GET['message'] === 'compte_suspendu') {
    echo "<div class='alert'>⛔ Votre compte a été suspendu. Veuillez contacter un administrateur.</div>";
}
?>
    <form action="../controllers/authController.php" method="post">
            <!-- Champ pour l’adresse e-mail -->
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" placeholder="Votre email" required>

            <!-- Champ pour le mot de passe -->
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>

            <!-- Bouton de connexion -->
            <button type="submit">Se connecter</button>
        </form>

        <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>