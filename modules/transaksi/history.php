<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Ambil data transaksi dari database
$query = "SELECT t.*, 
          (SELECT COUNT(*) FROM transaksi_detail WHERE transaksi_id = t.id) as jumlah_item 
          FROM transaksi t 
          ORDER BY t.tanggal DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800">Histori Transaksi</h1>
        <p class="text-muted">Menu untuk mengelola histori transaksi.</p>
        </div>
        <div>
            <a href="index.php?page=transaksi" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th>Jumlah Item</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['nomor_transaksi'] . "</td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row['tanggal'])) . "</td>";
                                echo "<td>" . $row['jumlah_item'] . " item</td>";
                                echo "<td>" . formatRupiah($row['total']) . "</td>";
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

<script>
function konfirmasiHapus(url, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ' + nama + '?')) {
        window.location.href = url;
    }
}
</script>