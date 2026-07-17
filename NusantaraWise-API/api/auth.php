<?php
require_once '../config/db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

switch ($action) {
    case 'register':
        // Validasi input
        if (empty($data['nama']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Semua field wajib diisi"]);
            exit;
        }

        $nama = trim($data['nama']);
        $email = trim($data['email']);
        $password = $data['password'];

        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Format email tidak valid"]);
            exit;
        }

        // Validasi panjang password
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Password minimal 6 karakter"]);
            exit;
        }

        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["status" => "error", "message" => "Email sudah terdaftar"]);
            exit;
        }

        // Hash password dan simpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$nama, $email, $hashedPassword]);

        $userId = $conn->lastInsertId();

        echo json_encode([
            "status" => "success",
            "message" => "Registrasi berhasil! Silakan login.",
            "data" => [
                "id" => (int)$userId,
                "nama" => $nama,
                "email" => $email,
                "role" => "user"
            ]
        ]);
        break;

    case 'login':
        // Validasi input
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Email dan password wajib diisi"]);
            exit;
        }

        $email = trim($data['email']);
        $password = $data['password'];

        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Email atau password salah"]);
            exit;
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Email atau password salah"]);
            exit;
        }

        // Login berhasil
        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil!",
            "data" => [
                "id" => (int)$user['id'],
                "nama" => $user['nama'],
                "email" => $user['email'],
                "role" => $user['role']
            ]
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Action tidak valid. Gunakan ?action=login atau ?action=register"]);
        break;
}
?>
