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

// Ambil ID transaksi dari URL
$id_transaksi = $_GET['id'] ?? null;
if (!$id_transaksi) {
    die("ID transaksi tidak ditemukan.");
}

// Ambil data transaksi dari database
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id_transaksi = ?");
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_assoc();

if (!$transactions) {
    die("Data transaksi tidak ditemukan.");
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body>
  <!-- Tombol Back -->
  <div class="absolute top-4 left-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
  <div class="max-w-xl mx-auto mt-20 bg-[#614C3A] text-white rounded-xl shadow-lg p-8">
    <h2 class="text-center text-xl font-semibold mb-6">Invoice</h2>
    <table class="w-full text-sm">
      <tr class="border-b border-gray-300">
        <td class="py-2 font-semibold">ID Transaksi</td>
        <td class="px-4 py-2 text-right"><?= $transactions['id_transaksi'] ?></td>
      </tr>
      <tr class="border-b border-gray-300">
  <td class="py-2 font-semibold">Nama Produk</td>
  <td class="px-4 py-2 text-right">
    <?= htmlspecialchars($transactions['nama_produk'] ?? 'Produk tidak tersedia'); ?>
  </td>
</tr>
      <tr class="border-b border-gray-300">
        <td class="py-2 font-semibold">Tanggal Pembelian</td>
        <td class="px-4 py-2 text-right"><?= date('d-m-Y') ?></td>
      </tr>
      <tr class="border-b border-gray-300">
  <td class="py-2 font-semibold">Admin</td>
  <td class="px-4 py-2 text-right">
    <?= htmlspecialchars($transactions['admin'] ?? 'Admin tidak diketahui'); ?>
  </td>
</tr>
      <tr class="border-b border-gray-300">
        <td class="py-2 font-semibold">Harga</td>
        <td class="px-4 py-2 text-right">Rp <?= number_format($transactions['harga'], 0, ',', '.') ?></td>
      </tr>
<tr class="border-b border-gray-300">
    <td class="py-2 font-semibold">Diskon</td>
    <td class="px-4 py-2 text-right text-red-400 font-semibold">
        <?php
            $total = $transactions['total_harga'] + $transactions['potongan'];
            $persen = ($total > 0) ? round(($transactions['potongan'] / $total) * 100) : 0;
        ?>
        <?= $persen ?>% (-Rp <?= number_format($transactions['potongan'], 0, ',', '.') ?>)
    </td>
</tr>
      <tr class="border-b border-gray-300">
        <td class="py-2 font-semibold">Total Harga</td>
        <td class="px-4 py-2 text-right">Rp <?= number_format($transactions['total_harga'], 0, ',', '.') ?></td>
      </tr>
      <tr class="border-b border-gray-300">
        <td class="py-2 font-semibold">Uang Dibayar</td>
        <td class="px-4 py-2 text-right">Rp <?= number_format($transactions['uang_dibayar'], 0, ',', '.') ?></td>
      </tr>
      <tr>
        <td class="py-2 font-semibold">Uang Kembalian</td>
        <td class="px-4 py-2 text-right">Rp <?= number_format($transactions['kembalian'], 0, ',', '.') ?></td>
      </tr>
    </table>

    <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
    <a href="struk.php?id=<?= $transactions['id_transaksi'] ?>" class="bg-[#723713] hover:bg-[#7a4324] text-white py-2 px-6 rounded-lg text-sm shadow inline-flex items-center justify-center">
  Lihat Struk
</a>
    </div>
  </div>
</body>
</html>
