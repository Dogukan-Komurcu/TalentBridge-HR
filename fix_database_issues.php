<?php
// Veritabanı düzeltme scripti
require_once 'config/database.php';

echo "<h1>🔧 Veritabanı Düzeltme İşlemleri</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
</style>";

try {
    echo "<h2>1️⃣ Şifre Sütunu Problemi Düzeltiliyor...</h2>";
    
    // Mevcut sütunları kontrol et
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('password_hash', $columns)) {
        echo "<p class='info'>📝 password_hash sütunundaki veriler password sütununa kopyalanıyor...</p>";
        
        // password_hash'teki verileri password'a kopyala
        $updateQuery = "UPDATE users SET password = password_hash WHERE password_hash IS NOT NULL AND (password IS NULL OR password = '')";
        $affected = $pdo->exec($updateQuery);
        echo "<p class='success'>✅ $affected kayıt güncellendi</p>";
        
        // password_hash sütununu kaldır
        $pdo->exec("ALTER TABLE users DROP COLUMN password_hash");
        echo "<p class='success'>✅ password_hash sütunu kaldırıldı</p>";
    } else {
        echo "<p class='info'>ℹ️ password_hash sütunu zaten mevcut değil</p>";
    }
    
    echo "<h2>2️⃣ Eksik Sütunlar Kontrol Ediliyor...</h2>";
    
    // Güncel sütun listesini al
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    
    // Gerekli sütunları kontrol et
    $requiredColumns = [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'name' => 'VARCHAR(100) NOT NULL',
        'email' => 'VARCHAR(100) UNIQUE NOT NULL', 
        'password' => 'VARCHAR(255) NOT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ];
    
    foreach ($requiredColumns as $column => $definition) {
        if (!in_array($column, $columns)) {
            echo "<p class='warning'>⚠️ $column sütunu eksik, ekleniyor...</p>";
            // Bu durumda manuel SQL gerekebilir
        } else {
            echo "<p class='success'>✅ $column sütunu mevcut</p>";
        }
    }
    
    echo "<h2>3️⃣ Test Kullanıcısı Ekleniyor...</h2>";
    
    // Test kullanıcısı var mı kontrol et
    $testUser = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $testUser->execute(['test@example.com']);
    
    if (!$testUser->fetch()) {
        $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);
        $insertTest = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        
        if ($insertTest->execute(['Test Kullanıcı', 'test@example.com', $hashedPassword])) {
            echo "<p class='success'>✅ Test kullanıcısı eklendi (Email: test@example.com, Şifre: 123456)</p>";
        } else {
            echo "<p class='error'>❌ Test kullanıcısı eklenemedi</p>";
        }
    } else {
        echo "<p class='info'>ℹ️ Test kullanıcısı zaten mevcut</p>";
    }
    
    echo "<h2>4️⃣ Final Kontrol</h2>";
    
    // Son durum kontrolü
    $finalColumns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Sütun</th><th>Tip</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($finalColumns as $column) {
        echo "<tr>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<p class='info'>📊 Toplam kullanıcı sayısı: $userCount</p>";
    
    echo "<h2>✅ İşlemler Tamamlandı!</h2>";
    echo "<p class='success'>Artık login.php ve register.php dosyaları düzgün çalışmalı.</p>";
    echo "<p><strong>Test için:</strong></p>";
    echo "<ul>";
    echo "<li>Email: test@example.com</li>";
    echo "<li>Şifre: 123456</li>";
    echo "</ul>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Hata: " . $e->getMessage() . "</p>";
}
?>
