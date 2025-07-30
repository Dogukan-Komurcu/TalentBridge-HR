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
    <title>İş İlanları - TalentBridge</title>
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
                    <h1 class="page-title">İş İlanları</h1>
                    <div class="page-actions">
                        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni İlan Ekle
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Departman:</label>
                        <select>
                            <option value="">Tümü</option>
                            <option value="1">Bilgi Teknolojileri</option>
                            <option value="2">İnsan Kaynakları</option>
                            <option value="3">Pazarlama</option>
                            <option value="4">Satış</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Pozisyon Türü:</label>
                        <select>
                            <option value="">Tümü</option>
                            <option value="full-time">Tam Zamanlı</option>
                            <option value="part-time">Yarı Zamanlı</option>
                            <option value="contract">Sözleşmeli</option>
                            <option value="intern">Stajyer</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Konum:</label>
                        <select>
                            <option value="">Tümü</option>
                            <option value="istanbul">İstanbul</option>
                            <option value="ankara">Ankara</option>
                            <option value="izmir">İzmir</option>
                            <option value="remote">Uzaktan</option>
                        </select>
                    </div>
                    <button class="btn btn-primary btn-filter">
                        <i class="fas fa-filter"></i> Filtrele
                    </button>
                </div>
                
                <div class="jobs-container">
                    <div class="job-card">
                        <div class="job-header">
                            <h3 class="job-title">Kıdemli Yazılım Geliştirici</h3>
                            <span class="job-type full-time">Tam Zamanlı</span>
                        </div>
                        <div class="job-details">
                            <div class="job-info">
                                <span><i class="fas fa-building"></i> Bilgi Teknolojileri</span>
                                <span><i class="fas fa-map-marker-alt"></i> İstanbul</span>
                                <span><i class="fas fa-money-bill-wave"></i> 35,000₺ - 45,000₺</span>
                            </div>
                            <p class="job-description">
                                5+ yıl deneyimli, ekip yönetimi tecrübesi olan, web teknolojilerine hakim yazılım geliştirici arayışımız bulunmaktadır.
                            </p>
                            <div class="job-skills">
                                <span class="skill-tag">PHP</span>
                                <span class="skill-tag">JavaScript</span>
                                <span class="skill-tag">MySQL</span>
                                <span class="skill-tag">React</span>
                                <span class="skill-tag">AWS</span>
                            </div>
                        </div>
                        <div class="job-footer">
                            <button class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Başvur
                            </button>
                            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                            <div class="admin-actions">
                                <button class="btn-icon btn-edit" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-delete" title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="job-card">
                        <div class="job-header">
                            <h3 class="job-title">İK Uzmanı</h3>
                            <span class="job-type part-time">Yarı Zamanlı</span>
                        </div>
                        <div class="job-details">
                            <div class="job-info">
                                <span><i class="fas fa-building"></i> İnsan Kaynakları</span>
                                <span><i class="fas fa-map-marker-alt"></i> Ankara</span>
                                <span><i class="fas fa-money-bill-wave"></i> 20,000₺ - 25,000₺</span>
                            </div>
                            <p class="job-description">
                                İşe alım süreçlerini yönetebilecek, çalışan memnuniyeti konusunda deneyimli İK uzmanı aranmaktadır.
                            </p>
                            <div class="job-skills">
                                <span class="skill-tag">İşe Alım</span>
                                <span class="skill-tag">Mülakat Teknikleri</span>
                                <span class="skill-tag">Çalışan İlişkileri</span>
                                <span class="skill-tag">Oryantasyon</span>
                            </div>
                        </div>
                        <div class="job-footer">
                            <button class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Başvur
                            </button>
                            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                            <div class="admin-actions">
                                <button class="btn-icon btn-edit" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-delete" title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="pagination-container">
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
