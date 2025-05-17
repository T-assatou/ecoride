<?php
// ============================
// Fichier : pages/contact.php
// Rôle : Affiche un formulaire de contact
// (Ce formulaire n'est pas connecté à une base ou traitement en PHP)
// ============================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/style.css"> <!-- Lien CSS global -->
</head>
<body>

<?php include('../includes/nav.php'); ?> <!-- Menu de navigation -->

<header>
    <h1>Contactez-nous</h1>
</header>

<main>
    <section>
        <h2>Formulaire de contact</h2>
        
        <!-- 📨 Formulaire non connecté (pas traité côté serveur) -->
        <form action="#" method="post" class="contact-form">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>

            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <label for="message">Message :</label>
            <textarea name="message" id="message" rows="5" required></textarea>

            <button type="submit">Envoyer</button>
        </form>
    </section>
</main>

<?php include('../includes/footer.php'); ?> <!-- Pied de page -->

</body>
</html>