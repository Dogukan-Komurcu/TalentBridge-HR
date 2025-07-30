<?php
require_once 'config/database.php';

echo "<h2>🔍 Users Tablosu Kolon Kontrolü</h2>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.exists { color: #28a745; font-weight: bold; }
.missing { color: #dc3545; font-weight: bold; }
</style>";

try {
    // Mevcut kolonları listele
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Mevcut Kolonlar:</h3>";
    echo "<table>";
    echo "<tr><th>Kolon Adı</th><th>Tip</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $columnNames = [];
    foreach($columns as $column) {
        $columnNames[] = $column['Field'];
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Gerekli kolonları kontrol et
    $requiredColumns = ['id', 'name', 'email', 'phone', 'role', 'position', 'department', 'salary', 'start_date', 'password_hash', 'created_at'];
    
    echo "<h3>✅ Gerekli Kolon Kontrolü:</h3>";
    echo "<ul>";
    foreach($requiredColumns as $required) {
        if (in_array($required, $columnNames)) {
            echo "<li class='exists'>✅ {$required} - Mevcut</li>";
        } else {
            echo "<li class='missing'>❌ {$required} - EKSIK!</li>";
        }
    }
    echo "</ul>";
    
    // Eksik kolonları ekle
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (!empty($missingColumns)) {
        echo "<h3>🔧 Eksik Kolonları Ekleme:</h3>";
        
        $alterQueries = [];
        foreach($missingColumns as $missing) {
            switch($missing) {
                case 'role':
                    $alterQueries[] = "ADD COLUMN role VARCHAR(50) DEFAULT 'employee'";
                    break;
                case 'position':
                    $alterQueries[] = "ADD COLUMN position VARCHAR(100) DEFAULT 'Belirtilmemiş'";
                    break;
                case 'department':
                    $alterQueries[] = "ADD COLUMN department VARCHAR(100) DEFAULT 'Belirtilmemiş'";
                    break;
                case 'phone':
                    $alterQueries[] = "ADD COLUMN phone VARCHAR(20) DEFAULT NULL";
                    break;
                case 'salary':
                    $alterQueries[] = "ADD COLUMN salary DECIMAL(10,2) DEFAULT NULL";
                    break;
                case 'start_date':
                    $alterQueries[] = "ADD COLUMN start_date DATE DEFAULT NULL";
                    break;
                case 'password_hash':
                    $alterQueries[] = "ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL";
                    break;
                case 'created_at':
                    $alterQueries[] = "ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                    break;
            }
        }
        
        if (!empty($alterQueries)) {
            $fullQuery = "ALTER TABLE users " . implode(', ', $alterQueries);
            echo "<p><strong>Çalıştırılacak SQL:</strong></p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>{$fullQuery}</pre>";
            
            try {
                $pdo->exec($fullQuery);
                echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
                echo "✅ <strong>BAŞARILI!</strong> Eksik kolonlar eklendi.";
                echo "</div>";
                
                // Güncellenmiş tabloyu göster
                echo "<h4>🔄 Güncellenmiş Tablo Yapısı:</h4>";
                $stmt = $pdo->query("SHOW COLUMNS FROM users");
                $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<table>";
                echo "<tr><th>Kolon Adı</th><th>Tip</th><th>Default</th></tr>";
                foreach($newColumns as $column) {
                    echo "<tr>";
                    echo "<td><strong>{$column['Field']}</strong></td>";
                    echo "<td>{$column['Type']}</td>";
                    echo "<td>{$column['Default']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
            } catch(PDOException $e) {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'>";
                echo "❌ <strong>HATA:</strong> " . htmlspecialchars($e->getMessage());
                echo "</div>";
            }
        }
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;'>";
        echo "✅ <strong>TAMAMDIR!</strong> Tüm gerekli kolonlar mevcut.";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'>";
    echo "❌ <strong>Veritabanı Hatası:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>📝 Not:</strong> Bu sayfa çalıştırıldıktan sonra employee modal'ı test edilebilir.</p>";
?>
