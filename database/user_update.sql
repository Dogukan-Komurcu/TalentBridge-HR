-- Kullanıcı tablosuna ek alanlar ekle
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) AFTER password,
ADD COLUMN IF NOT EXISTS location VARCHAR(100) AFTER department,
ADD COLUMN IF NOT EXISTS bio TEXT AFTER location,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER updated_at;

-- Kullanıcı ayarları tablosu oluştur
CREATE TABLE IF NOT EXISTS user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    system_notifications BOOLEAN DEFAULT TRUE,
    theme VARCHAR(50) DEFAULT 'light',
    language VARCHAR(10) DEFAULT 'tr',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Örnek kullanıcı ayarları ekle
INSERT IGNORE INTO user_settings (user_id, email_notifications, system_notifications, theme) VALUES 
(1, TRUE, TRUE, 'dark'),
(2, TRUE, FALSE, 'light'),
(3, FALSE, TRUE, 'light');

-- Güvenlik logları tablosu oluştur
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    user_id INT DEFAULT 0,
    user_name VARCHAR(100),
    user_role VARCHAR(50),
    ip_address VARCHAR(50),
    description TEXT,
    additional_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- İleri tarihli diğer veritabanı güncellemeleri buraya eklenebilir
