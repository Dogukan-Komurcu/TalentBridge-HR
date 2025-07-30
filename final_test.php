<?php
require_once 'config/database.php';

echo "<h2>✅ Final Test - Users Tablosu</h2>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";

try {
    // 1. Tablo yapısını göster
    echo "<h3>📋 Users Tablosu Yapısı:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Kolon</th><th>Tip</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Toplam kayıt sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $stmt->fetchColumn();
    echo "<h3>📊 Toplam Kayıt: {$total}</h3>";
    
    // 3. Employee API ile aynı INSERT testi
    echo "<h3>🚀 API Simülasyon Testi:</h3>";
    
    $testData = [
        'name' => 'Final Test User',
        'email' => 'final_test@test.com',
        'phone' => '05321234567',
        'position' => 'Test Developer',
        'department' => 'IT',
        'salary' => 25000,
        'start_date' => date('Y-m-d')
    ];
    
    // Email kontrolü
    $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $emailCheckStmt->execute([$testData['email']]);
    if ($emailCheckStmt->fetch()) {
        echo "<div class='error'>❌ Test email zaten kullanılıyor, farklı email deneyin</div>";
        $testData['email'] = 'final_test_' . time() . '@test.com';
        echo "<p>Yeni email: {$testData['email']}</p>";
    }
    
    // INSERT testi
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, position, department, salary, start_date, created_at, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    
    $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
    
    $result = $stmt->execute([
        $testData['name'],
        $testData['email'],
        $testData['phone'],
        'employee',
        $testData['position'],
        $testData['department'],
        $testData['salary'],
        $testData['start_date'],
        $passwordHash
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "<div class='success'>✅ BAŞARILI! Yeni kullanıcı eklendi. ID: #{$newId}</div>";
        
        // Eklenen veriyi göster
        $stmt = $pdo->prepare("SELECT id, name, email, phone, role, position, department, salary, start_date, created_at FROM users WHERE id = ?");
        $stmt->execute([$newId]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h4>📋 Eklenen Veri:</h4>";
        echo "<table>";
        echo "<tr><th>Alan</th><th>Değer</th></tr>";
        foreach($newUser as $key => $value) {
            echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
        }
        echo "</table>";
        
        echo "<div class='success'>";
        echo "<h4>🎉 TEST BAŞARILI!</h4>";
        echo "<p>Employee modal'ı artık çalışmalıdır.</p>";
        echo "<p><strong>Test adımları:</strong></p>";
        echo "<ol>";
        echo "<li>Employees sayfasına gidin</li>";
        echo "<li>'Yeni Çalışan Ekle' butonuna tıklayın</li>";
        echo "<li>Modal'daki formu doldurun</li>";
        echo "<li>'Çalışan Ekle' butonuna tıklayın</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>❌ INSERT başarısız!</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>";
    echo "❌ <strong>PDO Hatası:</strong><br>";
    echo "<strong>Kod:</strong> " . $e->getCode() . "<br>";
    echo "<strong>Mesaj:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
