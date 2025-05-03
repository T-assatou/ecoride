<?php
// ============================
// Fichier : models/db.php
// Rôle : Connexion à la base de données MySQL via PDO
// Version simple, débutant, claire
// ============================

// Infos de connexion à ta base locale (MAMP)
$host = 'localhost';
$dbname = 'ecoride';
$user = '***REMOVED***';
$password = '***REMOVED***'; // Mot de passe par défaut dans MAMP

try {
    // Connexion PDO à MySQL (charset = UTF8 pour les accents)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

    // On demande à PDO de lancer une erreur si problème (important pour débugger)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Si erreur de connexion : afficher le message d'erreur et arrêter le code
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>