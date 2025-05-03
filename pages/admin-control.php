<?php
// ============================
// Fichier : pages/admin-control.php
// Rôle : Interface de gestion admin pour voir les covoiturages et participants
// ============================

require_once('../models/db.php');
session_start();

// Vérification simple du rôle (on suppose que l'admin est connecté)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé. Cette page est réservée aux administrateurs.";
    exit;
}

// Récupérer tous les trajets
$sql = "SELECT rides.*, users.pseudo, vehicles.marque, vehicles.modele
        FROM rides
        INNER JOIN users ON rides.user_id = users.id
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        ORDER BY rides.date_depart DESC";
$stmt = $pdo->query($sql);
$rides = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panneau Admin - Covoiturages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Panel d'administration - Covoiturages</h1>
</header>
<main>
<section>
    <h2>Liste de tous les covoiturages</h2>
    <?php foreach ($rides as $ride): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
            <p><strong>Conducteur :</strong> <?= htmlspecialchars($ride['pseudo']) ?></p>
            <p><strong>Trajet :</strong> <?= htmlspecialchars($ride['depart']) ?> ➔ <?= htmlspecialchars($ride['arrivee']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($ride['date_depart']) ?></p>
            <p><strong>Véhicule :</strong> <?= htmlspecialchars($ride['marque']) . ' ' . htmlspecialchars($ride['modele']) ?></p>
            <p><strong>Places restantes :</strong> <?= $ride['places'] ?></p>

            <h4>Participants :</h4>
            <ul>
                <?php
                $pstmt = $pdo->prepare("SELECT users.pseudo FROM participants INNER JOIN users ON participants.user_id = users.id WHERE participants.ride_id = :ride_id");
                $pstmt->execute([':ride_id' => $ride['id']]);
                $participants = $pstmt->fetchAll();

                if (count($participants) === 0):
                    echo "<li>Aucun participant pour ce trajet</li>";
                else:
                    foreach ($participants as $p):
                        echo "<li>" . htmlspecialchars($p['pseudo']) . "</li>";
                    endforeach;
                endif;
                ?>
            </ul>
        </div>
    <?php endforeach; ?>
</section>



<section style="margin-bottom: 20px;">
    <a href="create-employee.php" style="padding: 10px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        ➕ Créer un employé
    </a>
</section>
<section>
<a href="dashboard.php">📊 Voir le Dashboard</a>
              </section>


              <section>
    <h2>Gestion des utilisateurs 🚦</h2>

    <?php
    // Récupérer tous les utilisateurs
    $stmt = $pdo->query("SELECT * FROM users ORDER BY pseudo");
    $users = $stmt->fetchAll();

    // Afficher chaque utilisateur avec bouton
    foreach ($users as $user) {
        echo "<p>" . htmlspecialchars($user['pseudo']) . " (" . htmlspecialchars($user['role']) . ")";

        if ($user['actif'] == 1) {
            echo " - <a href='suspendre-user.php?id=" . $user['id'] . "&action=suspendre' style='color:red;'>Suspendre</a>";
        } else {
            echo " - <a href='suspendre-user.php?id=" . $user['id'] . "&action=reactiver' style='color:green;'>Réactiver</a>";
        }

        echo "</p>";
    }
    ?>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
