<?php
// ============================
// Fichier : includes/nav.php
// Rôle : Menu principal du site
// ============================

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarre la session si ce n'est pas déjà fait
}
?>

<nav>
    <ul>
        <!-- Lien toujours visible -->
        <li><a href="/ecoride/index.php">Accueil</a></li>
        <li><a href="/ecoride/pages/search.php">Covoiturages</a></li>
        <li><a href="/ecoride/pages/contact.php">Contact</a></li>

        <!-- Si l'utilisateur est connecté -->
        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- Lien "Mon espace" selon le rôle -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="/ecoride/pages/admin-control.php">Mon espace</a></li>
            <?php elseif ($_SESSION['role'] === 'employe'): ?>
                <li><a href="/ecoride/pages/employe-space.php">Mon espace</a></li>
            <?php else: ?>
                <li><a href="/ecoride/pages/user-space.php">Mon espace</a></li>
            <?php endif; ?>

            <!-- Lien Déconnexion -->
            <li><a href="/ecoride/controllers/logout.php">Déconnexion</a></li>

        <?php else: ?>
            <!-- Si NON connecté : lien Connexion -->
            <li><a href="/ecoride/pages/login.php">Connexion</a></li>
        <?php endif; ?>
    </ul>
</nav>