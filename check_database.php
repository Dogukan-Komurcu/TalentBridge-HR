<?php
require_once 'config/database.php';

echo "<h2>📊 Users Tablosu - Güncel Durumu</h2>";

try {
    // Toplam kullanıcı sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<h3>📈 Toplam Kullanıcı Sayısı: <strong>{$total}</strong></h3>";
    echo "</div>";
    
    // Son 5 kullanıcıyı göster
    echo "<h3>👥 Son Eklenen 5 Kullanıcı:</h3>";
    $stmt = $pdo->query("SELECT id, name, email, position, department, phone, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($users)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Ad Soyad</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Email</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Pozisyon</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Departman</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Telefon</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Eklenme Tarihi</th>";
        echo "</tr>";
        
        foreach($users as $user) {
            echo "<tr>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'><strong>#{$user['id']}</strong></td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$user['name']}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$user['email']}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$user['position']}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$user['department']}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$user['phone']}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . date('d.m.Y H:i:s', strtotime($user['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: #dc3545;'>❌ Hiç kullanıcı bulunamadı</p>";
    }
    
    // Bugün eklenen kullanıcılar
    echo "<h3>📅 Bugün Eklenen Kullanıcılar:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as today_count FROM users WHERE DATE(created_at) = CURDATE()");
    $todayCount = $stmt->fetch(PDO::FETCH_ASSOC)['today_count'];
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<h4>🗓️ Bugün Eklenen: <strong>{$todayCount}</strong> kullanıcı</h4>";
    echo "</div>";
    
    if ($todayCount > 0) {
        $stmt = $pdo->query("SELECT name, email, created_at FROM users WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC");
        $todayUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
        foreach($todayUsers as $user) {
            $time = date('H:i:s', strtotime($user['created_at']));
            echo "<li style='margin: 5px 0;'><strong>{$user['name']}</strong> ({$user['email']}) - {$time}</li>";
        }
        echo "</ul>";
    }
    
    // Real-time test - Şu an bir test kullanıcısı ekleyelim
    echo "<hr><h3>🧪 Canlı Test - Yeni Kullanıcı Ekleme:</h3>";
    
    $testName = "Test User " . date('H:i:s');
    $testEmail = "test" . time() . "@test.com";
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, password_hash, position, department, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        $testName,
        $testEmail,
        '05321234567',
        'employee',
        password_hash('123456', PASSWORD_DEFAULT),
        'Test Pozisyon',
        'Test Departman'
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "✅ <strong>BAŞARILI!</strong> Yeni kullanıcı eklendi:";
        echo "<ul>";
        echo "<li><strong>ID:</strong> #{$newId}</li>";
        echo "<li><strong>Ad:</strong> {$testName}</li>";
        echo "<li><strong>Email:</strong> {$testEmail}</li>";
        echo "<li><strong>Zaman:</strong> " . date('d.m.Y H:i:s') . "</li>";
        echo "</ul>";
        echo "</div>";
        
        // Güncel toplam sayı
        $stmt = $pdo->query("SELECT COUNT(*) as new_total FROM users");
        $newTotal = $stmt->fetch(PDO::FETCH_ASSOC)['new_total'];
        echo "<p style='font-size: 18px; color: #28a745;'><strong>🆕 Güncel Toplam: {$newTotal} kullanıcı</strong></p>";
    } else {
        echo "<p style='color: #dc3545;'>❌ Test kullanıcısı eklenemedi!</p>";
    }
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;'>";
    echo "<strong>❌ Veritabanı Hatası:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #6c757d;'>";
echo "🔄 <strong>Sayfayı yenileyin</strong> ve Data Import formundan yeni kullanıcı ekleyip tekrar kontrol edin.";
echo "</p>";
?>

<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin: 20px; 
    background: #f8f9fa; 
}
h2, h3 { color: #343a40; }
table { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>
