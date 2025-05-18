<?php
// ============================
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


/*Ce fichier permet à l’employé de traiter les avis envoyés par les utilisateurs.
Si l’avis est validé, il est mis à jour avec un champ valide = 1.
Si l’avis est refusé, il est supprimé de la base.
L’action est sécurisée, limitée au rôle employe, et redirige automatiquement vers la bonne page.”*/