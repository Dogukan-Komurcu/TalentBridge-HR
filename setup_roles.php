<?php
// Admin rolü ve güvenlik sistemi kurulumu
require_once 'config/database.php';

echo "<h1>🔐 Rol Tabanlı Erişim Sistemi Kuruluyor...</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; padding: 15px; background: #f0fff4; border-left: 5px solid #10b981; margin: 10px 0; }
    .info { color: #3b82f6; font-weight: bold; padding: 15px; background: #f0f9ff; border-left: 5px solid #3b82f6; margin: 10px 0; }
    .warning { color: #f59e0b; font-weight: bold; padding: 15px; background: #fffbeb; border-left: 5px solid #f59e0b; margin: 10px 0; }
    .error { color: #ef4444; font-weight: bold; padding: 15px; background: #fef2f2; border-left: 5px solid #ef4444; margin: 10px 0; }
    .user-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin: 15px 0; }
    .role-badge { padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
    .role-admin { background: #ef4444; color: white; }
    .role-user { background: #10b981; color: white; }
</style>";

echo "<div class='container'>";

try {
    echo "<h2>1️⃣ Admin Kullanıcıları Güncelleniyor...</h2>";
    
    // Admin kullanıcıları güncelle/ekle
    $adminUsers = [
        [
            'name' => 'TalentBridge Admin',
            'email' => 'admin@talentbridge.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'position' => 'Sistem Yöneticisi',
            'department' => 'Bilgi İşlem',
            'phone' => '+90 532 100 20 30',
            'salary' => 35000
        ],
        [
            'name' => 'Süper Admin',
            'email' => 'superadmin@talentbridge.com', 
            'password' => password_hash('super123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'position' => 'Genel Müdür',
            'department' => 'Yönetim',
            'phone' => '+90 532 100 20 31',
            'salary' => 50000
        ]
    ];
    
    foreach ($adminUsers as $admin) {
        // Önce var mı kontrol et
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$admin['email']]);
        
        if ($checkStmt->fetch()) {
            // Varsa güncelle
            $updateStmt = $pdo->prepare("
                UPDATE users SET 
                name=?, password=?, role=?, position=?, department=?, phone=?, salary=?
                WHERE email=?
            ");
            $updateStmt->execute([
                $admin['name'], $admin['password'], $admin['role'], 
                $admin['position'], $admin['department'], $admin['phone'], 
                $admin['salary'], $admin['email']
            ]);
            echo "<div class='info'>✅ {$admin['name']} güncellendi</div>";
        } else {
            // Yoksa ekle
            $insertStmt = $pdo->prepare("
                INSERT INTO users (name, email, password, role, position, department, phone, salary, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
            ");
            $insertStmt->execute([
                $admin['name'], $admin['email'], $admin['password'], $admin['role'],
                $admin['position'], $admin['department'], $admin['phone'], $admin['salary']
            ]);
            echo "<div class='success'>✅ {$admin['name']} eklendi</div>";
        }
    }
    
    echo "<h2>2️⃣ Normal Kullanıcıları Güncelleniyor...</h2>";
    
    // Mevcut test kullanıcılarını normal kullanıcı yap
    $userUpdates = [
        [
            'name' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'role' => 'employee',
            'position' => 'Yazılım Geliştirici',
            'department' => 'Bilgi İşlem'
        ],
        [
            'name' => 'Ahmet Yılmaz',
            'email' => 'ahmet@company.com',
            'role' => 'manager',
            'position' => 'Proje Yöneticisi',
            'department' => 'Bilgi İşlem'
        ]
    ];
    
    foreach ($userUpdates as $user) {
        $updateStmt = $pdo->prepare("UPDATE users SET role=?, position=?, department=? WHERE email=?");
        $updateStmt->execute([$user['role'], $user['position'], $user['department'], $user['email']]);
        echo "<div class='info'>✅ {$user['name']} rolü güncellendi: {$user['role']}</div>";
    }
    
    echo "<h2>3️⃣ Kullanıcı Rolleri Durumu</h2>";
    
    // Tüm kullanıcıları listele
    $users = $pdo->query("SELECT * FROM users ORDER BY role DESC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        $roleClass = $user['role'] == 'admin' ? 'role-admin' : 'role-user';
        $roleText = $user['role'] == 'admin' ? 'ADMİN' : strtoupper($user['role']);
        
        echo "<div class='user-card'>";
        echo "<div style='display: flex; justify-content: space-between; align-items: center;'>";
        echo "<div>";
        echo "<h3 style='margin: 0;'>{$user['name']}</h3>";
        echo "<p style='margin: 5px 0; opacity: 0.9;'>{$user['email']}</p>";
        echo "<p style='margin: 5px 0; opacity: 0.8;'>{$user['position']} - {$user['department']}</p>";
        echo "</div>";
        echo "<div>";
        echo "<span class='role-badge {$roleClass}'>{$roleText}</span>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "<h2>4️⃣ Güvenlik Özellikleri</h2>";
    echo "<div class='success'>";
    echo "<h3>✅ Kurulum Tamamlandı!</h3>";
    echo "<ul>";
    echo "<li><strong>Admin Kullanıcıları:</strong> Tüm sayfalara erişim</li>";
    echo "<li><strong>Normal Kullanıcılar:</strong> 'Veri İçe Aktarma' hariç tüm sayfalara erişim</li>";
    echo "<li><strong>Otomatik Yönlendirme:</strong> Yetkisiz erişimde dashboard'a yönlendirilir</li>";
    echo "<li><strong>Güvenli Şifreler:</strong> Tüm şifreler hash'lenmiş durumda</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🔑 Giriş Bilgileri</h2>";
    echo "<div class='warning'>";
    echo "<h3>👑 Admin Hesapları:</h3>";
    echo "<p><strong>Email:</strong> admin@talentbridge.com <strong>Şifre:</strong> admin123</p>";
    echo "<p><strong>Email:</strong> superadmin@talentbridge.com <strong>Şifre:</strong> super123</p>";
    echo "<br>";
    echo "<h3>👤 Normal Kullanıcı Hesapları:</h3>";
    echo "<p><strong>Email:</strong> test@example.com <strong>Şifre:</strong> 123456</p>";
    echo "<p><strong>Email:</strong> ahmet@company.com <strong>Şifre:</strong> 123456</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='login.php' style='background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🔐 Admin Girişi Test Et</a>";
    echo "<a href='index.php' style='background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🏠 Ana Sayfaya Dön</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Hata: " . $e->getMessage() . "</div>";
}

echo "</div>";
?>
