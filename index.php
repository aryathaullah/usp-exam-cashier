<?php
// Memulai session
session_start();

// Definisikan BASE_PATH
define('BASE_PATH', __DIR__ . '/');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    // Jika belum login dan bukan di halaman login, redirect ke halaman login
    header("Location: modules/auth/login.php");
    exit();
}

// Include file konfigurasi database
require_once 'config/database.php';

// Include file fungsi
require_once 'includes/functions.php';

// Tentukan halaman yang akan ditampilkan
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Validasi halaman
$allowed_pages = ['dashboard', 'kategori', 'kategori_tambah', 'kategori_edit', 'item', 'item_tambah', 'item_edit', 'transaksi', 'cart', 'checkout', 'history', 'transaksi_detail'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Tentukan path file berdasarkan halaman
if ($page == 'dashboard') {
    $file_path = 'modules/dashboard/index.php';
} elseif ($page == 'kategori') {
    $file_path = 'modules/kategori/index.php';
} elseif ($page == 'kategori_tambah') {
    $file_path = 'modules/kategori/create.php';
} elseif ($page == 'kategori_edit') {
    $file_path = 'modules/kategori/edit.php';
} elseif ($page == 'item') {
    $file_path = 'modules/item/index.php';
} elseif ($page == 'item_tambah') {
    $file_path = 'modules/item/create.php';
} elseif ($page == 'item_edit') {
    $file_path = 'modules/item/edit.php';
} elseif ($page == 'transaksi') {
    $file_path = 'modules/transaksi/index.php';
} elseif ($page == 'cart') {
    $file_path = 'modules/transaksi/cart.php';
} elseif ($page == 'checkout') {
    $file_path = 'modules/transaksi/checkout.php';
} elseif ($page == 'history') {
    $file_path = 'modules/transaksi/history.php';
} elseif ($page == 'transaksi_detail') {
    $file_path = 'modules/transaksi/detail.php';
} else {
    $file_path = 'modules/dashboard/index.php';
}

// Header
include_once 'includes/header.php';

// Sidebar
include_once 'includes/sidebar.php';

// Tampilkan konten halaman
include($file_path);

// Footer
include_once 'includes/footer.php';
?>
