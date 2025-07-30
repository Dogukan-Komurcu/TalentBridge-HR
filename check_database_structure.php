<?php
// Veritabanı yapısını kontrol eden script
require_once 'config/database.php';

echo "<h1>🔍 TalentBridge Veritabanı Yapı Kontrolü</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .error { color: red; font-weight: bold; }
    .success { color: green; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .section { margin: 30px 0; }
</style>";

try {
    // Tüm tabloları listele
    echo "<div class='section'>";
    echo "<h2>📋 Mevcut Tablolar</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    echo "</div>";

    // Her tablo için yapı bilgisi
    foreach ($tables as $table) {
        echo "<div class='section'>";
        echo "<h3>📊 $table Tablosu Yapısı</h3>";
        
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Sütun</th><th>Tip</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td><strong>" . $column['Field'] . "</strong></td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    // Kod uyumluluğu kontrolü
    echo "<div class='section'>";
    echo "<h2>⚠️ Kod Uyumluluk Analizi</h2>";
    
    // Users tablosu kontrolleri
    echo "<h3>Users Tablosu Kontrolleri:</h3>";
    $userColumns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = ['id', 'name', 'email', 'password', 'created_at'];
    $optionalColumns = ['phone', 'role', 'position', 'department', 'salary', 'start_date', 'password_hash'];
    
    echo "<h4>Gerekli Sütunlar:</h4>";
    echo "<ul>";
    foreach ($requiredColumns as $col) {
        if (in_array($col, $userColumns)) {
            echo "<li class='success'>✅ $col - Mevcut</li>";
        } else {
            echo "<li class='error'>❌ $col - EKSİK!</li>";
        }
    }
    echo "</ul>";
    
    echo "<h4>İsteğe Bağlı Sütunlar:</h4>";
    echo "<ul>";
    foreach ($optionalColumns as $col) {
        if (in_array($col, $userColumns)) {
            echo "<li class='success'>✅ $col - Mevcut</li>";
        } else {
            echo "<li class='warning'>⚠️ $col - Eksik (isteğe bağlı)</li>";
        }
    }
    echo "</ul>";
    
    // Şifre sütunu kontrolü
    echo "<h4>Şifre Sütunu Problemi:</h4>";
    if (in_array('password', $userColumns) && in_array('password_hash', $userColumns)) {
        echo "<p class='error'>❌ Hem 'password' hem 'password_hash' sütunu mevcut! Bu sorun yaratabilir.</p>";
        echo "<p>Çözüm: Sadece 'password' sütunu kullanılmalı.</p>";
    } else if (in_array('password', $userColumns)) {
        echo "<p class='success'>✅ 'password' sütunu mevcut</p>";
    } else if (in_array('password_hash', $userColumns)) {
        echo "<p class='warning'>⚠️ Sadece 'password_hash' mevcut. Kodlarda 'password' kullanılıyor.</p>";
    }
    
    echo "</div>";

    // Veri kontrolü
    echo "<div class='section'>";
    echo "<h2>📊 Veri Durumu</h2>";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "<p><strong>$table:</strong> $count kayıt</p>";
    }
    echo "</div>";

} catch (PDOException $e) {
    echo "<p class='error'>Hata: " . $e->getMessage() . "</p>";
}
?>
