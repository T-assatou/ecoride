<?php
// ============================
// Fichier : pages/user-space.php
// Rôle : Affiche l’espace personnel de l’utilisateur connecté
// ============================

require_once('../controllers/auth.php');
require_once('../models/db.php');

// ====================================
// Traitement du changement de rôle
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $newRole = $_POST['role'];

    // Mise à jour du rôle dans la base de données
    $sql = "UPDATE users SET role = :role WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':role' => $newRole,
        ':id' => $_SESSION['user_id']
    ]);

    // Mise à jour dans la session
    $_SESSION['role'] = $newRole;

    // Redirection propre
    header("Location: user-space.php");
    exit;
}

// ====================================
// Traitement de l'ajout d'un véhicule
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicle'])) {
    $plaque = $_POST['plaque'];
    $modele = $_POST['modele'];
    $couleur = $_POST['couleur'];
    $marque = $_POST['marque'];
    $energie = $_POST['energie'];

    // Insertion dans la base
    $sql = "INSERT INTO vehicles (user_id, plaque, modele, couleur, marque, energie)
            VALUES (:user_id, :plaque, :modele, :couleur, :marque, :energie)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':plaque' => $plaque,
        ':modele' => $modele,
        ':couleur' => $couleur,
        ':marque' => $marque,
        ':energie' => $energie
    ]);

    header("Location: user-space.php");
    exit;
}

// ====================================
// Traitement de l'ajout d'un trajet
// ====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ride'])) {
  $depart = $_POST['depart'];
  $arrivee = $_POST['arrivee'];
  $date_depart = $_POST['date_depart'];
  $prix = $_POST['prix'];
  $places = $_POST['places']; // Très important de récupérer places
  $vehicle_id = $_POST['vehicle_id'];


  // Insertion dans la table rides
  $sql = "INSERT INTO rides (user_id, vehicle_id, depart, arrivee, date_depart, prix, places)
            VALUES (:user_id, :vehicle_id, :depart, :arrivee, :date_depart, :prix, :places)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':vehicle_id' => $vehicle_id,
        ':depart' => $depart,
        ':arrivee' => $arrivee,
        ':date_depart' => $date_depart,
        ':prix' => $prix,
        ':places' => $places
    ]);
  // Recharge la page après enregistrement
  header("Location: user-space.php");
  exit;
}

// ====================================
// Récupération des véhicules enregistrés
// ====================================
$sql = "SELECT * FROM vehicles WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$vehicles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon espace - EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<header>
    <h1>Espace personnel</h1>
</header>

<!-- Espace utilisateur -->
<section>
    <h2>Bonjour <?php echo htmlspecialchars($_SESSION['pseudo']); ?> 👋</h2>
    <p>Votre rôle actuel est : <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>

    <!-- Formulaire de changement de rôle -->
    <form action="user-space.php" method="post">
        <label for="role">Choisissez votre rôle :</label>
        <select name="role" id="role" required>
            <option value="">-- Sélectionner --</option>
            <option value="passager" <?php if ($_SESSION['role'] === 'passager') echo 'selected'; ?>>Passager</option>
            <option value="chauffeur" <?php if ($_SESSION['role'] === 'chauffeur') echo 'selected'; ?>>Chauffeur</option>
            <option value="les deux" <?php if ($_SESSION['role'] === 'les deux') echo 'selected'; ?>>Les deux</option>
        </select>
        <button type="submit" name="submit_role">Mettre à jour</button>
    </form>
</section>

<!-- Formulaire d'ajout de véhicule uniquement pour chauffeur ou les deux -->
<?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'chauffeur' || $_SESSION['role'] === 'les deux')): ?>
<section>
    <h2>Enregistrer un véhicule</h2>

    <form action="user-space.php" method="post">
        <label for="plaque">Plaque d'immatriculation :</label>
        <input type="text" name="plaque" id="plaque" required>

        <label for="modele">Modèle :</label>
        <input type="text" name="modele" id="modele" required>

        <label for="couleur">Couleur :</label>
        <input type="text" name="couleur" id="couleur" required>

        <label for="marque">Marque :</label>
        <input type="text" name="marque" id="marque" required>

        <label for="energie">Énergie :</label>
        <select name="energie" id="energie" required>
            <option value="">-- Choisir --</option>
            <option value="électrique">Électrique</option>
            <option value="essence">Essence</option>
            <option value="hybride">Hybride</option>
        </select>

        <button type="submit" name="submit_vehicle">Enregistrer le véhicule</button>
    </form>
