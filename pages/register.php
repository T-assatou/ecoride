<?php
// ============================
// Fichier : pages/register.php
// Rôle : Permet à un visiteur de créer un compte utilisateur
// Ce formulaire enverra les données vers un traitement PHP (à venir)
// ============================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Créer un compte</h1>
</header>

<main>
    <section>
    <form action="../controllers/userController.php" method="post">
            <!-- Champ pour le pseudo de l’utilisateur -->
            <label for="pseudo">Pseudo :</label>
            <input type="text" name="pseudo" id="pseudo" placeholder="Nom d'utilisateur" required>

            <!-- Champ pour l’adresse email -->
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" placeholder="exemple@mail.com" required>

            <!-- Champ pour le mot de passe -->
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe sécurisé" required>

            <!-- Bouton de création de compte -->
            <button type="submit">Créer mon compte</button>
        </form>

        <!-- Message d'information -->
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>