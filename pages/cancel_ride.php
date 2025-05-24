<?php
// ============================
// Fichier : pages/cancel_ride.php
// Rôle : Permet à un chauffeur d’annuler un trajet qu’il a créé
// ============================

require_once('../models/db.php');
require_once('../controllers/mail.php'); // Pour PHPMailer
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

// ✅ Vérifie que ce trajet appartient bien au chauffeur connecté
$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) {
    echo "❌ Trajet introuvable ou non autorisé.";
    exit;
}

// ✅ Récupère les passagers du trajet
$stmt = $pdo->prepare("SELECT u.email FROM participants p
                       JOIN users u ON p.user_id = u.id
                       WHERE p.ride_id = :ride_id");
$stmt->execute([':ride_id' => $ride_id]);
$emails = $stmt->fetchAll();

// ✅ Supprime le trajet
$pdo->prepare("DELETE FROM rides WHERE id = :ride_id")->execute([':ride_id' => $ride_id]);

// ✅ Rembourse les 2 crédits au chauffeur
$pdo->prepare("UPDATE users SET credits = credits + 2 WHERE id = :id")->execute([':id' => $user_id]);

// ✅ Envoie un e-mail à chaque passager
foreach ($emails as $email) {
    $destinataire = $email['email'];
    $sujet = "❌ Trajet annulé";
    $contenu = "<p>Bonjour,</p><p>Le chauffeur a annulé le trajet auquel vous étiez inscrit.</p><p>Nous vous prions de nous excuser pour la gêne occasionnée.</p>";
    envoyerMail($destinataire, $sujet, $contenu);
}

// ✅ Message de confirmation
$_SESSION['message'] = "❌ Trajet annulé. 2 crédits remboursés. Les passagers ont été notifiés.";
header("Location: user-space.php");
exit;