<?php
// ============================
// Fichier : pages/search.php
// Rôle : Rechercher des covoiturages avec filtres (US4)
// ============================

require_once('../models/db.php'); // Connexion à la BDD
$results = [];

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['depart'], $_GET['arrivee'], $_GET['date'])) {
    $depart = $_GET['depart'];
    $arrivee = $_GET['arrivee'];
    $date = $_GET['date'];

    // Filtres optionnels
    $prix_max = $_GET['prix_max'] ?? null;
    $duree_max = $_GET['duree_max'] ?? null;
    $note_min = $_GET['note_min'] ?? null;
    $electrique = isset($_GET['electrique']); // true si coché

    // Construction de la requête SQL
    $sql = "SELECT rides.*, vehicles.marque, vehicles.modele, vehicles.energie, users.pseudo,
                   IFNULL(AVG(avis.note), 0) AS note_moyenne
            FROM rides
            INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
            INNER JOIN users ON rides.user_id = users.id
            LEFT JOIN avis ON avis.chauffeur_id = users.id
            WHERE rides.depart = :depart
              AND rides.arrivee = :arrivee
              AND DATE(rides.date_depart) = :date
              AND rides.places > 0";

    // Ajout des filtres optionnels
    if (!empty($prix_max)) {
        $sql .= " AND rides.prix <= :prix_max";
    }

    if (!empty($duree_max)) {
        $sql .= " AND rides.duree <= :duree_max";
    }

    if (!empty($note_min)) {
        $sql .= " HAVING note_moyenne >= :note_min";
    }

    if ($electrique) {
        $sql .= (strpos($sql, 'HAVING') === false) ? " HAVING" : " AND";
        $sql .= " vehicles.energie = 'electrique'";
    }

    $sql .= " GROUP BY rides.id ORDER BY rides.date_depart";

    // Préparation de la requête
    $stmt = $pdo->prepare($sql);

    // Liaison des paramètres
    $stmt->bindValue(':depart', $depart);
    $stmt->bindValue(':arrivee', $arrivee);
    $stmt->bindValue(':date', $date);
    if (!empty($prix_max)) {
        $stmt->bindValue(':prix_max', $prix_max);
    }
    if (!empty($duree_max)) {
        $stmt->bindValue(':duree_max', $duree_max);
    }
    if (!empty($note_min)) {
        $stmt->bindValue(':note_min', $note_min);
    }

    // Exécution
    $stmt->execute();
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
    <form action="search.php" method="get" class="form-section">
        <label for="depart">Ville de départ :</label>
        <input type="text" name="depart" id="depart" required>

        <label for="arrivee">Ville d'arrivée :</label>
        <input type="text" name="arrivee" id="arrivee" required>

        <label for="date">Date de départ :</label>
        <input type="date" name="date" id="date" required>

        <!-- Filtres supplémentaires (US4) -->
        <label for="prix_max">Prix maximum (€) :</label>
        <input type="number" name="prix_max" id="prix_max" min="0" step="1">

        <label for="duree_max">Durée maximale (minutes) :</label>
        <input type="number" name="duree_max" id="duree_max" min="1">

        <label for="note_min">Note minimale du conducteur :</label>
        <input type="number" name="note_min" id="note_min" min="0" max="5" step="0.1">

        <label>
            <input type="checkbox" name="electrique"> Trajets en voiture électrique uniquement
        </label>

        <button type="submit">Rechercher</button>
    </form>
</section>

<section>
    <h2>Résultats</h2>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $ride): ?>
            <div class="ride-box">
                <p><strong>Départ :</strong> <?= htmlspecialchars($ride['depart']) ?> → <strong>Arrivée :</strong> <?= htmlspecialchars($ride['arrivee']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?> - <strong>Durée :</strong> <?= htmlspecialchars($ride['duree']) ?> min</p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
                <p><strong>Conducteur :</strong> <?= htmlspecialchars($ride['pseudo']) ?> - Note : <?= round($ride['note_moyenne'], 1) ?>/5</p>
                <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?> (<?= htmlspecialchars($ride['energie']) ?>)</p>
                <p><strong>Places disponibles :</strong> <?= htmlspecialchars($ride['places']) ?></p>
                <a href="details.php?ride_id=<?= $ride['id'] ?>" class="btn-blue">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun résultat trouvé. Essayez de modifier les filtres.</p>
    <?php endif; ?>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>