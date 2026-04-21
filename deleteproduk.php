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
    $id = $_GET['id'];

    if (!empty($id)) {
        $query = "DELETE FROM products WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            header("Location: produk.php");
            exit();
        } else {
            echo "ERROR: " . mysqli_error($conn);
        }
    } else {
        echo "Produk tidak valid!";
    }
} else {
    echo "Produk tidak ditemukan!";
}
?>
