<?php
// Koneksi ke database
$host = "localhost";
$dbname = "kasir";
$username_db = "root";
$password_db = "";

// Membuat koneksi
$conn = new mysqli($host, $username_db, $password_db, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


// Ambil nilai enum jabatan dari database
$jabatan_enum = [];
$query = "SHOW COLUMNS FROM admin LIKE 'jabatan'";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    $jabatan_enum = explode("','", $matches[1]);
}

// Ambil nilai enum gender dari database
$gender_enum = [];
$query = "SHOW COLUMNS FROM admin LIKE 'gender'";
$result = $conn->query($query);
if ($result && $row = $result->fetch_assoc()) {
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    $gender_enum = explode("','", $matches[1]);
}

// Inisialisasi variabel untuk pesan
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telepon = $_POST['telepon'];
    $jabatan = $_POST['jabatan'];
    $alamat = $_POST['alamat'];
    $gender = $_POST['gender'];

    // Validasi jika password dan confirm password cocok
    if ($password !== $confirm_password) {
        $message = "<p style='color: red;'>Password dan Confirm Password tidak cocok!</p>";
    } else {
        // Mengecek apakah email sudah ada di database
        $checkEmailSql = "SELECT email FROM admin WHERE email = ?";
        $stmt = $conn->prepare($checkEmailSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<p style='color: red;'>Email sudah terdaftar!</p>";
        } else {
            // Query untuk memasukkan data ke dalam tabel admin (pastikan tabel memiliki kolom baru)
            $sql = "INSERT INTO admin (username, email, password, telepon, jabatan, alamat, gender) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $username, $email, $password, $telepon, $jabatan, $alamat, $gender);
            
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Create Account</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
        body {
            background: linear-gradient(to bottom right,rgba(141, 81, 44, 0.5), #723713);
            font-family: 'Newsreader', serif;
        }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen py-4">
  <div class="w-full max-w-4xl p-8 bg-[rgba(217,217,217,0.3)] rounded-3xl shadow-md">
    <div class="flex justify-center mb-6">
      <img alt="Logo" class="w-24 h-24" src="./assets/logo1.png"/>
    </div>
    <?php echo $message; ?>
    <form action="" method="POST">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kolom Kiri -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-bold mb-2" for="username">Username</label>
            <input name="username" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="username" type="text" required/>
          </div>
          
          <div>
            <label class="block text-sm font-bold mb-2" for="email">Email</label>
            <input name="email" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="email" type="email" required/>
          </div>
          
          <div>
            <label class="block text-sm font-bold mb-2" for="telepon">Telepon</label>
            <input name="telepon" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="telepon" type="tel" required/>
          </div>
          
<div>
  <label class="block text-sm font-bold mb-2" for="jabatan">Jabatan</label>
  <select name="jabatan" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="jabatan" required>
    <option value="">Pilih Jabatan</option>
    <?php foreach ($jabatan_enum as $jabatan): ?>
      <option value="<?= $jabatan ?>"><?= $jabatan ?></option>
    <?php endforeach; ?>
  </select>
</div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-bold mb-2" for="password">Password</label>
            <div class="relative">
              <input name="password" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="password" type="password" required/>
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword('password', 'toggleEye1')">
                <i class="fas fa-eye-slash" id="toggleEye1"></i>
              </span>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
            <div class="relative">
              <input name="confirm_password" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="confirm_password" type="password" required/>
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword('confirm_password', 'toggleEye2')">
                <i class="fas fa-eye-slash" id="toggleEye2"></i>
              </span>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold mb-2" for="gender">Gender</label>
  <select name="gender" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="gender" required>
    <option value="">Pilih Gender</option>
    <?php foreach ($gender_enum as $gender): ?>
      <option value="<?= $gender ?>"><?= $gender ?></option>
    <?php endforeach; ?>
  </select>
          </div>
          
          <div>
            <label class="block text-sm font-bold mb-2" for="alamat">Alamat</label>
            <textarea name="alamat" class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline resize-none" id="alamat" rows="3" required></textarea>
          </div>
        </div>
      </div>

      <!-- Button dan Link di bawah form -->
      <div class="mt-6 space-y-4">
        <button class="w-full px-4 py-2 font-bold text-white bg-[#723713] rounded-lg hover:bg-[#614C3A] focus:outline-none focus:shadow-outline" type="submit">Create Account</button>
        <div class="text-center">
          <a class="text-[#2731EE] hover:underline" href="login.php">Already have an account? Log In</a>
        </div>
      </div>
    </form>
  </div>

  <script>
    function togglePassword(inputId, eyeId) {
      const input = document.getElementById(inputId);
      const eye = document.getElementById(eyeId);
      if (input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
      } else {
        input.type = 'password';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
      }
    }
  </script>
</body>
</html>