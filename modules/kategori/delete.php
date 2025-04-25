<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    // Redirect ke halaman daftar kategori jika tidak ada id
    header("Location: index.php?page=kategori");
    exit();
}

$id = (int)$_GET['id'];

// Cek apakah kategori digunakan dalam item
$check_query = "SELECT * FROM item WHERE kategori_id = $id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    showAlert('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh beberapa item');
    header("Location: index.php?page=kategori");
    exit();
}

// Hapus kategori
$delete_query = "DELETE FROM kategori WHERE id = $id";
if (mysqli_query($conn, $delete_query)) {
    showAlert('success', 'Kategori berhasil dihapus');
} else {
    showAlert('danger', 'Gagal menghapus kategori: ' . mysqli_error($conn));
}

// Redirect ke halaman daftar kategori
header("Location: index.php?page=kategori");
exit();
?>