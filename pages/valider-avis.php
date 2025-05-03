<?php
// ============================
// Fichier : pages/valider-avis.php
// Rôle : Traite la validation ou le refus d'un avis par un employé
// ============================

require_once('../models/db.php');
session_start();

// Vérifier que l'utilisateur est un employé
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    echo "Accès refusé.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avis_id = $_POST['avis_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($avis_id && in_array($action, ['valider', 'refuser'])) {
        if ($action === 'valider') {
            $stmt = $pdo->prepare("UPDATE avis SET valide = 1 WHERE id = :id");
            $stmt->execute([':id' => $avis_id]);
        } elseif ($action === 'refuser') {
            $stmt = $pdo->prepare("DELETE FROM avis WHERE id = :id");
            $stmt->execute([':id' => $avis_id]);
        }
    }
}

// Redirection vers l'espace employé
header("Location: employe-space.php");
exit;
