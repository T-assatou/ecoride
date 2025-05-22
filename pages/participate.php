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

if (!$ride) {
    echo "Trajet non trouvé.";
    exit;
}

// Vérifie qu'il reste des places
if ($ride['places'] <= 0) {
    echo "🚫 Ce trajet est complet.";
    exit;
}

// Traitement de la confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {

    // Récupère les crédits
    $stmt = $pdo->prepare("SELECT credits FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user['credits'] < $ride['prix']) {
        echo "<p class='error-message'> Crédit insuffisant pour réserver ce trajet.</p>";
        echo '<a href="user-space.php" class="btn-blue">Retour à mon espace</a>';
        exit;
    }

    // Vérifie si déjà inscrit
    $check = $pdo->prepare("SELECT * FROM participants WHERE user_id = :user_id AND ride_id = :ride_id");
    $check->execute([':user_id' => $_SESSION['user_id'], ':ride_id' => $ride_id]);
    if ($check->fetch()) {
        echo "<p class='error-message'> Vous avez déjà réservé ce trajet.</p>";
        echo '<a href="user-space.php" class="btn-blue">Retour à mon espace</a>';
        exit;
    }

    // Enregistre la participation
    $insert = $pdo->prepare("INSERT INTO participants (user_id, ride_id) VALUES (:user_id, :ride_id)");
    $insert->execute([
        ':user_id' => $_SESSION['user_id'],
        ':ride_id' => $ride_id
    ]);

    // Met à jour les places
    $pdo->prepare("UPDATE rides SET places = places - 1 WHERE id = :ride_id AND places > 0")
        ->execute([':ride_id' => $ride_id]);

    // Met à jour les crédits
    $pdo->prepare("UPDATE users SET credits = credits - :prix WHERE id = :id")
        ->execute([
            ':prix' => $ride['prix'],
            ':id' => $_SESSION['user_id']
        ]);

    echo "<p class='success-message'> Réservation confirmée !</p>";
    echo '<a href="user-space.php" class="btn-green">Voir mes trajets</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmer participation - EcoRide</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Confirmer votre participation</h1>
</header>

<main>
    <section class="form-section">
        <p><strong>Départ :</strong> <?= htmlspecialchars($ride['depart']) ?></p>
        <p><strong>Arrivée :</strong> <?= htmlspecialchars($ride['arrivee']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>
        <p><strong>Prix :</strong> <?= htmlspecialchars($ride['prix']) ?> €</p>
        <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></p>
        <p><strong>Places disponibles :</strong> <?= htmlspecialchars($ride['places']) ?></p>

        <form method="post">
            <p>Ce trajet coûte <strong><?= $ride['prix'] ?> crédits</strong> Souhaitez-vous vraiment réserver une place ?</p>
            <button type="submit" name="confirm"> Oui, je confirme</button>
            <a href="details.php?ride_id=<?= $ride_id ?>" class="btn-blue"> Non, annuler</a>
        </form>
    </section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>