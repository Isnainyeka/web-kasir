<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasir";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_kategori = intval($_GET['id']); // pastikan id numerik

    // Cek apakah ada produk yang pakai kategori ini
    $cekProduk = "SELECT COUNT(*) AS total FROM products WHERE fid_category = $id_kategori";
    $result = $conn->query($cekProduk);
    $data = $result->fetch_assoc();

    if ($data['total'] > 0) {
        echo "<script>alert('Kategori tidak bisa dihapus karena masih digunakan oleh produk!'); window.location.href='kategori.php';</script>";
        exit();
    }

    // Hapus kategori
    $query = "DELETE FROM category WHERE id = $id_kategori";
    if ($conn->query($query)) {
        header("Location: kategori.php");
        exit();
    } else {
        echo "ERROR: " . $conn->error;
    }
} else {
    echo "Kategori tidak ditemukan!";
}
?>
