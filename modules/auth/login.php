<?php
// Memulai session
session_start();

// Definisikan BASE_PATH
define('BASE_PATH', '../../');

// Include file konfigurasi database
require_once BASE_PATH . 'config/database.php';

// Include file fungsi
require_once BASE_PATH . 'includes/functions.php';

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Jika sudah login, redirect ke halaman dashboard
    header("Location: ../../index.php");
    exit();
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username dan password harus diisi";
    } else {
        // Query untuk mencari user dengan username yang sesuai
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect ke halaman dashboard
                header("Location: ../../index.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Password yang Anda masukkan salah";
            }
        } else {
            $_SESSION['login_error'] = "Username tidak ditemukan";
        }
    }
    
    // Redirect kembali ke halaman login untuk menghindari pengiriman ulang form
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil pesan error dari session jika ada
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    // Hapus pesan error dari session setelah diambil
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KASIRKU - LOGIN PAGE</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="login-container">

        <div class="login-header">
            <h2>LOGIN</h2>
        </div>
        
        <div class="login-form">
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn-login">
                    Masuk
                </button>
    
            </form>
        </div>
        
        <div class="login-footer">
            &copy; <?php echo date('Y'); ?> SISTEM KASIR - USP SMKN 1 SURABAYA
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>