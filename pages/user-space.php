<?php
require_once('../controllers/auth.php');
require_once('../models/db.php');

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// Gestion des rôles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([':role' => $_POST['role'], ':id' => $_SESSION['user_id']]);
    $_SESSION['role'] = $_POST['role'];
    header("Location: user-space.php");
    exit;
}

// Ajouter véhicule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicle'])) {
    $stmt = $pdo->prepare("INSERT INTO vehicles (
        user_id, plaque, date_immatriculation, modele, couleur, marque, energie, places_vehicule, fumeur, animal, preferences)
        VALUES (
        :user_id, :plaque, :immat_date, :modele, :couleur, :marque, :energie, :places, :fumeur, :animal, :preferences)");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':plaque' => $_POST['plaque'],
        ':immat_date' => $_POST['immat_date'],
        ':modele' => $_POST['modele'],
        ':couleur' => $_POST['couleur'],
        ':marque' => $_POST['marque'],
        ':energie' => $_POST['energie'],
        ':places' => $_POST['places'],
        ':fumeur' => isset($_POST['fumeur']) ? 1 : 0,
        ':animal' => isset($_POST['animal']) ? 1 : 0,
        ':preferences' => $_POST['preferences'] ?? ''
    ]);
    header("Location: user-space.php");
    exit;
}

// Créer un trajet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ride'])) {
    // 1. Enregistrement du trajet
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

    // 2. Retirer 2 crédits au chauffeur (après insertion réussie)
    $pdo->prepare("UPDATE users SET credits = credits - 2 WHERE id = :id")
        ->execute([':id' => $_SESSION['user_id']]);

    // 3. Redirection
    header("Location: user-space.php");
    exit;
}



// Avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_avis'])) {
    $stmt = $pdo->prepare("INSERT INTO avis (contenu, chauffeur_id, auteur_id, valide, created_at)
        VALUES (:contenu, :chauffeur_id, :auteur_id, 0, NOW())");
    $stmt->execute([
        ':contenu' => $_POST['contenu'],
        ':chauffeur_id' => $_POST['chauffeur_id'],
        ':auteur_id' => $_SESSION['user_id']
    ]);
    $_SESSION['message'] = "✅ Avis envoyé pour validation.";
    header("Location: user-space.php");
    exit;
}

// Litiges
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_litige'])) {
    $stmt = $pdo->prepare("INSERT INTO litiges (ride_id, passager_id, chauffeur_id, commentaire, created_at)
        VALUES (:ride_id, :passager_id, :chauffeur_id, :commentaire, NOW())");
    $stmt->execute([
        ':ride_id' => $_POST['ride_id'],
        ':passager_id' => $_SESSION['user_id'],
        ':chauffeur_id' => $_POST['chauffeur_id'],
        ':commentaire' => $_POST['commentaire']
    ]);
    $_SESSION['message'] = "⚠️ Litige enregistré.";
    header("Location: user-space.php");
    exit;
}

// Requêtes pour les données
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$vehicles = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele
    FROM rides INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    WHERE rides.user_id = :user_id ORDER BY rides.date_depart");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$rides = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele, users.id AS chauffeur_id, users.pseudo AS conducteur
    FROM participants
    INNER JOIN rides ON participants.ride_id = rides.id
    INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    INNER JOIN users ON rides.user_id = users.id
    WHERE participants.user_id = :user_id
    ORDER BY rides.date_arrivee DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reserved_rides = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele
    FROM rides INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
    WHERE rides.user_id = :user_id AND rides.date_arrivee < NOW()
    ORDER BY rides.date_arrivee DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$past_rides = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon espace</title>
    <link rel="stylesheet" href="../Assets/css/user-space.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>
<main>
    <h1>Bienvenue <?= htmlspecialchars($_SESSION['pseudo']) ?></h1>
    <?php if ($message): ?><p style="color:green;"><strong><?= $message ?></strong></p><?php endif; ?>

    <!-- Formulaire de rôle -->
    <form method="post" class="form-user-space">
        <label>Changer de rôle :</label>
        <select name="role" required>
            <option value="passager" <?= $_SESSION['role']=='passager'?'selected':'' ?>>Passager</option>
            <option value="chauffeur" <?= $_SESSION['role']=='chauffeur'?'selected':'' ?>>Chauffeur</option>
            <option value="les deux" <?= $_SESSION['role']=='les deux'?'selected':'' ?>>Les deux</option>
        </select>
        <button type="submit">Mettre à jour</button>
    </form>


<!-- === FORMULAIRE D'AJOUT DE VÉHICULE === -->

    <?php if ($_SESSION['role'] !== 'passager'): ?>
       <h2>🚘 Ajouter un véhicule</h2>
