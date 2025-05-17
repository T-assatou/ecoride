<?php
// ============================
// Fichier : pages/search.php
// Rôle : Recherche et filtres des covoiturages pour visiteurs
// ============================

require_once('../models/db.php');
session_start();

$results = [];
$message = "";

// Traitement de la recherche
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['depart'], $_GET['arrivee'], $_GET['date'])) {
    $depart = trim($_GET['depart']);
    $arrivee = trim($_GET['arrivee']);
    $date = $_GET['date'];

    if ($depart && $arrivee && $date) {
        // Requête principale
        $sql = "SELECT rides.*, vehicles.marque, vehicles.modele, vehicles.energie, users.pseudo
                FROM rides
                INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
                INNER JOIN users ON rides.user_id = users.id
                WHERE rides.depart = :depart
                AND rides.arrivee = :arrivee
                AND DATE(rides.date_depart) = :date
                AND rides.places > 0";

        $params = [
            ':depart' => $depart,
            ':arrivee' => $arrivee,
            ':date' => $date
        ];

        // Filtres facultatifs
        if (!empty($_GET['ecolo'])) {
            $sql .= " AND vehicles.energie = 'électrique'";
        }

        if (!empty($_GET['prix_max'])) {
            $sql .= " AND rides.prix <= :prix_max";
            $params[':prix_max'] = (float) $_GET['prix_max'];
        }

        if (!empty($_GET['duree_max'])) {
            $sql .= " AND TIMESTAMPDIFF(MINUTE, rides.date_depart, rides.date_arrivee) <= :duree_max";
            $params[':duree_max'] = (int) $_GET['duree_max'];
        }

        if (!empty($_GET['note_min'])) {
            // Simulation : tous les conducteurs ont 4★ par défaut
            $sql .= " AND 4 >= :note_min"; 
            $params[':note_min'] = (float) $_GET['note_min'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        if (empty($results)) {
            $message = "Aucun covoiturage trouvé pour ces critères.";
        }
    } else {
        $message = "Veuillez renseigner la ville de départ, d’arrivée et la date.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher un covoiturage - EcoRide</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Rechercher un covoiturage</h1>
</header>

<main>

<section class="search-bar">
    <form method="get" action="search.php">
        <label for="depart">Ville de départ :</label>
        <input type="text" name="depart" id="depart" required>

        <label for="arrivee">Ville d’arrivée :</label>
        <input type="text" name="arrivee" id="arrivee" required>

        <label for="date">Date de départ :</label>
        <input type="date" name="date" id="date" required>

        <hr>

        <label><input type="checkbox" name="ecolo"> Uniquement les trajets écologiques</label>

        <label for="prix_max">Prix maximum (€) :</label>
        <input type="number" name="prix_max" id="prix_max" min="0" step="0.5">

        <label for="duree_max">Durée maximale (minutes) :</label>
        <input type="number" name="duree_max" id="duree_max" min="0">

        <label for="note_min">Note minimale :</label>
        <input type="number" name="note_min" id="note_min" min="0" max="5" step="0.1">

        <button type="submit">🔍 Rechercher</button>
    </form>
</section>

<section>
    <h2>Résultats</h2>
    <?php if ($message): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $ride): ?>
            <div class="ride-box">
                <p><strong>Départ :</strong> <?= htmlspecialchars($ride['depart']) ?> → 
                   <strong>Arrivée :</strong> <?= htmlspecialchars($ride['arrivee']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>
                <p><strong>Heure d’arrivée :</strong> <?= htmlspecialchars($ride['date_arrivee']) ?></p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
                <p><strong>Places restantes :</strong> <?= htmlspecialchars($ride['places']) ?></p>
                <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['pseudo']) ?> (Note : 4★ simulée)</p>
                <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?> - <?= $ride['energie'] ?></p>
                <?php if ($ride['energie'] === 'électrique'): ?>
                    <p>🌱 Voyage écologique</p>
                <?php endif; ?>
                <a href="details.php?id=<?= $ride['id'] ?>" class="btn-blue">Détail</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</main>
<?php include('../includes/footer.php'); ?>
</body>
</html>