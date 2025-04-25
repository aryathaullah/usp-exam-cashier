<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Proses hapus item
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah item digunakan dalam transaksi
    $check_query = "SELECT * FROM transaksi_detail WHERE item_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        showAlert('danger', 'Item tidak dapat dihapus karena sudah digunakan dalam transaksi');
    } else {
        // Hapus item
        $delete_query = "DELETE FROM item WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            showAlert('success', 'Item berhasil dihapus');
        } else {
            showAlert('danger', 'Gagal menghapus item: ' . mysqli_error($conn));
        }
    }
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: index.php?page=item");
    exit();
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800">Master Produk</h1>
        <p class="text-muted">Menu untuk pengelolaan produk.</p>
        </div>
        <a href="index.php?page=item_tambah" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
        </a>
    </div>
    
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Item</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mendapatkan semua item dengan nama kategori
                        $query = "SELECT i.*, k.nama_kategori 
                                  FROM item i 
                                  LEFT JOIN kategori k ON i.kategori_id = k.id 
                                  ORDER BY i.nama_item ASC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['kode_item'] . "</td>";
                                echo "<td>" . $row['nama_item'] . "</td>";
                                echo "<td>" . $row['nama_kategori'] . "</td>";
                                echo "<td>" . formatRupiah($row['harga']) . "</td>";
                                echo "<td>" . $row['stok'] . "</td>";
                                echo "<td>
                                        <a href='index.php?page=item_edit&id=" . $row['id'] . "' class='btn btn-sm btn-warning'>
                                            <i class='fas fa-edit'></i> Edit
                                        </a>
                                        <a href='javascript:void(0);' onclick='konfirmasiHapus(\"index.php?page=item&delete=" . $row['id'] . "\", \"" . $row['nama_item'] . "\")' class='btn btn-sm btn-danger'>
                                            <i class='fas fa-trash'></i> Hapus
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Tidak ada data item</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>