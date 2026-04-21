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

// Ambil ID dari parameter URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM member WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='member.php';</script>";
        exit;
    }
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];
        // Cek apakah phone sudah dipakai
$check = $conn->prepare("SELECT * FROM member WHERE phone = ? AND id != ?");
$check->bind_param("si", $phone, $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Nomor telepon sudah digunakan oleh member lain!'); window.history.back();</script>";
    exit();
}

    $status = $_POST['status']; // ENUM: Active / Non-Active

$update_stmt = $conn->prepare("UPDATE member SET name = ?, email = ?, phone = ?, status = ?, last_transaction = NOW() WHERE id = ?");
$update_stmt->bind_param("ssssi", $name, $email, $phone, $status, $id);

if ($update_stmt->execute()) {
    echo "<script>alert('Member berhasil diperbarui!'); window.location.href='member.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan saat update!');</script>";
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Members</title>
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
        <h2 class="text-center text-white mb-4 text-xl">UPDATE MEMBER</h2>
        <form method="POST">
        <div class="mb-4 flex items-center">
    <label class="w-32 text-sm font-semibold">Name</label>
    <input type="text" name="name" value="<?= $row['name'] ?>" class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none" required>
</div>
<div class="mb-4 flex items-center">
    <label class="w-32 text-sm font-semibold">Email</label>
    <input type="email" name="email" value="<?= $row['email'] ?>" class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none" required>
</div>
<div class="mb-4 flex items-center">
    <label class="w-32 text-sm font-semibold">Phone</label>
    <input type="tel" name="phone" value="<?= $row['phone'] ?>" class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none" required>
</div>
<div class="mb-4 flex items-center">
    <label class="w-32 text-sm font-semibold">Status</label>
    <select name="status" class="flex-1 p-2 rounded-3xl bg-[#E6D8CD] border-none">
<option value="active" <?= ($row['status'] == 'active') ? 'selected' : '' ?>>Active</option>
<option value="non-active" <?= ($row['status'] == 'non-active') ? 'selected' : '' ?>>Non-Active</option>
    </select>
</div>
            <div class="flex justify-between space-x-4 mt-6">
                <button type="submit" class="w-1/2 px-4 py-2 bg-[#614C3A] hover:bg-[#723713] text-white rounded-3xl">Update</button>
                <a href="member.php" class="w-1/2 text-center px-4 py-2 bg-[#614C3A] hover:bg-[#723713] text-white rounded-3xl">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
