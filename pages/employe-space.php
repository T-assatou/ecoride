<?php
// ============================
// Fichier : pages/employe-space.php
// Rôle : Espace employé - validation des avis & gestion des litiges
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie que l'utilisateur est un employé
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    echo "Accès réservé aux employés.";
    exit;
}

// ============================
// Récupération des avis non validés
// ============================
$avisStmt = $pdo->query("SELECT avis.*, u.pseudo AS auteur FROM avis 
                         INNER JOIN users u ON avis.auteur_id = u.id 
                         WHERE valide = 0 ORDER BY created_at DESC");
$avis = $avisStmt->fetchAll();

// ============================
// Récupération des litiges signalés
// ============================
$litigeStmt = $pdo->query("
    SELECT 
        litiges.*,
        u1.pseudo AS passager, u1.email AS email_passager,
        u2.pseudo AS chauffeur, u2.email AS email_chauffeur,
        rides.depart, rides.arrivee, rides.date_depart, rides.date_arrivee
    FROM litiges
    INNER JOIN users u1 ON litiges.passager_id = u1.id
    INNER JOIN users u2 ON litiges.chauffeur_id = u2.id
    INNER JOIN rides ON litiges.ride_id = rides.id
    ORDER BY litiges.created_at DESC
");
$litiges = $litigeStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace employé - EcoRide</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Espace Employé</h1>
</header>

<main>

<!-- ✅ Section : Validation des avis -->
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

<!-- ✅ Section : Litiges en cours -->
<section>
    <h2>Litiges en cours ⚠️</h2>

    <?php if (empty($litiges)) : ?>
        <p>Aucun litige signalé.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($litiges as $l) : ?>
                <li>
                    <strong>Trajet #<?= $l['ride_id'] ?></strong><br>
                    <strong>Passager :</strong> <?= htmlspecialchars($l['passager']) ?> – <?= htmlspecialchars($l['email_passager']) ?><br>
                    <strong>Chauffeur :</strong> <?= htmlspecialchars($l['chauffeur']) ?> – <?= htmlspecialchars($l['email_chauffeur']) ?><br>
                    <strong>Trajet :</strong> <?= $l['depart'] ?> → <?= $l['arrivee'] ?><br>
                    <strong>Dates :</strong> <?= $l['date_depart'] ?> → <?= $l['date_arrivee'] ?><br>
                    <strong>Commentaire :</strong><br>
                    <em><?= nl2br(htmlspecialchars($l['commentaire'])) ?></em><br>
                    <small>Déclaré le : <?= $l['created_at'] ?></small>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
