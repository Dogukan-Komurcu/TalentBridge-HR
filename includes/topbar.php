<?php
// Mevcut sayfa için başlık belirle
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = '';

switch($currentPage) {
    case 'data_import.php':
        $pageTitle = '<i class="fas fa-database"></i> Veri İçe Aktarma';
        break;
    case 'dashboard.php':
        $pageTitle = '<i class="fas fa-home"></i> Ana Sayfa';
        break;
    case 'employees.php':
        $pageTitle = '<i class="fas fa-users"></i> Çalışanlar';
        break;
    case 'jobs.php':
        $pageTitle = '<i class="fas fa-briefcase"></i> İş İlanları';
        break;
    case 'applications.php':
        $pageTitle = '<i class="fas fa-file-alt"></i> Başvurular';
        break;
    case 'reports.php':
        $pageTitle = '<i class="fas fa-chart-bar"></i> Raporlar';
        break;
    case 'calendar.php':
        $pageTitle = '<i class="fas fa-calendar"></i> Takvim';
        break;
    case 'settings.php':
        $pageTitle = '<i class="fas fa-cog"></i> Ayarlar';
        break;
    default:
        $pageTitle = 'TalentBridge';
}

// Hem eski 'role' hem de yeni 'user_role' değişkenlerini kontrol et
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
$roleName = ($userRole === 'admin') ? 'YÖNETİCİ' : (($userRole === 'manager') ? 'MÜDÜR' : 'ÇALIŞAN');
$roleColor = ($userRole === 'admin') ? '#dc3545' : (($userRole === 'manager') ? '#fd7e14' : '#28a745');
?>

<!-- Top Bar -->
<div class="topbar">
    <div class="topbar-left">
        <h2><?php echo $pageTitle; ?></h2>
    </div>
    <div class="topbar-right">
        <div class="user-info">
            <span>Hoş geldiniz, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Kullanıcı'; ?></span>
            <span style="background: <?php echo $roleColor; ?>; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; margin-left: 8px;">
                <?php echo $roleName; ?>
            </span>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>
</div>
