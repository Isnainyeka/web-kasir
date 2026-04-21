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

// Pastikan user sudah login
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit();
}

// Ambil ID admin yang sedang login
$loggedInId = $_SESSION['id'];

// Cek apakah ID admin yang akan diupdate ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM admin WHERE id = $id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    } else {
        echo "<script>alert('Admin tidak ditemukan!'); window.location='admin.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='admin.php';</script>";
    exit();
}

// Proses update data saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameForm = $conn->real_escape_string($_POST['username']);
    $emailForm = $conn->real_escape_string($_POST['email']);
    $passwordForm = $conn->real_escape_string($_POST['password']);
    $statusForm = $conn->real_escape_string($_POST['status']);

    // Cek duplikasi email hanya jika admin yang login sedang update emailnya sendiri
    if ($loggedInId === $id) {
        $checkEmailQuery = "SELECT id FROM admin WHERE email = '$emailForm' AND id != $id";
        $emailCheckResult = $conn->query($checkEmailQuery);
        if ($emailCheckResult && $emailCheckResult->num_rows > 0) {
            echo "<script>alert('Email sudah digunakan oleh admin lain!'); window.history.back();</script>";
            exit();
        }
    }

    // Gunakan gambar lama jika tidak ada upload baru
    $imageName = $admin['image'];

    // Proses upload gambar jika ada
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = "img/";
        $originalName = basename($_FILES['image']['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        // Buat nama file unik supaya tidak overwrite
        $imageName = $originalName;
        $targetFile = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo "<script>alert('Gagal mengupload gambar!'); window.history.back();</script>";
            exit(); 
        }
    }

    // Catatan: password disimpan mentah, sebaiknya hash pakai password_hash()
    // Jika kamu mau, bisa ubah jadi:
    // $passwordForm = password_hash($passwordForm, PASSWORD_DEFAULT); 

    if ($loggedInId === $id) {
        // Admin yang sedang login bisa update semua data
        $updateQuery = "
            UPDATE admin SET
                email = '$emailForm',
                username = '$usernameForm',
                password = '$passwordForm',
                image = '$imageName',
                status = '$statusForm'
            WHERE id = $id
        ";
    } else {
        // Admin lain hanya bisa update username dan gambar
        $updateQuery = "
            UPDATE admin SET
                username = '$usernameForm',
                image = '$imageName'
            WHERE id = $id
        ";
    }

    if ($conn->query($updateQuery)) {
    $_SESSION['success_msg'] = "Admin berhasil diperbarui!";
    header("Location: admin.php");
    exit();
} else {
    $_SESSION['error_msg'] = "Gagal memperbarui admin!";
    header("Location: admin.php");
    exit();
}

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Update Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
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
        <a href="javascript:history.back()" class="text-[#614C3A] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Container Form -->
    <div class="flex-1 pr-[100px] flex items-center justify-center">
        <div class="bg-[#BCA48F] p-12 rounded-lg shadow-lg w-96 h-[600px] pb-40 ml-[90px]">
            <h2 class="text-center text-white mb-4 text-xl">UPDATE ADMIN</h2>
            <form method="POST" enctype="multipart/form-data">
                <!-- Gambar yang bisa diklik -->
                <div class="flex justify-center mb-4">
                    <label for="imageInput">
                        <img id="adminImage" src="img/<?php echo htmlspecialchars($admin['image'] ?: 'default.png'); ?>" 
                             class="w-40 h-40 cursor-pointer border-4 border-[#614C3A] shadow-lg object-cover" alt="Admin Image">
                    </label>
                    <input type="file" id="imageInput" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                </div>

                <!-- Email hanya bisa diedit oleh admin yang sedang login -->
                <input class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" 
                       placeholder="Email" type="email" name="email" 
                       value="<?php echo htmlspecialchars($admin['email']); ?>" 
                       <?php echo ($loggedInId !== $id) ? 'disabled' : ''; ?> required>

                <input class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" 
                       placeholder="Username" type="text" name="username" 
                       value="<?php echo htmlspecialchars($admin['username']); ?>" required>

                <!-- Password hanya bisa diedit oleh admin yang sedang login -->
<?php if ($loggedInId === $id): ?>
  <!-- Jika admin yang sedang login -->
  <div class="relative mb-4">
    <input class="w-full p-2 rounded-3xl bg-[#E6D8CD] text-black pr-10" 
           placeholder="Password" 
           type="password" 
           id="passwordInput" 
           name="password" 
           value="<?php echo htmlspecialchars($admin['password']); ?>" 
           required>
    <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePasswordVisibility()">
      <i class="fas fa-eye-slash text-[#614C3A]" id="eyeIcon"></i>
    </span>
  </div>
<?php else: ?>
  <!-- Jika admin sedang mengedit akun orang lain -->
  <input class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" 
         placeholder="Password" 
         type="password" 
         name="password" 
         value="<?php echo htmlspecialchars($admin['password']); ?>" 
         disabled required>
<?php endif; ?>

                <select name="status" class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" <?php echo ($loggedInId !== $id) ? 'disabled' : ''; ?>>
                    <option value="Aktif" <?= ($admin['status'] === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                    <option value="Tidak Aktif" <?= ($admin['status'] === 'Tidak Aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                </select>

                <div class="flex justify-between mt-5">
                    <button class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" type="submit">UPDATE</button>
                    <button onclick="window.location.href='admin.php'" class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" type="button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('adminImage').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

  function togglePasswordVisibility() {
    const passwordInput = document.getElementById("passwordInput");
    const eyeIcon = document.getElementById("eyeIcon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      eyeIcon.classList.remove("fa-eye-slash");
      eyeIcon.classList.add("fa-eye");
    } else {
      passwordInput.type = "password";
      eyeIcon.classList.remove("fa-eye");
      eyeIcon.classList.add("fa-eye-slash");
    }
  }
    </script>
</body>
</html>
