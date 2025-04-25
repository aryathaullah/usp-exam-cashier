<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    // Redirect ke halaman daftar kategori jika tidak ada id
    header("Location: index.php?page=kategori");
    exit();
}

$id = (int)$_GET['id'];

// Ambil data kategori berdasarkan id
$query = "SELECT * FROM kategori WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Redirect ke halaman daftar kategori jika id tidak ditemukan
    showAlert('danger', 'Kategori tidak ditemukan');
    header("Location: index.php?page=kategori");
    exit();
}

$kategori = mysqli_fetch_assoc($result);

// Proses edit kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = sanitize($_POST['nama_kategori']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama_kategori)) {
        $errors[] = "Nama kategori harus diisi";
    }
    
    // Cek apakah nama kategori sudah ada (kecuali untuk kategori yang sedang diedit)
    $check_query = "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori' AND id != $id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $errors[] = "Nama kategori sudah digunakan";
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $query = "UPDATE kategori SET 
                  nama_kategori = '$nama_kategori', 
                  deskripsi = '$deskripsi' 
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            showAlert('success', 'Kategori berhasil diupdate');
            // Redirect ke halaman daftar kategori
            header("Location: index.php?page=kategori");
            exit();
        } else {
            showAlert('danger', 'Gagal mengupdate kategori: ' . mysqli_error($conn));
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
        <h1 class="h3 mb-0 text-gray-800">Edit Kategori</h1>
        <a href="index.php?page=kategori" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Kategori</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo $kategori['nama_kategori']; ?>" required>
                    <div class="invalid-feedback">
                        Nama kategori harus diisi
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $kategori['deskripsi']; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php?page=kategori" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>