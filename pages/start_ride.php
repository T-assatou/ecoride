<?php
// ============================
// Fichier : pages/start_ride.php
// Rôle : Démarrer un trajet (chauffeur uniquement)
// ============================

require_once('../models/db.php');
session_start();

$ride_id = $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

// Vérifie que l'ID est fourni
if (!$ride_id) exit("❌ Trajet non spécifié.");

// Vérifie que le trajet appartient à ce chauffeur
$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) exit("❌ Accès interdit.");

// Marquer comme "en cours"
$pdo->prepare("UPDATE rides SET statut = 'en cours' WHERE id = :id")
    ->execute([':id' => $ride_id]);

$_SESSION['message'] = "✅ Trajet démarré avec succès.";
header("Location: user-space.php");
exit;
