<?php
// ============================
// FICHIER : controllers/auth.php
// ============================
// ROLE :
// Ce fichier est inclus en haut de toutes les pages qui nécessitent une protection par connexion.
// Il vérifie que l'utilisateur est bien connecté (session active) et que son compte est actif (non suspendu).
// Si ce n'est pas le cas, il est redirigé vers la page de connexion.
// ============================

session_start(); // Démarre la session

// Si l'utilisateur n'est pas connecté (pas de session user_id), on le redirige vers la page login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// Connexion à la base de données pour vérifier l'état du compte
require_once('../models/db.php');

// On récupère le champ 'actif' de l'utilisateur en base
$stmt = $pdo->prepare("SELECT actif FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Si l'utilisateur n'existe pas ou a un compte suspendu (actif = 0)
if (!$user || $user['actif'] == 0) {
    session_destroy(); // On détruit la session
    header("Location: ../pages/login.php?message=compte_suspendu");
    exit;
}