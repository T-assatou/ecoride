<?php
// ============================
// Fichier : submit-litige.php
// Rôle : Signaler un litige concernant un covoiturage
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// ✅ Vérifie que les champs nécessaires ont été envoyés
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['ride_id'], $_POST['chauffeur_id'], $_POST['commentaire'])
) {
    // Récupération et nettoyage des données
    $ride_id = $_POST['ride_id'];
    $chauffeur_id = $_POST['chauffeur_id'];
    $passager_id = $_SESSION['user_id'];
    $commentaire = trim($_POST['commentaire']);

    // ✅ Insertion dans la table des litiges
    $stmt = $pdo->prepare("INSERT INTO litiges (ride_id, passager_id, chauffeur_id, commentaire, created_at)
                           VALUES (:ride_id, :passager_id, :chauffeur_id, :commentaire, NOW())");
    $stmt->execute([
        ':ride_id' => $ride_id,
        ':passager_id' => $passager_id,
        ':chauffeur_id' => $chauffeur_id,
        ':commentaire' => $commentaire
    ]);

    // ✅ Message de confirmation affiché à l'utilisateur
    $_SESSION['message'] = "⚠️ Litige signalé. Un employé va l’examiner.";
    header("Location: user-space.php");
    exit;

} else {
    // ❌ Si données manquantes, on renvoie un message d'erreur
    $_SESSION['error'] = "❌ Erreur : données manquantes pour signaler le litige.";
    header("Location: user-space.php");
    exit;
}

/*Ce que tu peux dire au jury :

“Ce fichier permet à un utilisateur connecté de signaler un litige lié à un trajet. Les informations sont enregistrées dans une table litiges, avec les identifiants du passager, du chauffeur, du trajet concerné, et le commentaire. Une fois enregistré, un message de confirmation s’affiche. Ce litige sera ensuite visible dans l’espace employé.”*/