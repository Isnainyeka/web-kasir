<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Koneksi ke database
    $host = "localhost";
    $dbname = "kasir";
    $username_db = "root";
    $password_db = "";

    $conn = new mysqli($host, $username_db, $password_db, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]));
    }

    // Cek status admin sebelum menghapus
    $checkStatus = $conn->prepare("SELECT status FROM admin WHERE id = ?");
    $checkStatus->bind_param("i", $id);
    $checkStatus->execute();
    $result = $checkStatus->get_result();
    $row = $result->fetch_assoc();

    if ($row['status'] == 'Aktif') {
        echo json_encode(["status" => "error", "message" => "Admin dengan status 'Aktif' tidak bisa dihapus!"]);
        exit();
    }

    // Hapus admin
    $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Admin berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus admin"]);
    }

    $stmt->close();
    $conn->close();
}
?>
