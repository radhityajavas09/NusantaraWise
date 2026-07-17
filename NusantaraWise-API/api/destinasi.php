<?php
require_once '../config/db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read with average rating from reviews
        $stmt = $conn->prepare("
            SELECT d.*, 
                   COALESCE(r.avg_rating, d.rating) AS rating,
                   r.avg_rating AS user_avg_rating,
                   COALESCE(r.total_reviews, 0) AS total_reviews
            FROM destinasi d
            LEFT JOIN (
                SELECT destinasi_id, 
                       ROUND(AVG(rating), 1) AS avg_rating, 
                       COUNT(*) AS total_reviews
                FROM reviews
                GROUP BY destinasi_id
            ) r ON d.id = r.destinasi_id
            ORDER BY d.id ASC
        ");
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        // Convert highlights string back to array if needed for API compatibility
        foreach ($result as &$row) {
            if (!empty($row['highlights'])) {
                $row['highlights'] = array_map('trim', explode(',', $row['highlights']));
            } else {
                $row['highlights'] = [];
            }
            if ($row['rating']) {
                $row['rating'] = (float)$row['rating'];
            }
            if ($row['user_avg_rating']) {
                $row['user_avg_rating'] = (float)$row['user_avg_rating'];
            }
            $row['total_reviews'] = (int)$row['total_reviews'];
        }
        
        echo json_encode($result);
        break;

    case 'POST':
        // Create
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['nama']) && isset($data['kategori'])) {
            $highlights_str = isset($data['highlights']) && is_array($data['highlights']) 
                ? implode(',', $data['highlights']) : '';
                
            $stmt = $conn->prepare("INSERT INTO destinasi (nama, kategori, lokasi, deskripsi, gambar, rating, harga, highlights, tips, maps) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nama'],
                $data['kategori'],
                $data['lokasi'] ?? '',
                $data['deskripsi'] ?? '',
                $data['gambar'] ?? '',
                $data['rating'] ?? null,
                $data['harga'] ?? '',
                $highlights_str,
                $data['tips'] ?? '',
                $data['maps'] ?? ''
            ]);
            
            $data['id'] = $conn->lastInsertId();
            echo json_encode(["status" => "success", "message" => "Destinasi ditambahkan", "data" => $data]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        }
        break;

    case 'PUT':
        // Update
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['id'])) {
            $highlights_str = isset($data['highlights']) && is_array($data['highlights']) 
                ? implode(',', $data['highlights']) : '';
                
            $stmt = $conn->prepare("UPDATE destinasi SET nama=?, kategori=?, lokasi=?, deskripsi=?, gambar=?, rating=?, harga=?, highlights=?, tips=?, maps=? WHERE id=?");
            $stmt->execute([
                $data['nama'],
                $data['kategori'],
                $data['lokasi'] ?? '',
                $data['deskripsi'] ?? '',
                $data['gambar'] ?? '',
                $data['rating'] ?? null,
                $data['harga'] ?? '',
                $highlights_str,
                $data['tips'] ?? '',
                $data['maps'] ?? '',
                $data['id']
            ]);
            
            echo json_encode(["status" => "success", "message" => "Destinasi diperbarui"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID diperlukan"]);
        }
        break;

    case 'DELETE':
        // Delete
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? (isset($_GET['id']) ? $_GET['id'] : null);
        
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM destinasi WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success", "message" => "Destinasi dihapus"]);
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
