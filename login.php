<?php
session_start();

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_POST) {
    require_once 'config/database.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Debug için (geçici)
    if ($email === 'debug@test.com') {
        echo "<div style='background: white; padding: 20px; margin: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: absolute; top: 0; left: 0; z-index: 9999;'>";
        echo "<h3>Debug Bilgileri</h3>";
        
        // Veritabanındaki kullanıcıları listele
        $stmt = $pdo->query("SELECT id, name, email, role, password FROM users ORDER BY id LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Ad</th><th>Email</th><th>Rol</th><th>Şifre (ilk 20 karakter)</th></tr>";
        
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>{$u['id']}</td>";
            echo "<td>{$u['name']}</td>";
            echo "<td>{$u['email']}</td>";
            echo "<td>{$u['role']}</td>";
            echo "<td>" . substr($u['password'], 0, 20) . "...</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<p><strong>Not:</strong> Bu debug mesajını görebilmek için email olarak debug@test.com girin.</p>";
        echo "</div>";
    }
    
    if (empty($email) || empty($password)) {
        $error = 'Email ve şifre alanları boş bırakılamaz.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug için password_verify detayları
            $passwordVerifyResult = ($user) ? password_verify($password, $user['password']) : false;
            
            if ($email === 'debug@test.com') {
                echo "<div style='margin-top: 20px; background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
                echo "<h3>Password Verify Debug:</h3>";
                echo "<p>Password Verify Result: " . ($passwordVerifyResult ? 'TRUE' : 'FALSE') . "</p>";
                if ($user) {
                    echo "<p>Password from Form: " . htmlspecialchars($password) . "</p>";
                    echo "<p>Stored Password Hash: " . htmlspecialchars($user['password']) . "</p>";
                }
                echo "</div>";
            }
            
            if ($user && $passwordVerifyResult) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'] ?? 'employee'; // session değişkeni düzeltildi
                $_SESSION['role'] = $user['role'] ?? 'employee'; // Geriye dönük uyumluluk
                $_SESSION['position'] = $user['position'] ?? '';
                $_SESSION['department'] = $user['department'] ?? '';
                $_SESSION['phone'] = $user['phone'] ?? '';
                $_SESSION['location'] = $user['location'] ?? '';
                $_SESSION['bio'] = $user['bio'] ?? '';
                $_SESSION['profile_image'] = $user['profile_image'] ?? '';
                
                // Last login güncelle
                $updateLoginStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateLoginStmt->execute([$user['id']]);
                
                // Güvenlik log'u için includes/auth.php'yi dahil et
                if (file_exists('includes/auth.php')) {
                    require_once 'includes/auth.php';
                    logSecurityEvent('LOGIN_SUCCESS', "Role: {$user['role']}");
                }
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Email veya şifre hatalı.';
            }
        } catch (PDOException $e) {
            $error = 'Giriş işlemi sırasında bir hata oluştu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="assets/images/logo.jpg.jpg" alt="Logo" class="auth-logo">
                <h1><i class="fas fa-user-tie"></i> TalentBridge</h1>
                <p>Hesabınıza giriş yapın</p>
            </div>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Adresi</label>
                    <div class="input-group">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <input type="email" id="email" name="email" placeholder="📧 örnek@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <div class="input-group">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input type="password" id="password" name="password" placeholder="🔒 Şifrenizi girin" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Henüz hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
                <p><a href="index.php">← Ana Sayfaya Dön</a></p>
            </div>
        </div>
    </div>
</body>
</html>
