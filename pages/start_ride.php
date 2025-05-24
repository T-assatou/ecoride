<?php
require_once('../models/db.php');
session_start();

$ride_id = $_POST['ride_id'] ?? $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ride_id) exit("Trajet non spécifié.");

$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) exit(" Accès non autorisé.");

$pdo->prepare("UPDATE rides SET statut = 'en cours' WHERE id = :id")
    ->execute([':id' => $ride_id]);

header("Location: user-space.php");
exit;