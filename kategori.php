<?php
session_start();

// Koneksi ke database
$host = "localhost";
$dbname = "kasir";
$username_db = "root";
$password_db = "";

$conn = new mysqli($host, $username_db, $password_db, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT * FROM category";
$result = $conn->query($sql);

$id = $_SESSION['id'];
$admin = $conn->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="flex items-center min-h-screen">
    <div class="flex flex-col w-full">
        <div class="flex items-center p-4 bg-transparant">
        <i class="fas fa-bars text-black text-2xl cursor-pointer"></i>
            <span class="ml-2 text-[#a15c37] text-2xl font-bold">naabelle</span>
        </div>
        <div class="flex flex-1">
<!-- Sidebar -->
          <div id="sidebar" class="bg-[#614C3A] text-white w-64 min-h-screen flex flex-col justify-between p-6 transition-transform duration-300 rounded-r-xl">
              <!-- Profil Admin -->
              <div class="flex flex-col items-center text-center mb-8">
                  <img src="<?= $admin['image'] ? 'uploads/' . $admin['image'] : 'default.png' ?>" class="w-16 h-16 rounded-full mb-3 border-2 border-white object-cover" alt="Foto Profil">
                  <p class="font-semibold text-sm mb-2"><?= $admin['username'] ?></p>
                  <button onclick="toggleProfile()" class="text-xs text-gray-300 hover:underline">Lihat Profil</button>
              </div>

              <!-- Navigation Menu -->
              <ul class="flex-1 space-y-3">
                  <li>
                      <a href="dashboard.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-home mr-3"></i> Dashboard
                      </a>
                  </li>
                  <li>
                      <a href="admin.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-user mr-3"></i> Admin
                      </a>
                  </li>
                  <li>
                      <a href="member.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-users mr-3"></i> Members
                      </a>
                  </li>
                  <li>
                      <a href="kategori.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-th-large mr-3"></i> Category
                      </a>
                  </li>
                  <li>
                      <a href="produk.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-box mr-3"></i> Products
                      </a>
                  </li>
                  <li>
                      <a href="transaksi.php" class="flex items-center text-brown-light p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-receipt mr-3"></i> Transaction
                      </a>
                  </li>
                  <li>
                      <a href="laporan.php" class="flex items-center p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B]">
                          <i class="fas fa-chart-bar mr-3"></i> Report
                      </a>
                  </li>
              </ul>

              <!-- Logout Button -->
              <div class="mt-6">
                  <a href="logout.php" class="flex items-center justify-center text-white p-3 rounded-2xl bg-[#D61B1B] hover:bg-[#B91C1C] transition duration-300">
                      <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                  </a>
              </div>
          </div>

<!-- Modal Profil Premium -->
<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 w-[90%] max-w-xl relative border border-[#d4af37]">

    <!-- Tombol Close -->
    <button onclick="toggleProfile()" class="absolute top-4 right-5 text-gray-500 hover:text-[#d4af37] text-xl">
      <i class="fas fa-times-circle"></i>
    </button>

    <!-- Judul -->
    <h2 class="text-2xl font-semibold text-center text-[#5b4636] mb-6 tracking-wide">✨ Profile Information ✨</h2>

    <!-- Foto Profil -->
    <div class="flex justify-center mb-6">
      <img src="<?= $admin['image'] ? 'img/' . $admin['image'] : 'default.png' ?>" 
           class="w-28 h-28 rounded-full border-[4px] border-[#d4af37] object-cover shadow-lg" 
           alt="Foto Admin">
    </div>

    <!-- Data Diri -->
    <div class="space-y-3 text-[#3e3e3e] text-[15px]">
      <div class="flex justify-between">
        <span class="font-semibold">Nama Lengkap:</span>
        <span><?= htmlspecialchars($admin['username']) ?></span>
      </div>
      <div class="flex justify-between">
        <span class="font-semibold">Email:</span>
        <span><?= htmlspecialchars($admin['email']) ?></span>
      </div>
      <div class="flex justify-between">
        <span class="font-semibold">Telepon:</span>
        <span><?= htmlspecialchars($admin['telepon'] ?: '-') ?></span>
      </div>
      <div class="flex justify-between">
        <span class="font-semibold">Jabatan:</span>
        <span><?= htmlspecialchars($admin['jabatan'] ?: '-') ?></span>
      </div>
      <div class="flex justify-between">
        <span class="font-semibold">Alamat:</span>
        <span><?= htmlspecialchars($admin['alamat'] ?: '-') ?></span>
      </div>
      <div class="flex justify-between">
        <span class="font-semibold">Jenis Kelamin:</span>
        <span><?= htmlspecialchars($admin['gender'] ?: '-') ?></span>
      </div>
      <div class="flex justify-between items-center">
  <span class="font-semibold">Password:</span>
  <span class="flex items-center gap-2">
    <span id="passwordDots">●●●●●●●●</span>
  </span>
</div>
    </div>

    <!-- Tombol Edit -->
    <div class="mt-6 text-center">
      <a href="editadmin.php?id=<?= $admin['id'] ?>" class="bg-[#d4af37] hover:bg-[#b9972e] text-white font-medium px-6 py-2 rounded-full transition duration-300 shadow-md">
        Edit Profile
      </a>
    </div>
  </div>
</div>
            <!-- Content -->
            <div class="flex-1 p-12">
                <h2 class="text-2xl font-bold mb-4 pt-5 -mt-16">KATEGORI</h2>
                <div class="grid grid-cols-2 gap-6 justify-items-center">

                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="relative bg-[#BCA48F] rounded-lg w-64 h-64 overflow-hidden shadow-md group">
                                <img src="assets/' . htmlspecialchars($row['image']) . '" alt="Category Image" /> class="w-32 h-32 object-cover mb-4 rounded mx-auto"/>
                                
                                <!-- Overlay Buttons -->
                                <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition duration-300">
                                    <a href="editkategori.php?id=' . $row['id'] . '" class="bg-blue-500 w-10 h-10 flex items-center justify-center rounded-full hover:bg-blue-600">
                                        <i class="fas fa-pencil-alt text-[#4A3A2E]"></i>
                                    </a>
                                    <a href="hapuskategori.php?id=' . $row['id'] . '" class="bg-red-500 w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-600">
                                        <i class="fas fa-trash text-[#4A3A2E]"></i>
                                    </a>
                                </div>

                                <!-- Category Name -->
                                <div class="absolute bottom-0 left-0 right-0 bg-transparent text-black/60 text-center py-2 font-semibold">
                                    ' . htmlspecialchars($row['category']) . '
                                </div>
                            </div>';
                        }
                    }
                    // Tombol tambah kategori SELALU ditampilkan, baik ada data atau tidak
        echo '
        <a href="tambahkategori.php" class="w-64 h-64 bg-[#D9C3AC] rounded-lg flex items-center justify-center text-5xl text-[#614C3A] hover:bg-[#e4d3be] transition duration-300">
            +
        </a>';
                    ?>
                </div>
            </div>
        </div>
    </div>
<script>
    function toggleProfile() {
  const modal = document.getElementById('profileModal');
  modal.classList.toggle('hidden');
}
</script>
</body>
</html>
