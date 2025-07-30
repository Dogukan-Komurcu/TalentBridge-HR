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
    <title>Dashboard - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .card-body {
            min-height: 120px;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            color: #6b7280;
            font-size: 14px;
        }
        .stat-icon {
            align-self: flex-end;
            background: #f3f4f6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .stat-icon i {
            font-size: 20px;
        }
        .admin-panel {
            background: #fff1f0;
            border-left: 4px solid #ff4d4f;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .admin-tools {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .admin-tool {
            background: white;
            border-radius: 8px;
            border: 1px solid #eee;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        .admin-tool:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .admin-tool i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1890ff;
        }
        .admin-tool-name {
            font-weight: 500;
        }
        .welcome-message {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar_main.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <?php include 'includes/topbar.php'; ?>        <!-- Welcome Message -->
        <div class="welcome-message">
            <h3>Hoş Geldiniz, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Kullanıcı'; ?>!</h3>
            <p>TalentBridge İnsan Kaynakları Yönetim Sistemine hoş geldiniz. Bu panel üzerinden iş ilanları, başvurular ve çalışanları yönetebilirsiniz.</p>
        </div>
        
        <?php if ($userRole === 'admin'): ?>
        <!-- Admin Panel -->
        <div class="admin-panel">
            <h3><i class="fas fa-shield-alt"></i> Yönetici Paneli</h3>
            <p>Yönetici olarak, sistemde her türlü değişikliği yapabilir ve tüm verilere erişebilirsiniz.</p>
            
            <div class="admin-tools">
                <a href="admin/data_import.php" class="admin-tool">
                    <i class="fas fa-database"></i>
                    <div class="admin-tool-name">Veri İçe Aktarma</div>
                </a>
                <a href="settings.php" class="admin-tool">
                    <i class="fas fa-cog"></i>
                    <div class="admin-tool-name">Sistem Ayarları</div>
                </a>
                <a href="reports.php" class="admin-tool">
                    <i class="fas fa-chart-line"></i>
                    <div class="admin-tool-name">Gelişmiş Raporlar</div>
                </a>
                <a href="users.php" class="admin-tool">
                    <i class="fas fa-users-cog"></i>
                    <div class="admin-tool-name">Kullanıcı Yönetimi</div>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: #4f46e5;"></i>
                </div>
                <div class="stat-label">Toplam Çalışan</div>
                <div class="stat-value">42</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up" style="color: #10b981;"></i> 12% geçen aya göre
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-briefcase" style="color: #0ea5e9;"></i>
                </div>
                <div class="stat-label">Aktif İş İlanları</div>
                <div class="stat-value">18</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up" style="color: #10b981;"></i> 5% geçen haftaya göre
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt" style="color: #f59e0b;"></i>
                </div>
                <div class="stat-label">Yeni Başvurular</div>
                <div class="stat-value">24</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-down" style="color: #ef4444;"></i> 3% geçen haftaya göre
                </div>
            </div>
            
            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie" style="color: #ec4899;"></i>
                </div>
                <div class="stat-label">Doluluk Oranı</div>
                <div class="stat-value">87%</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up" style="color: #10b981;"></i> 7% geçen aya göre
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Content Rows -->
        <div class="row">
            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
            <!-- For Admin and Manager -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Son İşe Alımlar</h4>
                    <a href="employees.php" class="view-all">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <p>Son 30 günde işe alınan çalışanların listesi burada görüntülenecek.</p>
                </div>
            </div>
            <?php else: ?>
            <!-- For Regular Employees -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Departman Duyuruları</h4>
                </div>
                <div class="card-body">
                    <p>Departmanınıza ait duyurular burada görüntülenecek.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Yaklaşan Etkinlikler</h4>
                    <a href="calendar.php" class="view-all">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <p>Yaklaşan etkinlikler ve toplantılar burada görüntülenecek.</p>
                </div>
            </div>
        </div>
        
        <!-- Second Row -->
        <div class="row">
            <?php if ($userRole === 'admin'): ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sistem Durumu</h4>
                </div>
                <div class="card-body">
                    <p>Sistem durumu ve performans metrikleri burada görüntülenecek.</p>
                </div>
            </div>
            <?php elseif ($userRole === 'manager'): ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Departman Performansı</h4>
                </div>
                <div class="card-body">
                    <p>Departman performans metrikleri burada görüntülenecek.</p>
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Başvurularım</h4>
                </div>
                <div class="card-body">
                    <p>Yaptığınız iş başvuruları ve durumları burada görüntülenecek.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Son İş İlanları</h4>
                    <a href="jobs.php" class="view-all">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <p>En son yayınlanan iş ilanları burada görüntülenecek.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Dashboard JavaScript
    $(document).ready(function() {
        // Buraya dashboard için JavaScript kodları eklenebilir
    });
</script>

</body>
</html>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>156</h3>
                        <p>Toplam Çalışan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-info">
                        <h3>23</h3>
                        <p>Aktif İş İlanı</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>89</h3>
                        <p>Yeni Başvuru</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Bekleyen İşlem</p>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Row -->
            <div class="content-row">
                <!-- Recent Applications -->
                <div class="content-box">
                    <div class="box-header">
                        <h4><i class="fas fa-file-alt"></i> Son Başvurular</h4>
                        <a href="applications.php" class="view-all">Tümünü Gör</a>
                    </div>
                    <div class="table-container">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Başvuran</th>
                                    <th>Pozisyon</th>
                                    <th>Tarih</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ahmet Yılmaz</td>
                                    <td>Frontend Developer</td>
                                    <td>28.07.2025</td>
                                    <td><span class="status pending">Beklemede</span></td>
                                </tr>
                                <tr>
                                    <td>Ayşe Kaya</td>
                                    <td>UI/UX Designer</td>
                                    <td>27.07.2025</td>
                                    <td><span class="status approved">Onaylandı</span></td>
                                </tr>
                                <tr>
                                    <td>Mehmet Özkan</td>
                                    <td>Backend Developer</td>
                                    <td>26.07.2025</td>
                                    <td><span class="status pending">Beklemede</span></td>
                                </tr>
                                <tr>
                                    <td>Fatma Demir</td>
                                    <td>Project Manager</td>
                                    <td>25.07.2025</td>
                                    <td><span class="status rejected">Reddedildi</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-box">
                    <div class="box-header">
                        <h4><i class="fas fa-bolt"></i> Hızlı İşlemler</h4>
                    </div>
                    <div class="quick-actions">
                        <a href="jobs.php" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Yeni İlan Ekle</span>
                        </a>
                        <a href="employees.php" class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Çalışan Ekle</span>
                        </a>
                        <a href="calendar.php" class="action-btn">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Görüşme Planla</span>
                        </a>
                        <a href="reports.php" class="action-btn">
                            <i class="fas fa-file-export"></i>
                            <span>Rapor Al</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities and Notifications -->
            <div class="content-row">
                <div class="content-box">
                    <div class="box-header">
                        <h4><i class="fas fa-bell"></i> Son Bildirimler</h4>
                    </div>
                    <div class="notifications">
                        <div class="notification-item">
                            <div class="notification-icon blue">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="notification-content">
                                <p><strong>Yeni başvuru alındı</strong></p>
                                <span>Frontend Developer pozisyonu için Ahmet Yılmaz başvurdu</span>
                                <small>2 saat önce</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon green">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="notification-content">
                                <p><strong>Başvuru onaylandı</strong></p>
                                <span>UI/UX Designer pozisyonu için Ayşe Kaya'nın başvurusu onaylandı</span>
                                <small>5 saat önce</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon orange">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="notification-content">
                                <p><strong>Görüşme planlandı</strong></p>
                                <span>Yarın saat 14:00'da Mehmet Özkan ile görüşme</span>
                                <small>1 gün önce</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-box">
                    <div class="box-header">
                        <h4><i class="fas fa-chart-pie"></i> Departman Dağılımı</h4>
                    </div>
                    <div class="chart-container">
                        <div class="department-stats">
                            <div class="dept-item">
                                <div class="dept-color" style="background: #3498db;"></div>
                                <span>IT (45)</span>
                                <div class="dept-percentage">28.8%</div>
                            </div>
                            <div class="dept-item">
                                <div class="dept-color" style="background: #2ecc71;"></div>
                                <span>Satış (38)</span>
                                <div class="dept-percentage">24.4%</div>
                            </div>
                            <div class="dept-item">
                                <div class="dept-color" style="background: #f39c12;"></div>
                                <span>Pazarlama (32)</span>
                                <div class="dept-percentage">20.5%</div>
                            </div>
                            <div class="dept-item">
                                <div class="dept-color" style="background: #e74c3c;"></div>
                                <span>İK (25)</span>
                                <div class="dept-percentage">16.0%</div>
                            </div>
                            <div class="dept-item">
                                <div class="dept-color" style="background: #9b59b6;"></div>
                                <span>Finans (16)</span>
                                <div class="dept-percentage">10.3%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>