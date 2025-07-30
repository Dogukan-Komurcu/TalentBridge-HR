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

// Form verilerini al ve temizle
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';

// Gerekli alanları kontrol et
if (empty($name) || empty($email)) {
    $_SESSION['error'] = 'Ad Soyad ve E-posta alanları zorunludur.';
    header('Location: settings.php');
    exit;
}

// E-posta formatını kontrol et
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Geçerli bir e-posta adresi giriniz.';
    header('Location: settings.php');
    exit;
}

try {
    // Veritabanı bağlantısı
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kullanıcı bilgilerini güncelle
    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, location = :location, bio = :bio, updated_at = NOW() WHERE id = :id");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':id', $userId);
    
    $stmt->execute();
    
    // Profil fotoğrafı yükleme işlemi (eğer dosya yüklendiyse)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Sadece JPEG, PNG ve GIF formatındaki dosyalar kabul edilmektedir.';
            header('Location: settings.php');
            exit;
        }
        
        if ($_FILES['profile_image']['size'] > $maxSize) {
            $_SESSION['error'] = 'Dosya boyutu en fazla 2MB olabilir.';
            header('Location: settings.php');
            exit;
        }
        
        // Dosya adını oluştur
        $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
        $uploadPath = 'assets/images/profiles/' . $newFileName;
        
        // Profiles klasörü yoksa oluştur
        if (!file_exists('assets/images/profiles/')) {
            mkdir('assets/images/profiles/', 0777, true);
        }
        
        // Dosyayı taşı
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
            // Veritabanında profil resmi yolunu güncelle
            $stmt = $db->prepare("UPDATE users SET profile_image = :profile_image WHERE id = :id");
            $stmt->bindParam(':profile_image', $uploadPath);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } else {
            $_SESSION['error'] = 'Dosya yüklenirken bir hata oluştu.';
            header('Location: settings.php');
            exit;
        }
    }
    
    // Oturum bilgilerini güncelle
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;
    $_SESSION['location'] = $location;
    $_SESSION['bio'] = $bio;
    
    if (isset($uploadPath)) {
        $_SESSION['profile_image'] = $uploadPath;
    }
    
    $_SESSION['success'] = 'Profil bilgileriniz başarıyla güncellendi.';
    header('Location: settings.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    header('Location: settings.php');
    exit;
}
