<?php
// ============================
// Fichier : pages/participate.php
// Rôle : Confirmer la participation à un covoiturage
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérifie que ride_id est passé en GET
$ride_id = $_GET['ride_id'] ?? null;
if (!$ride_id) {
    echo "Aucun trajet sélectionné.";
    exit;
}

// Récupère les infos du trajet
$sql = "SELECT rides.*, vehicles.marque, vehicles.modele
        FROM rides
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        WHERE rides.id = :ride_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':ride_id' => $ride_id]);
$ride = $stmt->fetch();

// Vérifie que le trajet existe
if (!$ride) {
    echo "Trajet non trouvé.";
    exit;
}

// Vérifie qu'il reste des places
if ($ride['places'] <= 0) {
    echo "Désolé, il n'y a plus de place disponible pour ce covoiturage.";
    exit;
}

// Traitement si l'utilisateur confirme sa participation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Enregistre la participation dans la table participants
  $insert = $pdo->prepare("INSERT INTO participants (user_id, ride_id) VALUES (:user_id, :ride_id)");
  $insert->execute([
      ':user_id' => $_SESSION['user_id'],
      ':ride_id' => $ride_id
  ]);

  // On retire une place disponible
  $updateSql = "UPDATE rides SET places = places - 1 WHERE id = :ride_id AND places > 0";
  $updateStmt = $pdo->prepare($updateSql);
  $updateStmt->execute([':ride_id' => $ride_id]);

  echo "<p>✅ Vous avez réservé une place dans ce covoiturage !</p>";
  echo '<a href="user-space.php">Voir mes trajets</a>';
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmer participation - EcoRide</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Confirmer votre participation</h1>
</header>

<main>
    <section>
        <p><strong>Départ :</strong> <?php echo htmlspecialchars($ride['depart']); ?></p>
        <p><strong>Arrivée :</strong> <?php echo htmlspecialchars($ride['arrivee']); ?></p>
        <p><strong>Date de départ :</strong> <?php echo htmlspecialchars($ride['date_depart']); ?></p>
        <p><strong>Prix :</strong> <?php echo htmlspecialchars($ride['prix']); ?> €</p>
        <p><strong>Véhicule :</strong> <?php echo htmlspecialchars($ride['marque']) . ' ' . htmlspecialchars($ride['modele']); ?></p>
        <p><strong>Places disponibles :</strong> <?php echo htmlspecialchars($ride['places']); ?></p>

        <!-- Formulaire de confirmation -->
        <form action="" method="post">
            <button type="submit">Confirmer ma participation</button>
        </form>
    </section>
</main>

<?php include('../includes/footer.php'); ?>

</body>
</html>
