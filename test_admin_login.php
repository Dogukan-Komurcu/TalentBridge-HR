<?php
// Admin hesabını test etmek için script
session_start();
require_once 'config/database.php';

echo "<h1>Admin Giriş Testi</h1>";

// Veritabanı bağlantısı
try {
    $pdo = new PDO("mysql:host={$db_config['host']};dbname={$db_config['db_name']}", $db_config['username'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Veritabanı bağlantısı başarılı!</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Veritabanı bağlantı hatası: " . $e->getMessage() . "</p>";
    exit;
}

// Admin hesabını kontrol et
$email = "admin@talentbridge.com";
$password = "admin123";

try {
    // Admin hesabının varlığını kontrol et
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h2>Admin Hesap Detayları:</h2>";
    if ($user) {
        echo "<p>Admin hesabı bulundu!</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Ad: " . $user['name'] . "</li>";
        echo "<li>Email: " . $user['email'] . "</li>";
        echo "<li>Rol: " . $user['role'] . "</li>";
        echo "<li>Şifre Hash: " . $user['password'] . "</li>";
        echo "</ul>";

        // Şifre doğrulamasını test et
        $passwordVerifyResult = password_verify($password, $user['password']);
        echo "<h3>Şifre Doğrulama Testi:</h3>";
        echo "<p>Test Şifresi: $password</p>";
        echo "<p>password_verify() Sonucu: " . ($passwordVerifyResult ? "<span style='color:green'>BAŞARILI</span>" : "<span style='color:red'>BAŞARISIZ</span>") . "</p>";

        // Şifre uyumlu değilse admin şifresini sıfırla
        if (!$passwordVerifyResult) {
            echo "<h3>Şifre Sıfırlama:</h3>";
            try {
                // Yeni şifre oluştur ve güncelle
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateResult = $updateStmt->execute([$hashedPassword, $email]);
                
                if ($updateResult) {
                    echo "<p style='color:green'>Admin şifresi başarıyla sıfırlandı. Yeni şifre: $password</p>";
                    echo "<p>Yeni Hash: $hashedPassword</p>";
                    // Şifre doğrulamasını tekrar test et
                    echo "<p>Yeni password_verify() Testi: " . (password_verify($password, $hashedPassword) ? "<span style='color:green'>BAŞARILI</span>" : "<span style='color:red'>BAŞARISIZ</span>") . "</p>";
                } else {
                    echo "<p style='color:red'>Şifre güncellenemedi.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red'>Şifre güncelleme hatası: " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p style='color:red'>Admin hesabı bulunamadı!</p>";
        
        // Admin hesabı yoksa oluştur
        echo "<h3>Admin Hesabı Oluşturma:</h3>";
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $createStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $createResult = $createStmt->execute(['Admin User', $email, $hashedPassword, 'admin']);
            
            if ($createResult) {
                echo "<p style='color:green'>Admin hesabı başarıyla oluşturuldu:</p>";
                echo "<ul>";
                echo "<li>Email: $email</li>";
                echo "<li>Şifre: $password</li>";
                echo "<li>Hash: $hashedPassword</li>";
                echo "</ul>";
            } else {
                echo "<p style='color:red'>Admin hesabı oluşturulamadı.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>Admin hesabı oluşturma hatası: " . $e->getMessage() . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Sorgu hatası: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Giriş sayfasına dön</a></p>";
?>