<form method="post" class="form-user-space">
    <input name="plaque" placeholder="Plaque" required>

    <label>Date de première immatriculation :</label>
    <input type="date" name="immat_date" required>

    <input name="modele" placeholder="Modèle" required>
    <input name="couleur" placeholder="Couleur" required>
    <input name="marque" placeholder="Marque" required>

    <label>Nombre de places disponibles :</label>
    <input type="number" name="places" min="1" required>

    <label>Type de carburant :</label>
    <select name="energie" required>
        <option value="électrique">Électrique</option>
        <option value="essence">Essence</option>
        <option value="hybride">Hybride</option>
    </select>

    <label>Préférences :</label><br>
    <input type="checkbox" name="fumeur" value="1"> Accepte fumeur<br>
    <input type="checkbox" name="animal" value="1"> Accepte animaux<br>

    <label>Autres préférences :</label>
    <textarea name="preferences" placeholder="Ex : pas de musique forte, pas de bagages volumineux..." rows="3"></textarea>

    <button type="submit" name="submit_vehicle">Ajouter</button>
</form>


<!-- === FORMULAIRE  CREATION TRAJET === -->
        <h2>🗓️ Créer un trajet</h2>
        <form method="post" class="form-user-space">
            <input name="depart" placeholder="Départ" required>
            <input name="arrivee" placeholder="Arrivée" required>
            <input type="date" name="date_depart" required>
            <input type="date" name="date_arrivee" required>
            <input type="number" name="prix" placeholder="Prix (€)" required>
            <input type="number" name="places" placeholder="Places" required>
            <select name="vehicle_id" required>
                <option value="">-- Véhicule --</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= $v['marque'] . ' ' . $v['modele'] ?></option>
                <?php endforeach; ?>
            </select>
            <p s>  2 crédits seront prélevés à chaque création de trajet.</p>
            <button type="submit" name="submit_ride">Valider</button>
        </form>
    <?php endif; ?>

   <?php if (!empty($rides)): ?>
<h2>📋 Mes trajets créés</h2>
<ul>
  <?php foreach ($rides as $r): ?>
    <li class="trajet-box">
        <strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong> (<?= $r['date_depart'] ?>) - <?= $r['prix'] ?> €

        <!-- Statut du trajet -->
        <?php if ($r['statut'] === 'en attente'): ?>
            <form method="post" action="start_ride.php" style="display:inline;">
                <input type="hidden" name="ride_id" value="<?= $r['id'] ?>">
                <button type="submit" class="admin-button green">▶️ Démarrer</button>
            </form>
        <?php elseif ($r['statut'] === 'en cours'): ?>
            <form method="post" action="end_ride.php" style="display:inline;">
                <input type="hidden" name="ride_id" value="<?= $r['id'] ?>">
                <button type="submit" class="admin-button red">🛑 Arrivée à destination</button>
            </form>
        <?php elseif ($r['statut'] === 'terminé'): ?>
            <span >✅ Trajet terminé</span>
        <?php endif; ?>

        <!-- Bouton d'annulation -->
        <a href="cancel_ride.php?ride_id=<?= $r['id'] ?>" class="admin-button red" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce trajet ?');">❌ Annuler</a>
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

   <?php if (!empty($reserved_rides)): ?>
    <h2>🧍‍♂️ Mes covoiturages réservés</h2>
    <?php foreach ($reserved_rides as $r): ?>
        <div class="ride-box">
            <p><strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong> (<?= $r['date_arrivee'] ?>)</p>
            <p>Conducteur : <?= htmlspecialchars($r['conducteur']) ?></p>

           <?php if (strtotime($r['date_arrivee']) < time()): ?>
    <!-- ✅ Bouton de validation du trajet -->
    <a href="validate_ride.php?ride_id=<?= $r['id'] ?>" class="admin-button green">🔍 Valider ce trajet</a>

    <!-- ✍️ Laisser un avis -->
    <form action="submit-avis.php" method="post" class="form-user-space">
        <input type="hidden" name="chauffeur_id" value="<?= $r['chauffeur_id'] ?>">
        <textarea name="contenu" placeholder="Laisser un avis..." required></textarea>
        <button type="submit">Envoyer avis</button>
    </form>

    <!-- ⚠️ Signaler un litige -->
    <form action="submit-litige.php" method="post" class="form-user-space">
        <input type="hidden" name="ride_id" value="<?= $r['id'] ?>">
        <input type="hidden" name="chauffeur_id" value="<?= $r['chauffeur_id'] ?>">
        <textarea name="commentaire" placeholder="Signaler un litige..." required></textarea>
        <button type="submit">Signaler litige</button>
    </form>
               
                <!-- Trajet à venir : possibilité d’annuler -->
                <form action="cancel_participation.php" method="post" onsubmit="return confirm('Annuler votre participation ?');">
                    <input type="hidden" name="ride_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="admin-button red">❌ Annuler</button>
                </form>
                <p>(Vous pourrez laisser un avis ou signaler un litige après le trajet)</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

   <?php if (!empty($past_rides)): ?>
    <h2>🕓 Mes trajets terminés</h2>
    <ul>
        <?php foreach ($past_rides as $r): ?>
            <li class="trajet-box"><strong><?= $r['depart'] ?> → <?= $r['arrivee'] ?></strong> (<?= $r['date_arrivee'] ?>) - <?= $r['prix'] ?> €</li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</main>
<?php include('../includes/footer.php'); ?>
</body>
</html>
