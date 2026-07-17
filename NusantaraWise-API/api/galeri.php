<?php
require_once '../config/db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read
        $stmt = $conn->prepare("SELECT * FROM galeri ORDER BY id ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        echo json_encode($result);
        break;

    case 'POST':
        // Create (for future use)
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['src']) && isset($data['title'])) {
            $stmt = $conn->prepare("INSERT INTO galeri (src, title, loc, size) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['src'],
                $data['title'],
                $data['loc'] ?? '',
                $data['size'] ?? ''
            ]);
            
            $data['id'] = $conn->lastInsertId();
            echo json_encode(["status" => "success", "message" => "Foto galeri ditambahkan", "data" => $data]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        }
        break;

    case 'DELETE':
        // Delete (for future use)
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? (isset($_GET['id']) ? $_GET['id'] : null);
        
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM galeri WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success", "message" => "Foto galeri dihapus"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID diperlukan"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
