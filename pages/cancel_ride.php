<?php
// ============================
// Fichier : pages/cancel_ride.php
// Rôle : Permet à un chauffeur d’annuler un trajet qu’il a créé
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie que l’utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Vérifie que le trajet appartient bien à l'utilisateur
$ride_id = $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ride_id) {
    echo "❌ Aucun trajet sélectionné.";
    exit;
}

// Vérifie que ce trajet appartient bien à ce chauffeur
$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) {
    echo "❌ Trajet introuvable ou non autorisé.";
    exit;
}

// Supprimer le trajet
$pdo->prepare("DELETE FROM rides WHERE id = :ride_id")->execute([':ride_id' => $ride_id]);

// Rembourse les 2 crédits au chauffeur
$pdo->prepare("UPDATE users SET credits = credits + 2 WHERE id = :id")->execute([':id' => $user_id]);

// Simule un envoi d'email aux passagers
/*
$stmt = $pdo->prepare("SELECT u.email FROM participants p
                       JOIN users u ON p.user_id = u.id
                       WHERE p.ride_id = :ride_id");
$stmt->execute([':ride_id' => $ride_id]);
$emails = $stmt->fetchAll();

foreach ($emails as $email) {
    // mail($email['email'], "Trajet annulé", "Le conducteur a annulé le trajet.");
}
*/

$_SESSION['message'] = "❌ Trajet annulé. 2 crédits remboursés.";
header("Location: user-space.php");
exit;