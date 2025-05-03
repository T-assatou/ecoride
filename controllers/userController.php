<?php
// ============================
// Fichier : controllers/userController.php
// Rôle : Traite les données du formulaire d’inscription
// Enregistre un nouvel utilisateur dans la base de données
// ============================

// Connexion à la base
require_once('../models/db.php');

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère les champs du formulaire
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifie que tous les champs sont remplis (sécurité minimale)
    if (!empty($pseudo) && !empty($email) && !empty($password)) {

        // Hachage du mot de passe (sécurité obligatoire)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Préparation de la requête SQL
        $sql = "INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)";
        $stmt = $pdo->prepare($sql);

        // Exécution avec les données sécurisées
        try {
            $stmt->execute([
                ':pseudo' => $pseudo,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            // Redirection vers la page de connexion avec message de succès
            header("Location: login.php?success=1");
            exit;

        } catch (PDOException $e) {
            // Affiche une erreur si l’email est déjà pris
            echo "Erreur : " . $e->getMessage();
        }

    } else {
        echo "Tous les champs sont obligatoires.";
    }
}
?>