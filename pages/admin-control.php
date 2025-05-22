<?php
// 
// US 13
// Fichier : pages/admin-control.php
// Rôle : Interface de gestion administrateur
// Permet de voir les trajets, participants, suspendre ou réactiver des utilisateurs


require_once('../models/db.php');
session_start();

// 🔐 Sécurité : Accès réservé à l'admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé. Cette page est réservée aux administrateurs.";
    exit;
}

// 📦 Récupération de tous les trajets
$sql = "SELECT rides.*, users.pseudo, vehicles.marque, vehicles.modele
        FROM rides
        INNER JOIN users ON rides.user_id = users.id
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        ORDER BY rides.date_depart DESC";
$stmt = $pdo->query($sql);
$rides = $stmt->fetchAll();

// 📦 Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM users ORDER BY pseudo");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Covoiturages</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Espace Administrateur</h1>
</header>

<main>
    <section class="admin-section">
        <h2>Tous les covoiturages</h2>

        <?php foreach ($rides as $ride): ?>
            <div class="ride-card">
                <p><strong>Conducteur :</strong> <?= htmlspecialchars($ride['pseudo']) ?></p>
                <p><strong>Trajet :</strong> <?= htmlspecialchars($ride['depart']) ?> ➔ <?= htmlspecialchars($ride['arrivee']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>
                <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></p>
                <p><strong>Places restantes :</strong> <?= $ride['places'] ?></p>

                <h4>Participants :</h4>
                <ul>
                    <?php
                    $pstmt = $pdo->prepare("SELECT users.pseudo FROM participants 
                                            INNER JOIN users ON participants.user_id = users.id 
                                            WHERE participants.ride_id = :ride_id");
                    $pstmt->execute([':ride_id' => $ride['id']]);
                    $participants = $pstmt->fetchAll();

                    if (empty($participants)) {
                        echo "<li>Aucun participant</li>";
                    } else {
                        foreach ($participants as $p) {
                            echo "<li>" . htmlspecialchars($p['pseudo']) . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="admin-section">
        <h2>👤 Gestion des utilisateurs</h2>

        <?php foreach ($users as $user): ?>
            <p>
                <?= htmlspecialchars($user['pseudo']) ?> (<?= htmlspecialchars($user['role']) ?>)
                <?php if ($user['actif'] == 1): ?>
                    - <a href="suspendre-user.php?id=<?= $user['id'] ?>&action=suspendre"
                         class="confirm-action" 
                         data-confirm="Confirmer la suspension de cet utilisateur ?"
                         style="color:red;">Suspendre</a>
                <?php else: ?>
                    - <a href="suspendre-user.php?id=<?= $user['id'] ?>&action=reactiver"
                         class="confirm-action" 
                         data-confirm="Confirmer la réactivation de cet utilisateur ?"
                         style="color:green;">Réactiver</a>
                <?php endif; ?>
            </p>
        <?php endforeach; ?>
    </section>

    <section class="admin-section">
        <a href="create-employee.php" class="admin-button"> Créer un employé</a>
        <a href="dashboard.php" class="admin-button"> Voir le Dashboard</a>
    </section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>