<?php
// ============================
// Fichier : includes/nav.php
// Rôle : Menu principal du site
// Affiche "Déconnexion" uniquement si connecté
// ============================

session_start(); // Démarre la session pour voir si l'utilisateur est connecté
?>

<nav>
    <ul>
        <li><a href="/ecoride/index.php">Accueil</a></li>
        <li><a href="/ecoride/pages/search.php">Covoiturages</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Si l'utilisateur est connecté : on affiche Déconnexion -->
            <li><a href="/ecoride/controllers/logout.php">Déconnexion</a></li>
        <?php else: ?>
            <!-- Sinon, on affiche Connexion -->
            <li><a href="/ecoride/pages/login.php">Connexion</a></li>
        <?php endif; ?>

        <li><a href="/ecoride/pages/contact.php">Contact</a></li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li><a href="pages/admin-control.php">Admin</a></li>
<?php endif; ?>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'employe'): ?>
    <li><a href="/ecoride/pages/employe-space.php">Espace Employé</a></li>
<?php endif; ?>
    </ul>
</nav>
