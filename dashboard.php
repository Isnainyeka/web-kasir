<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$koneksi = new mysqli("localhost", "root", "", "kasir");

$jumlah_produk = $koneksi->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$jumlah_kategori = $koneksi->query("SELECT COUNT(*) as total FROM category")->fetch_assoc()['total'];
$jumlah_member = $koneksi->query("SELECT COUNT(*) as total FROM member")->fetch_assoc()['total'];
$penjualan_query = $koneksi->query("SELECT SUM(total_harga) as total FROM transactions");
$penjualan = $penjualan_query->fetch_assoc()['total'] ?? 0;

$penjualan_per_hari = [];
$result = $koneksi->query("SELECT DAY(tanggal_beli) as hari, SUM(total_harga) as total 
                          FROM transactions 
                          WHERE MONTH(tanggal_beli) = MONTH(CURDATE()) 
                          AND YEAR(tanggal_beli) = YEAR(CURDATE()) 
                          GROUP BY DAY(tanggal_beli)");

while ($row = $result->fetch_assoc()) {
    $penjualan_per_hari[intval($row['hari'])] = intval($row['total']);
}

$produk_counter = [];

$query = $koneksi->query("SELECT nama_produk FROM transactions WHERE nama_produk IS NOT NULL AND nama_produk != ''");

while ($row = $query->fetch_assoc()) {
    $produk_list = explode(',', $row['nama_produk']);

    foreach ($produk_list as $produk_raw) {
        // Ambil nama dan jumlah (misal: ' hijab instan (milo) (2x)')
        if (preg_match('/^(.*)\((\d+)x\)$/', trim($produk_raw), $match)) {
            $nama = trim($match[1]); // 'hijab instan (milo)'
            $jumlah = intval($match[2]); // 2
        } else {
            // Jika tidak sesuai pola, anggap 1
            $nama = trim(preg_replace('/\(\d+x\)$/', '', $produk_raw));
            $jumlah = 1;
        }

        // Tambahkan ke counter
        if (!isset($produk_counter[$nama])) {
            $produk_counter[$nama] = 0;
        }
        $produk_counter[$nama] += $jumlah;
    }
}

// Urutkan dari yang paling banyak
arsort($produk_counter);

// Ambil 5 teratas
$labels = array_slice(array_keys($produk_counter), 0, 5);
$data = array_slice(array_values($produk_counter), 0, 5);

$id = $_SESSION['id'];
$admin = $koneksi->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
?>

<html>
<head>
<title>Dashboard naabelle</title>
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
            <div class="flex-1 p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 -mt-8 justify-center">
    <!-- Produk -->
    <div class="bg-[#BCA48F] text-[#614C3A] rounded-xl p-6 text-center flex flex-col items-center gap-y-2 h-36 shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl cursor-pointer">
        <i class="fas fa-box fa-2x text-[#614C3A] mb-1"></i>
        <p class="text-sm font-medium uppercase tracking-wide">Produk</p>
        <p class="text-2xl font-bold"><?= $jumlah_produk ?></p>
    </div>
    
    <!-- Kategori -->
    <div class="bg-[#BCA48F] text-[#614C3A] rounded-xl p-6 text-center flex flex-col items-center gap-y-2 h-36 shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl cursor-pointer">
        <i class="fas fa-th-large fa-2x text-[#614C3A] mb-1"></i>
        <p class="text-sm font-medium uppercase tracking-wide">Kategori</p>
        <p class="text-2xl font-bold"><?= $jumlah_kategori ?></p>
    </div>

    <!-- Member -->
    <div class="bg-[#BCA48F] text-[#614C3A] rounded-xl p-6 text-center flex flex-col items-center gap-y-2 h-36 shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl cursor-pointer">
        <i class="fas fa-users fa-2x text-[#614C3A] mb-1"></i>
        <p class="text-sm font-medium uppercase tracking-wide">Member</p>
        <p class="text-2xl font-bold"><?= $jumlah_member ?></p>
    </div>

</div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="text-center font-bold text-lg mb-4">Data Penjualan</h2>
                        <canvas id="lineChart"></canvas>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <canvas id="pieChart"></canvas>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleProfile() {
  const modal = document.getElementById('profileModal');
  modal.classList.toggle('hidden');
}

    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: [<?php for ($i=1; $i<=date('t'); $i++) echo "'$i',"; ?>],
            datasets: [{
                label: 'Penjualan Harian',
                data: [
                    <?php
                        for ($i=1; $i<=date('t'); $i++) {
                            echo isset($penjualan_per_hari[$i]) ? $penjualan_per_hari[$i] . "," : "0,";
                        }
                    ?>
                ],
                borderColor: '#723713',
                borderWidth: 2,
                fill: false
            }]
        }
    });

    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($data) ?>,
                backgroundColor: ['#8B4513', '#D2691E', '#A0522D', '#CD853F', '#DEB887']
            }]
        }
    });
</script>

</body>
</html>
