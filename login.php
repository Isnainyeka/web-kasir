<?php
session_start();

if (isset($_SESSION['email'])) {
    header('Location: admin.php');
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasir";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Query untuk mencari admin dengan email yang sesuai
    $query = "SELECT id, username, email, password FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['id'] = $user['id'];  // Simpan ID admin di session
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $user['username'];  // Simpan username admin di session

            // Nonaktifkan semua admin dulu
            $conn->query("UPDATE admin SET status = 'Tidak Aktif'");

            // Ubah status admin ke Aktif
            $updateStatus = "UPDATE admin SET status = 'Aktif' WHERE id = ?";
            $updateStmt = $conn->prepare($updateStatus);
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=password");
            exit();
        }
    } else {
        header("Location: login.php?error=email");
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
        body {
            background: linear-gradient(to bottom right,rgba(141, 81, 44, 0.5), #723713);
            font-family: 'Newsreader', serif;
        }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-sm p-8 bg-[rgba(217,217,217,0.3)] rounded-2xl shadow-md">
    <div class="flex justify-center mb-6">
      <img alt="Logo" class="w-24 h-24" src="./assets/logo1.png"/>
    </div>

    <?php if (isset($_GET['error'])): ?>
  <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <strong class="font-bold">Login gagal!</strong>
    <span class="block sm:inline">
      <?php
        if ($_GET['error'] == 'password') {
          echo "Kata sandi yang kamu masukkan salah. Silakan coba lagi.";
        } elseif ($_GET['error'] == 'email') {
          echo "Email tidak ditemukan. Pastikan email yang kamu masukkan sudah benar dan terdaftar.";
        }
      ?>
    </span>
  </div>
<?php endif; ?>

    <form action="login.php" method="POST">
      <div class="mb-4">
        <label class="block text-sm font-bold mb-2" for="email">Email</label>
        <input class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="email" name="email" type="email" required/>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-bold mb-2" for="password">Password</label>
        <div class="relative">
          <input class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="password" name="password" type="password" required/>
          <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword()">
            <i class="fas fa-eye-slash" id="toggleEye"></i>
          </span>
        </div>
      </div>
      <div class="mb-4 text-right">
        <a class="text-[#2731EE] hover:underline" href="forgetpw.php">forgot password?</a>
      </div>
      <div class="mb-4">
        <button class="w-full px-4 py-2 font-bold text-white bg-[#723713] rounded-lg hover:bg-[#614C3A] focus:outline-none focus:shadow-outline" type="submit">Login</button>
      </div>
      <div class="text-center">
        <a class="text-[#2731EE] hover:underline" href="regis.php">Don't have an account? Create account</a>
      </div>
    </form>
  </div>

  <script>
    function togglePassword() {
      const password = document.getElementById('password');
      const toggleEye = document.getElementById('toggleEye');
      if (password.type === 'password') {
        password.type = 'text';
        toggleEye.classList.remove('fa-eye-slash');
        toggleEye.classList.add('fa-eye');
      } else {
        password.type = 'password';
        toggleEye.classList.remove('fa-eye');
        toggleEye.classList.add('fa-eye-slash');
      }
    }
  </script>
</body>
</html>
