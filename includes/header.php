<?php
// Eğer dashboard sayfasındaysak header'ı gösterme
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'dashboard.php') {
    return;
}
?>
<div class="navbar">
    <div class="logo">TalentBridge</div>
    <div class="menu">
        <a href="login.php">Giriş</a>
        <a href="register.php">Kayıt</a>
    </div>
</div>
