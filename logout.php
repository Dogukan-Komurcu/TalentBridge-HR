<?php
session_start();
require_once 'includes/auth.php';

// Güvenlik log'u
logSecurityEvent('LOGOUT', '');

// Session temizle
clearSession();

// Giriş sayfasına yönlendir
header('Location: login.php?message=Başarıyla çıkış yaptınız');
exit();
?>
