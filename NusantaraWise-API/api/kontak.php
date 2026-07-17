<?php
require_once '../config/db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read (for admin to read messages)
        $stmt = $conn->prepare("SELECT * FROM pesan_kontak ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        echo json_encode($result);
        break;

    case 'POST':
        // Create (when user submits form)
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['nama']) && isset($data['email']) && isset($data['message'])) {
            $stmt = $conn->prepare("INSERT INTO pesan_kontak (nama, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nama'],
                $data['email'],
                $data['phone'] ?? '',
                $data['subject'] ?? '',
                $data['message']
            ]);
            
            echo json_encode(["status" => "success", "message" => "Pesan berhasil disimpan"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
