<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    die('Akses langsung ke file ini tidak diizinkan');
}

// Mengambil data untuk dashboard
$totalKategori = getTotalKategori();
$totalItem = getTotalItem();
$totalTransaksi = getTotalTransaksi();
$totalPendapatan = getTotalPendapatan();

// Ambil data transaksi terbaru
$query_transaksi = "SELECT t.*, u.nama_lengkap
                    FROM transaksi t 
                    LEFT JOIN users u ON t.user_id = u.id 
                    ORDER BY t.tanggal DESC 
                    LIMIT 5";
$result_transaksi = mysqli_query($conn, $query_transaksi);

// Ambil data item terlaris
$query_item = "SELECT i.*, k.nama_kategori as kategori_nama, 
               SUM(td.jumlah) as total_terjual 
               FROM item i 
               LEFT JOIN kategori k ON i.kategori_id = k.id 
               LEFT JOIN transaksi_detail td ON i.id = td.item_id 
               GROUP BY i.id 
               ORDER BY total_terjual DESC 
               LIMIT 5";
$result_item = mysqli_query($conn, $query_item);

// Ambil data pendapatan bulanan untuk grafik
$pendapatan_bulanan = array_fill(0, 12, 0); // Inisialisasi array dengan 0 untuk 12 bulan
$jumlah_transaksi_bulanan = array_fill(0, 12, 0); // Inisialisasi array untuk jumlah transaksi

$query_pendapatan = "SELECT 
                      MONTH(tanggal) as bulan, 
                      SUM(total) as total_pendapatan,
                      COUNT(*) as jumlah_transaksi
                    FROM transaksi 
                    WHERE YEAR(tanggal) = YEAR(CURRENT_DATE())
                    GROUP BY MONTH(tanggal)
                    ORDER BY MONTH(tanggal)";
$result_pendapatan = mysqli_query($conn, $query_pendapatan);

if ($result_pendapatan) {
    while ($row = mysqli_fetch_assoc($result_pendapatan)) {
        $bulan_index = $row['bulan'] - 1; // Konversi ke index array (0-11)
        $pendapatan_bulanan[$bulan_index] = (int)$row['total_pendapatan'];
        $jumlah_transaksi_bulanan[$bulan_index] = (int)$row['jumlah_transaksi'];
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 font-weight-bold text-gray-800">Dashboard</h1>
            <p class="mb-4 text-muted">Dashboard untuk melihat rincian terkait kategori, items, transaksi serta keuangan.</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Card Total Kategori -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-90 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 text-center">
                                Total Kategori</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-1000 text-center mt-3"><?php echo $totalKategori; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Item -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-90 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 text-center">
                                Total Item</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-1000 text-center mt-3"><?php echo $totalItem; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Transaksi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-90 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-center">
                                Total Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-1000 text-center mt-3"><?php echo $totalTransaksi; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Pendapatan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-90 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 text-center">
                                Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-1000 text-center mt-3"><?php echo formatRupiah($totalPendapatan); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik Pendapatan Bulanan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Pendapatan Bulanan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="chartjs-line"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result_transaksi) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result_transaksi)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . $row['nomor_transaksi'] . "</td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['tanggal'])) . "</td>";
                                        // Perbaikan untuk total_harga yang tidak terdefinisi
                                        echo "<td>" . formatRupiah(isset($row['total']) ? $row['total'] : 0) . "</td>";
                                        // Perbaikan untuk status yang tidak terdefinisi
                                        $status = isset($row['status']) ? $row['status'] : 'selesai';
                                        echo "<td><span class='badge badge-" . ($status == 'selesai' ? 'success' : 'warning') . "'>" . ucfirst($status) . "</span></td>";

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
    </div>

    <!-- Item Terlaris -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Item Terlaris</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Item</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result_item) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result_item)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . ($row['kode_item'] ?? 'N/A') . "</td>";
                                        echo "<td>" . ($row['nama_item'] ?? 'N/A') . "</td>";
                                        echo "<td>" . ($row['kategori_nama'] ?? 'N/A') . "</td>";
                                        echo "<td>" . formatRupiah($row['harga'] ?? 0) . "</td>";
                                        echo "<td>" . ($row['total_terjual'] ?? 0) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data item</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Definisikan tema warna
    window.theme = {
        primary: '#4e73df',
        secondary: '#6c757d',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b'
    };
    
    // Data pendapatan bulanan dari PHP
    var pendapatanBulanan = <?php echo json_encode($pendapatan_bulanan); ?>;
    var jumlahTransaksiBulanan = <?php echo json_encode($jumlah_transaksi_bulanan); ?>;
    
    // Buat chart
    new Chart(document.getElementById("chartjs-line"), { 
        type: "line", 
        data: { 
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"], 
            datasets: [{ 
                label: "Pendapatan (Rp)", 
                fill: true, 
                backgroundColor: "transparent", 
                borderColor: window.theme.primary, 
                data: pendapatanBulanan
            }, { 
                label: "Jumlah Transaksi", 
                fill: true, 
                backgroundColor: "transparent", 
                borderColor: "#adb5bd", 
                borderDash: [4, 4], 
                data: jumlahTransaksiBulanan
            }] 
        }, 
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            scales: { 
                xAxes: [{ 
                    reverse: true, 
                    gridLines: { 
                        color: "rgba(0,0,0,0.05)" 
                    } 
                }], 
                yAxes: [{ 
                    borderDash: [5, 5], 
                    gridLines: { 
                        color: "rgba(0,0,0,0)", 
                        fontColor: "#fff" 
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }] 
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                        var value = tooltipItem.yLabel;
                        if (tooltipItem.datasetIndex === 0) { // Pendapatan
                            return datasetLabel + ': Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        } else { // Jumlah Transaksi
                            return datasetLabel + ': ' + value;
                        }
                    }
                }
            }
        } 
    });
});
</script>
