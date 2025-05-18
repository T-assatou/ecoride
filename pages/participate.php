<?php
// ============================
// Fichier : pages/participate.php
// Rôle : Confirmer la participation à un covoiturage
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que l'utilisateur est connecté, sinon redirige vers login avec ride_id
$ride_id = $_GET['ride_id'] ?? null;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=participate.php&ride_id=" . urlencode($ride_id));
    exit;
}

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

if (!$ride) {
    echo "Trajet non trouvé.";
    exit;
}

if ($ride['places'] <= 0) {
    echo "Désolé, il n'y a plus de place disponible.";
    exit;
}

// Traitement de la participation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Vérifie les crédits
    $stmt = $pdo->prepare("SELECT credits FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if ($user['credits'] < 2) {
        echo "Crédits insuffisants.";
        exit;
    }

    // Inscription + retrait crédits + retrait place
    $pdo->prepare("INSERT INTO participants (user_id, ride_id) VALUES (:user_id, :ride_id)")
        ->execute([':user_id' => $user_id, ':ride_id' => $ride_id]);

    $pdo->prepare("UPDATE users SET credits = credits - 2 WHERE id = :id")
        ->execute([':id' => $user_id]);

    $pdo->prepare("UPDATE rides SET places = places - 1 WHERE id = :ride_id")
        ->execute([':ride_id' => $ride_id]);

    echo "<p>✅ Vous avez réservé une place !</p>";
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
        <p><strong>Départ :</strong> <?= htmlspecialchars($ride['depart']) ?></p>
        <p><strong>Arrivée :</strong> <?= htmlspecialchars($ride['arrivee']) ?></p>
        <p><strong>Date de départ :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>
        <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
        <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></p>
        <p><strong>Places disponibles :</strong> <?= htmlspecialchars($ride['places']) ?></p>

        <!-- Formulaire de confirmation -->
        <form action="" method="post" onsubmit="return confirm('Utiliser 2 crédits pour ce trajet ?')">
            <button type="submit">Confirmer ma participation</button>
        </form>
    </section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>