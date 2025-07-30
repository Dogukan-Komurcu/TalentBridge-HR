<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - TalentBridge</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="form-container">
    <h2>Kayıt Ol</h2>
    <form action="register_process.php" method="POST">
        <input type="text" name="name" placeholder="Ad Soyad" required>
        <input type="email" name="email" placeholder="E-posta" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <button type="submit">Kayıt Ol</button>
    </form>
</div>

</body>
</html>
