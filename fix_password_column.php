<?php
require_once 'config/database.php';

echo "<h2>🔍 Password Kolon Analizi</h2>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
.warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";

try {
    // Mevcut kolonları kontrol et
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Mevcut Kolonlar:</h3>";
    echo "<table>";
    echo "<tr><th>Kolon Adı</th><th>Tip</th><th>Null</th><th>Default</th></tr>";
    
    $passwordColumns = [];
    foreach($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
        
        // Password ile ilgili kolonları bul
        if (strpos(strtolower($column['Field']), 'password') !== false) {
            $passwordColumns[] = $column['Field'];
        }
    }
    echo "</table>";
    
    echo "<h3>🔐 Password Kolonları:</h3>";
    if (empty($passwordColumns)) {
        echo "<div class='error'>❌ Password ile ilgili kolon bulunamadı!</div>";
        
        // password_hash kolonunu ekle
        echo "<h4>🔧 password_hash Kolonu Ekleme:</h4>";
        $pdo->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL");
        echo "<div class='success'>✅ password_hash kolonu eklendi</div>";
        
    } else {
        echo "<ul>";
        foreach($passwordColumns as $col) {
            echo "<li><strong>{$col}</strong></li>";
        }
        echo "</ul>";
        
        // Eğer password var ama password_hash yoksa
        if (in_array('password', $passwordColumns) && !in_array('password_hash', $passwordColumns)) {
            echo "<div class='warning'>⚠️ 'password' kolonu mevcut ama 'password_hash' yok. Ekleniyor...</div>";
            $pdo->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL");
            echo "<div class='success'>✅ password_hash kolonu eklendi</div>";
        }
    }
    
    // Test INSERT
    echo "<h3>🧪 Test INSERT:</h3>";
    
    $testName = 'Test User ' . date('H:i:s');
    $testEmail = 'test' . time() . '@test.com';
    $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
    
    // Önce hangi password kolonunu kullanacağımızı belirle
    $passwordColumn = 'password_hash';
    if (in_array('password', $passwordColumns) && !in_array('password_hash', $passwordColumns)) {
        $passwordColumn = 'password';
    }
    
    $sql = "INSERT INTO users (name, email, phone, role, position, department, {$passwordColumn}, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    echo "<p><strong>Kullanılacak SQL:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>{$sql}</pre>";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $testName,
        $testEmail,
        '05321234567',
        'employee',
        'Test Position',
        'IT',
        $passwordHash
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "<div class='success'>✅ Test başarılı! Yeni ID: #{$newId}</div>";
        
        // API dosyasını güncelle
        echo "<h4>📝 API Güncellemesi Gerekli:</h4>";
        echo "<p>API dosyasında <code>{$passwordColumn}</code> kolonu kullanılmalı.</p>";
        
    } else {
        echo "<div class='error'>❌ Test başarısız!</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Hata:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>📝 Sonuç:</strong> Password kolonu analizi tamamlandı.</p>";
?>
