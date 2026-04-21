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

// Nonaktifkan member yang tidak transaksi dalam 1 menit
$limit = date('Y-m-d H:i:s', strtotime('-1 minute'));
$conn->query("UPDATE member SET status='non-active' WHERE last_transaction < '$limit' AND status='active'");

// Cek apakah ini request AJAX untuk refresh status tanpa reload
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $sql_ajax = "SELECT id, status FROM member";
    $result_ajax = $conn->query($sql_ajax);
    $members_status = [];
    while ($row_ajax = $result_ajax->fetch_assoc()) {
        $members_status[] = $row_ajax;
    }
    header('Content-Type: application/json');
    echo json_encode($members_status);
    exit;
}

// Ambil data member untuk ditampilkan di halaman utama
$sql = "SELECT * FROM member";
$result = $conn->query($sql);

$id = $_SESSION['id'];
$admin = $conn->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Members</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
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
                <h2 class="text-2xl font-bold mb-4 pt-5 -mt-16">LIST MEMBER</h2>
                <div class="bg-[#BCA48F] p-8 rounded-lg shadow-lg overflow-x-auto">
                    <table class="w-full text-left" id="memberTable">
                        <thead class="bg-[#d3bba3] text-black">
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Phone</th>
                                <th class="p-3">Transaction Amount</th>
                                <th class="p-3">Point</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='border-b'>";
                                    echo "<td class='p-3'>" . $row['id'] . "</td>";
                                    echo "<td class='p-3'>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td class='p-3'>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td class='p-3'>" . htmlspecialchars($row['phone']) . "</td>";
                                    echo "<td class='p-3'>" . $row['transaction_amount'] . "</td>";
                                    echo "<td class='p-3'>" . $row['point'] . "</td>";

                                    // Status kolom dengan id unik untuk update AJAX
                                    echo "<td class='p-3' id='status-" . $row['id'] . "'>";
                                    if ($row['status'] == 'active') {
                                        echo "<span class='px-3 py-1 rounded-full bg-green-500 text-white'>Active</span>";
                                    } else {
                                        echo "<span class='px-3 py-1 rounded-full bg-red-500 text-white'>Non-Active</span>";
                                    }
                                    echo "</td>";

                                    echo "<td class='p-3 flex space-x-2'>    
    <button onclick=\"window.location.href='editmember.php?id=" . $row['id'] . "'\" class='bg-blue-500 text-black p-1 rounded-full w-8 h-8 flex items-center justify-center'>
        <i class='fas fa-pencil-alt text-xs'></i>
    </button>";

if (strtolower($row['status']) == 'non-active') {
    echo "<button onclick=\"delete_member(" . $row['id'] . ")\" class='bg-red-500 text-black p-1 rounded-full w-8 h-8 flex items-center justify-center'>
            <i class='fas fa-trash text-xs'></i>
        </button>";
} else {
    echo "<button class='bg-gray-400 text-black p-1 rounded-full w-8 h-8 flex items-center justify-center cursor-not-allowed opacity-50' disabled>
            <i class='fas fa-trash text-xs'></i>
        </button>";
}

echo "</td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center p-3'>Tidak ada data member</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="flex justify-center mt-8">
                        <button onclick="window.location.href='createmember.php'" class="bg-[#614C3A] hover:bg-[#723713] text-white py-3 px-10 rounded-3xl">ADD Member</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function toggleProfile() {
  const modal = document.getElementById('profileModal');
  modal.classList.toggle('hidden');
}

    function delete_member(id) {
        if (confirm("Yakin ingin menghapus member ini?")) {
            fetch('delete_member.php?id=' + id, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Menampilkan pesan sukses atau error
                location.reload(); // Refresh halaman setelah menghapus
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Fungsi untuk refresh status member tanpa reload halaman
    function refreshMemberStatus() {
        fetch('member.php?ajax=1')
        .then(response => response.json())
        .then(data => {
            data.forEach(member => {
                const statusCell = document.getElementById('status-' + member.id);
                if (statusCell) {
                    // Ubah isi status dan warna badge sesuai status terbaru
                    if (member.status === 'active') {
                        statusCell.innerHTML = "<span class='px-3 py-1 rounded-full bg-green-500 text-white'>active</span>";
                    } else {
                        statusCell.innerHTML = "<span class='px-3 py-1 rounded-full bg-red-500 text-white'>non-Active</span>";
                    }
                }
            });
        })
        .catch(error => console.error('Error refreshing member status:', error));
    }

    // Refresh status setiap 10 detik (sesuaikan sesuai kebutuhan)
    setInterval(refreshMemberStatus, 10000);
</script>
</body>
</html>
