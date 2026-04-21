<?php
session_start();
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

// Cek apakah token tersedia di URL
if (!isset($_GET['token'])) {
    die("Token tidak valid!");
}

$token = $_GET['token'];

// Cek token dalam database
$stmt = $conn->prepare("SELECT email, expires FROM password_reset WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$reset = $result->fetch_assoc();

// Jika token tidak ditemukan atau sudah kadaluarsa
if (!$reset || strtotime($reset['expires']) < time()) {
    die("Token telah kadaluarsa atau tidak valid!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
    <form action="updatepw.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="mb-4">
          <label class="block text-sm font-bold mb-2" for="password">Password</label>
          <div class="relative">
            <input class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="password" type="password" name="new_password" required/>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword('password', 'toggleEye1')">
            <i class="fas fa-eye-slash" id="toggleEye1"></i>
          </span>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-bold mb-2" for="confirmpassword">Confirm Password</label>
          <div class="relative">
            <input class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="confirmpassword" type="password" name="confirm_password" required/>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword('confirm_password', 'toggleEye2')">
            <i class="fas fa-eye-slash" id="toggleEye2"></i>
          </span>
          </div>
        </div>
        <button class="w-full px-4 py-2 font-bold text-white bg-[#723713] rounded-lg hover:bg-[#614C3A] focus:outline-none focus:shadow-outline" type="submit" name="reset_password">Update</button>
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
