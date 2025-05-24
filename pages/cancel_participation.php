<?php
// ============================
// Fichier : cancel_participation.php
// Rôle : Permet à un utilisateur d’annuler sa participation à un covoiturage
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ride_id = $_POST['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($ride_id) {
    //  Récupère le prix du trajet
    $stmt = $pdo->prepare("SELECT prix FROM rides WHERE id = :ride_id");
    $stmt->execute([':ride_id' => $ride_id]);
    $ride = $stmt->fetch();

    if ($ride) {
        $prix = $ride['prix'];

        //  Rembourse le passager
        $pdo->prepare("UPDATE users SET credits = credits + :prix WHERE id = :user_id")
            ->execute([':prix' => $prix, ':user_id' => $user_id]);

        //  Supprime la participation
        $stmt = $pdo->prepare("DELETE FROM participants WHERE user_id = :user_id AND ride_id = :ride_id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':ride_id' => $ride_id
        ]);

        // Remet une place dans le trajet
        $pdo->prepare("UPDATE rides SET places = places + 1 WHERE id = :ride_id")
            ->execute([':ride_id' => $ride_id]);

        $_SESSION['message'] = "❌ Participation annulée. Crédit remboursé.";
    }
}

header("Location: user-space.php");
exit;
?>