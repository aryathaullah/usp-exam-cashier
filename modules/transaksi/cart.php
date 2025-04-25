<?php
// Memastikan file ini tidak diakses langsung
if (!defined('BASE_PATH')) {
    // Definisikan BASE_PATH jika belum didefinisikan (untuk pengujian langsung)
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/usp-exam-project/');
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Proses tambah item ke keranjang
if (isset($_POST['add_to_cart'])) {
    $item_id = (int)$_POST['item_id'];
    $jumlah = (int)$_POST['jumlah'];
    
    // Validasi jumlah
    if ($jumlah <= 0) {
        showAlert('danger', 'Jumlah item harus lebih dari 0');
    } else {
        // Ambil data item
        $query = "SELECT * FROM item WHERE id = $item_id";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $item = mysqli_fetch_assoc($result);
            
            // Cek stok
            if ($jumlah > $item['stok']) {
                showAlert('danger', 'Stok tidak mencukupi. Stok tersedia: ' . $item['stok']);
            } else {
                // Cek apakah item sudah ada di keranjang
                $item_exists = false;
                foreach ($_SESSION['cart'] as $key => $cart_item) {
                    if ($cart_item['id'] == $item_id) {
                        // Update jumlah
                        $new_jumlah = $cart_item['jumlah'] + $jumlah;
                        
                        // Cek stok lagi
                        if ($new_jumlah > $item['stok']) {
                            showAlert('danger', 'Stok tidak mencukupi. Stok tersedia: ' . $item['stok']);
                            $item_exists = true;
                            break;
                        }
                        
                        $_SESSION['cart'][$key]['jumlah'] = $new_jumlah;
                        $item_exists = true;
                        showAlert('success', 'Item berhasil ditambahkan ke keranjang');
                        break;
                    }
                }
                
                // Jika item belum ada di keranjang
                if (!$item_exists) {
                    $_SESSION['cart'][] = [
                        'id' => $item['id'],
                        'kode_item' => $item['kode_item'],
                        'nama_item' => $item['nama_item'],
                        'harga' => $item['harga'],
                        'jumlah' => $jumlah
                    ];
                    showAlert('success', 'Item berhasil ditambahkan ke keranjang');
                }
            }
        } else {
            showAlert('danger', 'Item tidak ditemukan');
        }
    }
}

// Proses update jumlah item di keranjang
if (isset($_POST['update_cart'])) {
    foreach ($_POST['jumlah'] as $key => $jumlah) {
        $jumlah = (int)$jumlah;
        
        if ($jumlah <= 0) {
            // Hapus item dari keranjang jika jumlah 0 atau negatif
            unset($_SESSION['cart'][$key]);
        } else {
            // Ambil data item untuk cek stok
            $item_id = $_SESSION['cart'][$key]['id'];
            $query = "SELECT stok FROM item WHERE id = $item_id";
            $result = mysqli_query($conn, $query);
            $item = mysqli_fetch_assoc($result);
            
            // Cek stok
            if ($jumlah > $item['stok']) {
                showAlert('danger', 'Stok ' . $_SESSION['cart'][$key]['nama_item'] . ' tidak mencukupi. Stok tersedia: ' . $item['stok']);
            } else {
                // Update jumlah
                $_SESSION['cart'][$key]['jumlah'] = $jumlah;
            }
        }
    }
    
    // Reindex array setelah unset
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    
    showAlert('success', 'Keranjang berhasil diupdate');
}

// Proses hapus item dari keranjang
if (isset($_GET['remove'])) {
    $key = (int)$_GET['remove'];
    
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
        // Reindex array setelah unset
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        showAlert('success', 'Item berhasil dihapus dari keranjang');
    }
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: index.php?page=cart");
    exit();
}

// Proses kosongkan keranjang
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    showAlert('success', 'Keranjang berhasil dikosongkan');
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: index.php?page=cart");
    exit();
}

