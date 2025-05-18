<?php
// ============================
// Fichier : models/user.php
// Rôle : Fonctions liées aux utilisateurs
// ============================

// Récupère un utilisateur à partir de son email
function getUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

// Récupère tous les utilisateurs
function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY pseudo");
    return $stmt->fetchAll();
}

// Change le statut actif/suspendu
function updateUserStatus($pdo, $user_id, $actif) {
    $stmt = $pdo->prepare("UPDATE users SET actif = :actif WHERE id = :id");
    $stmt->execute([':actif' => $actif, ':id' => $user_id]);
}