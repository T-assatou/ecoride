<!DOCTYPE html>
<!-- Déclare que c'est un document HTML5 -->
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Autorise les accents et caractères spéciaux -->
    <title>EcoRide - Accueil</title> <!-- Titre de l'onglet du navigateur -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive pour mobile -->
    <link rel="stylesheet" href="Assets/css/style.css"> <!-- Lien vers la feuille de style CSS -->

    <head>

</head>
    
</head>
<body>

<!-- Inclut le menu de navigation (fichier séparé) -->
<?php include('includes/nav.php'); ?>

<!-- En-tête principal du site -->
<header>
    <h1>Bienvenue sur EcoRide</h1>
    <p>La plateforme de covoiturage écologique</p>
</header>

<main>
    <!-- Présentation de l’entreprise -->
    <section>
        <h2>Notre mission</h2>
        <p>
            Réduire l'empreinte carbone de vos déplacements
            en encourageant le covoiturage responsable.
        </p>
        <!-- Image illustrative -->
        <img src="assets/img/home.jpg" alt="Co-voiturage dans une voiture écologique" width="300">
    </section>

    <!-- Formulaire de recherche d'itinéraires -->
    <section class="search-bar">
        <h2>Rechercher un itinéraire</h2>

        <!-- Le formulaire envoie les données par l'URL (méthode GET) vers la page search.php -->
        <form action="pages/search.php" method="get">
            <!-- Champ pour saisir la ville de départ -->
            <input type="text" name="depart" placeholder="Ville de départ" required>

            <!-- Champ pour saisir la ville d’arrivée -->
            <input type="text" name="arrivee" placeholder="Ville d'arrivée" required>

            <!-- Champ pour saisir la date du trajet -->
            <input type="date" name="date" required>

            <!-- Bouton pour envoyer la recherche -->
            <button type="submit">Rechercher</button>
        </form>
    </section>
</main>

<!-- Inclut le pied de page (fichier séparé) -->
<?php include('includes/footer.php'); ?>

</body>
</html>