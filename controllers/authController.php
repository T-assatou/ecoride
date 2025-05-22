<?php
// ============================
// Fichier : controllers/authController.php
// Rôle : Authentifie l’utilisateur avec email + mot de passe sécurisé
// Redirige selon le rôle (admin, employé, utilisateur)
// Gère aussi la redirection automatique vers participate.php si nécessaire (US6)
// ============================

// 🔗 Connexion à la base de données
require_once('../models/db.php');

// ✅ Démarre la session si ce n’est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Vérifie que le formulaire a bien été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 📨 Récupération des données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // 🔁 Récupération de paramètres supplémentaires pour redirection personnalisée (US6)
    $redirect = $_POST['redirect'] ?? '';
    $ride_id = $_POST['ride_id'] ?? '';

    // ✅ Vérifie que les champs email et mot de passe ne sont pas vides
    if (!empty($email) && !empty($password)) {

        // 🔍 Recherche de l’utilisateur dans la base via l’email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // ✅ Si un utilisateur est trouvé
        if ($user) {

            // 🔐 Vérifie que le mot de passe est correct
            if (password_verify($password, $user['password'])) {

                // ⛔ Vérifie que le compte est actif
                if ($user['actif'] == 1) {

                    // 💾 Stocke les données de l’utilisateur dans la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['pseudo'] = $user['pseudo'];
                    $_SESSION['role'] = $user['role'];

                    // 🔁 Cas spécial : rediriger vers participate.php après login (US6)
                    if ($redirect === 'participate.php' && !empty($ride_id)) {
                        header("Location: ../pages/participate.php?ride_id=" . urlencode($ride_id));
                        exit;
                    }

                    // 🔁 Sinon : redirection normale selon le rôle
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: ../pages/admin-control.php");
                            break;
                        case 'employe':
                            header("Location: ../pages/employe-space.php");
                            break;
                        default:
                            header("Location: ../pages/user-space.php");
                    }
                    exit;

                } else {
                    // ⚠️ Compte désactivé
                    echo "Votre compte est suspendu. Veuillez contacter un administrateur.";
                }

            } else {
                // ❌ Mauvais mot de passe
                echo " Mot de passe incorrect.";
            }

        } else {
            // ❌ Email introuvable
            echo "Email inconnu.";
        }

    } else {
        // ⚠️ Champs manquants
        echo "Veuillez remplir tous les champs.";
    }
}
?>