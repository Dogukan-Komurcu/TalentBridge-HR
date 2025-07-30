<?php
require_once 'config/database.php';

echo "<h2>Users Tablosu Kolonları:</h2>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach($columns as $column) {
        echo "<tr>";
        foreach($column as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Users Tablosu Verileri:</h2>";
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    if (!empty($users)) {
        $keys = array_keys($users[0]);
        echo "<tr>";
        foreach($keys as $key) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";
        
        foreach($users as $user) {
            echo "<tr>";
            foreach($user as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</table>";
    
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>
