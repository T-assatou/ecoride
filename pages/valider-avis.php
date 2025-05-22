<?php
// ============================
// US12 - Validation avis

// Fichier : pages/valider-avis.php
// Rôle : Traite la validation ou le refus d'un avis par un employé
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérification du rôle : seuls les employés peuvent valider/refuser
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    echo "Accès refusé.";
    exit;
}

// ✅ Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avis_id = $_POST['avis_id'] ?? null;           // ID de l'avis ciblé
    $action = $_POST['action'] ?? '';               // Action : valider ou refuser

    // ✅ Vérifie que l'avis existe et que l'action est autorisée
    if ($avis_id && in_array($action, ['valider', 'refuser'])) {
        
        if ($action === 'valider') {
            // 🟢 Met à jour l'avis comme "valide"
            $stmt = $pdo->prepare("UPDATE avis SET valide = 1 WHERE id = :id");
            $stmt->execute([':id' => $avis_id]);
        } elseif ($action === 'refuser') {
            // 🔴 Supprime l'avis de la base (refusé)
            $stmt = $pdo->prepare("DELETE FROM avis WHERE id = :id");
            $stmt->execute([':id' => $avis_id]);
        }
    }
}

// ✅ Redirection automatique vers la page employé
header("Location: employe-space.php");
exit;


