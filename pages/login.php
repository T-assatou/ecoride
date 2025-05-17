<?php
// ============================
// Fichier : pages/login.php
// Rôle : Affiche le formulaire de connexion utilisateur
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
<section class="form-section">

    <?php
    // ✅ Message affiché si l’utilisateur a été suspendu
    if (isset($_GET['message']) && $_GET['message'] === 'compte_suspendu') {
        echo "<div class='alert'>⛔ Votre compte a été suspendu. Veuillez contacter un administrateur.</div>";
    }

    // ✅ Vérifie si redirection spéciale (ex: retour après tentative de participation)
    $redirect = $_GET['redirect'] ?? '';
    $ride_id = $_GET['ride_id'] ?? '';
    ?>

    <!-- 🔐 Formulaire de connexion -->
    <form action="../controllers/authController.php" method="post" class="form-create-user">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" placeholder="Votre email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" placeholder="Mot de passe" required>

        <!-- Champs cachés pour redirection intelligente après login (US6) -->
        <?php if (!empty($redirect) && !empty($ride_id)): ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <input type="hidden" name="ride_id" value="<?= htmlspecialchars($ride_id) ?>">
        <?php endif; ?>

        <button type="submit">Se connecter</button>
    </form>

    <p style="text-align: center;">
        Pas encore inscrit ? <a href="register.php">Créer un compte</a>
    </p>

</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>