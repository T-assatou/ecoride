<?php
// ============================
// Fichier : pages/mentions-legales.php
// Rôle : Affiche les mentions légales de l’entreprise EcoRide
// ============================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mentions légales - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Mentions légales</h1>
</header>

<main>
    <section>
        <h2>Éditeur du site</h2>
        <p><strong>Nom :</strong> EcoRide</p>
        <p><strong>Email :</strong> eco@ecoride.com</p>
        <p><strong>Adresse :</strong> 123 rue de l'Écologie, 75000 Paris</p>

        <h2>Hébergement</h2>
        <p>Le site est hébergé par un prestataire tiers.</p>

        <h2>Utilisation des données</h2>
        <p>Les données personnelles sont utilisées uniquement dans le cadre de l'utilisation du service EcoRide.  
        Aucune donnée n’est partagée à des tiers sans consentement.</p>

        <h2>Cookies</h2>
        <p>Le site peut utiliser des cookies à des fins de fonctionnement et de statistiques anonymes.</p>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>