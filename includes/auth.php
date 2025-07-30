<?php
// Erişim kontrolü fonksiyonları

/**
 * Kullanıcının giriş yapıp yapmadığını kontrol eder
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?error=Giriş yapmanız gerekiyor');
        exit();
    }
}

/**
 * Kullanıcının admin yetkisi olup olmadığını kontrol eder
 */
function checkAdminAccess() {
    checkLogin(); // Önce giriş kontrolü
    
    // Hem eski 'role' hem de yeni 'user_role' değişkenlerini kontrol et
    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
    
    if ($role !== 'admin') {
        header('Location: ../dashboard.php?error=Bu sayfaya erişim yetkiniz bulunmuyor');
        exit();
    }
}

/**
 * Belirli roller için erişim kontrolü
 */
function checkRoleAccess($allowedRoles = []) {
    checkLogin();
    
    if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
        header('Location: dashboard.php?error=Bu sayfaya erişim yetkiniz bulunmuyor');
        exit();
    }
}

/**
 * Kullanıcının rolünü döndürür
 */
function getUserRole() {
    // Hem eski 'role' hem de yeni 'user_role' değişkenlerini kontrol et
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : 'guest');
}

/**
 * Kullanıcının admin olup olmadığını kontrol eder
 */
function isAdmin() {
    return (getUserRole() === 'admin');
}

/**
 * Kullanıcının manager olup olmadığını kontrol eder
 */
function isManager() {
    return (getUserRole() === 'manager');
}

/**
 * Kullanıcının employee olup olmadığını kontrol eder
 */
function isEmployee() {
    return (getUserRole() === 'employee');
}

/**
 * Sayfa başlığına rol bilgisi ekler
 */
function getRoleTitle() {
    $role = getUserRole();
    switch($role) {
        case 'admin':
            return ' - Admin Panel';
        case 'manager':
            return ' - Yönetici Paneli';
        case 'employee':
            return ' - Çalışan Paneli';
        default:
            return '';
    }
}

/**
 * Sidebar'da sadece yetkili menüleri gösterir
 */
function getSidebarMenu() {
    $role = getUserRole();
    
    $menu = [
        'dashboard.php' => ['icon' => 'fas fa-home', 'title' => 'Ana Sayfa', 'roles' => ['admin', 'manager', 'employee']],
        'employees.php' => ['icon' => 'fas fa-users', 'title' => 'Çalışanlar', 'roles' => ['admin', 'manager', 'employee']],
        'departments.php' => ['icon' => 'fas fa-building', 'title' => 'Departmanlar', 'roles' => ['admin', 'manager', 'employee']],
        'jobs.php' => ['icon' => 'fas fa-briefcase', 'title' => 'İş İlanları', 'roles' => ['admin', 'manager', 'employee']],
        'applications.php' => ['icon' => 'fas fa-file-alt', 'title' => 'Başvurular', 'roles' => ['admin', 'manager', 'employee']],
        'reports.php' => ['icon' => 'fas fa-chart-bar', 'title' => 'Raporlar', 'roles' => ['admin', 'manager', 'employee']],
        'calendar.php' => ['icon' => 'fas fa-calendar', 'title' => 'Takvim', 'roles' => ['admin', 'manager', 'employee']],
        'admin/data_import.php' => ['icon' => 'fas fa-database', 'title' => 'Veri İçe Aktarma', 'roles' => ['admin']], // Sadece admin
        'settings.php' => ['icon' => 'fas fa-cog', 'title' => 'Ayarlar', 'roles' => ['admin', 'manager', 'employee']],
        'logout.php' => ['icon' => 'fas fa-sign-out-alt', 'title' => 'Çıkış', 'roles' => ['admin', 'manager', 'employee']]
    ];
    
    $allowedMenu = [];
    foreach ($menu as $url => $item) {
        if (in_array($role, $item['roles'])) {
            $allowedMenu[$url] = $item;
        }
    }
    
    return $allowedMenu;
}

// Önce database.php dosyasını dahil et
require_once __DIR__ . '/../config/database.php';
// Sonra security.php dosyasını dahil et
require_once __DIR__ . '/security.php';

/**
 * Oturum bilgilerini günceller (login sırasında kullanılır)
 */
function updateSessionData($userData) {
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['name'] = $userData['name'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['role'] = $userData['role'];
    $_SESSION['position'] = $userData['position'] ?? '';
    $_SESSION['department'] = $userData['department'] ?? '';
    
    // Güvenlik log'u
    logSecurityEvent('LOGIN_SUCCESS', "Role: {$userData['role']}");
}

/**
 * Oturum temizler (logout sırasında kullanılır)
 */
function clearSession() {
    logSecurityEvent('LOGOUT', '');
    
    session_unset();
    session_destroy();
}
?>
