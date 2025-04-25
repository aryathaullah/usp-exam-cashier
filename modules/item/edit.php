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

// Ambil data item berdasarkan id
$query = "SELECT * FROM item WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Redirect ke halaman daftar item jika id tidak ditemukan
    showAlert('danger', 'Item tidak ditemukan');
    header("Location: index.php?page=item");
    exit();
}

$item = mysqli_fetch_assoc($result);

// Proses edit item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_item = sanitize($_POST['kode_item']);
    $nama_item = sanitize($_POST['nama_item']);
    $kategori_id = (int)$_POST['kategori_id'];
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = sanitize($_POST['deskripsi']);
    
    // Validasi input
    $errors = [];
    
    if (empty($kode_item)) {
        $errors[] = "Kode item harus diisi";
    }
    
    if (empty($nama_item)) {
        $errors[] = "Nama item harus diisi";
    }
    
    if ($kategori_id <= 0) {
        $errors[] = "Kategori harus dipilih";
    }
    
    if ($harga <= 0) {
        $errors[] = "Harga harus lebih dari 0";
    }
    
    if ($stok < 0) {
        $errors[] = "Stok tidak boleh negatif";
    }
    
    // Cek apakah kode item sudah ada (kecuali untuk item yang sedang diedit)
    $check_query = "SELECT * FROM item WHERE kode_item = '$kode_item' AND id != $id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $errors[] = "Kode item sudah digunakan";
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $query = "UPDATE item SET 
                  kode_item = '$kode_item', 
                  nama_item = '$nama_item', 
                  kategori_id = $kategori_id, 
                  harga = $harga, 
                  stok = $stok, 
                  deskripsi = '$deskripsi' 
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            showAlert('success', 'Item berhasil diupdate');
            // Redirect ke halaman daftar item
            header("Location: index.php?page=item");
            exit();
        } else {
            showAlert('danger', 'Gagal mengupdate item: ' . mysqli_error($conn));
        }
    } else {
        // Tampilkan error
        foreach ($errors as $error) {
            showAlert('danger', $error);
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Item</h1>
        <a href="index.php?page=item" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Item</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="form-group">
                    <label for="kode_item">Kode Item</label>
                    <input type="text" class="form-control" id="kode_item" name="kode_item" value="<?php echo $item['kode_item']; ?>" required>
                    <div class="invalid-feedback">
                        Kode item harus diisi
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nama_item">Nama Item</label>
                    <input type="text" class="form-control" id="nama_item" name="nama_item" value="<?php echo $item['nama_item']; ?>" required>
                    <div class="invalid-feedback">
                        Nama item harus diisi
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select class="form-control" id="kategori_id" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php
                        // Ambil semua kategori
                        $kategori_query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
                        $kategori_result = mysqli_query($conn, $kategori_query);
                        
                        while ($kategori = mysqli_fetch_assoc($kategori_result)) {
                            $selected = ($kategori['id'] == $item['kategori_id']) ? 'selected' : '';
                            echo "<option value='" . $kategori['id'] . "' $selected>" . $kategori['nama_kategori'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Kategori harus dipilih
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" class="form-control" id="harga" name="harga" min="0" value="<?php echo $item['harga']; ?>" required>
                        <div class="invalid-feedback">
                            Harga harus lebih dari 0
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?php echo $item['stok']; ?>" required>
                    <div class="invalid-feedback">
                        Stok tidak boleh negatif
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $item['deskripsi']; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php?page=item" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>