<?php
// cart_manager.php

// Selalu mulai session di file API
session_start();

// Inisialisasi keranjang di session jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil aksi dari URL, defaultnya 'get'
$action = $_GET['action'] ?? 'get';

switch ($action) {
    case 'add':
        // Ambil data JSON yang dikirim dari JavaScript
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data && isset($data['id'])) {
            $is_duplicate = false;
            foreach ($_SESSION['cart'] as $item) {
                if ($item['id'] === $data['id']) {
                    $is_duplicate = true;
                    break;
                }
            }
            // Hanya tambahkan jika belum ada di keranjang
            if (!$is_duplicate) {
                $_SESSION['cart'][] = $data;
            }
        }
        break;

    case 'remove':
        $id_to_remove = $_GET['id'] ?? null;
        if ($id_to_remove !== null) {
            // Filter array untuk menghapus item dengan ID yang cocok
            $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function($item) use ($id_to_remove) {
                return $item['id'] != $id_to_remove;
            }));
        }
        break;

    case 'get':
    default:
        // Tidak perlu melakukan apa-apa, biarkan kode di bawah mengirim data
        break;
}

// Set header sebagai JSON dan kirim data keranjang yang terbaru
header('Content-Type: application/json');
echo json_encode(array_values($_SESSION['cart']));
exit();