// Proses checkout
if (isset($_POST['checkout'])) {
    // Cek apakah keranjang kosong
    if (empty($_SESSION['cart'])) {
        showAlert('danger', 'Keranjang belanja kosong');
    } else {
        // Buat nomor transaksi
        $nomor_transaksi = 'TRX-' . date('YmdHis');
        
        // Hitung total transaksi
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['harga'] * $item['jumlah'];
        }
        
        // Simpan transaksi
        $query = "INSERT INTO transaksi (nomor_transaksi, tanggal, total) 
                  VALUES ('$nomor_transaksi', NOW(), $total)";
        
        if (mysqli_query($conn, $query)) {
            $transaksi_id = mysqli_insert_id($conn);
            
            // Simpan detail transaksi
            $success = true;
            
            foreach ($_SESSION['cart'] as $item) {
                $item_id = $item['id'];
                $harga = $item['harga'];
                $jumlah = $item['jumlah'];
                
                // Simpan detail transaksi
                $detail_query = "INSERT INTO transaksi_detail (transaksi_id, item_id, harga, jumlah) 
                                VALUES ($transaksi_id, $item_id, $harga, $jumlah)";
                
                if (!mysqli_query($conn, $detail_query)) {
                    $success = false;
                    showAlert('danger', 'Gagal menyimpan detail transaksi: ' . mysqli_error($conn));
                    break;
                }
                
                // Update stok
                $update_stok_query = "UPDATE item SET stok = stok - $jumlah WHERE id = $item_id";
                if (!mysqli_query($conn, $update_stok_query)) {
                    $success = false;
                    showAlert('danger', 'Gagal mengupdate stok: ' . mysqli_error($conn));
                    break;
                }
            }
            
            if ($success) {
                // Kosongkan keranjang
                $_SESSION['cart'] = [];
                
                showAlert('success', 'Transaksi berhasil disimpan');
                
                // Redirect ke halaman detail transaksi
                header("Location: index.php?page=transaksi_detail&id=$transaksi_id");
                exit();
            }
        } else {
            showAlert('danger', 'Gagal menyimpan transaksi: ' . mysqli_error($conn));
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 font-weight-bold text-gray-800">KERANJANG BELANJA</h1>
        <div>
            <?php if (!empty($_SESSION['cart'])): ?>
            <a href="index.php?page=cart&clear=1" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm mr-2">
                <i class="fas fa-trash fa-sm text-white-50"></i> Kosongkan Keranjang
            </a>
            <?php endif; ?>
            <a href="index.php?page=transaksi" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Item</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
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
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($_SESSION['cart'])) {
                                        $no = 1;
                                        $total = 0;
                                        foreach ($_SESSION['cart'] as $key => $item) {
                                            $subtotal = $item['harga'] * $item['jumlah'];
                                            $total += $subtotal;
                                            
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $item['kode_item'] . "</td>";
                                            echo "<td>" . $item['nama_item'] . "</td>";
                                            echo "<td>" . formatRupiah($item['harga']) . "</td>";
                                            echo "<td><input type='number' name='jumlah[$key]' value='" . $item['jumlah'] . "' min='1' class='form-control form-control-sm' style='width: 70px;'></td>";
                                            echo "<td>" . formatRupiah($subtotal) . "</td>";
                                            echo "<td>
                                                    <a href='index.php?page=cart&remove=$key' class='btn btn-sm btn-danger'>
                                                        <i class='fas fa-trash'></i>
                                                    </a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "<tr class='font-weight-bold'>";
                                        echo "<td colspan='5' class='text-right'>Total</td>";
                                        echo "<td>" . formatRupiah($total) . "</td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>Keranjang belanja kosong</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <div class="text-center mt-3">
                            <button type="submit" name="update_cart" class="btn btn-warning">
                                <i class="fas fa-sync"></i> Update Keranjang
                            </button>
                            <button type="submit" name="checkout" class="btn btn-success">
                                <i class="fas fa-check"></i> Checkout
                            </button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Item</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="item_id">Pilih Item</label>
                            <select class="form-control" id="item_id" name="item_id" required>
                                <option value="">Pilih Item</option>
                                <?php
                                // Ambil semua item yang stoknya > 0
                                $item_query = "SELECT * FROM item WHERE stok > 0 ORDER BY nama_item ASC";
                                $item_result = mysqli_query($conn, $item_query);
                                
                                while ($item = mysqli_fetch_assoc($item_result)) {
                                    echo "<option value='" . $item['id'] . "' data-stok='" . $item['stok'] . "' data-harga='" . $item['harga'] . "'>" . $item['nama_item'] . " (" . $item['kode_item'] . ") - Stok: " . $item['stok'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="text" class="form-control" id="harga" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" value="1" required>
                            <small class="form-text text-muted">Stok tersedia: <span id="stok-tersedia">0</span></small>
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script untuk menampilkan harga dan stok tersedia saat memilih item
document.addEventListener('DOMContentLoaded', function() {
    const itemSelect = document.getElementById('item_id');
    const hargaInput = document.getElementById('harga');
    const stokTersedia = document.getElementById('stok-tersedia');
    const jumlahInput = document.getElementById('jumlah');
    
    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stok = selectedOption.getAttribute('data-stok');
        const harga = selectedOption.getAttribute('data-harga');
        
        if (stok && harga) {
            stokTersedia.textContent = stok;
            hargaInput.value = formatRupiah(harga);
            jumlahInput.max = stok;
        } else {
            stokTersedia.textContent = '0';
            hargaInput.value = '';
            jumlahInput.max = '';
        }
    });
    
    function formatRupiah(angka) {
        return 'Rp ' + parseFloat(angka).toLocaleString('id-ID');
    }
});
</script>