<?php
// ============================
// Fichier : pages/details.php
// Rôle : Afficher les détails d’un trajet sélectionné
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que l'ID du trajet est fourni
$trajet_id = $_GET['ride_id'] ?? null;
if (!$trajet_id) {
    echo "Aucun identifiant de trajet fourni.";
    exit;
}

// Récupérer les infos du trajet + conducteur + véhicule
$sql = "SELECT rides.*, users.pseudo AS conducteur, users.id AS conducteur_id,
               vehicles.marque, vehicles.modele, vehicles.energie
        FROM rides
        INNER JOIN users ON rides.user_id = users.id
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        WHERE rides.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $trajet_id]);
$trajet = $stmt->fetch();

if (!$trajet) {
    echo "Trajet introuvable.";
    exit;
}

// Récupération des avis sur le conducteur
$avis_stmt = $pdo->prepare("SELECT contenu FROM avis WHERE chauffeur_id = :id AND valide = 1");
$avis_stmt->execute([':id' => $trajet['conducteur_id']]);
$avis = $avis_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du covoiturage - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Détails du covoiturage</h1>
</header>

<main>
<section class="trajet-detail">
    <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Photo conducteur" width="100">
    <h2><?= htmlspecialchars($trajet['conducteur']) ?> - ⭐ 4.5★</h2>

    <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?></p>
    <p><strong>Heure de départ :</strong> <?= date('H:i', strtotime($trajet['date_depart'])) ?></p>
    <p><strong>Heure d’arrivée :</strong> <?= date('H:i', strtotime($trajet['date_arrivee'])) ?></p>
    <p><strong>Places restantes :</strong> <?= $trajet['places'] ?></p>
    <p><strong>Prix :</strong> <?= $trajet['prix'] ?> €</p>
    <p><strong>Véhicule :</strong> <?= htmlspecialchars($trajet['marque']) ?> <?= htmlspecialchars($trajet['modele']) ?> (<?= $trajet['energie'] ?>)</p>

    <?php if ($trajet['energie'] === 'électrique'): ?>
        <p><strong>🌱 Voyage écologique</strong></p>
    <?php endif; ?>

    <!-- Préférences du conducteur (simulées pour débutant) -->
    <h3>Préférences du conducteur :</h3>
    <ul>
        <li> Fumeur non autorisé</li>
        <li>Animaux autorisés</li>
        <li>Pas de gros bagages</li>
    </ul>

    <!-- Avis du conducteur -->
    <h3>Avis des passagers :</h3>
    <?php if (!empty($avis)): ?>
        <?php foreach ($avis as $a): ?>
            <blockquote>
                <p><?= htmlspecialchars($a['contenu']) ?></p>
            </blockquote>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun avis disponible.</p>
    <?php endif; ?>

    <!-- ✅ Bouton Participer -->
    <h3>Souhaitez-vous participer à ce trajet ?</h3>
    <?php if (isset($_SESSION['user_id']) && $trajet['places'] > 0): ?>
        <a href="participate.php?ride_id=<?= $trajet['id'] ?>" class="btn-green">✅ Participer</a>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
        <p><a href="login.php" class="btn-blue">🔐 Connectez-vous pour participer</a></p>
    <?php else: ?>
        <p> Ce trajet est complet.</p>
    <?php endif; ?>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>