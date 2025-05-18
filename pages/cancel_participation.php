<?php
// ============================
// Fichier : cancel_participation.php
// Rôle : Permet à un utilisateur d’annuler sa participation à un covoiturage
// ============================

require_once('../models/db.php'); // Connexion à la base de données
session_start(); // Démarre la session

// ✅ Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Récupère l'ID du trajet depuis le formulaire
$ride_id = $_POST['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($ride_id) {
    // ❌ Supprime la participation de la base de données
    $stmt = $pdo->prepare("DELETE FROM participants WHERE user_id = :user_id AND ride_id = :ride_id");
    $stmt->execute([
        ':user_id' => $user_id,
        ':ride_id' => $ride_id
    ]);

    // ➕ Remet une place disponible dans le trajet
    $update = $pdo->prepare("UPDATE rides SET places = places + 1 WHERE id = :ride_id");
    $update->execute([':ride_id' => $ride_id]);

    // ✅ Message de confirmation stocké en session
    $_SESSION['message'] = "❌ Participation annulée avec succès.";
}

// 🔁 Redirige vers l’espace personnel après l’action
header("Location: user-space.php");
exit;
?>