<?php
// ============================
// Fichier : pages/create-employee.php
// Rôle : Permet à l'administrateur de créer un compte employé
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que seul l'admin peut accéder à cette page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($pseudo) && !empty($email) && !empty($password)) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Création du compte dans la BDD
        $sql = "INSERT INTO users (pseudo, email, password, credits, role) VALUES (:pseudo, :email, :password, :credits, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':credits' => 0,
            ':role' => 'employe'
        ]);

        // Redirection vers le panneau admin
        header("Location: admin-control.php");
        exit;
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un employé - EcoRide</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Création d'un compte employé</h1>
</header>
<main>
<section>
    <?php if (!empty($message)) echo '<p style="color:red">' . $message . '</p>'; ?>

    <form action="" method="post">
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Créer l'employé</button>
    </form>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
