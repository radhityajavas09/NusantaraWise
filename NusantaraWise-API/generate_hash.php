<?php
// Script untuk generate hash password yang benar
// Jalankan sekali via: php generate_hash.php
// Atau akses via browser: http://localhost:8000/generate_hash.php

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\n";
echo "Jalankan SQL ini di phpMyAdmin:\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE email = 'admin@nusantarawise.id';\n";
echo "\n";
echo "Verifikasi hash: " . (password_verify($password, $hash) ? "BENAR ✓" : "SALAH ✗") . "\n";
?>
