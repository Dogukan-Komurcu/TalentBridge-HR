<?php
session_start();
require_once 'config/database.php';

echo "<h2>🧪 Employee API Test</h2>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";

// Test verisi
$testData = [
    'name' => 'Test Employee ' . date('H:i:s'),
    'email' => 'test' . time() . '@example.com',
    'phone' => '05321234567',
    'position' => 'Test Position',
    'department' => 'IT',
    'salary' => 15000,
    'start_date' => date('Y-m-d')
];

echo "<h3>📝 Test Verisi:</h3>";
echo "<ul>";
foreach($testData as $key => $value) {
    echo "<li><strong>{$key}:</strong> {$value}</li>";
}
echo "</ul>";

try {
    // 1. Tablo yapısını kontrol et
    echo "<h3>🔍 Tablo Yapısı Kontrolü:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Mevcut Kolonlar:</strong> " . implode(', ', $columns) . "</p>";
    
    // 2. Email kontrolü testi
    echo "<h3>📧 Email Kontrolü:</h3>";
    $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $emailCheckStmt->execute([$testData['email']]);
    $existingUser = $emailCheckStmt->fetch();
    
    if ($existingUser) {
        echo "<p class='error'>❌ Bu email zaten kullanılıyor: ID " . $existingUser['id'] . "</p>";
    } else {
        echo "<p class='success'>✅ Email uygun</p>";
        
        // 3. INSERT testi
        echo "<h3>💾 INSERT Testi:</h3>";
        
        $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
        
        $insertSql = "INSERT INTO users (name, email, phone, role, position, department, salary, start_date, created_at, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        
        echo "<p><strong>SQL Sorgusu:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>{$insertSql}</pre>";
        
        $stmt = $pdo->prepare($insertSql);
        
        $params = [
            $testData['name'],
            $testData['email'],
            $testData['phone'],
            'employee',
            $testData['position'],
            $testData['department'],
            $testData['salary'],
            $testData['start_date'],
            $passwordHash
        ];
        
        echo "<p><strong>Parametreler:</strong></p>";
        echo "<ol>";
        foreach($params as $i => $param) {
            if ($i == 8) { // password_hash
                echo "<li>[HASH] (gizli)</li>";
            } else {
                echo "<li>{$param}</li>";
            }
        }
        echo "</ol>";
        
        $result = $stmt->execute($params);
        
        if ($result) {
            $newId = $pdo->lastInsertId();
            echo "<div class='success'>";
            echo "✅ <strong>BAŞARILI!</strong> Yeni kullanıcı eklendi. ID: #{$newId}";
            echo "</div>";
            
            // Eklenen kaydı göster
            $checkStmt = $pdo->prepare("SELECT id, name, email, phone, role, position, department, salary, start_date, created_at FROM users WHERE id = ?");
            $checkStmt->execute([$newId]);
            $newUser = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h4>📋 Eklenen Kayıt:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            foreach($newUser as $key => $value) {
                echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
            }
            echo "</table>";
            
        } else {
            echo "<div class='error'>❌ INSERT başarısız!</div>";
        }
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>";
    echo "❌ <strong>PDO Hatası:</strong><br>";
    echo "<strong>Kod:</strong> " . $e->getCode() . "<br>";
    echo "<strong>Mesaj:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Dosya:</strong> " . $e->getFile() . ":" . $e->getLine();
    echo "</div>";
} catch(Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Genel Hata:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>📝 Sonuç:</strong> Bu test başarılıysa, employee modal'ı da çalışmalıdır.</p>";
?>
