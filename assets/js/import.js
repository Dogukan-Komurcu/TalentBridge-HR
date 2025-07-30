// Veri İçe Aktarma JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Departmanları yükle
    loadDepartments();
    loadJobs();
    
    // Form event listeners
    document.getElementById('userForm').addEventListener('submit', handleUserSubmit);
    document.getElementById('departmentForm').addEventListener('submit', handleDepartmentSubmit);
    document.getElementById('jobForm').addEventListener('submit', handleJobSubmit);
    document.getElementById('applicationForm').addEventListener('submit', handleApplicationSubmit);
    
    // Toplu ekleme butonları ekle
    addBulkInsertButtons();
});

// Departmanları yükle
async function loadDepartments() {
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_departments'
        });
        
        const result = await response.json();
        
        if (result.success) {
            const select = document.getElementById('job_department');
            select.innerHTML = '<option value="">Departman Seçin</option>';
            
            result.data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Departmanlar yüklenirken hata:', error);
    }
}

// İş ilanlarını yükle
async function loadJobs() {
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_jobs'
        });
        
        const result = await response.json();
        
        if (result.success) {
            const select = document.getElementById('application_job');
            select.innerHTML = '<option value="">İş Seçin</option>';
            
            result.data.forEach(job => {
                const option = document.createElement('option');
                option.value = job.id;
                option.textContent = job.title;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('İş ilanları yüklenirken hata:', error);
    }
}

// Kullanıcı ekleme
async function handleUserSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'add_user');
    
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            e.target.reset();
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Kullanıcı eklenirken hata oluştu');
    }
}

// Departman ekleme
async function handleDepartmentSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'add_department');
    
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            e.target.reset();
            loadDepartments(); // Departman listesini güncelle
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Departman eklenirken hata oluştu');
    }
}

// İş ilanı ekleme
async function handleJobSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'add_job');
    
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            e.target.reset();
            loadJobs(); // İş listesini güncelle
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'İş ilanı eklenirken hata oluştu');
    }
}

// Başvuru ekleme
async function handleApplicationSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'add_application');
    
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            e.target.reset();
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Başvuru eklenirken hata oluştu');
    }
}

// Toplu ekleme butonları
function addBulkInsertButtons() {
    // Toplu kullanıcı ekleme
    const userSection = document.querySelector('.import-section:nth-child(2)');
    const userBulkBtn = document.createElement('button');
    userBulkBtn.type = 'button';
    userBulkBtn.className = 'btn btn-secondary';
    userBulkBtn.innerHTML = '<i class="fas fa-users"></i> Örnek Kullanıcıları Ekle';
    userBulkBtn.onclick = () => bulkInsert('bulk_insert_users');
    userSection.querySelector('form').appendChild(userBulkBtn);
    
    // Toplu departman ekleme
    const deptSection = document.querySelector('.import-section:nth-child(3)');
    const deptBulkBtn = document.createElement('button');
    deptBulkBtn.type = 'button';
    deptBulkBtn.className = 'btn btn-secondary';
    deptBulkBtn.innerHTML = '<i class="fas fa-building"></i> Örnek Departmanları Ekle';
    deptBulkBtn.onclick = () => bulkInsert('bulk_insert_departments');
    deptSection.querySelector('form').appendChild(deptBulkBtn);
    
    // Toplu iş ilanı ekleme
    const jobSection = document.querySelector('.import-section:nth-child(4)');
    const jobBulkBtn = document.createElement('button');
    jobBulkBtn.type = 'button';
    jobBulkBtn.className = 'btn btn-secondary';
    jobBulkBtn.innerHTML = '<i class="fas fa-briefcase"></i> Örnek İş İlanlarını Ekle';
    jobBulkBtn.onclick = () => bulkInsert('bulk_insert_jobs');
    jobSection.querySelector('form').appendChild(jobBulkBtn);
}

// Toplu ekleme işlemi
async function bulkInsert(action) {
    if (!confirm('Örnek verileri eklemek istediğinizden emin misiniz?')) {
        return;
    }
    
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=${action}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            
            // Listeleri güncelle
            if (action === 'bulk_insert_departments') {
                loadDepartments();
            } else if (action === 'bulk_insert_jobs') {
                loadJobs();
            }
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Toplu ekleme işleminde hata oluştu');
    }
}

// Alert gösterme
function showAlert(type, message) {
    // Mevcut alert'leri temizle
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Dashboard content'in başına ekle
    const dashboardContent = document.querySelector('.dashboard-content');
    dashboardContent.insertBefore(alert, dashboardContent.firstChild);
    
    // 5 saniye sonra otomatik kaldır
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Sayfa yüklenirken mevcut verileri kontrol et
window.addEventListener('load', function() {
    checkExistingData();
});

// Mevcut veri kontrolü
async function checkExistingData() {
    try {
        const response = await fetch('../api/data_import.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=check_data'
        });
        
        // Veri kontrolü ve bilgilendirme mesajı
        console.log('Veri kontrolü tamamlandı');
    } catch (error) {
        console.error('Veri kontrolü hatası:', error);
    }
}
