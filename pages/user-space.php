<?php
// ============================
// Fichier : pages/user-space.php
// Rôle : Espace personnel de l'utilisateur connecté (trajets, rôles, véhicules...)
// ============================

require_once('../controllers/auth.php'); // Vérifie la connexion
require_once('../models/db.php'); // Connexion BDD

session_start();

// Message temporaire (ex : annulation participation)
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// ====================================
// Changement de rôle
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $newRole = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([':role' => $newRole, ':id' => $_SESSION['user_id']]);
    $_SESSION['role'] = $newRole;
    header("Location: user-space.php");
    exit;
}

// ====================================
// Ajout de véhicule
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicle'])) {
    $stmt = $pdo->prepare("INSERT INTO vehicles (user_id, plaque, modele, couleur, marque, energie)
        VALUES (:user_id, :plaque, :modele, :couleur, :marque, :energie)");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':plaque' => $_POST['plaque'],
        ':modele' => $_POST['modele'],
        ':couleur' => $_POST['couleur'],
        ':marque' => $_POST['marque'],
        ':energie' => $_POST['energie']
    ]);
    header("Location: user-space.php");
    exit;
}

// ====================================
// Création de trajet
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ride'])) {
    $stmt = $pdo->prepare("INSERT INTO rides (user_id, vehicle_id, depart, arrivee, date_depart, date_arrivee, prix, places)
        VALUES (:user_id, :vehicle_id, :depart, :arrivee, :date_depart, :date_arrivee, :prix, :places)");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':vehicle_id' => $_POST['vehicle_id'],
        ':depart' => $_POST['depart'],
        ':arrivee' => $_POST['arrivee'],
        ':date_depart' => $_POST['date_depart'],
        ':date_arrivee' => $_POST['date_arrivee'],
        ':prix' => $_POST['prix'],
        ':places' => $_POST['places']
    ]);
    header("Location: user-space.php");
    exit;
}

// ====================================
// Récupération des véhicules
// ====================================
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$vehicles = $stmt->fetchAll();

// ====================================
// Récupération des trajets créés
// ====================================
$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele
    FROM rides
    INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    WHERE rides.user_id = :user_id
    ORDER BY rides.date_depart");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$rides = $stmt->fetchAll();

// ====================================
// Récupération des trajets réservés
// ====================================
$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele
    FROM participants
    INNER JOIN rides ON participants.ride_id = rides.id
    INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    WHERE participants.user_id = :user_id
    AND rides.date_arrivee >= NOW()
    ORDER BY rides.date_depart DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reserved_rides = $stmt->fetchAll();

// ====================================
// Récupération de l'historique des trajets terminés (US10)
// ====================================
$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele
    FROM participants
    INNER JOIN rides ON participants.ride_id = rides.id
    INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    WHERE participants.user_id = :user_id
    AND rides.date_arrivee < NOW()
    ORDER BY rides.date_arrivee DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$past_rides = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon espace - EcoRide</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>
<main>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['pseudo']) ?> !</h1>
    <?php if ($message): ?>
        <p style="color:green;"><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <!-- Changement de rôle -->
    <section>
        <form method="post">
            <label>Changer de rôle :</label>
            <select name="role" required>
                <option value="">-- Sélectionner --</option>
                <option value="passager" <?= $_SESSION['role']==='passager'?'selected':'' ?>>Passager</option>
                <option value="chauffeur" <?= $_SESSION['role']==='chauffeur'?'selected':'' ?>>Chauffeur</option>
                <option value="les deux" <?= $_SESSION['role']==='les deux'?'selected':'' ?>>Les deux</option>
            </select>
            <button type="submit">Mettre à jour</button>
        </form>
    </section>

<?php if ($_SESSION['role'] === 'chauffeur' || $_SESSION['role'] === 'les deux'): ?>
    <!-- Ajout de véhicule -->
    <section>
        <h2>Ajouter un véhicule</h2>
        <form method="post">
            <input type="text" name="plaque" placeholder="Plaque" required>
            <input type="text" name="modele" placeholder="Modèle" required>
            <input type="text" name="couleur" placeholder="Couleur" required>
            <input type="text" name="marque" placeholder="Marque" required>
            <select name="energie" required>
                <option value="">-- Énergie --</option>
                <option value="essence">Essence</option>
                <option value="électrique">Électrique</option>
                <option value="hybride">Hybride</option>
            </select>
            <button type="submit" name="submit_vehicle">Ajouter</button>
        </form>
    </section>

    <!-- Création de trajet -->
    <section>
        <h2>Créer un trajet</h2>
        <form method="post">
            <input type="text" name="depart" placeholder="Ville de départ" required>
            <input type="text" name="arrivee" placeholder="Ville d'arrivée" required>
            <input type="date" name="date_depart" required>
            <input type="date" name="date_arrivee" required>
            <input type="number" name="prix" step="0.01" placeholder="Prix" required>
            <input type="number" name="places" placeholder="Places disponibles" required>
            <select name="vehicle_id" required>
                <option value="">-- Véhicule --</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= $v['marque'] . ' - ' . $v['modele'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="submit_ride">Valider</button>
        </form>
    </section>

    <!-- Liste trajets créés -->
    <?php if (!empty($rides)): ?>
    <section>
        <h2>🚘 Mes trajets créés</h2>
        <ul>
            <?php foreach ($rides as $r): ?>
                <li><strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong> (<?= $r['date_depart'] ?>) - <?= $r['prix'] ?> €</li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>
<?php endif; ?>

<!-- Trajets réservés à venir -->
<?php if (!empty($reserved_rides)): ?>
    <section>
        <h2>🧍‍♂️ Covoiturages à venir</h2>
        <ul>
            <?php foreach ($reserved_rides as $r): ?>
                <li><strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong> (<?= $r['date_depart'] ?>) - <?= $r['prix'] ?> €<br>
                <form action="cancel_participation.php" method="post">
                    <input type="hidden" name="ride_id" value="<?= $r['id'] ?>">
                    <button type="submit">❌ Annuler ma participation</button>
                </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<!-- Historique des trajets (US10) -->
<?php if (!empty($past_rides)): ?>
    <section>
        <h2>📜 Historique de mes covoiturages</h2>
        <ul>
            <?php foreach ($past_rides as $r): ?>
                <li><strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong><br>
                Arrivée le <?= date('d/m/Y', strtotime($r['date_arrivee'])) ?> -
                Prix : <?= $r['prix'] ?> €</li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
</main>
<?php include('../includes/footer.php'); ?>
</body>
</html>