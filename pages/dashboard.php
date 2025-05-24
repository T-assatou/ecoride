<?php
require_once('../models/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

$ridesData = $pdo->query("SELECT DATE(date_depart) as day, COUNT(*) as total FROM rides GROUP BY day ORDER BY day")->fetchAll();
$creditsData = $pdo->query("SELECT DATE(date_depart) as day, COUNT(*) * 2 as credits FROM rides GROUP BY day ORDER BY day")->fetchAll();
$totalCredits = $pdo->query("SELECT COUNT(*) * 2 as total FROM rides")->fetch(PDO::FETCH_ASSOC)['total'];

$labels = [];
$ridesCount = [];
$creditsCount = [];

foreach ($ridesData as $row) {
    $labels[] = $row['day'];
    $ridesCount[] = $row['total'];
}
foreach ($creditsData as $row) {
    $creditsCount[] = $row['credits'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - EcoRide</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/Assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Dashboard Administrateur</h1>
</header>

<p class="text-center italic">Analyse quotidienne de l’activité EcoRide</p>

<main class="dashboard-container">
    <section>
        <h2> Nombre de covoiturages par jour</h2>
        <canvas id="ridesChart"></canvas>
    </section>

    <hr>

    <section>
        <h2> Crédits gagnés par jour</h2>
        <canvas id="creditsChart"></canvas>

        <div class="credits-total-box">
            <strong> Total des crédits gagnés par la plateforme :</strong><br>
            <span class="credits-total-number"><?= $totalCredits ?> crédits</span>
        </div>
    </section>

    <section class="text-center">
        <a href="admin-control.php" class="btn-green">← Retour à l’espace admin</a>
    </section>
</main>

<script>
const labels = <?= json_encode($labels); ?>;
const ridesCount = <?= json_encode($ridesCount); ?>;
const creditsCount = <?= json_encode($creditsCount); ?>;

new Chart(document.getElementById('ridesChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Trajets créés',
            data: ridesCount,
            borderWidth: 2,
            fill: false,
            tension: 0.3
        }]
    }
});

new Chart(document.getElementById('creditsChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Crédits gagnés',
            data: creditsCount,
            backgroundColor: 'rgba(8, 243, 44, 0.5)',
            borderWidth: 1
        }]
    }
});
</script>

<?php include('../includes/footer.php'); ?>
</body>
</html>