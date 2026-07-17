<?php
require_once '../config/db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil semua review untuk destinasi tertentu
        $destinasi_id = isset($_GET['destinasi_id']) ? (int)$_GET['destinasi_id'] : 0;
        
        if (!$destinasi_id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "destinasi_id diperlukan"]);
            exit;
        }

        // Ambil reviews dengan join ke tabel users
        $stmt = $conn->prepare("
            SELECT r.id, r.destinasi_id, r.user_id, r.rating, r.komentar, r.created_at, u.nama AS user_nama
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.destinasi_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$destinasi_id]);
        $reviews = $stmt->fetchAll();

        // Hitung rata-rata rating & total
        $stmt2 = $conn->prepare("
            SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
            FROM reviews
            WHERE destinasi_id = ?
        ");
        $stmt2->execute([$destinasi_id]);
        $stats = $stmt2->fetch();

        echo json_encode([
            "reviews" => $reviews,
            "avg_rating" => $stats['avg_rating'] ? round((float)$stats['avg_rating'], 1) : null,
            "total_reviews" => (int)$stats['total_reviews']
        ]);
        break;

    case 'POST':
        // Tambah atau update review
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['destinasi_id']) || empty($data['user_id']) || empty($data['rating']) || empty($data['komentar'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Semua field wajib diisi"]);
            exit;
        }

        $destinasi_id = (int)$data['destinasi_id'];
        $user_id = (int)$data['user_id'];
        $rating = (int)$data['rating'];
        $komentar = trim($data['komentar']);

        // Validasi rating 1-5
        if ($rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Rating harus antara 1-5"]);
            exit;
        }

        // Cek apakah user sudah pernah review destinasi ini
        $stmt = $conn->prepare("SELECT id FROM reviews WHERE destinasi_id = ? AND user_id = ?");
        $stmt->execute([$destinasi_id, $user_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update review yang sudah ada
            $stmt = $conn->prepare("UPDATE reviews SET rating = ?, komentar = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$rating, $komentar, $existing['id']]);
            echo json_encode(["status" => "success", "message" => "Review berhasil diperbarui"]);
        } else {
            // Insert review baru
            $stmt = $conn->prepare("INSERT INTO reviews (destinasi_id, user_id, rating, komentar) VALUES (?, ?, ?, ?)");
            $stmt->execute([$destinasi_id, $user_id, $rating, $komentar]);
            echo json_encode(["status" => "success", "message" => "Review berhasil ditambahkan"]);
        }
        break;

    case 'DELETE':
        // Hapus review (hanya oleh pemilik)
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        $user_id = $data['user_id'] ?? null;

        if (!$id || !$user_id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID review dan user_id diperlukan"]);
            exit;
        }

        // Cek kepemilikan review
        $stmt = $conn->prepare("SELECT id FROM reviews WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Anda tidak berhak menghapus review ini"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Review berhasil dihapus"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
