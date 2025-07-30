<?php
// Test API endpoint
session_start();
require_once '../config/database.php';

// Test için session ayarla
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Admin';
    $_SESSION['role'] = 'admin';
}

// Test verileri
$testData = [
    'action' => 'add_user',
    'full_name' => 'Test Kullanıcı',
    'email' => 'test@test.com',
    'phone' => '05321234567',
    'position' => 'Developer',
    'department' => 'IT',
    'role' => 'employee',
    'password' => '123456'
];

// POST isteği simüle et
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>API Testi</h2>";
echo "<p>Test verisi gönderiliyor...</p>";

// API'yi çağır
ob_start();
include '../api/data_import.php';
$response = ob_get_clean();

echo "<h3>API Yanıtı:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// JSON geçerli mi kontrol et
$json = json_decode($response, true);
if ($json) {
    echo "<h3>JSON Ayrıştırma:</h3>";
    echo "<pre>" . print_r($json, true) . "</pre>";
} else {
    echo "<p style='color: red;'>JSON geçersiz!</p>";
}
?>
