<?php
require_once 'config/database.php';

echo "<h2>Users Tablosu - İlk Kayıt:</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>Kolon</th><th>Değer</th></tr>";
        foreach($user as $key => $value) {
            echo "<tr>";
            echo "<td><strong>{$key}</strong></td>";
            echo "<td>{$value}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tabloda veri yok</p>";
    }
    
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}

echo "<h2>Yeni Kullanıcı Ekleme Testi:</h2>";
try {
    // Test verisi ekle
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, password_hash, position, department, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        'Test User ' . date('H:i:s'),
        'test' . time() . '@test.com',
        '05321234567',
        'employee',
        password_hash('123456', PASSWORD_DEFAULT),
        'Test Pozisyon',
        'Test Departman'
    ]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Başarıyla eklendi! ID: " . $pdo->lastInsertId() . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Ekleme başarısız</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
}
?>
