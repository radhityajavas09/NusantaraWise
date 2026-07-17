<?php
/**
 * SCRIPT PERBAIKAN DATABASE - Jalankan SEKALI saja!
 * Akses: http://localhost:8000/fix_db.php
 * Setelah selesai, HAPUS file ini dari server!
 */

require_once 'config/db.php';
header("Content-Type: text/html; charset=utf-8");

$results = [];
$errors = [];

// ============================================================
// 1. UPDATE / INSERT AKUN ADMIN DENGAN PASSWORD YANG BENAR
// ============================================================
$adminPassword = 'admin123';
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
$adminEmail = 'admin@nusantarawise.id';

try {
    // Cek apakah admin sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ?, nama = 'Administrator', role = 'admin' WHERE email = ?");
        $stmt->execute([$adminHash, $adminEmail]);
        $results[] = "✅ Password admin berhasil diupdate! Email: $adminEmail | Password: $adminPassword";
    } else {
        // Insert admin baru
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES ('Administrator', ?, ?, 'admin')");
        $stmt->execute([$adminEmail, $adminHash]);
        $results[] = "✅ Akun admin berhasil dibuat! Email: $adminEmail | Password: $adminPassword";
    }

    // Verifikasi
    $stmt = $conn->prepare("SELECT id, nama, email, role, password FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($adminPassword, $admin['password'])) {
        $results[] = "✅ Verifikasi password: BERHASIL - Admin dapat login!";
    } else {
        $errors[] = "❌ Verifikasi password GAGAL!";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error update admin: " . $e->getMessage();
}

// ============================================================
// 2. HAPUS DUPLIKAT DESTINASI (simpan ID terkecil per nama)
// ============================================================
try {
    $stmt = $conn->query("
        DELETE d1 FROM destinasi d1
        INNER JOIN destinasi d2
        WHERE d1.id > d2.id AND d1.nama = d2.nama
    ");
    $affected = $stmt->rowCount();
    $results[] = "✅ Hapus duplikat destinasi: $affected baris dihapus";
} catch (Exception $e) {
    $errors[] = "❌ Error hapus duplikat destinasi: " . $e->getMessage();
}

// ============================================================
// 3. HAPUS DUPLIKAT GALERI (simpan ID terkecil per title)
// ============================================================
try {
    $stmt = $conn->query("
        DELETE g1 FROM galeri g1
        INNER JOIN galeri g2
        WHERE g1.id > g2.id AND g1.title = g2.title
    ");
    $affected = $stmt->rowCount();
    $results[] = "✅ Hapus duplikat galeri: $affected baris dihapus";
} catch (Exception $e) {
    $errors[] = "❌ Error hapus duplikat galeri: " . $e->getMessage();
}

// ============================================================
// 4. TAMPILKAN HASIL
// ============================================================
$stmt = $conn->query("SELECT COUNT(*) as total FROM destinasi");
$totalDest = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM galeri");
$totalGaleri = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT id, nama, email, role FROM users ORDER BY id");
$allUsers = $stmt->fetchAll();

$stmt = $conn->query("SELECT id, nama, kategori FROM destinasi ORDER BY id");
$allDest = $stmt->fetchAll();

$stmt = $conn->query("SELECT id, title, loc FROM galeri ORDER BY id");
$allGaleri = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database - NusantaraWise</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; }
        h2 { color: #374151; font-size: 1.1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px; }
        .success { color: #16a34a; margin: 8px 0; }
        .error { color: #dc2626; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 14px; }
        th { background: #1e3a8a; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        tr:hover { background: #f9fafb; }
        .warning { background: #fef3c7; border: 1px solid #f59e0b; padding: 12px; border-radius: 8px; color: #92400e; margin-top: 20px; }
        .badge-admin { background: #dc2626; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; }
        .badge-user { background: #2563eb; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 Fix Database NusantaraWise</h1>
        
        <h2>📋 Hasil Perbaikan</h2>
        <?php foreach ($results as $r): ?>
            <p class="success"><?= htmlspecialchars($r) ?></p>
        <?php endforeach; ?>
        <?php foreach ($errors as $e): ?>
            <p class="error"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h2>👤 Daftar Users (Total: <?= count($allUsers) ?>)</h2>
        <table>
            <tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th></tr>
            <?php foreach ($allUsers as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nama']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>🗺️ Daftar Destinasi (Total: <?= $totalDest ?>)</h2>
        <table>
            <tr><th>ID</th><th>Nama</th><th>Kategori</th></tr>
            <?php foreach ($allDest as $d): ?>
            <tr>
                <td><?= $d['id'] ?></td>
                <td><?= htmlspecialchars($d['nama']) ?></td>
                <td><?= htmlspecialchars($d['kategori']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>🖼️ Daftar Galeri (Total: <?= $totalGaleri ?>)</h2>
        <table>
            <tr><th>ID</th><th>Title</th><th>Lokasi</th></tr>
            <?php foreach ($allGaleri as $g): ?>
            <tr>
                <td><?= $g['id'] ?></td>
                <td><?= htmlspecialchars($g['title']) ?></td>
                <td><?= htmlspecialchars($g['loc']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="warning">
        ⚠️ <strong>PENTING:</strong> Setelah script ini berhasil, segera hapus file <code>fix_db.php</code> dari server untuk keamanan!<br>
        Login admin: <strong><?= $adminEmail ?></strong> | Password: <strong><?= $adminPassword ?></strong>
    </div>
</body>
</html>
