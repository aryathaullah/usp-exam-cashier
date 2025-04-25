-- Database: usp_kasir
-- Struktur database untuk aplikasi kasir USP Exam Project

-- Hapus database jika sudah ada
DROP DATABASE IF EXISTS usp_kasir;

-- Buat database baru
CREATE DATABASE usp_kasir;

-- Gunakan database
USE usp_kasir;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel item (produk)
CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_item VARCHAR(20) NOT NULL UNIQUE,
    nama_item VARCHAR(100) NOT NULL,
    kategori_id INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(20) NOT NULL UNIQUE,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(10,2) NOT NULL,
    bayar DECIMAL(10,2) NOT NULL,
    kembali DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'selesai', 'batal') NOT NULL DEFAULT 'pending',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel detail transaksi
CREATE TABLE transaksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    item_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES item(id)
);

-- Tambahkan user admin default
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@example.com', 'admin');
-- Password: password

-- Tambahkan beberapa kategori
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Makanan', 'Berbagai jenis makanan'),
('Minuman', 'Berbagai jenis minuman'),
('Snack', 'Berbagai jenis makanan ringan');

-- Tambahkan beberapa item
INSERT INTO item (kode_item, nama_item, kategori_id, harga, stok, deskripsi) VALUES
('MKN001', 'Nasi Goreng', 1, 15000, 100, 'Nasi goreng spesial'),
('MKN002', 'Mie Goreng', 1, 12000, 100, 'Mie goreng spesial'),
('MNM001', 'Es Teh', 2, 5000, 100, 'Es teh manis'),
('MNM002', 'Es Jeruk', 2, 6000, 100, 'Es jeruk segar'),
('SNK001', 'Keripik Kentang', 3, 8000, 50, 'Keripik kentang renyah');