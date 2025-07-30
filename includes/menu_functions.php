<?php
/**
 * Geçerli sayfanın URL'sine göre menü öğesinin aktif olup olmadığını kontrol eder
 * 
 * @param string $pageName - Kontrol edilecek sayfa adı (örn: 'dashboard.php', 'employees.php')
 * @return string - Eğer sayfa aktifse 'active' sınıfını döndürür, değilse boş string döndürür
 */
function isActiveMenu($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Ana dizindeki sayfalar için
    if ($currentPage === $pageName) {
        return 'active';
    }
    
    // Alt dizinlerdeki sayfalar için (örn: admin/data_import.php)
    if (strpos($currentPage, $pageName) !== false) {
        return 'active';
    }
    
    return '';
}
?>
