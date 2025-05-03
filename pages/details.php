<?php
// ============================
// Fichier : pages/detail.php
// Rôle : Afficher les détails d’un trajet sélectionné
// À terme, ce fichier affichera dynamiquement les infos du trajet via une base de données
// ============================

// Pour l’instant, on simule l'affichage avec des données fictives
// L’identifiant du trajet (ex: ?id=1) sera utilisé plus tard avec une base SQL
$trajet_id = $_GET['id'] ?? 1; // valeur par défaut : 1
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du covoiturage - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Détails du covoiturage</h1>
</header>

<main>
    <section class="trajet-detail">
        <!-- Détail du trajet (exemple simulé) -->
        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Photo conducteur" width="100">
        <h2>Samir - 4.5★</h2>

        <p><strong>Date :</strong> 22 avril 2025</p>
        <p><strong>Heure de départ :</strong> 08:00</p>
        <p><strong>Heure d’arrivée :</strong> 10:00</p>
        <p><strong>Places restantes :</strong> 2</p>
        <p><strong>Prix :</strong> 12 €</p>
        <p><strong>Véhicule :</strong> Renault Zoé - Électrique</p>
        <p><strong>🌱 Voyage écologique</strong></p>

        <!-- Préférences conducteur -->
        <h3>Préférences du conducteur :</h3>
        <ul>
            <li>❌ Fumeur non autorisé</li>
            <li>✅ Animaux autorisés</li>
            <li>📦 Pas de gros bagages</li>
        </ul>

        <!-- Avis des passagers (simulés) -->
        <h3>Avis :</h3>
        <blockquote>
            <p>Super trajet, conducteur très sympa et ponctuel !</p>
            <cite>– Leïla</cite>
        </blockquote>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>