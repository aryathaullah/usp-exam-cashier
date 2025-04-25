<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    die('Akses langsung ke file ini tidak diizinkan');
}

// Proses hapus kategori
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah kategori digunakan oleh item
    $check_query = "SELECT COUNT(*) as total FROM item WHERE kategori_id = $id";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['total'] > 0) {
        showAlert('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh item');
    } else {
        $query = "DELETE FROM kategori WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            showAlert('success', 'Kategori berhasil dihapus');
        } else {
            showAlert('danger', 'Gagal menghapus kategori: ' . mysqli_error($conn));
        }
    }
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: index.php?page=kategori");
    exit();
}

// Ambil data kategori
$query = "SELECT k.*, COUNT(i.id) as jumlah_item 
          FROM kategori k 
          LEFT JOIN item i ON k.id = i.kategori_id 
          GROUP BY k.id 
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 font-weight-bold text-gray-800">Master Kategori</h1>
        <p class="text-muted">Menu untuk pengelolaan kategori produk.</p>
        </div>
    <a href="index.php?page=kategori_tambah" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori
    </a>
    </div>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th >No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th >Jumlah Item</th>
                        <th >Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row['nama_kategori'] . "</td>";
                            echo "<td>" . $row['deskripsi'] . "</td>";
                            echo "<td>" . $row['jumlah_item'] . "</td>";
                            echo "<td>
                                    <a href='index.php?page=kategori_edit&id=" . $row['id'] . "' class='btn btn-sm btn-warning'>
                                        <i class='fas fa-edit'></i> Edit
                                    </a>
                                    <a href='javascript:void(0);' onclick='konfirmasiHapus(\"index.php?page=kategori&delete=" . $row['id'] . "\", \"" . $row['nama_kategori'] . "\")' class='btn btn-sm btn-danger'>
                                        <i class='fas fa-trash'></i> Hapus
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data kategori</td></tr>";
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
    if (confirm('Apakah Anda yakin ingin menghapus kategori ' + nama + '?')) {
        window.location.href = url;
    }
}
</script>