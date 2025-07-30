-- TalentBridge veritabanını oluştur
CREATE DATABASE IF NOT EXISTS talentbridge;
USE talentbridge;

-- Users tablosunu oluştur
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    role VARCHAR(50) DEFAULT 'employee',
    position VARCHAR(100),
    department VARCHAR(100),
    salary DECIMAL(10,2),
    start_date DATE,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments tablosunu oluştur
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Job Postings tablosunu oluştur
CREATE TABLE IF NOT EXISTS job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    location VARCHAR(100),
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    employment_type ENUM('full-time', 'part-time', 'contract', 'internship') DEFAULT 'full-time',
    experience_level ENUM('entry', 'mid', 'senior', 'expert') DEFAULT 'mid',
    department VARCHAR(100),
    status ENUM('active', 'inactive', 'filled') DEFAULT 'active',
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Applications tablosunu oluştur
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_name VARCHAR(100) NOT NULL,
    applicant_email VARCHAR(100) NOT NULL,
    applicant_phone VARCHAR(15),
    cover_letter TEXT,
    resume_path VARCHAR(255),
    status ENUM('pending', 'reviewing', 'interview', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (job_id) REFERENCES job_postings(id) ON DELETE CASCADE
);

-- Interviews tablosunu oluştur
CREATE TABLE IF NOT EXISTS interviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    interviewer_id INT,
    interview_date DATETIME NOT NULL,
    interview_type ENUM('phone', 'video', 'in-person') DEFAULT 'in-person',
    location VARCHAR(200),
    status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    notes TEXT,
    score INT CHECK (score >= 1 AND score <= 10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Notifications tablosunu oluştur
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- System Settings tablosunu oluştur
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Test verileri ekle
INSERT IGNORE INTO users (name, email, password, role, position, department, salary, phone, status) VALUES 
('Admin Kullanıcı', 'admin@talentbridge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Sistem Yöneticisi', 'IT', 25000, '0532 123 45 67', 'active'),
('Test Kullanıcı', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', 'Yazılım Geliştirici', 'IT', 15000, '0533 123 45 67', 'active'),
('Ahmet Yılmaz', 'ahmet@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'Proje Yöneticisi', 'IT', 20000, '0534 123 45 67', 'active');

INSERT IGNORE INTO departments (name, description, manager_id) VALUES 
('Bilgi İşlem', 'Şirketin teknoloji altyapısını yöneten departman', 1),
('İnsan Kaynakları', 'Personel işleri ve bordro departmanı', NULL),
('Satış', 'Satış ve pazarlama faaliyetleri', NULL),
('Finans', 'Mali işler ve muhasebe', NULL),
('Üretim', 'Üretim ve kalite kontrol', NULL);

INSERT IGNORE INTO job_postings (title, description, requirements, location, salary_min, salary_max, employment_type, experience_level, department, deadline) VALUES 
('Senior PHP Developer', 'Laravel ve PHP konusunda deneyimli geliştirici aranıyor', 'PHP, Laravel, MySQL, Git, 5+ yıl deneyim', 'İstanbul', 18000, 25000, 'full-time', 'senior', 'IT', '2025-12-31'),
('UI/UX Designer', 'Kullanıcı deneyimi tasarımcısı', 'Figma, Adobe XD, Sketch, 3+ yıl deneyim', 'İstanbul', 12000, 18000, 'full-time', 'mid', 'IT', '2025-11-30'),
('İK Uzmanı', 'İnsan kaynakları uzmanı aranıyor', 'İK süreçleri, bordro, 2+ yıl deneyim', 'Ankara', 10000, 15000, 'full-time', 'mid', 'İnsan Kaynakları', '2025-10-31');

INSERT IGNORE INTO applications (job_id, applicant_name, applicant_email, applicant_phone, cover_letter, status) VALUES 
(1, 'Mehmet Özkan', 'mehmet@email.com', '0535 111 22 33', 'PHP konusunda 7 yıllık deneyimim var...', 'pending'),
(1, 'Ayşe Kaya', 'ayse@email.com', '0536 111 22 33', 'Laravel framework konusunda uzmanım...', 'reviewing'),
(2, 'Fatma Demir', 'fatma@email.com', '0537 111 22 33', 'UI/UX tasarım konusunda 4 yıllık deneyimim var...', 'interview');

INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description) VALUES 
('company_name', 'TalentBridge', 'string', 'Şirket adı'),
('company_email', 'info@talentbridge.com', 'string', 'Şirket e-posta adresi'),
('max_file_size', '10485760', 'number', 'Maksimum dosya boyutu (byte)'),
('allowed_file_types', '["pdf","doc","docx"]', 'json', 'İzin verilen dosya türleri');
