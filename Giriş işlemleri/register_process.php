<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Aynı e-posta varsa engelle
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        echo "Bu e-posta adresi zaten kayıtlı!";
    } else {
        $insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if ($insert->execute([$name, $email, $password])) {
            header("Location: login.php?success=1");
            exit;
        } else {
            echo "Kayıt sırasında hata oluştu.";
        }
    }
}
?>
