<?php
// ============================
// Fichier : cancel_participation.php
// Rôle : Annuler la participation à un covoiturage
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que l’utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérifie que ride_id est bien reçu
$ride_id = $_POST['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($ride_id) {
    // Supprimer la participation
    $delete = $pdo->prepare("DELETE FROM participants WHERE user_id = :user_id AND ride_id = :ride_id");
    $delete->execute([
        ':user_id' => $user_id,
        ':ride_id' => $ride_id
    ]);

    // Remettre 1 place dans le trajet
    $update = $pdo->prepare("UPDATE rides SET places = places + 1 WHERE id = :ride_id");
    $update->execute([':ride_id' => $ride_id]);
}

// Redirection
header("Location: user-space.php");
exit;