<?php
require_once('../models/db.php');
session_start();

$ride_id = $_POST['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];
$validation = $_POST['validation'] ?? '';

if (!$ride_id || !$user_id) exit("Erreur");

if ($validation === 'oui') {
    // ✅ Ajoute 1 crédit au chauffeur
    $stmt = $pdo->prepare("SELECT user_id FROM rides WHERE id = :ride_id");
    $stmt->execute([':ride_id' => $ride_id]);
    $ride = $stmt->fetch();
    
    if ($ride) {
        $pdo->prepare("UPDATE users SET credits = credits + 1 WHERE id = :id")
            ->execute([':id' => $ride['user_id']]);
    }

    $_SESSION['message'] = "✅ Trajet validé.";
} else {
    $_SESSION['message'] = "⚠️ Merci de remplir un litige.";
    header("Location: submit-litige.php?ride_id=$ride_id");
    exit;
}

header("Location: user-space.php");
exit;