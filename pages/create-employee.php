<?php
// ============================
// US13
// Fichier : pages/create-employee.php
// Rôle : Permet à l'administrateur de créer un compte employé
// ============================

require_once('../models/db.php');
session_start();

// Vérifie que seul l'admin peut accéder à cette page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Traitement du formulaire d’inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Vérifie que tous les champs sont remplis
    if ($pseudo && $email && $password) {
        // Vérifie si l’email est déjà pris
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);

        if ($check->fetch()) {
            $message = "🚫 Cet email est déjà utilisé.";
        } else {
            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertion de l'employé (les autres valeurs sont directement dans la requête)
            $stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password, credits, role, actif)
                                   VALUES (:pseudo, :email, :password, 0, 'employe', 1)");
            $stmt->execute([
                ':pseudo' => $pseudo,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            // Redirection après création
            header("Location: admin-control.php");
            exit;
        }
    } else {
        $message = "⚠️ Merci de remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un employé - EcoRide</title>
    <link rel="stylesheet" href="/Assets/css/style.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

<header>
    <h1>Créer un compte employé</h1>
</header>

<main>
<section class="form-section">
    <?php if ($message): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="" method="post" class="form-create-user">
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Créer l’employé</button>
    </form>
</section>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>