<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

// Kullanıcı giriş kontrolü
checkLogin();

// Kullanıcı rolünü al
$userRole = getUserRole();

// Admin kontrol
$isAdmin = ($userRole === 'admin');

// Kullanıcı bilgilerini veritabanından çek
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$userData = [];

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Kullanıcı ayarlarını çek
    $stmtSettings = $db->prepare("SELECT * FROM user_settings WHERE user_id = :user_id");
    $stmtSettings->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtSettings->execute();
    
    $userSettings = $stmtSettings->fetch(PDO::FETCH_ASSOC);
    
    // Kullanıcı ayarları yoksa oluştur
    if (!$userSettings) {
        $stmtInsert = $db->prepare("INSERT INTO user_settings (user_id, email_notifications, system_notifications) VALUES (:user_id, 1, 1)");
        $stmtInsert->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtInsert->execute();
        
        $userSettings = [
            'email_notifications' => 1,
            'system_notifications' => 1,
            'theme' => 'light',
            'language' => 'tr'
        ];
    }
    
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar_main.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Ayarlar</h1>
                </div>
                
                <div class="settings-container">
                    <div class="settings-sidebar">
                        <ul class="settings-menu">
                            <li class="active">
                                <a href="#profile">
                                    <i class="fas fa-user"></i> Profil Ayarları
                                </a>
                            </li>
                            <li>
                                <a href="#password">
                                    <i class="fas fa-lock"></i> Şifre Değiştir
                                </a>
                            </li>
                            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                            <li>
                                <a href="#notifications">
                                    <i class="fas fa-bell"></i> Bildirim Ayarları
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="#system">
                                    <i class="fas fa-cogs"></i> Sistem Ayarları
                                </a>
                            </li>
                            <li>
                                <a href="#users">
                                    <i class="fas fa-users-cog"></i> Kullanıcı Yönetimi
                                </a>
                            </li>
                            <li>
                                <a href="#backup">
                                    <i class="fas fa-database"></i> Yedekleme
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="settings-content">
                        <div class="settings-section active" id="profile-section">
                            <div class="settings-header">
                                <h2>Profil Ayarları</h2>
                                <p>Kişisel bilgilerinizi ve hesap ayarlarınızı güncelleyin</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-content">
                                    <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form class="settings-form" action="profile_update.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label>Profil Fotoğrafı</label>
                                                <div class="profile-upload">
                                                    <div class="profile-image">
                                                        <?php
                                                        $profileImage = isset($userData['profile_image']) && !empty($userData['profile_image']) 
                                                            ? $userData['profile_image'] 
                                                            : 'assets/images/logo.jpg.jpg';
                                                        ?>
                                                        <img src="<?php echo $profileImage; ?>" alt="Profil">
                                                    </div>
                                                    <div class="profile-actions">
                                                        <label class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-upload"></i> Yükle
                                                            <input type="file" name="profile_image" style="display: none;">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="name">Ad Soyad</label>
                                                <input type="text" id="name" name="name" value="<?php echo isset($userData['name']) ? htmlspecialchars($userData['name']) : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">E-posta</label>
                                                <input type="email" id="email" name="email" value="<?php echo isset($userData['email']) ? htmlspecialchars($userData['email']) : ''; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="phone">Telefon</label>
                                                <input type="tel" id="phone" name="phone" value="<?php echo isset($userData['phone']) ? htmlspecialchars($userData['phone']) : ''; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="position">Pozisyon</label>
                                                <input type="text" id="position" name="position" value="<?php echo isset($userData['position']) ? htmlspecialchars($userData['position']) : 'Belirtilmedi'; ?>" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="department">Departman</label>
                                                <input type="text" id="department" name="department" value="<?php echo isset($userData['department']) ? htmlspecialchars($userData['department']) : 'Belirtilmedi'; ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="location">Konum</label>
                                                <input type="text" id="location" name="location" value="<?php echo isset($userData['location']) ? htmlspecialchars($userData['location']) : ''; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="bio">Hakkımda</label>
                                                <textarea id="bio" name="bio" rows="4"><?php echo isset($userData['bio']) ? htmlspecialchars($userData['bio']) : ''; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-buttons">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                                            </button>
                                            <button type="reset" class="btn btn-secondary">
                                                <i class="fas fa-undo"></i> Sıfırla
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section" id="password-section" style="display:none;">
                            <div class="settings-header">
                                <h2>Şifre Değiştir</h2>
                                <p>Hesabınızın güvenliği için şifrenizi düzenli olarak değiştirin</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-content">
                                    <?php if (isset($_SESSION['password_success'])): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $_SESSION['password_success']; unset($_SESSION['password_success']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['password_error'])): ?>
                                    <div class="alert alert-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo $_SESSION['password_error']; unset($_SESSION['password_error']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form class="settings-form" action="password_update.php" method="POST">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="current_password">Mevcut Şifre</label>
                                                <input type="password" id="current_password" name="current_password" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="new_password">Yeni Şifre</label>
                                                <input type="password" id="new_password" name="new_password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm_password">Şifre Tekrar</label>
                                                <input type="password" id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>
                                        
                                        <div class="password-requirements">
                                            <p><i class="fas fa-info-circle"></i> Şifre gereksinimleri:</p>
                                            <ul>
                                                <li>En az 8 karakter uzunluğunda</li>
                                                <li>En az bir büyük harf</li>
                                                <li>En az bir küçük harf</li>
                                                <li>En az bir rakam</li>
                                                <li>En az bir özel karakter</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="form-buttons">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-key"></i> Şifreyi Değiştir
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Diğer ayar bölümleri gizli olarak duruyor -->
                        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                        <div class="settings-section" id="notifications-section" style="display:none;">
                            <div class="settings-header">
                                <h2>Bildirim Ayarları</h2>
                                <p>Hangi bildirimler almak istediğinizi özelleştirin</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-content">
                                    <?php if (isset($_SESSION['notification_success'])): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $_SESSION['notification_success']; unset($_SESSION['notification_success']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['notification_error'])): ?>
                                    <div class="alert alert-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo $_SESSION['notification_error']; unset($_SESSION['notification_error']); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form class="settings-form" action="notifications_update.php" method="POST">
                                        <div class="notification-group">
                                            <h4>E-posta Bildirimleri</h4>
                                            <div class="form-check">
                                                <input type="checkbox" id="email_new_application" name="email_new_application" <?php echo (isset($userSettings['email_notifications']) && $userSettings['email_notifications']) ? 'checked' : ''; ?>>
                                                <label for="email_new_application">Yeni başvuru geldiğinde</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="email_new_job" name="email_new_job" <?php echo (isset($userSettings['email_notifications']) && $userSettings['email_notifications']) ? 'checked' : ''; ?>>
                                                <label for="email_new_job">Yeni iş ilanı eklendiğinde</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="email_interview" name="email_interview" <?php echo (isset($userSettings['email_notifications']) && $userSettings['email_notifications']) ? 'checked' : ''; ?>>
                                                <label for="email_interview">Görüşme takvimi güncellendiğinde</label>
                                            </div>
                                        </div>
                                        
                                        <div class="notification-group">
                                            <h4>Sistem Bildirimleri</h4>
                                            <div class="form-check">
                                                <input type="checkbox" id="system_new_message" name="system_new_message" <?php echo (isset($userSettings['system_notifications']) && $userSettings['system_notifications']) ? 'checked' : ''; ?>>
                                                <label for="system_new_message">Yeni mesaj geldiğinde</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="system_task" name="system_task" <?php echo (isset($userSettings['system_notifications']) && $userSettings['system_notifications']) ? 'checked' : ''; ?>>
                                                <label for="system_task">Yeni görev atandığında</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="system_reminder" name="system_reminder" <?php echo (isset($userSettings['system_notifications']) && $userSettings['system_notifications']) ? 'checked' : ''; ?>>
                                                <label for="system_reminder">Etkinlik hatırlatmaları</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-buttons">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Ayarları Kaydet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($isAdmin): ?>
                        <div class="settings-section" id="system-section" style="display:none;">
                            <!-- Sistem ayarları içeriği -->
                        </div>
                        
                        <div class="settings-section" id="users-section" style="display:none;">
                            <!-- Kullanıcı yönetimi içeriği -->
                        </div>
                        
                        <div class="settings-section" id="backup-section" style="display:none;">
                            <!-- Yedekleme içeriği -->
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mobil menü toggle
            $('.menu-toggle').on('click', function() {
                $('.sidebar').toggleClass('active');
            });
            
            // Ayarlar menüsü
            $('.settings-menu li a').on('click', function(e) {
                e.preventDefault();
                
                // Aktif menü elemanını değiştir
                $('.settings-menu li').removeClass('active');
                $(this).parent().addClass('active');
                
                // İlgili bölümü göster
                var target = $(this).attr('href').substring(1) + '-section';
                $('.settings-section').hide();
                $('#' + target).show();
            });
        });
    </script>
</body>
</html>
