<?php
// ============================
//US13
// Fichier : pages/suspendre-user.php
// Rôle : Suspendre ou réactiver un compte utilisateur
// ============================

require_once('../models/db.php');
session_start();

// Vérifier que l'utilisateur est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Traitement du formulaire
if (isset($_GET['id']) && isset($_GET['action'])) {
    $userId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'suspendre') {
        $stmt = $pdo->prepare("UPDATE users SET actif = 0 WHERE id = :id");
        $stmt->execute([':id' => $userId]);
    } elseif ($action === 'reactiver') {
        $stmt = $pdo->prepare("UPDATE users SET actif = 1 WHERE id = :id");
        $stmt->execute([':id' => $userId]);
    }
}

// Retour à la liste admin
header("Location: admin-control.php");
exit;

