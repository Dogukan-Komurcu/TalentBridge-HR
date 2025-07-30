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
    <title>Çalışanlar - TalentBridge</title>
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
                    <h1 class="page-title">Çalışanlar</h1>
                    <div class="page-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Çalışan Ekle
                        </button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Çalışan Listesi</h3>
                        <div class="card-tools">
                            <div class="search-box">
                                <input type="text" placeholder="Çalışan ara...">
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
                                        <th>Ad Soyad</th>
                                        <th>Departman</th>
                                        <th>Pozisyon</th>
                                        <th>Email</th>
                                        <th>Telefon</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Ahmet Yılmaz</td>
                                        <td>Bilgi Teknolojileri</td>
                                        <td>Yazılım Geliştirici</td>
                                        <td>ahmet.yilmaz@example.com</td>
                                        <td>555-123-4567</td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-view" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" title="Düzenle">
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
                                        <td>Ayşe Demir</td>
                                        <td>İnsan Kaynakları</td>
                                        <td>IK Uzmanı</td>
                                        <td>ayse.demir@example.com</td>
                                        <td>555-987-6543</td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-view" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" title="Düzenle">
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