</section>

<!-- Liste des véhicules enregistrés -->
<?php if (!empty($vehicles)): ?>
<section>
    <h2>Mes véhicules enregistrés</h2>
    <ul>
        <?php foreach ($vehicles as $vehicle): ?>
            <li>
                <?php echo htmlspecialchars($vehicle['marque']); ?> -
                <?php echo htmlspecialchars($vehicle['modele']); ?> 
                (<?php echo htmlspecialchars($vehicle['couleur']); ?>, 
                <?php echo htmlspecialchars($vehicle['energie']); ?>) 
                - Plaque : <?php echo htmlspecialchars($vehicle['plaque']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'chauffeur' || $_SESSION['role'] === 'les deux')): ?>
<section>
    <h2>Ajouter un trajet</h2>

    <form action="user-space.php" method="post">
        <label for="depart">Adresse de départ :</label>
        <input type="text" name="depart" id="depart" required>

        <label for="arrivee">Adresse d'arrivée :</label>
        <input type="text" name="arrivee" id="arrivee" required>

        <label for="date_depart">Date de départ :</label>
        <input type="date" name="date_depart" id="date_depart" required>

        <label for="prix">Prix (€) :</label>
        <input type="number" step="0.01" name="prix" id="prix" required>

        <label for="places">Nombre de places disponibles :</label>
        <input type="number" name="places" id="places" min="1" required>


        <label for="vehicle_id">Véhicule utilisé :</label>
        <select name="vehicle_id" id="vehicle_id" required>
            <option value="">-- Sélectionner un véhicule --</option>
            <?php
            foreach ($vehicles as $vehicle) {
                echo '<option value="' . htmlspecialchars($vehicle['id']) . '">' . htmlspecialchars($vehicle['marque']) . ' - ' . htmlspecialchars($vehicle['modele']) . '</option>';
            }
            ?>
        </select>

        <button type="submit" name="submit_ride">Créer le trajet</button>
    </form>
</section>
<?php
// Afficher les trajets créés par l'utilisateur connecté
$sql = "SELECT rides.*, vehicles.marque, vehicles.modele
        FROM rides
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        WHERE rides.user_id = :user_id
        ORDER BY rides.date_depart ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$rides = $stmt->fetchAll();

if (!empty($rides)): ?>
<section>
    <h2>Mes trajets créés</h2>
    <ul>
        <?php foreach ($rides as $ride): ?>
            <li>
                <strong>Départ :</strong> <?php echo htmlspecialchars($ride['depart']); ?> → 
                <strong>Arrivée :</strong> <?php echo htmlspecialchars($ride['arrivee']); ?><br>
                <strong>Date de départ :</strong> <?php echo htmlspecialchars($ride['date_depart']); ?><br>
                <strong>Prix :</strong> <?php echo htmlspecialchars($ride['prix']); ?> €<br>
                <strong>Véhicule :</strong> <?php echo htmlspecialchars($ride['marque']) . ' ' . htmlspecialchars($ride['modele']); ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
</section>
<?php
// Récupérer les trajets auxquels l’utilisateur a participé
$sql = "SELECT rides.*, vehicles.marque, vehicles.modele
        FROM participants
        INNER JOIN rides ON participants.ride_id = rides.id
        INNER JOIN vehicles ON rides.vehicle_id = vehicles.id
        WHERE participants.user_id = :user_id
        ORDER BY rides.date_depart DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$reserved_rides = $stmt->fetchAll();
?>

<?php if (!empty($reserved_rides)): ?>
<section>
    <h2>Mes covoiturages réservés</h2>
    <ul>
        <?php foreach ($reserved_rides as $ride): ?>
            <li>
                <strong>Départ :</strong> <?php echo htmlspecialchars($ride['depart']); ?> → 
                <strong>Arrivée :</strong> <?php echo htmlspecialchars($ride['arrivee']); ?><br>
                <strong>Date :</strong> <?php echo htmlspecialchars($ride['date_depart']); ?><br>
                <strong>Véhicule :</strong> <?php echo htmlspecialchars($ride['marque']) . ' ' . htmlspecialchars($ride['modele']); ?><br>
                <strong>Prix :</strong> <?php echo htmlspecialchars($ride['prix']); ?> €
                <form action="cancel_participation.php" method="post" style="margin-top: 10px;">
    <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>">
    <button type="submit">❌ Annuler ma participation</button>
</form>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
<?php endif; ?>

<?php endif; ?>

<?php endif; ?>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>

</body>
</html>
