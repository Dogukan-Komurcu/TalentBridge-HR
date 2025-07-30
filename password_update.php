<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

// Kullanıcı giriş kontrolü
checkLogin();

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
$currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Gerekli alanları kontrol et
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['error'] = 'Tüm şifre alanları zorunludur.';
    header('Location: settings.php');
    exit;
}

// Yeni şifre ve onay şifresinin eşleştiğini kontrol et
if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = 'Yeni şifre ve şifre tekrarı eşleşmiyor.';
    header('Location: settings.php');
    exit;
}

// Şifre gereksinimlerini kontrol et
if (strlen($newPassword) < 8) {
    $_SESSION['error'] = 'Şifre en az 8 karakter olmalıdır.';
    header('Location: settings.php');
    exit;
}

if (!preg_match('/[A-Z]/', $newPassword)) {
    $_SESSION['error'] = 'Şifre en az bir büyük harf içermelidir.';
    header('Location: settings.php');
    exit;
}

if (!preg_match('/[a-z]/', $newPassword)) {
    $_SESSION['error'] = 'Şifre en az bir küçük harf içermelidir.';
    header('Location: settings.php');
    exit;
}

if (!preg_match('/[0-9]/', $newPassword)) {
    $_SESSION['error'] = 'Şifre en az bir rakam içermelidir.';
    header('Location: settings.php');
    exit;
}

if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
    $_SESSION['error'] = 'Şifre en az bir özel karakter içermelidir.';
    header('Location: settings.php');
    exit;
}

try {
    // Veritabanı bağlantısı
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mevcut şifreyi kontrol et
    $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['error'] = 'Kullanıcı bulunamadı.';
        header('Location: settings.php');
        exit;
    }
    
    // Şifre doğrulama
    if (!password_verify($currentPassword, $user['password'])) {
        $_SESSION['error'] = 'Mevcut şifre yanlış.';
        header('Location: settings.php');
        exit;
    }
    
    // Yeni şifreyi hashle
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Şifreyi güncelle
    $stmt = $db->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    $_SESSION['success'] = 'Şifreniz başarıyla güncellendi.';
    header('Location: settings.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    header('Location: settings.php');
    exit;
}
