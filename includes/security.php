<?php
/**
 * Güvenlikle ilgili fonksiyonlar ve kayıt (log) işlemleri
 */

// Veritabanı bağlantı bilgilerini al
global $host, $dbname, $username, $password;

/**
 * Güvenlik olaylarını kayıt altına alır
 * 
 * @param string $event_type Olay tipi (örn: LOGIN_ATTEMPT, PASSWORD_CHANGE, DATABASE_UPDATE)
 * @param string $description Olay açıklaması
 * @param array $additional_data Ek veriler (opsiyonel)
 * @return void
 */
function logSecurityEvent($event_type, $description, $additional_data = []) {
    global $host, $dbname, $username, $password;
    
    // Kullanıcı bilgilerini al
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Misafir';
    $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : 'guest');
    
    // IP adresini al
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    
    // Tarih ve zaman
    $date = date('Y-m-d H:i:s');
    
    // Ek verileri JSON formatına dönüştür
    $json_data = !empty($additional_data) ? json_encode($additional_data, JSON_UNESCAPED_UNICODE) : "{}";
    
    // Log dosyası yolu
    $log_file = __DIR__ . '/../logs/security.log';
    $log_dir = __DIR__ . '/../logs';
    
    // Logs klasörü yoksa oluştur
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    // Log formatı
    $log_entry = sprintf(
        "[%s] %s | User: %s (ID: %d, Role: %s) | IP: %s | %s | %s | %s\n",
        $date,
        $event_type,
        $user_name,
        $user_id,
        $user_role,
        $ip_address,
        substr($user_agent, 0, 150),
        $description,
        $json_data
    );
    
    // Log dosyasına yaz
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Kritik olayları veritabanına da kaydet
    $critical_events = [
        'LOGIN_FAILURE', 
        'PASSWORD_CHANGE', 
        'ADMIN_LOGIN', 
        'DATABASE_UPDATE', 
        'UNAUTHORIZED_ACCESS',
        'UNAUTHORIZED_DATABASE_UPDATE_ATTEMPT'
    ];
    
    if (in_array($event_type, $critical_events)) {
        try {
            // Veritabanı bağlantısı oluştur
            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Güvenlik logları tablosu yoksa oluştur
            $db->exec("CREATE TABLE IF NOT EXISTS security_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_type VARCHAR(50) NOT NULL,
                user_id INT DEFAULT 0,
                user_name VARCHAR(100),
                user_role VARCHAR(50),
                ip_address VARCHAR(50),
                description TEXT,
                additional_data TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Log kaydını ekle
            $stmt = $db->prepare("INSERT INTO security_logs 
                (event_type, user_id, user_name, user_role, ip_address, description, additional_data) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $event_type,
                $user_id,
                $user_name,
                $user_role,
                $ip_address,
                $description,
                $json_data
            ]);
        } catch (PDOException $e) {
            // Veritabanı hatası durumunda sadece dosyaya log at
            $error_log = sprintf("[%s] DATABASE_LOG_ERROR: %s\n", $date, $e->getMessage());
            file_put_contents($log_file, $error_log, FILE_APPEND);
        }
    }
}

/**
 * Güvenli form doğrulama tokeni oluşturur
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF tokeni doğrular
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        logSecurityEvent('CSRF_VALIDATION_FAILURE', 'CSRF token doğrulama hatası');
        return false;
    }
    return true;
}

/**
 * IP adresinden şüpheli aktivite kontrolü
 */
function checkSuspiciousActivity($ip_address = null) {
    $ip = $ip_address ?? $_SERVER['REMOTE_ADDR'];
    
    // Burada IP adresinden şüpheli aktiviteyi kontrol eden kod eklenebilir
    
    return false; // Şimdilik hep false döndür
}
