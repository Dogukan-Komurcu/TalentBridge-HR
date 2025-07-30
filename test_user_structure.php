<?php
require_once 'config/database.php';

echo "<h2>📊 Users Tablosu - Güncel Yapı</h2>";
echo "<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f8f9fa; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
th { background: #f8f9fa; font-weight: bold; }
.status-ok { color: #28a745; font-weight: bold; }
.status-error { color: #dc3545; font-weight: bold; }
</style>";

try {
    // Tablo yapısını göster
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>🏗️ Tablo Yapısı:</h3>";
    echo "<table>";
    echo "<tr><th>Kolon Adı</th><th>Veri Tipi</th><th>Null</th><th>Anahtar</th><th>Varsayılan</th><th>Extra</th></tr>";
    
    foreach($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test INSERT yapacağımız alanları kontrol et
    echo "<h3>🧪 Test Verisi Ekleme:</h3>";
    
    $testData = [
        'name' => 'Test Çalışan ' . date('H:i:s'),
        'email' => 'test' . time() . '@test.com',
        'phone' => '05321234567',
        'role' => 'employee',
        'position' => 'Test Pozisyon',
        'department' => 'IT',
        'salary' => 15000,
        'start_date' => date('Y-m-d')
    ];
    
    echo "<p><strong>Eklenecek test verisi:</strong></p>";
    echo "<ul>";
    foreach($testData as $key => $value) {
        echo "<li><strong>{$key}:</strong> {$value}</li>";
    }
    echo "</ul>";
    
    // Test INSERT
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, position, department, salary, start_date, created_at, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    
    $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
    
    $result = $stmt->execute([
        $testData['name'],
        $testData['email'],
        $testData['phone'],
        $testData['role'],
        $testData['position'],
        $testData['department'],
        $testData['salary'],
        $testData['start_date'],
        $passwordHash
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "✅ <strong>BAŞARILI!</strong> Test kullanıcısı eklendi. ID: #{$newId}";
        echo "</div>";
        
        // Eklenen veriyi göster
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$newId]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h4>📋 Eklenen Veri:</h4>";
        echo "<table>";
        echo "<tr><th>Alan</th><th>Değer</th></tr>";
        foreach($newUser as $key => $value) {
            if ($key !== 'password_hash') { // Şifreyi gösterme
                echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
            }
        }
        echo "</table>";
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;'>";
        echo "❌ <strong>HATA!</strong> Test kullanıcısı eklenemedi.";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;'>";
    echo "<strong>❌ Veritabanı Hatası:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #6c757d;'>";
echo "✅ Artık employee modal'ı çalışmalı!";
echo "</p>";
?>
