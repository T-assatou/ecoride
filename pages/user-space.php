<?php
require_once('../controllers/auth.php');
require_once('../models/db.php');

// 🔄 Changement de rôle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $newRole = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([':role' => $newRole, ':id' => $_SESSION['user_id']]);
    $_SESSION['role'] = $newRole;
    header("Location: user-space.php");
    exit;
}

// 🚗 Ajout véhicule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicle'])) {
    $stmt = $pdo->prepare("INSERT INTO vehicles (
        user_id, plaque, date_immatriculation, modele, couleur, marque,
        energie, places_vehicule, fumeur, animaux, preferences
    ) VALUES (
        :user_id, :plaque, :date_immat, :modele, :couleur, :marque,
        :energie, :places, :fumeur, :animaux, :preferences
    )");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':plaque' => $_POST['plaque'],
        ':date_immat' => $_POST['date_immatriculation'],
        ':modele' => $_POST['modele'],
        ':couleur' => $_POST['couleur'],
        ':marque' => $_POST['marque'],
        ':energie' => $_POST['energie'],
        ':places' => $_POST['places_vehicule'],
        ':fumeur' => isset($_POST['fumeur']) ? 1 : 0,
        ':animaux' => isset($_POST['animaux']) ? 1 : 0,
        ':preferences' => trim($_POST['preferences'] ?? '')
    ]);
    header("Location: user-space.php");
    exit;
}

// 📦 Récupère les véhicules
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$vehicles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon espace - EcoRide</title>
  <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
  <h1>Espace personnel</h1>
</header>

<main>
<section>
  <h2>Bonjour <?= htmlspecialchars($_SESSION['pseudo']) ?> 👋</h2>
  <p>Rôle actuel : <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>
  <form action="user-space.php" method="post">
    <label>Changer de rôle :</label>
    <select name="role" required>
      <option value="">-- Choisir --</option>
      <option value="passager" <?= $_SESSION['role'] === 'passager' ? 'selected' : '' ?>>Passager</option>
      <option value="chauffeur" <?= $_SESSION['role'] === 'chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
      <option value="les deux" <?= $_SESSION['role'] === 'les deux' ? 'selected' : '' ?>>Les deux</option>
    </select>
    <button type="submit">Mettre à jour</button>
  </form>
</section>

<?php if ($_SESSION['role'] !== 'passager'): ?>
<section>
  <h2>Ajouter un véhicule</h2>
  <form method="post" action="user-space.php">
    <input type="text" name="plaque" placeholder="Plaque" required>
    <input type="date" name="date_immatriculation" required>
    <input type="text" name="modele" placeholder="Modèle" required>
    <input type="text" name="couleur" placeholder="Couleur" required>
    <input type="text" name="marque" placeholder="Marque" required>
    <select name="energie" required>
      <option value="">-- Énergie --</option>
      <option value="essence">Essence</option>
      <option value="électrique">Électrique</option>
      <option value="hybride">Hybride</option>
    </select>
    <input type="number" name="places_vehicule" placeholder="Places" required min="1">
    <label><input type="checkbox" name="fumeur"> Fumeur</label>
    <label><input type="checkbox" name="animaux"> Animaux</label>
    <textarea name="preferences" placeholder="Autres préférences..."></textarea>
    <button type="submit" name="submit_vehicle">Ajouter</button>
  </form>
</section>

<?php if (!empty($vehicles)): ?>
<section>
  <h2>Mes véhicules</h2>
  <ul>
    <?php foreach ($vehicles as $v): ?>
      <li>
        <?= $v['marque'] ?> <?= $v['modele'] ?> - <?= $v['plaque'] ?> (<?= $v['places_vehicule'] ?> places)
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<section>
  <h2>Créer un trajet</h2>
  <form method="post" action="../controllers/rideController.php">
    <input type="text" name="depart" placeholder="Départ" required>
    <input type="text" name="arrivee" placeholder="Arrivée" required>
    <input type="date" name="date_depart" required>
    <input type="date" name="date_arrivee" required>
    <input type="number" name="prix" placeholder="Prix (€)" required>
    <input type="number" name="places" placeholder="Places disponibles" required>
    <select name="vehicle_id" required>
      <option value="">-- Véhicule --</option>
      <?php foreach ($vehicles as $v): ?>
        <option value="<?= $v['id'] ?>"><?= $v['marque'] ?> - <?= $v['modele'] ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" name="submit_ride">Créer</button>
  </form>
</section>

<?php
$stmt = $pdo->prepare("SELECT * FROM rides WHERE user_id = :id ORDER BY date_depart");
$stmt->execute([':id' => $_SESSION['user_id']]);
$myRides = $stmt->fetchAll();
?>

<?php if (!empty($myRides)): ?>
<section>
  <h2>Mes trajets créés</h2>
  <ul>
    <?php foreach ($myRides as $r): ?>
      <li>
        <?= $r['depart'] ?> → <?= $r['arrivee'] ?> le <?= $r['date_depart'] ?> (<?= $r['places'] ?> places)
        - Statut : <?= $r['statut'] ?><br>

        <?php if ($r['statut'] === 'en attente'): ?>
          <a href="start_ride.php?ride_id=<?= $r['id'] ?>" class="btn-blue">Démarrer</a>
        <?php elseif ($r['statut'] === 'en cours'): ?>
          <a href="end_ride.php?ride_id=<?= $r['id'] ?>" class="btn-green">Arrivée à destination</a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>
<?php endif; ?>

<?php
$stmt = $pdo->prepare("SELECT rides.*, users.pseudo AS chauffeur, rides.user_id AS chauffeur_id
                       FROM participants
                       INNER JOIN rides ON participants.ride_id = rides.id
                       INNER JOIN users ON rides.user_id = users.id
                       WHERE participants.user_id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<?php if (!empty($bookings)): ?>
<section>
  <h2>Mes trajets réservés</h2>
  <ul>
    <?php foreach ($bookings as $b): ?>
      <li>
        <strong><?= $b['depart'] ?> → <?= $b['arrivee'] ?></strong><br>
        Date : <?= $b['date_depart'] ?><br>
        Statut : <?= $b['statut'] ?><br>
        Conducteur : <?= htmlspecialchars($b['chauffeur']) ?><br>

        <?php if ($b['statut'] === 'terminé'): ?>
          <form action="confirmer_trajet.php" method="post">
            <input type="hidden" name="ride_id" value="<?= $b['id'] ?>">
            <p>Validez-vous que tout s’est bien passé ?</p>
            <button name="validation" value="oui">✅ Oui</button>
            <button name="validation" value="non">❌ Non</button>
          </form>
        <?php endif; ?>
      </li>
      <hr>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

</main>
<?php include('../includes/footer.php'); ?>
</body>

</html>

