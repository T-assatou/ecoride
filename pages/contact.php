<?php
// ============================
// Fichier : pages/contact.php
// Rôle : Permet aux visiteurs d’envoyer un message de contact à l’entreprise
// Ce formulaire n’est pas obligatoire à traiter dans l’ECF, mais utile pour la navigation
// ============================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Contactez-nous</h1>
</header>

<main>
    <section>
        <!-- Formulaire de contact simple -->
        <form action="#" method="post">
            <!-- Nom de l’expéditeur -->
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>

            <!-- Email de l’expéditeur -->
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <!-- Message à envoyer -->
            <label for="message">Message :</label>
            <textarea name="message" id="message" rows="5" required></textarea>

            <!-- Bouton d’envoi -->
            <button type="submit">Envoyer</button>
        </form>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>