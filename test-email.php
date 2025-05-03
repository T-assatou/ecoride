
<?php
require_once('models/db.php');

// On teste un email précis
$emailTest = 'test@ecoride.fr';

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $emailTest]);
$user = $stmt->fetch();

if ($user) {
    echo "✅ L'email existe dans la base.";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "❌ Email introuvable.";
}
?>
