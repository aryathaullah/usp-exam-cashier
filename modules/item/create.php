<?php
// Cek apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses form
    $kode_item = sanitize($_POST['kode_item']);
    $nama_item = sanitize($_POST['nama_item']);
    $kategori_id = sanitize($_POST['kategori_id']);
    $harga = sanitize($_POST['harga']);
    $stok = sanitize($_POST['stok']);
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    
    // Validasi input
    if (empty($kode_item) || empty($nama_item) || empty($kategori_id) || empty($harga) || empty($stok)) {
        showAlert('danger', 'Semua field harus diisi!');
    } else {
        // Cek apakah kode sudah ada
        $query_check = "SELECT * FROM item WHERE kode_item = '$kode_item'";
        $result_check = mysqli_query($conn, $query_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            showAlert('danger', 'Kode item sudah digunakan!');
        } else {
            // Insert data
            $query = "INSERT INTO item (kode_item, nama_item, kategori_id, harga, stok, deskripsi) VALUES ('$kode_item', '$nama_item', '$kategori_id', '$harga', '$stok', '$deskripsi')";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                showAlert('success', 'Item berhasil ditambahkan!');
                // Redirect ke halaman item
                header("Location: index.php?page=item");
                exit();
            } else {
                showAlert('danger', 'Gagal menambahkan item: ' . mysqli_error($conn));
            }
        }
    }
}

// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori";
$result_kategori = mysqli_query($conn, $query_kategori);
?>

<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="font-weight-bold mt-2">TAMBAH PRODUK</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="" >
                        <div class="form-group">
                            <label for="kode_item">Kode Item</label>
                            <input type="text" class="form-control" id="kode_item" name="kode_item" <input type="text" value="<?= 'TRX-' . date('YmdHis') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_item">Nama Item</label>
                            <input type="text" class="form-control" id="nama_item" name="nama_item" required>
                        </div>
                        <div class="form-group">
                            <label for="kategori_id">Kategori</label>
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                <option value="<?php echo $kategori['id']; ?>"><?php echo $kategori['nama_kategori']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control" id="harga" name="harga" min="0" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="index.php?page=item" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>