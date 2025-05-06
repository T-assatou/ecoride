<?php
// ============================
// Fichier : controllers/authController.php
// Rôle : Authentifie l’utilisateur avec email + mot de passe sécurisé
// Redirige selon le rôle (admin, employé, utilisateur)
// ============================

require_once('../models/db.php'); // Connexion à la base de données
session_start(); // Démarre la session

// Vérifie que le formulaire de connexion a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère les champs envoyés depuis le formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifie que les champs ne sont pas vides
    if (!empty($email) && !empty($password)) {

        // Requête pour récupérer l'utilisateur correspondant à l'email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Si l'utilisateur existe
        if ($user) {

            // Vérifie si le mot de passe est correct (haché)
            if (password_verify($password, $user['password'])) {

                // Vérifie si le compte est actif
                if ($user['actif'] == 1) {

                    // Stocke les infos utiles en session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['pseudo'] = $user['pseudo'];
                    $_SESSION['role'] = $user['role'];

                    // Redirige selon le rôle
                    if ($user['role'] === 'admin') {
                        header("Location: ../pages/admin-control.php");
                    } elseif ($user['role'] === 'employe') {
                        header("Location: ../pages/employe-space.php");
                    } else {
                        header("Location: ../pages/user-space.php");
                    }
                    exit;

                } else {
                    echo "🚫 Votre compte a été suspendu. Veuillez contacter un administrateur.";
                }

            } else {
                echo "❌ Mot de passe incorrect.";
            }

        } else {
            echo "❌ Email inconnu.";
        }

    } else {
        echo "⚠️ Veuillez remplir tous les champs.";
    }
}
?>