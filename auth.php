<?php
// Selalu mulai session di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database
require_once 'config/database.php';

// Pastikan request adalah metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika bukan, kembalikan ke halaman utama
    header('Location: index.php');
    exit();
}

$action = $_POST['action'] ?? '';

// --- PROSES REGISTRASI ---
if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($name) || empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Seharusnya ada penanganan error yang lebih baik, tapi untuk sekarang kita redirect
        header('Location: index.php?error=invalid_register_data');
        exit();
    }

    // Cek apakah email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: index.php?error=email_taken');
        exit();
    }

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Simpan user baru ke database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashed_password])) {
        // Setelah registrasi berhasil, kita anggap langsung login untuk alur OTP
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        // Arahkan ke alur OTP
        header('Location: otp.php');
    } else {
        header('Location: index.php?error=register_failed');
    }
    exit();
}

// --- PROSES LOGIN ---
if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header('Location: index.php?error=empty_login_fields');
        exit();
    }

    // Cari user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verifikasi user dan password
    if ($user && password_verify($password, $user['password'])) {
        // Login berhasil, set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        // Arahkan ke alur OTP sesuai skenario
        header('Location: otp.php');
    } else {
        // Login gagal
        header('Location: index.php?error=login_failed');
    }
    exit();
}

// Jika action tidak dikenali, redirect
header('Location: index.php');
?>