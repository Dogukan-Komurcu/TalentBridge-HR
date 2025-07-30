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
        case 'add_employee':
            // Gerekli alanları kontrol et
            $requiredFields = ['name', 'email', 'position', 'department', 'phone'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => ucfirst($field) . ' alanı zorunludur']);
                    exit();
                }
            }
            
            // Email kontrolü
            $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $emailCheckStmt->execute([$_POST['email']]);
            if ($emailCheckStmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Bu email adresi zaten kullanılıyor']);
                exit();
            }
            
            // Çalışanı ekle
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, position, department, salary, start_date, created_at, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            
            // Varsayılan şifre oluştur (email'in ilk kısmı + 123)
            $emailParts = explode('@', $_POST['email']);
            $defaultPassword = $emailParts[0] . '123';
            $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            $result = $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                'employee', // varsayılan rol
                $_POST['position'],
                $_POST['department'],
                $_POST['salary'] ?? null,
                $_POST['start_date'] ?? date('Y-m-d'),
                $passwordHash
            ]);
            
            if ($result) {
                $employeeId = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Çalışan başarıyla eklendi',
                    'employee_id' => $employeeId,
                    'default_password' => $defaultPassword
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Çalışan eklenirken hata oluştu']);
            }
            break;
            
        case 'get_employee':
            $employeeId = $_POST['employee_id'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$employeeId]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employee) {
                // Şifreyi çıkar
                unset($employee['password']);
                echo json_encode(['success' => true, 'employee' => $employee]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Çalışan bulunamadı']);
            }
            break;
            
        case 'update_employee':
            $employeeId = $_POST['employee_id'] ?? 0;
            
            if (!$employeeId) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz çalışan ID']);
                exit();
            }
            
            $updateFields = [];
            $updateValues = [];
            
            $allowedFields = ['name', 'email', 'phone', 'position', 'department', 'salary', 'start_date'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field]) && !empty($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $updateValues[] = $_POST[$field];
                }
            }
            
            if (empty($updateFields)) {
                echo json_encode(['success' => false, 'message' => 'Güncellenecek alan bulunamadı']);
                exit();
            }
            
            $updateValues[] = $employeeId;
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($updateValues);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Çalışan bilgileri güncellendi']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu']);
            }
            break;
            
        case 'delete_employee':
            $employeeId = $_POST['employee_id'] ?? 0;
            
            if (!$employeeId) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz çalışan ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$employeeId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Çalışan başarıyla silindi']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Silme işlemi sırasında hata oluştu']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
            break;
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Genel hata: ' . $e->getMessage()]);
}
?>
