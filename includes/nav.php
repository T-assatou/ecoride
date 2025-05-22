<?php
// ============================
// Fichier : includes/nav.php
// Rôle : Menu principal du site (sans session_start ici !)
// ============================
?>

<nav>
    <ul>
        <!-- Lien toujours visible -->
        <li><a href="/index.php">Accueil</a></li>
        <li><a href="/pages/search.php">Covoiturages</a></li>
        <li><a href="/pages/contact.php">Contact</a></li>

        <!-- Si l'utilisateur est connecté -->
        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- Lien "Mon espace" selon le rôle -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="/pages/admin-control.php">Mon espace</a></li>
            <?php elseif ($_SESSION['role'] === 'employe'): ?>
                <li><a href="/pages/employe-space.php">Mon espace</a></li>
            <?php else: ?>
                <li><a href="/pages/user-space.php">Mon espace</a></li>
            <?php endif; ?>

            <!-- Lien Déconnexion -->
            <li><a href="/controllers/logout.php">Déconnexion</a></li>

        <?php else: ?>
            <!-- Si NON connecté : lien Connexion -->
            <li><a href="/pages/login.php">Connexion</a></li>
        <?php endif; ?>
    </ul>
</nav>