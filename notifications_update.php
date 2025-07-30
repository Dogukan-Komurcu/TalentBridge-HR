<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

// Kullanıcı giriş kontrolü
checkLogin();

// Sadece admin ve manager rollerinin bildirim ayarlarını değiştirebilmesi için kontrol
$userRole = getUserRole();
if ($userRole !== 'admin' && $userRole !== 'manager') {
    header('Location: settings.php');
    exit;
}

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: settings.php');
    exit;
}

// Kullanıcı kimliğini oturumdan al
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    $_SESSION['error'] = 'Oturum bilgisi bulunamadı. Lütfen tekrar giriş yapın.';
    header('Location: login.php');
    exit;
}

// Form verilerini al
$emailNotifications = isset($_POST['email_new_application']) || isset($_POST['email_new_job']) || isset($_POST['email_interview']) ? 1 : 0;
$systemNotifications = isset($_POST['system_new_message']) || isset($_POST['system_task']) || isset($_POST['system_reminder']) ? 1 : 0;

try {
    // Veritabanı bağlantısı
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kullanıcı ayarlarını kontrol et
    $stmt = $db->prepare("SELECT * FROM user_settings WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $userSettings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userSettings) {
        // Mevcut ayarları güncelle
        $stmt = $db->prepare("UPDATE user_settings SET email_notifications = :email, system_notifications = :system, updated_at = NOW() WHERE user_id = :user_id");
    } else {
        // Yeni ayarlar oluştur
        $stmt = $db->prepare("INSERT INTO user_settings (user_id, email_notifications, system_notifications) VALUES (:user_id, :email, :system)");
    }
    
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':email', $emailNotifications);
    $stmt->bindParam(':system', $systemNotifications);
    $stmt->execute();
    
    $_SESSION['success'] = 'Bildirim ayarlarınız başarıyla güncellendi.';
    header('Location: settings.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    header('Location: settings.php');
    exit;
}
