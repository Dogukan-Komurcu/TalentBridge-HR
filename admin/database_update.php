<?php
session_start();
// Önce veritabanı bağlantısını dahil et
require_once '../config/database.php';
// Sonra auth dosyasını dahil et
require_once '../includes/auth.php';

// Security dosyasını dahil et - hata olursa atlayacak
try {
    require_once '../includes/security.php';
} catch (Exception $e) {
    // Security dosyası yoksa veya hatalıysa basit bir log fonksiyonu tanımla
    if (!function_exists('logSecurityEvent')) {
        function logSecurityEvent($event_type, $description, $additional_data = []) {
            $log_file = __DIR__ . '/../logs/security.log';
            $log_dir = dirname($log_file);
            
            if (!file_exists($log_dir)) {
                mkdir($log_dir, 0755, true);
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $userId = $_SESSION['user_id'] ?? 'guest';
            $userEmail = $_SESSION['email'] ?? 'unknown';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            $logEntry = "[$timestamp] User: $userId ($userEmail) | IP: $ip | Event: $event_type | Details: $description" . PHP_EOL;
            
            file_put_contents($log_file, $logEntry, FILE_APPEND | LOCK_EX);
        }
    }
}

// Admin erişim kontrolü - Sadece admin'ler bu sayfaya erişebilir
checkAdminAccess();

// Güvenlik log'u
try {
    logSecurityEvent('ADMIN_AREA_ACCESS', 'Admin kullanıcı database_update.php sayfasına erişti', ['page' => 'database_update.php']);
} catch (Exception $e) {
    // Log hatası olursa sessizce devam et
}

$message = '';

// Sadece admin rolü veritabanını güncelleyebilir
if (isset($_POST['update_db']) && isAdmin()) {
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // SQL dosyasını oku
        $sql_file = '../database/user_update.sql';
        if (!file_exists($sql_file)) {
            throw new Exception("SQL dosyası bulunamadı: $sql_file");
        }
        
        $sql = file_get_contents($sql_file);
        if (!$sql) {
            throw new Exception("SQL dosyası okunamadı");
        }
        
        // SQL komutlarını ayır ve tek tek çalıştır
        $queries = explode(';', $sql);
        $successCount = 0;
        $errorMessages = [];
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            try {
                $db->exec($query);
                $successCount++;
            } catch (PDOException $e) {
                $errorMessages[] = "SQL hatası: " . $e->getMessage() . " (Sorgu: " . substr($query, 0, 50) . "...)";
            }
        }
        
        if (empty($errorMessages)) {
            $message = '<div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Veritabanı başarıyla güncellendi. Toplam çalıştırılan sorgu: ' . $successCount . '
                        </div>';
            
            // Güvenlik log'u
            try {
                logSecurityEvent('DATABASE_UPDATE', "Admin tarafından veritabanı güncellendi", ['queries' => $successCount]);
            } catch (Exception $e) {
                // Log hatası olursa sessizce devam et
            }
        } else {
            $message = '<div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Veritabanı güncellenirken bazı hatalar oluştu:<br>' . 
                            implode('<br>', $errorMessages) . '<br>
                            Başarılı sorgular: ' . $successCount . '
                        </div>';
        }
    } catch (Exception $e) {
        $message = '<div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        Veritabanı güncellenirken hata oluştu: ' . $e->getMessage() . '
                    </div>';
    }
} elseif (isset($_POST['update_db']) && !isAdmin()) {
    // Yetkisiz erişim denemesi log'la
    try {
        logSecurityEvent('UNAUTHORIZED_DATABASE_UPDATE_ATTEMPT', 'Yetkisiz kullanıcı veritabanı güncelleme denedi');
    } catch (Exception $e) {
        // Log hatası olursa sessizce devam et
    }
    $message = '<div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    Bu işlemi yapma yetkiniz bulunmuyor.
                </div>';
}

