<?php
// Connexion à la base
require_once('models/db.php');

// Récupérer tous les utilisateurs
$sql = "SELECT id, pseudo, email, password, credits, role FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();

echo "<h1>Liste des utilisateurs dans la base :</h1>";

foreach ($users as $user) {
    echo "<p>";
    echo "ID : " . htmlspecialchars($user['id']) . "<br>";
    echo "Pseudo : " . htmlspecialchars($user['pseudo']) . "<br>";
    echo "Email : " . htmlspecialchars($user['email']) . "<br>";
    echo "Mot de passe (haché) : " . htmlspecialchars($user['password']) . "<br>";
    echo "Crédits : " . htmlspecialchars($user['credits']) . "<br>";
    echo "Rôle : " . htmlspecialchars($user['role']) . "<br>";
    echo "</p><hr>";
}
?>