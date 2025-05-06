<?php
// ============================
// Fichier : insert-user.php
// Rôle : Traite le formulaire de création de compte
// ============================

require_once('models/db.php'); // Connexion à la base
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifie que tous les champs sont remplis
    if (!empty($pseudo) && !empty($email) && !empty($password)) {

        // Vérifie si l’email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            echo "<p style='color:red;'>🚫 Cet email est déjà utilisé. Veuillez en choisir un autre.</p>";
            exit;
        }

        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Création de l’utilisateur avec rôle par défaut "utilisateur" et actif = 1
        $stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password, credits, role, actif)
                               VALUES (:pseudo, :email, :password, 0, 'utilisateur', 1)");
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        echo "<p style='color:green;'>✅ Compte créé avec succès ! Vous pouvez maintenant vous connecter.</p>";
        echo "<p><a href='pages/login.php'>Aller à la page de connexion</a></p>";
        exit;

    } else {
        echo "<p style='color:red;'>⚠️ Merci de remplir tous les champs.</p>";
    }
}


// Après l’insertion réussie dans la base
echo "<p style='color:green;'>✅ Compte créé avec succès ! Vous allez être redirigé vers la page de connexion...</p>";

// Redirection automatique après 3 secondes
echo "<script>
    setTimeout(function() {
        window.location.href = 'pages/login.php';
    }, 3000); // 3000 ms = 3 secondes
</script>";
exit;
?>

