<?php
// ============================
// Fichier : pages/validate_ride.php
// Rôle : Permettre à un passager de valider ou signaler un trajet terminé
// ============================

require_once('../models/db.php');
session_start();

// ✅ Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ride_id = $_GET['ride_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$ride_id) {
    echo "Trajet non spécifié.";
    exit;
}

// ✅ Vérifie que ce passager a bien participé à ce trajet
$stmt = $pdo->prepare("SELECT * FROM participants WHERE user_id = :user_id AND ride_id = :ride_id");
$stmt->execute([':user_id' => $user_id, ':ride_id' => $ride_id]);
$participation = $stmt->fetch();

if (!$participation) {
    echo "Accès non autorisé.";
    exit;
}

// ✅ Récupère les infos sur le trajet (pour récupérer le chauffeur)
$stmt = $pdo->prepare("SELECT r.*, u.pseudo AS chauffeur_pseudo, u.id AS chauffeur_id
                       FROM rides r
                       JOIN users u ON r.user_id = u.id
                       WHERE r.id = :ride_id");
$stmt->execute([':ride_id' => $ride_id]);
$ride = $stmt->fetch();

if (!$ride) {
    echo "Trajet introuvable.";
    exit;
}

// ✅ Traitement si validation envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validation'])) {
    $validation = $_POST['validation'];

    if ($validation === 'oui') {
        // ➕ Ajoute 1 crédit au chauffeur
        $stmt = $pdo->prepare("UPDATE users SET credits = credits + 1 WHERE id = :id");
        $stmt->execute([':id' => $ride['chauffeur_id']]);

        $_SESSION['message'] = "✅ Merci ! Le trajet a été validé, le chauffeur a reçu 1 crédit.";
        header("Location: user-space.php");
        exit;

    } elseif ($validation === 'non') {
        // Redirection vers le formulaire de litige avec les données
        header("Location: submit-litige.php?ride_id=$ride_id&chauffeur_id=" . $ride['chauffeur_id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation du trajet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<main class="form-section">
    <h1>Validation du trajet</h1>
    <p><strong>Trajet :</strong> <?= htmlspecialchars($ride['depart']) ?> → <?= htmlspecialchars($ride['arrivee']) ?></p>
    <p><strong>Chauffeur :</strong> <?= htmlspecialchars($ride['chauffeur_pseudo']) ?></p>

    <form method="post">
        <p>Souhaitez-vous confirmer que le trajet s’est bien déroulé ?</p>
        <button type="submit" name="validation" value="oui" class="btn-green">Oui</button>
        <button type="submit" name="validation" value="non" class="btn-blue"> Non, signaler un problème</button>
    </form>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>