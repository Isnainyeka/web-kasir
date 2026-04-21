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

// Ambil data produk dari database
$sql = "SELECT id, product_name, selling_price, qty, image FROM products";
$result = $conn->query($sql);

$id = $_SESSION['id'];
$admin = $conn->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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
            <div class="flex items-center gap-2 ml-auto">
                <h2 onclick="window.location.href='produk_perkategori.php'" class="text-lg font-medium hover:underline">Category</h2>
                <button onclick="window.location.href='addproduk.php'" class="bg-[#723713] text-white p-1 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fas fa-plus text-xs"></i>
                </button>
            </div>
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
            <div class="flex-1 p-10">
                <div class="grid grid-cols-3 gap-10 -mt-10"> 
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="bg-[#BCA48F] p-4 rounded w-64 mx-auto">
                            <img src="./assets/produk/<?= htmlspecialchars($row['image']) ?>" class="w-full h-48 object-cover rounded-lg mb-4"/>
                            <h3 class="text-center text-black font-bold"><?= htmlspecialchars($row['product_name']) ?></h3>
                            <p class="text-center text-black">Rp<?= number_format($row['selling_price'], 0, ',', '.') ?></p>
                            <p class="text-center text-blue-700 font-bold">Stok: <?= htmlspecialchars($row['qty']) ?></p>
                             <!-- Tombol Edit dan Hapus Produk -->
                            <div class="flex justify-center gap-4 mt-4">
                            <!-- Tombol Edit Produk -->
                            <a href="editproduk.php?id=<?php echo $row['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <!-- Tombol Hapus Produk -->
                            <?php if ($row['qty'] == 0): ?>
                                <!-- Bisa dihapus -->
                                <a href="deleteproduk.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <!-- Tidak bisa dihapus -->
                                <button class="bg-gray-500 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center cursor-not-allowed opacity-50" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                            <!-- Tombol Detail Produk -->
                            <button onclick="window.location.href='detailproduk.php?id=<?= $row['id'] ?>'" class="bg-[#614C3A] hover:bg-[#723713] text-white p-2 rounded-full w-10 h-10 flex items-center justify-center">
                                <i class="fas fa-eye"></i> <!-- Ikon Mata untuk Detail -->
                            </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
