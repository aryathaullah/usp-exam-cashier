<?php
// Cek apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses form
    $nama_kategori = sanitize($_POST['nama_kategori']);
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    
    // Validasi input
    if (empty($nama_kategori)) {
        showAlert('danger', 'Nama kategori harus diisi!');
    } else {
        // Cek apakah nama kategori sudah ada
        $query_check = "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'";
        $result_check = mysqli_query($conn, $query_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            showAlert('danger', 'Nama kategori sudah digunakan!');
        } else {
            // Insert data
            $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                showAlert('success', 'Kategori berhasil ditambahkan!');
                // Redirect ke halaman kategori
                header("Location: index.php?page=kategori");
                exit();
            } else {
                showAlert('danger', 'Gagal menambahkan kategori: ' . mysqli_error($conn));
            }
        }
    }
}
?>

<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold mt-2 text-center">TAMBAH KATEGORI</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="index.php?page=kategori" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
