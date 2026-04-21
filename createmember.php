<?php
session_start();

// Set timezone supaya waktu sinkron
date_default_timezone_set('Asia/Jakarta');

// Koneksi ke database
$host = "localhost";
$dbname = "kasir";
$username_db = "root";
$password_db = "";

$conn = new mysqli($host, $username_db, $password_db, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validasi sederhana (bisa dikembangkan)
    if (empty($name) || empty($email) || empty($phone)) {
        echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
        exit();
    }

    // Cek apakah phone sudah dipakai
    $check = $conn->prepare("SELECT * FROM member WHERE phone = ?");
    $check->bind_param("s", $phone);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Member sudah ada!'); window.history.back();</script>";
        exit();
    }

    $status = 'active'; // Status default aktif
    $last_transaction = date('Y-m-d H:i:s'); // waktu saat ini

    // Insert member baru
    $stmt = $conn->prepare("INSERT INTO member (name, email, phone, status, last_transaction) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $status, $last_transaction);

    if ($stmt->execute()) {
        echo "<script>alert('Member berhasil ditambahkan!'); window.location.href='member.php';</script>";
    } else {
        echo "Gagal menambahkan member: " . $conn->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Members</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="flex min-h-screen">
    <!-- Tombol Back -->
    <div class="absolute top-4 left-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Container Form -->
    <div class="flex-1 pr-[100px] flex items-center justify-center">
    <div class="bg-[#BCA48F] p-12 rounded-lg shadow-lg w-[550px] ml-[90px] pb-10 mt-[-10px]">
            <h2 class="text-center text-white mb-6 text-xl">CREATE MEMBER</h2>
            <form method="POST">
                <div class="mb-4 flex items-center">
                    <label class="w-40 text-sm font-semibold">Name</label>
                    <input type="text" name="name" required class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4 flex items-center">
                    <label class="w-40 text-sm font-semibold">Email</label>
                    <input type="email" name="email" required class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4 flex items-center">
                    <label class="w-40 text-sm font-semibold">Phone</label>
                    <input type="tel" name="phone" required class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="flex justify-between space-x-4">
                    <button type="submit" class="w-1/2 px-4 py-2 bg-[#614C3A] hover:bg-[#723713] text-white rounded-3xl">Create</button>
                    <button type="button" onclick="window.location.href='member.php'" class="w-1/2 px-4 py-2 bg-[#614C3A] hover:bg-[#723713] text-white rounded-3xl">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
