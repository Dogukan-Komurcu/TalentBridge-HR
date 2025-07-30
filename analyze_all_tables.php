<?php
// Tüm veritabanı tabloları ve yapılarını analiz eden script
require_once 'config/database.php';

echo "<h1>📊 TalentBridge - Tüm Veritabanı Tabloları Analizi</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #3498db; color: white; }
    .table-section { margin: 30px 0; padding: 20px; background: #f9f9f9; border-radius: 8px; }
    .success { color: green; font-weight: bold; }
    .info { color: blue; font-weight: bold; }
    .code { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
</style>";

echo "<div class='container'>";

try {
    // Tüm tabloları listele
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>📋 Mevcut Tablolar (" . count($tables) . " adet)</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";

    // Her tablo için detaylı yapı analizi
    foreach ($tables as $table) {
        echo "<div class='table-section'>";
        echo "<h2>🗃️ " . strtoupper($table) . " TABLOSU</h2>";
        
        // Tablo yapısı
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sütun Yapısı:</h3>";
        echo "<table>";
        echo "<tr><th>Sütun Adı</th><th>Veri Tipi</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td><strong>" . $column['Field'] . "</strong></td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . ($column['Default'] ?: '-') . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Kayıt sayısı
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "<p class='info'>📊 Mevcut kayıt sayısı: <strong>$count</strong></p>";
        
        // Örnek kayıtlar (varsa)
        if ($count > 0) {
            $sample = $pdo->query("SELECT * FROM `$table` LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($sample)) {
                echo "<h3>Örnek Kayıtlar:</h3>";
                echo "<table>";
                echo "<tr>";
                foreach (array_keys($sample[0]) as $header) {
                    echo "<th>$header</th>";
                }
                echo "</tr>";
                
                foreach ($sample as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        $displayValue = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                        echo "<td>" . htmlspecialchars($displayValue) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
        echo "</div>";
    }
    
    // Tablo ilişkileri analizi
    echo "<div class='table-section'>";
    echo "<h2>🔗 Tablo İlişkileri</h2>";
    
    // Foreign key'leri bul
    $fks = $pdo->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_SCHEMA = 'talentbridge'
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($fks)) {
        echo "<table>";
        echo "<tr><th>Tablo</th><th>Sütun</th><th>Referans Tablo</th><th>Referans Sütun</th></tr>";
        foreach ($fks as $fk) {
            echo "<tr>";
            echo "<td>" . $fk['TABLE_NAME'] . "</td>";
            echo "<td>" . $fk['COLUMN_NAME'] . "</td>";
            echo "<td>" . $fk['REFERENCED_TABLE_NAME'] . "</td>";
            echo "<td>" . $fk['REFERENCED_COLUMN_NAME'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Henüz foreign key ilişkisi tanımlanmamış.</p>";
    }
    echo "</div>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
}

echo "</div>";
?>
