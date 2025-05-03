<?php
// ============================
// Fichier : controllers/logout.php
// Rôle : Déconnecte l’utilisateur en détruisant la session
// ============================

session_start();
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit complètement la session

// Redirection vers la page de connexion ou d'accueil
header("Location: ../pages/login.php");
exit;
?>
