<?php
session_start();
$conn = new mysqli("localhost", "root", "", "kasir");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_transaksi = $_GET['id'] ?? null;
if (!$id_transaksi) {
    die("ID transaksi tidak ditemukan.");
}

$stmt = $conn->prepare("SELECT * FROM transactions WHERE id_transaksi = ?");
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) {
    die("Data transaksi tidak ditemukan.");
}

// Format nomor WA
$phone = trim($data['phone'] ?? '');
$nomor_wa = '';
if ($phone) {
    $nomor_wa = preg_replace('/^0/', '62', $phone); // Ubah awalan 0 jadi 62
}

// Siapkan pesan WhatsApp
$pesan_wa = "*NAABELLE - Struk Pembayaran*\n";
$pesan_wa .= "Order ID: #" . $data['id_transaksi'] . "\n";
$pesan_wa .= "Kasir: " . $data['admin'] . "\n";
$pesan_wa .= "-------------------------\n";
$pesan_wa .= "Item: " . $data['nama_produk'] . "\n";
$pesan_wa .= "Harga: Rp" . number_format($data['harga'], 0, ',', '.') . "\n";
$totalSebelumDiskon = $data['total_harga'] + $data['potongan'];
$persenDiskon = ($totalSebelumDiskon > 0) ? round(($data['potongan'] / $totalSebelumDiskon) * 100) : 0;
$pesan_wa .= "Diskon: {$persenDiskon}% (-Rp" . number_format($data['potongan'], 0, ',', '.') . ")\n";
$pesan_wa .= "-------------------------\n";
$pesan_wa .= "Total Belanja: Rp" . number_format($data['total_harga'], 0, ',', '.') . "\n";
$pesan_wa .= "Uang Dibayar: Rp" . number_format($data['uang_dibayar'], 0, ',', '.') . "\n";
$pesan_wa .= "Uang Kembalian: Rp" . number_format($data['kembalian'], 0, ',', '.') . "\n";
$pesan_wa .= "-------------------------\n";
$pesan_wa .= "Terima kasih telah berbelanja di NAABELLE ❤️";

$pesan_wa = rawurlencode($pesan_wa);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Struk Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
  <style>
    body {
      background: #F5E6CA;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center">

  <div class="absolute top-4 left-4">
    <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
      <i class="fas fa-arrow-left"></i>
    </a>
  </div>

  <div class="bg-white w-full max-w-xs p-4 shadow-lg text-[12px] text-black font-mono">
    <div class="text-center font-bold text-[14px] mb-1">NAABELLE</div>
    <div class="text-center text-[10px] mb-2">Babelan, Bekasi</div>
    <hr class="border border-dashed border-gray-400 mb-2" />

    <div class="mb-2">
      <div class="flex justify-between">
        <span>Order ID:</span>
        <span>#<?= $data['id_transaksi'] ?></span>
      </div>
      <div class="flex justify-between">
        <span>Kasir:</span>
        <span><?= htmlspecialchars($data['admin']) ?></span>
      </div>
    </div>

    <hr class="border border-gray-400 mb-2" />

<div class="mb-2">
  <div class="flex justify-between font-bold">
    <span>Item</span>
    <span>Total</span>
  </div>
  <div class="flex justify-between"> 
    <span><?= htmlspecialchars($data['nama_produk']) ?></span>
    <span>Rp<?= number_format($data['harga'], 0, ',', '.') ?></span>
  </div>
</div>

<hr class="border border-dashed border-gray-400 mb-2" />

<div class="mb-2">
  <div class="flex justify-between">
    <span>Total Belanja:</span>
    <span>Rp<?= number_format($data['total_harga'], 0, ',', '.') ?></span>
  </div>

  <?php
    $totalSebelumDiskon = $data['total_harga'] + $data['potongan'];
    $persenDiskon = ($totalSebelumDiskon > 0) ? round(($data['potongan'] / $totalSebelumDiskon) * 100) : 0;
  ?>
  <div class="flex justify-between text-red-600">
    <span>Diskon:</span>
    <span><?= $persenDiskon ?>% (-Rp<?= number_format($data['potongan'], 0, ',', '.') ?>)</span>
  </div>

  <div class="flex justify-between">
    <span>Jumlah Uang:</span>
    <span>Rp<?= number_format($data['uang_dibayar'], 0, ',', '.') ?></span>
  </div>
  <div class="flex justify-between font-bold">
    <span>Uang Kembalian:</span>
    <span>Rp<?= number_format($data['kembalian'], 0, ',', '.') ?></span>
  </div>
</div>

    <hr class="border border-gray-400 my-2" />

    <div class="text-center text-green-600 font-bold mb-2">PEMBAYARAN BERHASIL</div>

    <div class="text-center text-[10px] text-gray-600">
      Terima kasih telah berbelanja!
    </div>

    <?php if ($nomor_wa): ?>
      <a 
        href="https://wa.me/<?= $nomor_wa ?>?text=<?= $pesan_wa ?>" 
        target="_blank" 
        class="mt-2 bg-green-500 text-white text-[10px] rounded-full px-6 py-1 mx-auto block text-center">
        Kirim ke WhatsApp
      </a>
    <?php else: ?>
      <div class="text-red-500 text-[10px] text-center mt-2">Nomor WhatsApp tidak tersedia</div>
    <?php endif; ?>

    <a href="dashboard.php" class="mt-4 bg-[#5f4e3f] text-white text-[10px] rounded-full px-6 py-1 mx-auto block text-center">
      Selesai
    </a>
  </div>

  <?php if ($nomor_wa): ?>
  <script>
    // Redirect otomatis ke WhatsApp setelah 2 detik
    setTimeout(function () {
      window.location.href = "https://wa.me/<?= $nomor_wa ?>?text=<?= $pesan_wa ?>";
    }, 2000);
  </script>
  <?php endif; ?>
  
</body>
</html>
