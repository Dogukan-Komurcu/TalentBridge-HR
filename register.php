<?php
session_start();

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_POST) {
    require_once 'config/database.php';
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasyon
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Tüm alanları doldurunuz.';
    } elseif (strlen($name) < 2) {
        $error = 'İsim en az 2 karakter olmalıdır.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir email adresi giriniz.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $confirm_password) {
        $error = 'Şifreler eşleşmiyor.';
    } else {
        try {
            // Email zaten kayıtlı mı kontrol et
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Bu email adresi zaten kayıtlı.';
            } else {
                // Şifreyi hashle ve kullanıcıyı kaydet
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Tüm yeni kayıtlar için role'ü 'employee' olarak ayarla
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'employee', NOW())");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
                    // Formu temizle
                    $_POST = array();
                } else {
                    $error = 'Kayıt işlemi sırasında bir hata oluştu.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Kayıt işlemi sırasında bir hata oluştu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="assets/images/logo.jpg.jpg" alt="Logo" class="auth-logo">
                <h1><i class="fas fa-user-plus"></i> TalentBridge</h1>
                <p>Yeni hesap oluşturun</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">Ad Soyad</label>
                    <div class="input-group">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input type="text" id="name" name="name" placeholder="👤 Adınızı ve soyadınızı girin" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                </div>
                
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
                        <input type="password" id="password" name="password" placeholder="🔒 En az 6 karakter" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Şifre Tekrar</label>
                    <div class="input-group">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="🔒 Şifrenizi tekrar girin" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-user-plus"></i>
                    Kayıt Ol
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
                <p><a href="index.php">← Ana Sayfaya Dön</a></p>
            </div>
        </div>
    </div>
</body>
</html>
