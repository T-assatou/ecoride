<?php
// ============================
// Fichier : models/db.php
// Rôle : Connexion à la base de données Railway
// ============================

$host = '***REMOVED***';
$dbname = '***REMOVED***';
$username = '***REMOVED***';
$password = '***REMOVED***';
$port = 32252;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    // Active le mode erreur pour PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}