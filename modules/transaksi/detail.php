<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    // Redirect ke halaman daftar transaksi jika tidak ada id
    header("Location: index.php?page=transaksi");
    exit();
}

$id = (int)$_GET['id'];

// Ambil data transaksi berdasarkan id
$query = "SELECT * FROM transaksi WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Redirect ke halaman daftar transaksi jika id tidak ditemukan
    showAlert('danger', 'Transaksi tidak ditemukan');
    header("Location: index.php?page=transaksi");
    exit();
}

$transaksi = mysqli_fetch_assoc($result);
?>

<div class="container-fluid">
    <div class="d-sm-flex mb-4">
        <h1 class="h3 mb-0 font-weight-bold text-gray-800">DETAIL TRANSAKSI</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nomor Transaksi</th>
                            <td>: <?php echo $transaksi['nomor_transaksi']; ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>: <?php echo date('d-m-Y H:i', strtotime($transaksi['tanggal'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Item</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Item</th>
                            <th>Nama Item</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mendapatkan detail transaksi
                        $detail_query = "SELECT td.*, i.kode_item, i.nama_item 
                                        FROM transaksi_detail td
                                        JOIN item i ON td.item_id = i.id
                                        WHERE td.transaksi_id = $id";
                        $detail_result = mysqli_query($conn, $detail_query);
                        
                        if (mysqli_num_rows($detail_result) > 0) {
                            $no = 1;
                            $total = 0;
                            while ($detail = mysqli_fetch_assoc($detail_result)) {
                                $subtotal = $detail['harga'] * $detail['jumlah'];
                                $total += $subtotal;
                                
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $detail['kode_item'] . "</td>";
                                echo "<td>" . $detail['nama_item'] . "</td>";
                                echo "<td>" . formatRupiah($detail['harga']) . "</td>";
                                echo "<td>" . $detail['jumlah'] . "</td>";
                                echo "<td>" . formatRupiah($subtotal) . "</td>";
                                echo "</tr>";
                            }
                            
                            echo "<tr class='font-weight-bold'>";
                            echo "<td colspan='5' class='text-right'>Total</td>";
                            echo "<td>" . formatRupiah($total) . "</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada detail transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="text-center mb-4">
        <a href="index.php?page=cart" class="btn btn-primary">Transaksi Kembali</a>
        <a href="index.php?page=transaksi" class="btn btn-secondary">Kembali</a>
    </div>
</div>