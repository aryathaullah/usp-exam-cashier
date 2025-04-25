<?php
// Dapatkan halaman aktif
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!-- css custom -->
<link rel="stylesheet" href="assets/css/navigation.css">

<!-- sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h3>E-CASHIER</h3>
    </div>

    <ul class="list-unstyled components">
        <li>
            <a href="index.php?page=dashboard" class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="index.php?page=kategori" class="<?php echo ($current_page == 'kategori' || $current_page == 'kategori_tambah' || $current_page == 'kategori_edit') ? 'active' : ''; ?>">
                Master Kategori
            </a>
        </li>
        <li>
            <a href="index.php?page=item" class="<?php echo ($current_page == 'item' || $current_page == 'item_tambah' || $current_page == 'item_edit') ? 'active' : ''; ?>">
                Master Items
            </a>
        </li>
        <li>
            <a href="index.php?page=transaksi" class="<?php echo ($current_page == 'transaksi' || $current_page == 'cart' || $current_page == 'checkout') ? 'active' : ''; ?>">
                Transaksi
            </a>
        </li>
        <li class="logout">
            <a href="modules/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </li>
    </ul>
</nav>

<!-- Content -->
<div class="content">
    