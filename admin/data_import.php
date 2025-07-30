<?php
session_start();
// Önce veritabanı bağlantısını dahil et
require_once '../config/database.php';
// Sonra auth ve security dosyalarını dahil et
require_once '../includes/auth.php';

// Admin erişim kontrolü - Sadece admin'ler bu sayfaya erişebilir
checkAdminAccess();

// Bu kontrol gereksiz çünkü checkAdminAccess zaten bunu yapıyor
// Ancak güvenlik için log tutalım
logSecurityEvent('ADMIN_AREA_ACCESS', 'Admin kullanıcı data_import.php sayfasına erişti', ['page' => 'data_import.php']);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veri İçe Aktarma - TalentBridge</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar_main.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/topbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Veri İçe Aktarma</h1>
                    <div class="page-actions">
                        <button class="btn btn-primary" id="helpBtn">
                            <i class="fas fa-question-circle"></i> Yardım
                        </button>
                    </div>
                </div>

                <!-- Kullanıcı Ekleme -->
                <div class="import-section">
                    <div class="section-header">
                        <h3><i class="fas fa-users"></i> Kullanıcı Ekle</h3>
                    </div>
                    <form id="userForm" class="import-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Ad Soyad</label>
                                <input type="text" id="full_name" name="full_name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-posta</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Telefon</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="position">Pozisyon</label>
                                <input type="text" id="position" name="position" placeholder="İş pozisyonu">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="department">Departman</label>
                                <input type="text" id="department" name="department" placeholder="Departman adı">
                            </div>
                            <div class="form-group">
                                <label for="role">Rol</label>
                                <select id="role" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="hr">HR Uzmanı</option>
                                    <option value="manager">Yönetici</option>
                                    <option value="employee">Çalışan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Şifre</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Kullanıcı Ekle
                        </button>
                    </form>
                </div>

                <!-- Departman Ekleme -->
                <div class="import-section">
                    <div class="section-header">
                        <h3><i class="fas fa-building"></i> Departman Ekle</h3>
                    </div>
                    <form id="departmentForm" class="import-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dept_name">Departman Adı</label>
                                <input type="text" id="dept_name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="dept_manager">Yönetici</label>
                                <input type="text" id="dept_manager" name="manager" placeholder="Yönetici adı">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dept_description">Açıklama</label>
                            <textarea id="dept_description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-building"></i> Departman Ekle
                        </button>
                    </form>
                </div>

                <!-- İş İlanı Ekleme -->
                <div class="import-section">
                    <div class="section-header">
                        <h3><i class="fas fa-briefcase"></i> İş İlanı Ekle</h3>
                    </div>
                    <form id="jobForm" class="import-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="job_title">İş Başlığı</label>
                                <input type="text" id="job_title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="job_department">Departman</label>
                                <select id="job_department" name="department_id" required>
                                    <option value="">Departman Seçin</option>
                                    <!-- Dinamik olarak doldurulacak -->
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="job_type">İş Türü</label>
                                <select id="job_type" name="job_type" required>
                                    <option value="full-time">Tam Zamanlı</option>
                                    <option value="part-time">Yarı Zamanlı</option>
                                    <option value="contract">Sözleşmeli</option>
                                    <option value="internship">Staj</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="job_location">Lokasyon</label>
                                <input type="text" id="job_location" name="location" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="salary_min">Min. Maaş</label>
                                <input type="number" id="salary_min" name="salary_min">
                            </div>
                            <div class="form-group">
                                <label for="salary_max">Max. Maaş</label>
                                <input type="number" id="salary_max" name="salary_max">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="job_description">İş Tanımı</label>
                            <textarea id="job_description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="job_requirements">Gereksinimler</label>
                            <textarea id="job_requirements" name="requirements" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-briefcase"></i> İş İlanı Ekle
                        </button>
                    </form>
                </div>

                <!-- Başvuru Ekleme -->
                <div class="import-section">
                    <div class="section-header">
                        <h3><i class="fas fa-file-alt"></i> Başvuru Ekle</h3>
                    </div>
                    <form id="applicationForm" class="import-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="applicant_name">Başvuran Adı</label>
                                <input type="text" id="applicant_name" name="applicant_name" required>
                            </div>
                            <div class="form-group">
                                <label for="applicant_email">E-posta</label>
                                <input type="email" id="applicant_email" name="applicant_email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="applicant_phone">Telefon</label>
                                <input type="tel" id="applicant_phone" name="applicant_phone">
                            </div>
                            <div class="form-group">
                                <label for="application_job">İş Pozisyonu</label>
                                <select id="application_job" name="job_posting_id" required>
                                    <option value="">İş Seçin</option>
                                    <!-- Dinamik olarak doldurulacak -->
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cover_letter">Kapak Mektubu</label>
                            <textarea id="cover_letter" name="cover_letter" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i> Başvuru Ekle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/import.js"></script>
</body>
</html>

<style>
.import-section {
    background: white;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.section-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #3498db;
}

.section-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.import-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.page-header p {
    color: #7f8c8d;
    font-size: 16px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .import-section {
        padding: 20px;
    }
}
</style>
