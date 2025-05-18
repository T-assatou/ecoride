<?php
// ============================
// Fichier : controllers/userController.php
// Rôle : Gérer l'inscription utilisateur
// ============================

require_once('../models/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Vérifie que tous les champs sont remplis
    if (empty($pseudo) || empty($email) || empty($password)) {
        $_SESSION['message'] = "⚠️ Tous les champs sont obligatoires.";
        header("Location: ../pages/register.php");
        exit;
    }

    // 🔐 Vérification de la complexité du mot de passe
    $longueur = strlen($password);
    $maj = preg_match('@[A-Z]@', $password);
    $min = preg_match('@[a-z]@', $password);
    $chiffre = preg_match('@[0-9]@', $password);

    if ($longueur < 9 || !$maj || !$min || !$chiffre) {
        $_SESSION['message'] = "❌ Le mot de passe doit contenir au moins 9 caractères, une majuscule, une minuscule et un chiffre.";
        header("Location: ../pages/register.php");
        exit;
    }

    // Vérifie si l'email est déjà utilisé
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        $_SESSION['message'] = "🚫 Cet email est déjà utilisé.";
        header("Location: ../pages/register.php");
        exit;
    }

    // Hashage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insertion dans la BDD avec 20 crédits offerts
    $stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password, credits, role, actif)
                           VALUES (:pseudo, :email, :password, 20, 'passager', 1)");

    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);

    $_SESSION['message'] = "✅ Compte créé avec succès. Vous pouvez vous connecter.";
    header("Location: ../pages/login.php");
    exit;
} else {
    header("Location: ../pages/register.php");
    exit;
}