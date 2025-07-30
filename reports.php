<?php
session_start();
require_once 'includes/auth.php';

// Kullanıcı giriş kontrolü
checkLogin();

// Kullanıcı rolünü al
$userRole = getUserRole();

// Admin ve Manager rollerini kontrol et
if ($userRole !== 'admin' && $userRole !== 'manager') {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar_main.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Raporlar</h1>
                </div>
                
                <div class="reports-tabs">
                    <button class="tab-btn active" data-tab="employees">
                        <i class="fas fa-users"></i> Çalışan Raporları
                    </button>
                    <button class="tab-btn" data-tab="recruitment">
                        <i class="fas fa-user-plus"></i> İşe Alım Raporları
                    </button>
                    <button class="tab-btn" data-tab="performance">
                        <i class="fas fa-chart-line"></i> Performans Raporları
                    </button>
                    <button class="tab-btn" data-tab="financial">
                        <i class="fas fa-money-bill-wave"></i> Finansal Raporlar
                    </button>
                </div>
                
                <div class="tab-content active" id="employees-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Departman Dağılımı</h3>
                                </div>
                                <div class="card-content">
                                    <div class="chart-container">
                                        <!-- Burada daire grafiği olacak -->
                                        <div class="placeholder-chart" style="height: 250px; background: #f3f3f3; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chart-pie" style="font-size: 50px; color: #ccc;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Çalışan Yaş Dağılımı</h3>
                                </div>
                                <div class="card-content">
                                    <div class="chart-container">
                                        <!-- Burada çubuk grafiği olacak -->
                                        <div class="placeholder-chart" style="height: 250px; background: #f3f3f3; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chart-bar" style="font-size: 50px; color: #ccc;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Özet İstatistikler</h3>
                            <div class="card-tools">
                                <button class="btn btn-secondary btn-sm">
                                    <i class="fas fa-download"></i> PDF İndir
                                </button>
                                <button class="btn btn-secondary btn-sm">
                                    <i class="fas fa-file-excel"></i> Excel İndir
                                </button>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Departman</th>
                                            <th>Çalışan Sayısı</th>
                                            <th>Ortalama Yaş</th>
                                            <th>Kadın/Erkek Oranı</th>
                                            <th>Ort. Kıdem (Yıl)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Bilgi Teknolojileri</td>
                                            <td>24</td>
                                            <td>32</td>
                                            <td>35% / 65%</td>
                                            <td>3.5</td>
                                        </tr>
                                        <tr>
                                            <td>İnsan Kaynakları</td>
                                            <td>12</td>
                                            <td>35</td>
                                            <td>75% / 25%</td>
                                            <td>4.2</td>
                                        </tr>
                                        <tr>
                                            <td>Pazarlama</td>
                                            <td>18</td>
                                            <td>29</td>
                                            <td>60% / 40%</td>
                                            <td>2.8</td>
                                        </tr>
                                        <tr>
                                            <td>Satış</td>
                                            <td>30</td>
                                            <td>33</td>
                                            <td>45% / 55%</td>
                                            <td>3.1</td>
                                        </tr>
                                        <tr>
                                            <td>Finans</td>
                                            <td>15</td>
                                            <td>38</td>
                                            <td>55% / 45%</td>
                                            <td>5.4</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Toplam / Ortalama</th>
                                            <th>99</th>
                                            <th>33.4</th>
                                            <th>54% / 46%</th>
                                            <th>3.8</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content" id="recruitment-tab" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">İşe Alım Trendleri (Son 6 Ay)</h3>
                                </div>
                                <div class="card-content">
                                    <div class="chart-container">
                                        <!-- Burada çizgi grafiği olacak -->
                                        <div class="placeholder-chart" style="height: 300px; background: #f3f3f3; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chart-line" style="font-size: 50px; color: #ccc;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content" id="performance-tab" style="display: none;">
                    <div class="placeholder-content" style="text-align: center; padding: 100px 0;">
                        <i class="fas fa-clipboard-list" style="font-size: 80px; color: #ddd;"></i>
                        <h3 style="margin-top: 20px; color: #888;">Performans Raporları Yakında</h3>
                        <p style="color: #888;">Bu özellik şu anda geliştirme aşamasındadır.</p>
                    </div>
                </div>
                
                <div class="tab-content" id="financial-tab" style="display: none;">
                    <div class="placeholder-content" style="text-align: center; padding: 100px 0;">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 80px; color: #ddd;"></i>
                        <h3 style="margin-top: 20px; color: #888;">Finansal Raporlar Yakında</h3>
                        <p style="color: #888;">Bu özellik şu anda geliştirme aşamasındadır.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mobil menü toggle
            $('.menu-toggle').on('click', function() {
                $('.sidebar').toggleClass('active');
            });
            
            // Tab değiştirme
            $('.tab-btn').on('click', function() {
                var tabId = $(this).data('tab');
                
                // Aktif tab butonunu değiştir
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');
                
                // Tab içeriğini göster/gizle
                $('.tab-content').hide();
                $('#' + tabId + '-tab').show();
            });
        });
    </script>
</body>
</html>
