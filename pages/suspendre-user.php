<?php
require_once('../models/db.php');
session_start();

// Vérifie que c'est bien un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

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

header("Location: admin-control.php");
exit;