<?php
session_start();
require_once 'includes/auth.php';

// Kullanıcı giriş kontrolü
checkLogin();

// Kullanıcı rolünü al
$userRole = getUserRole();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Başvurular - TalentBridge</title>
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
                    <h1 class="page-title">Başvurular</h1>
                    <div class="page-actions">
                        <button class="btn btn-secondary">
                            <i class="fas fa-filter"></i> Filtrele
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Rapor İndir
                        </button>
                    </div>
                </div>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Toplam Başvuru</h3>
                            <p class="stat-value">156</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Bekleyen</h3>
                            <p class="stat-value">42</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Görüşmeye Çağrılan</h3>
                            <p class="stat-value">64</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Reddedilen</h3>
                            <p class="stat-value">50</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Başvuru Listesi</h3>
                        <div class="card-tools">
                            <div class="search-box">
                                <input type="text" placeholder="Başvuru ara...">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Başvuran</th>
                                        <th>Pozisyon</th>
                                        <th>Başvuru Tarihi</th>
                                        <th>Deneyim</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Mehmet Kaya</td>
                                        <td>Kıdemli Yazılım Geliştirici</td>
                                        <td>15.04.2023</td>
                                        <td>7 Yıl</td>
                                        <td><span class="status status-pending">Bekliyor</span></td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-view" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" title="Durumu Değiştir">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Zeynep Demir</td>
                                        <td>İK Uzmanı</td>
                                        <td>12.04.2023</td>
                                        <td>3 Yıl</td>
                                        <td><span class="status status-interview">Görüşmede</span></td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-view" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" title="Durumu Değiştir">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Ali Yıldız</td>
                                        <td>Yazılım Geliştirici</td>
                                        <td>10.04.2023</td>
                                        <td>2 Yıl</td>
                                        <td><span class="status status-rejected">Reddedildi</span></td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-view" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" title="Durumu Değiştir">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pagination">
                            <button class="btn-page" disabled><i class="fas fa-chevron-left"></i></button>
                            <button class="btn-page active">1</button>
                            <button class="btn-page">2</button>
                            <button class="btn-page">3</button>
                            <button class="btn-page"><i class="fas fa-chevron-right"></i></button>
                        </div>
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
        });
    </script>
</body>
</html>
