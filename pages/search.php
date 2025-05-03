<?php
// ============================
// Fichier : pages/search.php
// Rôle : Page publique pour chercher des covoiturages
// ============================

require_once('../models/db.php');

// Initialisation des variables
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['depart'], $_GET['arrivee'], $_GET['date'])) {
    $depart = $_GET['depart'];
    $arrivee = $_GET['arrivee'];
    $date = $_GET['date'];

    // Rechercher les trajets correspondants
    $sql = "SELECT rides.*, vehicles.marque, vehicles.modele
            FROM rides
            INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
            WHERE rides.depart = :depart
            AND rides.arrivee = :arrivee
            AND rides.date_depart = :date_depart";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':depart' => $depart,
        ':arrivee' => $arrivee,
        ':date_depart' => $date
    ]);

    $results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher un covoiturage - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Rechercher un covoiturage</h1>
</header>

<main>

<section>
    <h2>Formulaire de recherche</h2>
    <form action="search.php" method="get">
        <label for="depart">Ville de départ :</label>
        <input type="text" name="depart" id="depart" required>

        <label for="arrivee">Ville d'arrivée :</label>
        <input type="text" name="arrivee" id="arrivee" required>

        <label for="date">Date de départ :</label>
        <input type="date" name="date" id="date" required>

        <button type="submit">Rechercher</button>
    </form>
</section>

<!-- Résultats de recherche -->
<section>
    <h2>Résultats</h2>

    <?php if (!empty($results)): ?>
        <ul>
            <?php foreach ($results as $ride): ?>
                <li>
                    <strong>Départ :</strong> <?php echo htmlspecialchars($ride['depart']); ?> → 
                    <strong>Arrivée :</strong> <?php echo htmlspecialchars($ride['arrivee']); ?><br>
                    <strong>Date :</strong> <?php echo htmlspecialchars($ride['date_depart']); ?><br>
                    <strong>Prix :</strong> <?php echo htmlspecialchars($ride['prix']); ?> €<br>
                    <strong>Véhicule :</strong> <?php echo htmlspecialchars($ride['marque']) . ' ' . htmlspecialchars($ride['modele']); ?><br>
                    <a href="participate.php?ride_id=<?php echo $ride['id']; ?>">Participer à ce covoiturage</a>

                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun covoiturage trouvé pour votre recherche.</p>
    <?php endif; ?>

</section>

</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>
