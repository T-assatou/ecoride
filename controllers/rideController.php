<?php
// ============================
// Fichier : controllers/rideController.php
// Rôle : Traiter la création d’un trajet par un chauffeur
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie que l’utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// ✅ Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id'])) {
    $user_id = $_SESSION['user_id'];

    // 🔽 Données du formulaire
    $depart = trim($_POST['depart'] ?? '');
    $arrivee = trim($_POST['arrivee'] ?? '');
    $date_depart = $_POST['date_depart'] ?? '';
    $date_arrivee = $_POST['date_arrivee'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    $places = intval($_POST['places'] ?? 0);
    $vehicle_id = intval($_POST['vehicle_id']);

    // ✅ Enregistrement du trajet
    $stmt = $pdo->prepare("INSERT INTO rides (
        user_id, vehicle_id, depart, arrivee, date_depart, date_arrivee, prix, places
    ) VALUES (
        :user_id, :vehicle_id, :depart, :arrivee, :date_depart, :date_arrivee, :prix, :places
    )");

    $stmt->execute([
        ':user_id' => $user_id,
        ':vehicle_id' => $vehicle_id,
        ':depart' => $depart,
        ':arrivee' => $arrivee,
        ':date_depart' => $date_depart,
        ':date_arrivee' => $date_arrivee,
        ':prix' => $prix,
        ':places' => $places
    ]);

    // ✅ Soustraction de 2 crédits à l'utilisateur
    $creditStmt = $pdo->prepare("UPDATE users SET credits = credits - 2 WHERE id = :id");
    $creditStmt->execute([':id' => $user_id]);

    // ✅ Message de confirmation + redirection
    $_SESSION['message'] = "✅ Trajet créé avec succès. 2 crédits ont été déduits.";
    header("Location: ../pages/user-space.php");
    exit;

} else {
    echo "❌ Données invalides ou incomplètes.";
}