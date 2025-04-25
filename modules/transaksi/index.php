<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Proses hapus transaksi
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Hapus detail transaksi terlebih dahulu
    $delete_detail_query = "DELETE FROM transaksi_detail WHERE transaksi_id = $id";
    if (mysqli_query($conn, $delete_detail_query)) {
        // Hapus transaksi
        $delete_query = "DELETE FROM transaksi WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            showAlert('success', 'Transaksi berhasil dihapus');
        } else {
            showAlert('danger', 'Gagal menghapus transaksi: ' . mysqli_error($conn));
        }
    } else {
        showAlert('danger', 'Gagal menghapus detail transaksi: ' . mysqli_error($conn));
    }
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: index.php?page=transaksi");
    exit();
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800">Transaksi</h1>
        <p class="text-muted">Menu untuk transaksi produk.</p>
        </div>
        <a href="index.php?page=cart" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Transaksi Baru
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Histori Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mendapatkan semua transaksi dengan total item dan total harga
                        $query = "SELECT t.*, 
                                  COUNT(td.id) as total_item,
                                  SUM(td.harga * td.jumlah) as total_harga
                                  FROM transaksi t 
                                  LEFT JOIN transaksi_detail td ON t.id = td.transaksi_id 
                                  GROUP BY t.id 
                                  ORDER BY t.tanggal DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['nomor_transaksi'] . "</td>";
                                echo "<td>" . date('d-m-Y H:i', strtotime($row['tanggal'])) . "</td>";
                                echo "<td>" . $row['total_item'] . "</td>";
                                echo "<td>" . formatRupiah($row['total_harga']) . "</td>";
                                echo "<td>
                                        <a href='index.php?page=transaksi_detail&id=" . $row['id'] . "' class='btn btn-sm btn-info'>
                                            <i class='fas fa-eye'></i> Detail
                                        </a>
                                        <a href='javascript:void(0);' onclick='konfirmasiHapus(\"index.php?page=transaksi&delete=" . $row['id'] . "\", \"" . $row['nomor_transaksi'] . "\")' class='btn btn-sm btn-danger'>
                                            <i class='fas fa-trash'></i> Hapus
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>