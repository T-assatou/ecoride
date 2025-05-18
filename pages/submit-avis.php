<?php
// ============================
// Fichier : submit-avis.php
// Rôle : Enregistrer un avis laissé par un passager
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// ✅ Vérifie que les données nécessaires ont été envoyées
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chauffeur_id'], $_POST['contenu'])) {
    
    // Récupération des données
    $chauffeur_id = $_POST['chauffeur_id'];
    $auteur_id = $_SESSION['user_id'];
    $contenu = trim($_POST['contenu']); // Nettoyage de l'avis

    // ✅ Insertion dans la table "avis" (valide = 0 par défaut)
    $stmt = $pdo->prepare("INSERT INTO avis (contenu, chauffeur_id, auteur_id, valide, created_at)
                           VALUES (:contenu, :chauffeur_id, :auteur_id, 0, NOW())");
    $stmt->execute([
        ':contenu' => $contenu,
        ':chauffeur_id' => $chauffeur_id,
        ':auteur_id' => $auteur_id
    ]);

    // ✅ Message de confirmation stocké temporairement
    $_SESSION['message'] = "✅ Votre avis a été enregistré et sera validé par un employé.";
    header("Location: user-space.php");
    exit;

} else {
    // ⚠️ Données manquantes → message d'erreur + redirection
    $_SESSION['error'] = "❌ Erreur : les données du formulaire sont incomplètes.";
    header("Location: user-space.php");
    exit;
}

/*✅ Ce que tu peux dire à l’oral ou écrire dans ton rapport ECF

“Ce fichier permet à un utilisateur connecté de laisser un avis sur un chauffeur après un trajet. L’avis est enregistré dans la base de données, mais il n’est pas affiché tant qu’il n’est pas validé par un employé. L’utilisateur reçoit un message de confirmation grâce à une variable $_SESSION.”*/