<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Test için basit session kontrolü
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Admin';
    $_SESSION['role'] = 'admin';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek methodu']);
    exit();
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_user':
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, password_hash, position, department, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $_POST['full_name'], // form'dan gelen name
                $_POST['email'],
                $_POST['phone'] ?? null,
                $_POST['role'] ?? 'employee',
                $password_hash,
                $_POST['position'] ?? 'Belirtilmemiş',
                $_POST['department'] ?? 'Belirtilmemiş'
            ]);
            echo json_encode(['success' => true, 'message' => 'Kullanıcı başarıyla eklendi']);
            break;

        case 'add_department':
            $stmt = $pdo->prepare("INSERT INTO departments (name, manager, description, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $_POST['name'],
                $_POST['manager'],
                $_POST['description']
            ]);
            echo json_encode(['success' => true, 'message' => 'Departman başarıyla eklendi']);
            break;

        case 'add_job':
            $stmt = $pdo->prepare("INSERT INTO job_postings (title, department_id, description, requirements, job_type, location, salary_min, salary_max, created_by, created_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'active')");
            $stmt->execute([
                $_POST['title'],
                $_POST['department_id'],
                $_POST['description'],
                $_POST['requirements'],
                $_POST['job_type'],
                $_POST['location'],
                $_POST['salary_min'] ?: null,
                $_POST['salary_max'] ?: null,
                $_SESSION['user_id']
            ]);
            echo json_encode(['success' => true, 'message' => 'İş ilanı başarıyla eklendi']);
            break;

        case 'add_application':
            $stmt = $pdo->prepare("INSERT INTO applications (job_posting_id, applicant_name, applicant_email, applicant_phone, cover_letter, applied_at, status) VALUES (?, ?, ?, ?, ?, NOW(), 'pending')");
            $stmt->execute([
                $_POST['job_posting_id'],
                $_POST['applicant_name'],
                $_POST['applicant_email'],
                $_POST['applicant_phone'],
                $_POST['cover_letter']
            ]);
            echo json_encode(['success' => true, 'message' => 'Başvuru başarıyla eklendi']);
            break;

        case 'get_departments':
            $stmt = $pdo->query("SELECT id, name FROM departments ORDER BY name");
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $departments]);
            break;

        case 'get_jobs':
            $stmt = $pdo->query("SELECT id, title FROM job_postings WHERE status = 'active' ORDER BY title");
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $jobs]);
            break;

        case 'bulk_insert_users':
            // Örnek kullanıcılar toplu ekleme
            $users = [
                ['Ahmet Yılmaz', 'ahmet@talentbridge.com', '0555-123-4567', 'admin', 'admin123'],
                ['Fatma Özkan', 'fatma@talentbridge.com', '0555-234-5678', 'hr', 'hr123'],
                ['Mehmet Kaya', 'mehmet@talentbridge.com', '0555-345-6789', 'manager', 'manager123'],
                ['Ayşe Demir', 'ayse@talentbridge.com', '0555-456-7890', 'employee', 'employee123']
            ];

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            
            $added = 0;
            foreach ($users as $user) {
                $password_hash = password_hash($user[4], PASSWORD_DEFAULT);
                if ($stmt->execute([$user[0], $user[1], $user[2], $user[3], $password_hash])) {
                    $added++;
                }
            }
            
            echo json_encode(['success' => true, 'message' => "$added kullanıcı başarıyla eklendi"]);
            break;

        case 'bulk_insert_departments':
            // Örnek departmanlar toplu ekleme
            $departments = [
                ['İnsan Kaynakları', 'Fatma Özkan', 'Şirket personel işlerini yönetir'],
                ['Bilgi İşlem', 'Mehmet Kaya', 'IT altyapısı ve yazılım geliştirme'],
                ['Pazarlama', 'Ayşe Demir', 'Ürün tanıtımı ve satış stratejileri'],
                ['Muhasebe', 'Ali Veli', 'Finansal işlemler ve raporlama'],
                ['Satış', 'Zeynep Aydın', 'Müşteri ilişkileri ve satış süreçleri']
            ];

            $stmt = $pdo->prepare("INSERT INTO departments (name, manager, description, created_at) VALUES (?, ?, ?, NOW())");
            
            $added = 0;
            foreach ($departments as $dept) {
                if ($stmt->execute($dept)) {
                    $added++;
                }
            }
            
            echo json_encode(['success' => true, 'message' => "$added departman başarıyla eklendi"]);
            break;

        case 'bulk_insert_jobs':
            // Örnek iş ilanları toplu ekleme
            $jobs = [
                ['Frontend Developer', 2, 'React ve Vue.js ile modern web uygulamaları geliştirme', '3+ yıl deneyim, React, Vue.js, HTML5, CSS3', 'full-time', 'İstanbul', 15000, 25000],
                ['Backend Developer', 2, 'Node.js ve PHP ile API geliştirme', '2+ yıl deneyim, Node.js, PHP, MySQL', 'full-time', 'İstanbul', 12000, 20000],
                ['UI/UX Designer', 3, 'Kullanıcı deneyimi tasarımı ve arayüz geliştirme', 'Figma, Adobe XD, tasarım portfolyosu', 'full-time', 'İstanbul', 10000, 18000],
                ['Pazarlama Uzmanı', 3, 'Dijital pazarlama kampanyaları yönetimi', 'Google Ads, sosyal medya pazarlama deneyimi', 'full-time', 'Ankara', 8000, 15000],
                ['Muhasebe Uzmanı', 4, 'Genel muhasebe işlemleri ve raporlama', 'Muhasebe mezunu, 2+ yıl deneyim', 'full-time', 'İzmir', 7000, 12000]
            ];

            $stmt = $pdo->prepare("INSERT INTO job_postings (title, department_id, description, requirements, job_type, location, salary_min, salary_max, created_by, created_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'active')");
            
            $added = 0;
            foreach ($jobs as $job) {
                $job[] = $_SESSION['user_id']; // created_by
                if ($stmt->execute($job)) {
                    $added++;
                }
            }
            
            echo json_encode(['success' => true, 'message' => "$added iş ilanı başarıyla eklendi"]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
