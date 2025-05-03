<?php
// ============================
// Fichier : controllers/auth.php
// Rôle : Vérifie que l’utilisateur est connecté ET actif
// ============================

session_start(); // Démarre la session si pas déjà démarrée

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Pas connecté ➔ redirige vers login
    header("Location: ../pages/login.php");
    exit;
}

// Connexion à la BDD pour vérifier actif
require_once('../models/db.php');

$stmt = $pdo->prepare("SELECT actif FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['actif'] == 0) {
    // Utilisateur suspendu ➔ déconnecte et redirige
    session_destroy();
    header("Location: ../pages/login.php?message=compte_suspendu");
    exit;
}
?>