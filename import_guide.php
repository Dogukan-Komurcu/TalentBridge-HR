<?php
session_start();
require_once 'config/database.php';

// Test kullanıcısı oluştur
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Test Admin';
    $_SESSION['role'] = 'admin';
}

echo "<h2>🎯 TalentBridge Veri İçe Aktarma Rehberi</h2>";
echo "<p>Web sitenizdeki bilgileri XAMPP MySQL veritabanına kaydetmek için:</p>";

echo "<h3>📋 Adım Adım Rehber:</h3>";
echo "<ol>";
echo "<li><strong>XAMPP'i Başlatın:</strong> Apache ve MySQL servislerini çalıştırın</li>";
echo "<li><strong>phpMyAdmin'e Gidin:</strong> <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "<li><strong>Veritabanını Kontrol Edin:</strong> 'talentbridge' veritabanınızın var olduğundan emin olun</li>";
echo "<li><strong>Veri Ekleme Paneline Gidin:</strong> <a href='admin/data_import.php' target='_blank'>Veri İçe Aktarma Sayfası</a></li>";
echo "</ol>";

echo "<h3>🔧 Mevcut Tablolar:</h3>";



echo "<h3>🚀 Hızlı Başlangıç:</h3>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<p><strong>1. Örnek Verileri Eklemek İçin:</strong></p>";
echo "<a href='admin/data_import.php' class='btn btn-primary'>Veri İçe Aktarma Paneli</a>";
echo "<br><br>";
echo "<p><strong>2. Manuel Veri Ekleme:</strong></p>";
echo "<ul>";
echo "<li>Kullanıcılar için form doldurabilirsiniz</li>";
echo "<li>Departmanları tek tek ekleyebilirsiniz</li>";
echo "<li>İş ilanlarını detaylı olarak girebilirsiniz</li>";
echo "<li>Başvuruları sisteme kaydedebilirsiniz</li>";
echo "</ul>";
echo "</div>";

echo "<h3> İpuçları:</h3>";
echo "<ul>";
echo "<li>Önce kullanıcıları ve departmanları ekleyin</li>";
echo "<li>Sonra iş ilanlarını oluşturun</li>";
echo "<li>En son başvuruları kaydedin</li>";
echo "<li>Toplu ekleme butonlarını kullanarak örnek verileri hızlıca ekleyebilirsiniz</li>";
echo "</ul>";

?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

h2 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
}

h3 {
    color: #34495e;
    margin-top: 30px;
}

ol, ul {
    line-height: 1.8;
}

li {
    margin-bottom: 8px;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
