<?php
// Session kontrolü
if (!isset($_SESSION)) {
    session_start();
}

// Menü fonksiyonlarını ekle
require_once __DIR__ . '/menu_functions.php';
?>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/images/logo.jpg.jpg' : 'assets/images/logo.jpg.jpg'; ?>" alt="Logo" class="sidebar-logo">
        <h3><i class="fas fa-user-tie"></i> TalentBridge</h3>
    </div>
    
    <?php 
    // Hem eski 'role' hem de yeni 'user_role' değişkenlerini kontrol et
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
    
    // Kullanıcı bilgilerini göster
    $userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Kullanıcı';
    $userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $roleName = ($userRole === 'admin') ? 'Yönetici' : (($userRole === 'manager') ? 'Müdür' : 'Çalışan');
    ?>
    
    <div class="user-info" style="padding: 10px; margin-bottom: 15px; background: rgba(255,255,255,0.1); border-radius: 5px; text-align: center;">
        <p style="margin: 0; font-weight: bold;"><?php echo $userName; ?></p>
        <p style="margin: 0; font-size: 0.8em;"><?php echo $userEmail; ?></p>
        <span style="display: inline-block; padding: 2px 8px; background: <?php echo ($userRole === 'admin') ? '#dc3545' : (($userRole === 'manager') ? '#fd7e14' : '#28a745'); ?>; color: white; border-radius: 10px; font-size: 0.7em; margin-top: 5px;"><?php echo $roleName; ?></span>
    </div>
    
    <?php
    // Admin dizini içinde miyiz?
    $isInAdminDir = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $baseUrl = $isInAdminDir ? '../' : '';
    ?>
    
    <ul class="sidebar-menu">
        <li class="<?php echo isActiveMenu('dashboard.php'); ?>">
            <a href="<?php echo $baseUrl; ?>dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        </li>
        
        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
        <li class="<?php echo isActiveMenu('employees.php'); ?>">
            <a href="<?php echo $baseUrl; ?>employees.php"><i class="fas fa-users"></i> Çalışanlar</a>
        </li>
        <?php endif; ?>
        
        <?php if ($userRole === 'admin'): ?>
        <li class="<?php echo isActiveMenu('jobs.php'); ?>">
            <a href="<?php echo $baseUrl; ?>jobs.php"><i class="fas fa-briefcase"></i> İş İlanları Yönetimi</a>
        </li>
        <?php else: ?>
        <li class="<?php echo isActiveMenu('jobs.php'); ?>">
            <a href="<?php echo $baseUrl; ?>jobs.php"><i class="fas fa-briefcase"></i> İş İlanları</a>
        </li>
        <?php endif; ?>
        
        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
        <li class="<?php echo isActiveMenu('applications.php'); ?>">
            <a href="<?php echo $baseUrl; ?>applications.php"><i class="fas fa-file-alt"></i> Başvuru Yönetimi</a>
        </li>
        <?php else: ?>
        <li class="<?php echo isActiveMenu('applications.php'); ?>">
            <a href="<?php echo $baseUrl; ?>applications.php"><i class="fas fa-file-alt"></i> Başvurularım</a>
        </li>
        <?php endif; ?>
        
        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
        <li class="<?php echo isActiveMenu('reports.php'); ?>">
            <a href="<?php echo $baseUrl; ?>reports.php"><i class="fas fa-chart-bar"></i> Raporlar</a>
        </li>
        <?php endif; ?>
        
        <li class="<?php echo isActiveMenu('calendar.php'); ?>">
            <a href="<?php echo $baseUrl; ?>calendar.php"><i class="fas fa-calendar"></i> Takvim</a>
        </li>
        
        <?php if ($userRole === 'admin'): ?>
        <li class="<?php echo isActiveMenu('data_import.php'); ?>">
            <a href="<?php echo $baseUrl; ?>admin/data_import.php"><i class="fas fa-database"></i> Veri İçe Aktarma</a>
        </li>
        <li class="<?php echo isActiveMenu('database_update.php'); ?>">
            <a href="<?php echo $baseUrl; ?>admin/database_update.php"><i class="fas fa-sync"></i> Veritabanı Güncelleme</a>
        </li>
        <?php endif; ?>
        
        <?php if ($userRole === 'admin'): ?>
        <li class="<?php echo isActiveMenu('settings.php'); ?>">
            <a href="<?php echo $baseUrl; ?>settings.php"><i class="fas fa-cog"></i> Sistem Ayarları</a>
        </li>
        <?php else: ?>
        <li class="<?php echo isActiveMenu('settings.php'); ?>">
            <a href="<?php echo $baseUrl; ?>settings.php"><i class="fas fa-cog"></i> Hesap Ayarları</a>
        </li>
        <?php endif; ?>
        
        <li>
            <a href="<?php echo $baseUrl; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </li>
    </ul>
</div>
