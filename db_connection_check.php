<?php
echo "<h1>🔍 TalentBridge - Veritabanı Bağlantı Analizi</h1>";
echo "<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f8f9fa; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; }
.warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0; }
.info { background: #cce7ff; color: #004085; padding: 15px; border-radius: 8px; margin: 10px 0; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
th { background: #f8f9fa; font-weight: bold; }
.status-ok { color: #28a745; font-weight: bold; }
.status-error { color: #dc3545; font-weight: bold; }
.file-path { font-family: monospace; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; }
</style>";

// Database konfigürasyon dosyalarını test et
$configFiles = [
    'config/database.php' => 'Ana Database Config',
    'includes/db.php' => 'Alternatif DB Config'
];

echo "<h2>📁 Database Konfigürasyon Dosyaları</h2>";
echo "<table>";
echo "<tr><th>Dosya</th><th>Açıklama</th><th>Durum</th><th>Bağlantı Testi</th></tr>";

foreach ($configFiles as $file => $description) {
    echo "<tr>";
    echo "<td><span class='file-path'>{$file}</span></td>";
    echo "<td>{$description}</td>";
    
    if (file_exists($file)) {
        echo "<td class='status-ok'>✅ Mevcut</td>";
        
        // Bağlantıyı test et
        try {
            ob_start();
            include $file;
            ob_end_clean();
            
            if (isset($pdo)) {
                $stmt = $pdo->query("SELECT 1");
                echo "<td class='status-ok'>✅ Başarılı</td>";
            } else {
                echo "<td class='status-error'>❌ PDO değişkeni yok</td>";
            }
        } catch (Exception $e) {
            echo "<td class='status-error'>❌ " . htmlspecialchars($e->getMessage()) . "</td>";
        }
    } else {
        echo "<td class='status-error'>❌ Bulunamadı</td>";
        echo "<td class='status-error'>❌ Test edilemedi</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Ana config ile bağlantı bilgilerini al
require_once 'config/database.php';

echo "<h2>🔗 Database Bağlantı Bilgileri</h2>";
echo "<div class='info'>";
echo "<strong>Host:</strong> localhost<br>";
echo "<strong>Database:</strong> talentbridge<br>";
echo "<strong>Username:</strong> root<br>";
echo "<strong>Password:</strong> [boş]<br>";
echo "</div>";

// Veritabanı tablolarını kontrol et
echo "<h2>📊 Database Tabloları</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table>";
    echo "<tr><th>Tablo Adı</th><th>Kayıt Sayısı</th><th>Son Güncelleme</th><th>Durum</th></tr>";
    
    foreach ($tables as $table) {
        echo "<tr>";
        echo "<td><strong>{$table}</strong></td>";
        
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = $countStmt->fetchColumn();
            echo "<td>{$count} kayıt</td>";
            
            // Son güncelleme zamanını al (eğer created_at/updated_at varsa)
            $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
            $timeColumn = null;
            if (in_array('updated_at', $columns)) $timeColumn = 'updated_at';
            elseif (in_array('created_at', $columns)) $timeColumn = 'created_at';
            
            if ($timeColumn && $count > 0) {
                $timeStmt = $pdo->query("SELECT MAX(`{$timeColumn}`) FROM `{$table}`");
                $lastUpdate = $timeStmt->fetchColumn();
                echo "<td>" . date('d.m.Y H:i:s', strtotime($lastUpdate)) . "</td>";
            } else {
                echo "<td>-</td>";
            }
            
            echo "<td class='status-ok'>✅ OK</td>";
        } catch (Exception $e) {
            echo "<td class='status-error'>❌ Hata</td>";
            echo "<td>-</td>";
            echo "<td class='status-error'>❌ " . htmlspecialchars($e->getMessage()) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Tablolar listelenemedi: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Database bağlantısı kullanan dosyaları listele
echo "<h2>📄 Database Kullanan PHP Dosyaları</h2>";

$dbFiles = [
    'login.php' => 'Kullanıcı girişi',
    'register.php' => 'Kullanıcı kaydı',
    'employees.php' => 'Çalışan listesi',
    'admin/data_import.php' => 'Veri içe aktarma sayfası',
    'api/data_import.php' => 'Veri işleme API\'si'
];

echo "<table>";
echo "<tr><th>PHP Dosyası</th><th>Açıklama</th><th>DB Config</th><th>Durum</th></tr>";

foreach ($dbFiles as $file => $description) {
    echo "<tr>";
    echo "<td><span class='file-path'>{$file}</span></td>";
    echo "<td>{$description}</td>";
    
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        if (strpos($content, "require_once 'config/database.php'") !== false) {
            echo "<td class='status-ok'>✅ config/database.php</td>";
        } elseif (strpos($content, "require_once '../config/database.php'") !== false) {
            echo "<td class='status-ok'>✅ ../config/database.php</td>";
        } elseif (strpos($content, "include") !== false && strpos($content, "db.php") !== false) {
            echo "<td class='status-warning'>⚠️ includes/db.php</td>";
        } else {
            echo "<td class='status-error'>❌ Bulunamadı</td>";
        }
        
        if (strpos($content, '$pdo') !== false) {
            echo "<td class='status-ok'>✅ PDO kullanıyor</td>";
        } else {
            echo "<td class='status-warning'>⚠️ PDO kullanmıyor</td>";
        }
    } else {
        echo "<td class='status-error'>❌ Dosya yok</td>";
        echo "<td class='status-error'>❌ Test edilemedi</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Test sorguları çalıştır
echo "<h2>🧪 Database Fonksiyonellik Testleri</h2>";

$tests = [
    'Users Tablosu SELECT' => "SELECT COUNT(*) FROM users",
    'Users Tablosu Kolonları' => "SHOW COLUMNS FROM users",
    'Son Eklenen Kullanıcı' => "SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 1"
];

foreach ($tests as $testName => $query) {
    echo "<h4>{$testName}</h4>";
    try {
        $stmt = $pdo->query($query);
        
        if (strpos($query, 'COUNT') !== false) {
            $result = $stmt->fetchColumn();
            echo "<div class='success'>✅ Sonuç: {$result}</div>";
        } elseif (strpos($query, 'SHOW COLUMNS') !== false) {
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<div class='success'>✅ Kolonlar: " . implode(', ', $columns) . "</div>";
        } else {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo "<div class='success'>✅ Sonuç: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "</div>";
            } else {
                echo "<div class='warning'>⚠️ Sonuç bulunamadı</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Hata: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// XAMPP servisleri kontrol et
echo "<h2>🚀 XAMPP Servis Durumu</h2>";
echo "<div class='info'>";
echo "MySQL Port kontrolü yapılıyor...<br>";

$connection = @fsockopen('localhost', 3306, $errno, $errstr, 5);
if ($connection) {
    echo "<strong class='status-ok'>✅ MySQL Servisi Çalışıyor (Port 3306)</strong><br>";
    fclose($connection);
} else {
    echo "<strong class='status-error'>❌ MySQL Servisi Çalışmıyor (Port 3306)</strong><br>";
    echo "Hata: {$errno} - {$errstr}<br>";
}

$connection = @fsockopen('localhost', 80, $errno, $errstr, 5);
if ($connection) {
    echo "<strong class='status-ok'>✅ Apache Servisi Çalışıyor (Port 80)</strong><br>";
    fclose($connection);
} else {
    echo "<strong class='status-error'>❌ Apache Servisi Çalışmıyor (Port 80)</strong><br>";
}
echo "</div>";

echo "<hr>";
echo "<h2>📋 Özet</h2>";
echo "<div class='info'>";
echo "<strong>Kontrol Edilen Alanlar:</strong><br>";
echo "• Database konfigürasyon dosyaları<br>";
echo "• Tablo yapıları ve kayıt sayıları<br>";
echo "• PHP dosyalarındaki DB bağlantıları<br>";
echo "• Temel SQL sorgu testleri<br>";
echo "• XAMPP servis durumu<br>";
echo "</div>";

echo "<p style='text-align: center; color: #6c757d; margin-top: 30px;'>";
echo "🔄 Bu sayfayı düzenli olarak kontrol ederek database durumunu izleyebilirsiniz.";
echo "</p>";
?>
