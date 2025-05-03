<?php
// ============================
// Fichier : controllers/authController.php
// Rôle : Authentifie l’utilisateur avec email + mot de passe sécurisé
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que le formulaire de connexion a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user) {

            // Vérification sécurisée du mot de passe
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['role'] = $user['role'];

                header("Location: ../pages/user-space.php");
                exit;
            } else {
                echo "Mot de passe incorrect.";
            }

        } else {
            echo "Email inconnu.";
        }

    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>