// Kullanıcı tablosunun yapısını kontrol et
$columns = [];
$tableExists = false;

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tablo var mı diye kontrol et
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $stmt = $db->prepare("DESCRIBE users");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $message .= '<div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        users tablosu mevcut değil! Lütfen önce temel veritabanı yapısını oluşturun.
                    </div>';
    }
    
    // user_settings tablosunu kontrol et
    $tableExists = false;
    $stmt = $db->prepare("SHOW TABLES LIKE 'user_settings'");
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;
} catch (PDOException $e) {
    $message .= '<div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    Veritabanı tablosu kontrol edilirken hata oluştu: ' . $e->getMessage() . '
                </div>';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Güncelleme - TalentBridge</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .db-status-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .db-status-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            flex: 1;
            min-width: 280px;
            position: relative;
            overflow: hidden;
        }
        
        .db-status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .db-status-card.ready::before {
            background-color: #28a745;
        }
        
        .db-status-card.warning::before {
            background-color: #ffc107;
        }
        
        .db-status-card.error::before {
            background-color: #dc3545;
        }
        
        .db-status-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .db-status-title i {
            margin-right: 10px;
            font-size: 22px;
        }
        
        .db-status-details {
            margin-bottom: 15px;
        }
        
        .progress-container {
            height: 6px;
            background: #f1f1f1;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s;
        }
        
        .progress-ready {
            background-color: #28a745;
        }
        
        .progress-warning {
            background-color: #ffc107;
        }
        
        .progress-error {
            background-color: #dc3545;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        
        .badge-ready {
            background-color: #28a745;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-error {
            background-color: #dc3545;
        }
        
        .check-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .check-list li {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            background: #f8f9fa;
        }
        
        .check-list li.success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
        }
        
        .check-list li.error {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
        }
        
        .check-list li i {
            margin-right: 10px;
        }
        
        .check-list li i.fa-check {
            color: #28a745;
        }
        
        .check-list li i.fa-times {
            color: #dc3545;
        }
        
        .update-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 30px;
            background: linear-gradient(135deg, #2c3e50, #4CA1AF);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
            transition: all 0.3s;
        }
        
        .update-btn:hover {
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.3);
            transform: translateY(-2px);
        }
        
        .update-btn i {
            margin-right: 10px;
        }
        
        .update-notes {
            border-left: 4px solid #2c3e50;
            padding-left: 20px;
        }
        
        .update-version {
            display: inline-block;
            padding: 5px 10px;
            background: #2c3e50;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .update-notes h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .update-notes ul {
            padding-left: 20px;
        }
        
        .update-notes li {
            margin-bottom: 8px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar_main.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/topbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title"><i class="fas fa-database"></i> Veritabanı Güncelleme</h1>
                </div>
                
                <?php echo $message; ?>
                
                <div class="db-status-container">
                    <?php
                    // Kullanıcı tablosu için eksik sütunları hesapla
                    $requiredColumns = ['id', 'name', 'email', 'password', 'profile_image', 'phone', 'role', 'position', 
                                     'department', 'location', 'bio', 'salary', 'start_date', 'status', 'created_at', 
                                     'updated_at', 'last_login'];
                    $missingColumns = array_diff($requiredColumns, $columns);
                    $existingColumns = array_intersect($requiredColumns, $columns);
                    $usersProgress = empty($requiredColumns) ? 0 : round((count($existingColumns) / count($requiredColumns)) * 100);
                    
                    $usersStatus = 'ready';
                    if (count($missingColumns) > 0 && count($missingColumns) <= 3) {
                        $usersStatus = 'warning';
                    } else if (count($missingColumns) > 3) {
                        $usersStatus = 'error';
                    }
                    ?>
                    
                    <div class="db-status-card <?php echo $usersStatus; ?>">
                        <div class="db-status-title">
                            <i class="fas fa-users"></i> Kullanıcı Tablosu
                        </div>
                        <div class="db-status-details">
                            <p>Gerekli sütunlar: <strong><?php echo count($requiredColumns); ?></strong></p>
                            <p>Mevcut sütunlar: <strong><?php echo count($existingColumns); ?></strong></p>
                            <p>Eksik sütunlar: <strong><?php echo count($missingColumns); ?></strong></p>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-<?php echo $usersStatus; ?>" style="width: <?php echo $usersProgress; ?>%"></div>
                        </div>
                        <div class="status-text">
                            <span class="status-badge badge-<?php echo $usersStatus; ?>">
                                <?php 
                                if ($usersStatus === 'ready') echo 'Hazır';
                                else if ($usersStatus === 'warning') echo 'Güncelleme Gerekli';
                                else echo 'Önemli Eksikler';
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="db-status-card <?php echo $tableExists ? 'ready' : 'error'; ?>">
                        <div class="db-status-title">
                            <i class="fas fa-bell"></i> Kullanıcı Ayarları Tablosu
                        </div>
                        <div class="db-status-details">
                            <p>Tablo durumu: <strong><?php echo $tableExists ? 'Mevcut' : 'Mevcut Değil'; ?></strong></p>
                            <p>Bu tablo, kullanıcı bildirim tercihlerini ve diğer ayarları saklamak için gereklidir.</p>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-<?php echo $tableExists ? 'ready' : 'error'; ?>" style="width: <?php echo $tableExists ? '100' : '0'; ?>%"></div>
                        </div>
                        <div class="status-text">
                            <span class="status-badge badge-<?php echo $tableExists ? 'ready' : 'error'; ?>">
                                <?php echo $tableExists ? 'Hazır' : 'Oluşturulması Gerekli'; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Veritabanı Detaylı Durum</h3>
                    </div>
                    <div class="card-content">
                        <h4>Users Tablosu Sütunları:</h4>
                        <ul class="check-list">
                            <?php foreach($requiredColumns as $col): ?>
                            <li class="<?php echo in_array($col, $columns) ? 'success' : 'error'; ?>">
                                <i class="fas fa-<?php echo in_array($col, $columns) ? 'check' : 'times'; ?>"></i>
                                <?php echo $col; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <?php if (isAdmin()): // Sadece admin görebilsin ?>
                        <div style="text-align: center; margin-top: 30px;">
                            <form method="post" action="">
                                <p style="margin-bottom: 20px;">
                                    <?php if (count($missingColumns) > 0 || !$tableExists): ?>
                                    Veritabanı güncellemesi gerekiyor. Kullanıcı profilleri ve ayarlar için gerekli tabloları oluşturmak/güncellemek için aşağıdaki butona tıklayın.
                                    <?php else: ?>
                                    Veritabanı güncel görünüyor. Herhangi bir sorun olursa güncelleme yapabilirsiniz.
                                    <?php endif; ?>
                                </p>
                                <button type="submit" name="update_db" class="update-btn">
                                    <i class="fas fa-sync-alt"></i> Veritabanını Güncelle
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Uygulama Güncelleme Notları</h3>
                    </div>
                    <div class="card-content">
                        <div class="update-notes">
                            <div class="update-note">
                                <span class="update-version">v1.2.0</span>
                                <h4>Gelişmiş Güvenlik ve Veritabanı Yönetimi</h4>
                                <ul>
                                    <li>Gelişmiş güvenlik logları eklendi</li>
                                    <li>Veritabanı yönetim arayüzü yenilendi</li>
                                    <li>Rol bazlı erişim kontrolü iyileştirildi</li>
                                    <li>Veritabanı güncelleme işlemleri artık daha güvenli</li>
                                    <li>Performans iyileştirmeleri yapıldı</li>
                                </ul>
                            </div>
                            
                            <div class="update-note" style="margin-top: 20px;">
                                <span class="update-version">v1.1.0</span>
                                <h4>Kullanıcı Profili Geliştirmeleri</h4>
                                <ul>
                                    <li>Kullanıcı profillerine profil fotoğrafı desteği eklendi</li>
                                    <li>Biyografi ve konum bilgileri eklendi</li>
                                    <li>Rol bazlı profil düzenleme özellikleri eklendi</li>
                                    <li>Bildirim tercihleri için ayarlar eklendi</li>
                                    <li>Güvenlik iyileştirmeleri yapıldı</li>
                                </ul>
                            </div>
                        </div>
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
        });
    </script>
</body>
</html>
