<?php
require_once('models/db.php');

// ===========================================
// Fichier : insert-user.php
// Rôle : Insère un utilisateur avec mot de passe sécurisé
// ===========================================

// Préparation des données
$pseudo = 'TestUser';
$email = 'test@ecoride.fr';
$password = password_hash('123456', PASSWORD_DEFAULT); // Sécurisation du mot de passe
$credits = 20;
$role = 'utilisateur';

try {
    $sql = "INSERT INTO users (pseudo, email, password, credits, role) VALUES (:pseudo, :email, :password, :credits, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':password' => $password,
        ':credits' => $credits,
        ':role' => $role
    ]);

    echo "✅ Utilisateur TestUser inséré avec succès (mot de passe sécurisé).";
} catch (Exception $e) {
    echo "❌ Erreur lors de l'insertion : " . $e->getMessage();
}
?>
