<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

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

// Ambil data admin
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

if (isset($_SESSION['success_msg'])) {
    echo "<script>alert('{$_SESSION['success_msg']}');</script>";
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    echo "<script>alert('{$_SESSION['error_msg']}');</script>";
    unset($_SESSION['error_msg']);
}

$id = $_SESSION['id'];
$admin = $conn->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
?>

<html>
<head>
<title>Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    body {
        background: #F5E6CA;
        font-family: 'Newsreader', serif;
    }
</style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 bg-[#F5E6CA]">
        <div class="flex items-center">
            <i class="fas fa-bars text-black text-2xl cursor-pointer"></i>
            <span class="ml-2 text-[#a15c37] text-2xl font-bold">naabelle</span>
        </div>
    </div>

    <!-- Konten Utama -->
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

            <!-- Main Content -->
            <div class="flex-1 p-8">
                <div class="bg-[#BCA48F] p-6 rounded -mt-8 min-h-screen max-h-screen overflow-auto flex flex-col">
                    <h1 class="text-2xl font-bold mb-4">LIST ADMIN</h1>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="bg-[#4A3A2E] p-4 rounded w-64 mx-auto">
    <img src="img/' . htmlspecialchars($row['image']) . '" alt="Admin Photo" class="w-32 h-32 object-cover mb-4 rounded mx-auto"/>
    <div class="text-white text-center">
        <p>Email: ' . htmlspecialchars($row['email']) . '</p>
        <p>Username: ' . htmlspecialchars($row['username']) . '</p>';
        
        // Menampilkan Status
        echo '<p>Status: <span class="' . ($row['status'] == 'Aktif' ? 'text-green-400' : 'text-red-400') . '">' . $row['status'] . '</span></p>';

        // Cek apakah admin yang sedang login
        if ($_SESSION['id'] == $row['id']) {
            echo '<p>Password: ' . htmlspecialchars($row['password']) . '</p>';
        } else {
            echo '<p>Password: ********</p>'; // Sembunyikan password untuk admin lain
        }

echo '</div>
    <div class="flex justify-center space-x-2 mt-4">';

// Hanya admin yang sedang login yang bisa edit semua data
if ($_SESSION['id'] == $row['id']) {
    echo '<button onclick="window.location.href=\'editadmin.php?id=' . $row['id'] . '\'" class="bg-blue-500 text-black p-2 rounded-full w-10 h-10 flex items-center justify-center">
        <i class="fas fa-pencil-alt"></i>
    </button>';
} else {
    // Admin lain hanya bisa edit username
    echo '<button onclick="window.location.href=\'editadmin.php?id=' . $row['id'] . '\'" class="bg-yellow-500 text-black p-2 rounded-full w-10 h-10 flex items-center justify-center">
        <i class="fas fa-user-edit"></i>
    </button>';
}

// Hanya admin dengan status "Tidak Aktif" yang bisa dihapus
if ($row['status'] == 'Tidak Aktif') {
    echo '<button onclick="deleteAdmin(' . $row['id'] . ')" class="bg-red-500 text-black p-2 rounded-full w-10 h-10 flex items-center justify-center">
        <i class="fas fa-trash"></i>
    </button>';
} else {
    echo '<button class="bg-gray-500 text-black p-2 rounded-full w-10 h-10 flex items-center justify-center cursor-not-allowed opacity-50" disabled>
        <i class="fas fa-trash"></i>
    </button>';
}

echo '</div></div>';

        }
    } else {
        echo "<p class='text-white text-center'>Tidak ada admin yang terdaftar.</p>";
    }
    ?>
</div>

                </div>
                <button onclick="window.location.href='createadmin.php'" class="fixed bottom-8 right-8 bg-[#614C3A] text-white p-4 rounded-full">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    
    <script>
function toggleProfile() {
  const modal = document.getElementById('profileModal');
  modal.classList.toggle('hidden');
}
        function deleteAdmin(id) {
            if (confirm("Apakah Anda yakin ingin menghapus admin ini?")) {
                fetch('delete_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }, 
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert(data.message);
                        location.reload(); 
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
