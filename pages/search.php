<?php
// ============================
// Fichier : pages/search.php
// Rôle : Rechercher des covoiturages avec filtres (US3 + US4)
// ============================

require_once('../models/db.php');
$results = [];
$suggestion = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['depart'], $_GET['arrivee'], $_GET['date'])) {
    $depart = $_GET['depart'];
    $arrivee = $_GET['arrivee'];
    $date = $_GET['date'];

    $prix_max = $_GET['prix_max'] ?? null;
    $duree_max = $_GET['duree_max'] ?? null;
    $note_min = $_GET['note_min'] ?? null;
    $electrique = isset($_GET['electrique']);

    $sql = "SELECT rides.id, rides.depart, rides.arrivee, rides.date_depart, rides.prix, rides.places, 
                   rides.duree, vehicles.marque, vehicles.modele, vehicles.energie, 
                   users.pseudo, IFNULL(AVG(avis.note), 0) AS note_moyenne
            FROM rides
            INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
            INNER JOIN users ON rides.user_id = users.id
            LEFT JOIN avis ON avis.chauffeur_id = users.id
            WHERE rides.depart = :depart
              AND rides.arrivee = :arrivee
              AND DATE(rides.date_depart) = :date
              AND rides.places > 0";

    if (!empty($prix_max)) $sql .= " AND rides.prix <= :prix_max";
    if (!empty($duree_max)) $sql .= " AND rides.duree <= :duree_max";
    if (!empty($note_min)) $sql .= " HAVING note_moyenne >= :note_min";
    if ($electrique) $sql .= (strpos($sql, 'HAVING') === false ? " HAVING" : " AND") . " vehicles.energie = 'électrique'";

    $sql .= " GROUP BY rides.id ORDER BY rides.date_depart";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':depart', $depart);
    $stmt->bindValue(':arrivee', $arrivee);
    $stmt->bindValue(':date', $date);
    if (!empty($prix_max)) $stmt->bindValue(':prix_max', $prix_max);
    if (!empty($duree_max)) $stmt->bindValue(':duree_max', $duree_max);
    if (!empty($note_min)) $stmt->bindValue(':note_min', $note_min);

    $stmt->execute();
    $results = $stmt->fetchAll();

    // Suggestion si aucun résultat
    if (empty($results)) {
        $sql_alt = "SELECT rides.*, users.pseudo, vehicles.marque, vehicles.modele, vehicles.energie,
                       IFNULL(AVG(avis.note), 0) AS note_moyenne
                    FROM rides
                    INNER JOIN users ON rides.user_id = users.id
                    INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
                    LEFT JOIN avis ON avis.chauffeur_id = users.id
                    WHERE rides.depart = :depart AND rides.arrivee = :arrivee
                      AND rides.places > 0 AND rides.date_depart > :date
                    GROUP BY rides.id
                    ORDER BY rides.date_depart ASC LIMIT 1";
        $stmt_alt = $pdo->prepare($sql_alt);
        $stmt_alt->execute([':depart' => $depart, ':arrivee' => $arrivee, ':date' => $date]);
        $suggestion = $stmt_alt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher un covoiturage - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Rechercher un covoiturage</h1>
</header>

<main>
<section class="form-section">
    <form action="search.php" method="get">
        <label for="depart">Ville de départ :</label>
        <input type="text" name="depart" id="depart" required>

        <label for="arrivee">Ville d'arrivée :</label>
        <input type="text" name="arrivee" id="arrivee" required>

        <label for="date">Date de départ :</label>
        <input type="date" name="date" id="date" required>

        <!-- Filtres supplémentaires -->
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
                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Photo conducteur" width="100">
                <p><strong>Départ :</strong> <?= htmlspecialchars($ride['depart']) ?> → <strong>Arrivée :</strong> <?= htmlspecialchars($ride['arrivee']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>

                <?php
                    $heure_depart = new DateTime($ride['date_depart']);
                    $heure_arrivee = clone $heure_depart;
                    $heure_arrivee->modify("+{$ride['duree']} minutes");
                ?>
                <p><strong>Heure de départ :</strong> <?= $heure_depart->format('H:i') ?> - <strong>Heure d’arrivée :</strong> <?= $heure_arrivee->format('H:i') ?></p>

                <p><strong>Durée :</strong> <?= htmlspecialchars($ride['duree']) ?> min</p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
                <p><strong>Conducteur :</strong> <?= htmlspecialchars($ride['pseudo']) ?> - Note :
                    <?= $ride['note_moyenne'] > 0 ? round($ride['note_moyenne'], 1) . '/5' : '⭐ Pas encore noté' ?>
                </p>
                <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?> (<?= htmlspecialchars($ride['energie']) ?>)</p>
                <p><strong>Type de trajet :</strong> <?= $ride['energie'] === 'électrique' ? '✅ Écologique' : '❌ Non écologique' ?></p>
                <p><strong>Places disponibles :</strong> <?= htmlspecialchars($ride['places']) ?></p>
                <a href="details.php?ride_id=<?= $ride['id'] ?>" class="btn-blue">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php elseif (!empty($suggestion)): ?>
        <p>Aucun covoiturage trouvé à cette date. Essayez à la date suivante : <strong><?= htmlspecialchars($suggestion['date_depart']) ?></strong></p>
    <?php else: ?>
        <p>Aucun résultat trouvé. Essayez de modifier les filtres.</p>
    <?php endif; ?>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>