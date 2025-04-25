<?php

// Fungsi untuk menampilkan pesan alert
function showAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Fungsi untuk menampilkan alert dan menghapusnya setelah ditampilkan
function displayAlert() {
    if (isset($_SESSION['alert'])) {
        // Pastikan $_SESSION['alert'] adalah array
        if (is_array($_SESSION['alert']) && isset($_SESSION['alert']['type']) && isset($_SESSION['alert']['message'])) {
            $type = $_SESSION['alert']['type'];
            $message = $_SESSION['alert']['message'];
            echo '<div class="container-fluid left:100">
                        <div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                            ' . $message . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                  </div>';
                    
        }
        // Hapus alert dari session setelah ditampilkan
        unset($_SESSION['alert']);
    }
}

// Fungsi untuk mengamankan input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan data user yang sedang login
function getCurrentUser() {
    global $conn;
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = $userId";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Fungsi untuk mendapatkan jumlah total kategori
function getTotalKategori() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM kategori";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

// Fungsi untuk mendapatkan jumlah total item
function getTotalItem() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM item";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

// Fungsi untuk mendapatkan jumlah total transaksi
function getTotalTransaksi() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM transaksi";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Fungsi untuk mendapatkan total pendapatan
function getTotalPendapatan() {
    global $conn;
    $query = "SELECT SUM(td.harga * td.jumlah) as total 
              FROM transaksi_detail td 
              JOIN transaksi t ON td.transaksi_id = t.id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ? $row['total'] : 0;
}

// Fungsi untuk format angka ke format rupiah
function formatRupiah($angka) {
    // Pastikan angka tidak null
    $angka = $angka ?? 0;
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi untuk generate nomor transaksi
function generateTransactionNumber() {
    return 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);
}

/**
 * Fungsi untuk generate kode item otomatis
 * @param string $prefix Awalan kode (contoh: MKN untuk makanan, MNM untuk minuman)
 * @return string Kode item dengan format PREFIX000X
 */
function generateItemCode($prefix = 'ITM') {
    global $conn;
    
    // Cari kode item terakhir dengan prefix yang sama
    $query = "SELECT kode_item FROM item WHERE kode_item LIKE '$prefix%' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_code = $row['kode_item'];
        
        // Ambil angka dari kode terakhir
        $number = intval(substr($last_code, strlen($prefix)));
        
        // Tambahkan 1 untuk kode baru
        $new_number = $number + 1;
        
        // Format angka dengan leading zeros
        $formatted_number = str_pad($new_number, 3, '0', STR_PAD_LEFT);
        
        // Gabungkan prefix dengan angka baru
        return $prefix . $formatted_number;
    } else {
        // Jika belum ada kode dengan prefix tersebut, mulai dari 001
        return $prefix . '001';
    }
}
?>