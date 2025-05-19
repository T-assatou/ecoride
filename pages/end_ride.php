<?php
// ============================
// Fichier : pages/end_ride.php
// Rôle : Terminer un trajet et notifier les passagers
// ============================

require_once('../models/db.php');
session_start();

$ride_id = $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ride_id) exit("❌ Trajet non spécifié.");

// Vérifie que le trajet appartient à ce chauffeur
$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) exit("❌ Accès interdit.");

// ✅ Marquer comme "terminé"
$pdo->prepare("UPDATE rides SET statut = 'terminé' WHERE id = :id")
    ->execute([':id' => $ride_id]);

// ✅ Récupérer les emails des participants
$stmt = $pdo->prepare("SELECT u.email, u.pseudo FROM participants p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.ride_id = :ride_id");
$stmt->execute([':ride_id' => $ride_id]);
$participants = $stmt->fetchAll();

foreach ($participants as $p) {
    $to = $p['email'];
    $subject = "EcoRide - Validation de votre trajet";
    $message = "Bonjour {$p['pseudo']},\n\nLe trajet auquel vous avez participé est terminé.\nMerci de vous connecter à EcoRide pour valider si tout s'est bien passé.\n\nhttps://votre-site/ecoride/pages/user-space.php";
    $headers = "From: contact@ecoride.fr";

    // Envoi de mail (si config active)
    mail($to, $subject, $message, $headers);
}

$_SESSION['message'] = "📬 Trajet terminé. Les passagers ont été notifiés.";
header("Location: user-space.php");
exit;
