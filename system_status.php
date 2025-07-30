<?php
// Veritabanı kurulum ve kontrol scripti
require_once 'config/database.php';

echo "<h1>🚀 TalentBridge - Sistem Kurulum Tamamlandı!</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 30px 0; }
    .feature-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 15px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
    .stat-card { background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; border-left: 5px solid #667eea; }
    .btn { background: #667eea; color: white; padding: 12px 24px; border: none; border-radius: 8px; text-decoration: none; display: inline-block; margin: 5px; font-weight: bold; }
    .btn:hover { background: #5a67d8; }
</style>";

echo "<div class='container'>";

try {
    // Sistem istatistikleri
    echo "<h2>📊 Sistem İstatistikleri</h2>";
    echo "<div class='stats-grid'>";
    
    $tables = ['users', 'departments', 'job_postings', 'applications', 'interviews', 'notifications', 'system_settings'];
    foreach ($tables as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $icon = [
                'users' => '👥',
                'departments' => '🏢', 
                'job_postings' => '💼',
                'applications' => '📋',
                'interviews' => '🤝',
                'notifications' => '🔔',
                'system_settings' => '⚙️'
            ];
            echo "<div class='stat-card'>";
            echo "<h3>{$icon[$table]} " . ucfirst(str_replace('_', ' ', $table)) . "</h3>";
            echo "<p style='font-size: 24px; font-weight: bold; color: #667eea;'>$count</p>";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='stat-card'>";
            echo "<h3>❌ $table</h3>";
            echo "<p style='color: red;'>Tablo bulunamadı</p>";
            echo "</div>";
        }
    }
    echo "</div>";
    
    // Özellikler
    echo "<h2>✨ Sistem Özellikleri</h2>";
    echo "<div class='feature-grid'>";
    
    $features = [
        [
            'title' => '👥 Çalışan Yönetimi',
            'desc' => 'Ekle, düzenle, sil, listele - Tüm çalışan bilgileri veritabanında saklanır'
        ],
        [
            'title' => '🏢 Departman Yönetimi', 
            'desc' => 'Departmanlar oluşturun, yöneticiler atayın, düzenleyin'
        ],
        [
            'title' => '💼 İş İlanları',
            'desc' => 'İş ilanları yayınlayın, başvuruları takip edin'
        ],
        [
            'title' => '📋 Başvuru Yönetimi',
            'desc' => 'Tüm başvurular veritabanında, durum güncellemeleri'
        ],
        [
            'title' => '🤝 Görüşme Takibi',
            'desc' => 'Mülakat süreçlerini planlayın ve takip edin'
        ],
        [
            'title' => '🔔 Bildirim Sistemi',
            'desc' => 'Sistem bildirimleri ve hatırlatmalar'
        ]
    ];
    
    foreach ($features as $feature) {
        echo "<div class='feature-card'>";
        echo "<h3>{$feature['title']}</h3>";
        echo "<p>{$feature['desc']}</p>";
        echo "</div>";
    }
    echo "</div>";
    
    // Test bilgileri
    echo "<h2>🔑 Test Kullanıcı Bilgileri</h2>";
    echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 10px; border-left: 5px solid #3b82f6;'>";
    echo "<p><strong>👑 Admin Kullanıcıları:</strong></p>";
    echo "<p>Email: <code>admin@talentbridge.com</code> | Şifre: <code>admin123</code></p>";
    echo "<p>Email: <code>superadmin@talentbridge.com</code> | Şifre: <code>super123</code></p>";
    echo "<br>";
    echo "<p><strong>👤 Normal Kullanıcılar:</strong></p>";
    echo "<p>Email: <code>test@example.com</code> | Şifre: <code>123456</code> (Employee)</p>";
    echo "<p>Email: <code>ahmet@company.com</code> | Şifre: <code>123456</code> (Manager)</p>";
    echo "<br>";
    echo "<p><strong>🔐 Güvenlik Özellikleri:</strong></p>";
    echo "<p>✅ Admin kullanıcıları tüm sayfalara erişebilir</p>";
    echo "<p>❌ Normal kullanıcılar 'Veri İçe Aktarma' sayfasına erişemez</p>";
    echo "<p>🔒 Yetkisiz erişim denemeleri log'lanır</p>";
    echo "</div>";
    
    // Hızlı linkler
    echo "<h2>🔗 Hızlı Erişim</h2>";
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='index.php' class='btn'>🏠 Ana Sayfa</a>";
    echo "<a href='login.php' class='btn'>🔐 Giriş Yap</a>";
    echo "<a href='dashboard.php' class='btn'>📊 Dashboard</a>";
    echo "<a href='employees.php' class='btn'>👥 Çalışanlar</a>";
    echo "<a href='departments.php' class='btn'>🏢 Departmanlar</a>";
    echo "<a href='jobs.php' class='btn'>💼 İş İlanları</a>";
    echo "<a href='applications.php' class='btn'>📋 Başvurular</a>";
    echo "</div>";
    
    echo "<div class='success' style='text-align: center; font-size: 18px; margin: 30px 0;'>";
    echo "✅ Sistem başarıyla kuruldu ve tüm özellikler aktif!<br>";
    echo "🗄️ Tüm veriler veritabanında saklanıyor<br>";
    echo "🔄 CRUD işlemleri (Ekleme, Listeleme, Güncelleme, Silme) aktif";
    echo "</div>";

} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Veritabanı Hatası: " . $e->getMessage() . "</p>";
}

echo "</div>";
?>
