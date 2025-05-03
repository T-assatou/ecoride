<?php
// ============================
// Fichier : pages/dashboard.php
// Rôle : Dashboard admin avec graphiques statistiques
// ============================

require_once('../models/db.php');
session_start();

// Vérification : uniquement admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Récupérer les trajets créés par jour
$ridesData = $pdo->query("SELECT DATE(date_depart) as day, COUNT(*) as total FROM rides GROUP BY day ORDER BY day")->fetchAll();

// Récupérer les crédits gagnés par jour (2 crédits prélevés par trajet)
$creditsData = $pdo->query("SELECT DATE(date_depart) as day, SUM(2) as credits FROM rides GROUP BY day ORDER BY day")->fetchAll();

// Formatter les données pour Chart.js
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Dashboard Administrateur</h1>
</header>

<main>
<section>
    <h2>📈 Nombre de trajets par jour</h2>
    <canvas id="ridesChart"></canvas>
</section>

<hr>

<section>
    <h2>💰 Crédits gagnés par jour</h2>
    <canvas id="creditsChart"></canvas>
</section>
</main>

<script>
// Récupération des données PHP -> JS
const labels = <?php echo json_encode($labels); ?>;
const ridesCount = <?php echo json_encode($ridesCount); ?>;
const creditsCount = <?php echo json_encode($creditsCount); ?>;

// Premier graphique : trajets par jour
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

// Deuxième graphique : crédits par jour
new Chart(document.getElementById('creditsChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Crédits gagnés',
            data: creditsCount,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderWidth: 1
        }]
    }
});
</script>

<?php include('../includes/footer.php'); ?>

</body>
</html>
