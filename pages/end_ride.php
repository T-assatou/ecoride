<?php
// ============================
// Fichier : pages/end_ride.php
// Rôle : Terminer un trajet et notifier les participants par mail
// ============================

require_once('../models/db.php');
require_once('../controllers/mail.php'); // 📩 PHPMailer via Mailjet
session_start();

// ✅ Vérifie l'identité de l'utilisateur connecté
$ride_id = $_POST['ride_id'] ?? $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$ride_id || !$user_id) {
    exit("❌ Trajet ou utilisateur non spécifié.");
}

// ✅ Vérifie que l'utilisateur est bien le créateur du trajet
$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = :ride_id AND user_id = :user_id");
$stmt->execute([':ride_id' => $ride_id, ':user_id' => $user_id]);
$ride = $stmt->fetch();

if (!$ride) {
    exit("❌ Accès interdit ou trajet introuvable.");
}

// ✅ Met à jour le statut du trajet à "terminé"
$pdo->prepare("UPDATE rides SET statut = 'terminé' WHERE id = :id")
    ->execute([':id' => $ride_id]);

// ✅ Récupère les participants inscrits à ce trajet
$stmt = $pdo->prepare("SELECT u.email, u.pseudo FROM participants p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.ride_id = :ride_id");
$stmt->execute([':ride_id' => $ride_id]);
$participants = $stmt->fetchAll();

// ✅ Envoie un email à chaque participant
foreach ($participants as $p) {
    $email = $p['email'];
    $pseudo = htmlspecialchars($p['pseudo']);
    $sujet = "EcoRide - Trajet terminé";

    $messageHTML = "
        <p>Bonjour $pseudo,</p>
        <p>Le trajet auquel vous avez participé est maintenant terminé.</p>
        <p>👉 Connectez-vous à votre espace pour laisser un avis ou signaler un litige si nécessaire :</p>
        <p><a href='https://ton-site.fly.dev/pages/user-space.php'>Accéder à mon espace</a></p>
        <p>Merci d'avoir utilisé EcoRide !</p>
    ";

    $messageTexte = "Bonjour $pseudo,\n\nLe trajet auquel vous avez participé est maintenant terminé.\nConnectez-vous à votre espace pour laisser un avis ou signaler un litige.\n\nhttps://ton-site.fly.dev/pages/user-space.php";

    envoyerMail($email, $sujet, $messageHTML, $messageTexte);
}

// ✅ Message de confirmation
$_SESSION['message'] = "📬 Trajet terminé. Les passagers ont été notifiés par email.";
header("Location: user-space.php");
exit;