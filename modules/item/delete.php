<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    // Redirect ke halaman daftar item jika tidak ada id
    header("Location: index.php?page=item");
    exit();
}

$id = (int)$_GET['id'];

// Cek apakah item digunakan dalam transaksi
$check_query = "SELECT * FROM transaksi_detail WHERE item_id = $id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    showAlert('danger', 'Item tidak dapat dihapus karena sudah digunakan dalam transaksi');
    header("Location: index.php?page=item");
    exit();
}

// Hapus item
$delete_query = "DELETE FROM item WHERE id = $id";
if (mysqli_query($conn, $delete_query)) {
    showAlert('success', 'Item berhasil dihapus');
} else {
    showAlert('danger', 'Gagal menghapus item: ' . mysqli_error($conn));
}

// Redirect ke halaman daftar item
header("Location: index.php?page=item");
exit();
?>