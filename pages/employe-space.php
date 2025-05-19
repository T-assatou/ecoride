<?php
// ============================
// US12 - Page employé
// Fichier : pages/employe-space.php
// Rôle : Espace employé - validation des avis & gestion des litiges
// ============================

require_once('../models/db.php');
session_start();

// Vérification du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    echo "Accès réservé aux employés.";
    exit;
}

// Exemple : Récupérer les avis à valider (non validés)
$avisStmt = $pdo->query("SELECT * FROM avis WHERE valide = 0 ORDER BY created_at DESC");
$avis = $avisStmt->fetchAll();

// Exemple : Récupérer les trajets signalés (litiges)
$litigeStmt = $pdo->query("SELECT * FROM litiges ORDER BY created_at DESC");
$litiges = $litigeStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace employé - EcoRide</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Espace employé</h1>
</header>
<main>
<section>
    <h2>Validation des avis ❌/✅</h2>
    <?php if (empty($avis)) : ?>
        <p>Aucun avis en attente de validation.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($avis as $a) : ?>
                <li>
                    <strong><?= htmlspecialchars($a['auteur']) ?> :</strong> <?= htmlspecialchars($a['contenu']) ?><br>
                    <form method="post" action="valider-avis.php">
                        <input type="hidden" name="avis_id" value="<?= $a['id'] ?>">
                        <button name="action" value="valider">Valider</button>
                        <button name="action" value="refuser">Refuser</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<hr>

<section>
    <h2>Litiges en cours ⚠️</h2>
    <?php if (empty($litiges)) : ?>
        <p>Aucun litige signalé pour l'instant.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($litiges as $l) : ?>
                <li>
                    <strong>Trajet #<?= $l['ride_id'] ?></strong><br>
                    Passager : <?= htmlspecialchars($l['passager']) ?><br>
                    Chauffeur : <?= htmlspecialchars($l['chauffeur']) ?><br>
                    Détail : <?= htmlspecialchars($l['commentaire']) ?><br>
                    Date : <?= $l['created_at'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
