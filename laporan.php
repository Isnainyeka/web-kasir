<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "kasir");
if ($koneksi->connect_error) die("Koneksi gagal: " . $koneksi->connect_error);

$filter = $_GET['filter'] ?? 'tahunan';
$tanggal = $_GET['tanggal'] ?? null;

$labels = [];
$totals_modal = [];
$totals_keuntungan = [];
$totals_penjualan = [];

if ($tanggal) {
    $tgl = new DateTime($tanggal);
    $tahun = (int)$tgl->format('Y');
    $bulan = (int)$tgl->format('m');
    $minggu_ke = (int)$tgl->format('W');
    $hari = $tgl->format('Y-m-d');
}

// Function to get financial data for transactions
function getFinancialData($koneksi, $where_clause) {
    $query = "
        SELECT 
            t.tanggal_beli,
            t.nama_produk,
            t.total_harga,
            p.starting_price,
            p.margin
        FROM transactions t
        LEFT JOIN products p ON p.product_name LIKE CONCAT('%', SUBSTRING_INDEX(TRIM(SUBSTRING_INDEX(t.nama_produk, '(', 1)), ' ', -1), '%')
        WHERE $where_clause
        ORDER BY t.tanggal_beli
    ";
    
    $result = $koneksi->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        // Extract quantity from product name (assuming format like "product name (qty x)")
        preg_match('/\((\d+)x\)/', $row['nama_produk'], $matches);
        $qty = isset($matches[1]) ? (int)$matches[1] : 1;
        
$modal = ($row['starting_price'] ?? 0) * $qty;
$penjualan = $row['total_harga'] ?? 0;
$keuntungan = $penjualan - $modal;
        
        $date_key = $row['tanggal_beli'];
        if (!isset($data[$date_key])) {
            $data[$date_key] = ['modal' => 0, 'keuntungan' => 0, 'penjualan' => 0];
        }
        
        $data[$date_key]['modal'] += $modal;
        $data[$date_key]['keuntungan'] += $keuntungan;
        $data[$date_key]['penjualan'] += $penjualan;
    }
    
    return $data;
}

if ($filter == 'harian' && isset($hari)) {
    $labels[] = $tgl->format('d M Y');
    $financial_data = getFinancialData($koneksi, "tanggal_beli = '$hari'");
    
    $totals_modal[] = $financial_data[$hari]['modal'] ?? 0;
    $totals_keuntungan[] = $financial_data[$hari]['keuntungan'] ?? 0;
    $totals_penjualan[] = $financial_data[$hari]['penjualan'] ?? 0;
}
elseif ($filter == 'mingguan' && isset($minggu_ke)) {
    $start = new DateTime();
    $start->setISODate($tahun, $minggu_ke);
    $end = clone $start;
    $end->modify('+6 days');

    $period = new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day'));
    foreach ($period as $day) {
        $labels[] = $day->format('d M');
        $totals_modal[] = 0;
        $totals_keuntungan[] = 0;
        $totals_penjualan[] = 0;
    }

    $financial_data = getFinancialData($koneksi, "tanggal_beli BETWEEN '{$start->format('Y-m-d')}' AND '{$end->format('Y-m-d')}'");
    
    foreach ($financial_data as $date => $data) {
        $key = array_search((new DateTime($date))->format('d M'), $labels);
        if ($key !== false) {
            $totals_modal[$key] = $data['modal'];
            $totals_keuntungan[$key] = $data['keuntungan'];
            $totals_penjualan[$key] = $data['penjualan'];
        }
    }
}
elseif ($filter == 'bulanan' && isset($bulan)) {
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    for ($i = 1; $i <= $days_in_month; $i++) {
        $tgl_str = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
        $labels[] = date('d M', strtotime($tgl_str));
        $totals_modal[] = 0;
        $totals_keuntungan[] = 0;
        $totals_penjualan[] = 0;
    }

    $financial_data = getFinancialData($koneksi, "YEAR(tanggal_beli) = $tahun AND MONTH(tanggal_beli) = $bulan");
    
    foreach ($financial_data as $date => $data) {
        $key = array_search((new DateTime($date))->format('d M'), $labels);
        if ($key !== false) {
            $totals_modal[$key] = $data['modal'];
            $totals_keuntungan[$key] = $data['keuntungan'];
            $totals_penjualan[$key] = $data['penjualan'];
        }
    }
}
elseif ($filter == 'tahunan' && isset($tahun)) {
    $bulan_nama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $labels = $bulan_nama;
    $totals_modal = array_fill(0, 12, 0);
    $totals_keuntungan = array_fill(0, 12, 0);
    $totals_penjualan = array_fill(0, 12, 0);

    $financial_data = getFinancialData($koneksi, "YEAR(tanggal_beli) = $tahun");
    
    foreach ($financial_data as $date => $data) {
        $month_index = (int)date('m', strtotime($date)) - 1;
        $totals_modal[$month_index] += $data['modal'];
        $totals_keuntungan[$month_index] += $data['keuntungan'];
        $totals_penjualan[$month_index] += $data['penjualan'];
    }
}
else {
    // Default: group by year
    $financial_data = getFinancialData($koneksi, "1=1");
    $yearly_data = [];
    
    foreach ($financial_data as $date => $data) {
        $year = date('Y', strtotime($date));
        if (!isset($yearly_data[$year])) {
            $yearly_data[$year] = ['modal' => 0, 'keuntungan' => 0, 'penjualan' => 0];
        }
        $yearly_data[$year]['modal'] += $data['modal'];
        $yearly_data[$year]['keuntungan'] += $data['keuntungan'];
        $yearly_data[$year]['penjualan'] += $data['penjualan'];
    }
    
    foreach ($yearly_data as $year => $data) {
        $labels[] = $year;
        $totals_modal[] = $data['modal'];
        $totals_keuntungan[] = $data['keuntungan'];
        $totals_penjualan[] = $data['penjualan'];
    }
}

$id = $_SESSION['id'];
$admin = $koneksi->query("SELECT * FROM admin WHERE id = $id")->fetch_assoc();
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Keuangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="min-h-screen">
  <div class="flex flex-col w-full">
    <!-- Navbar -->
    <div class="flex items-center p-4 bg-transparent">
      <i class="fas fa-bars text-black text-2xl cursor-pointer"></i>
      <span class="ml-2 text-[#a15c37] text-2xl font-bold">naabelle</span>
    </div>

      <div class="flex flex-row min-h-screen">
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
                      <a href="laporan.php" class="flex items-center p-3 rounded w-full transition duration-300 hover:bg-[#A48D7B] bg-[#A48D7B]">
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
<div class="flex-1 p-12">
<div class="bg-[#BCA48F] p-8 rounded-lg shadow-lg overflow-x-auto -mt-12">
  <h1 class="text-4xl font-bold text-[#6b4226] mb-8 text-center">Laporan Keuangan</h1>
  
  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-green-100 text-sm">Total Modal</p>
          <p class="text-2xl font-bold" id="totalModal">Rp <?= number_format(array_sum($totals_modal), 0, ',', '.') ?></p>
        </div>
        <i class="fas fa-coins text-3xl text-green-200"></i>
      </div>
    </div>
    
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-blue-100 text-sm">Total Keuntungan</p>
          <p class="text-2xl font-bold" id="totalKeuntungan">Rp <?= number_format(array_sum($totals_keuntungan), 0, ',', '.') ?></p>
        </div>
        <i class="fas fa-chart-line text-3xl text-blue-200"></i>
      </div>
    </div>
    
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-purple-100 text-sm">Total Penjualan</p>
          <p class="text-2xl font-bold" id="totalPenjualan">Rp <?= number_format(array_sum($totals_penjualan), 0, ',', '.') ?></p>
        </div>
        <i class="fas fa-money-bill-wave text-3xl text-purple-200"></i>
      </div>
    </div>
  </div>

    <!-- Filter Form -->
    <form method="GET" class="flex flex-col lg:flex-row items-center justify-between gap-6 mb-8 bg-[#e8d8c3] p-6 rounded-2xl shadow-lg">
      <div class="flex items-center gap-2 w-full lg:w-auto">
        <label for="filter" class="text-sm font-semibold text-gray-700">Filter Waktu:</label>
        <select id="filter" name="filter" class="rounded border border-gray-300 p-2 w-full lg:w-auto">
          <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian</option>
          <option value="mingguan" <?= $filter == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
          <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
          <option value="tahunan" <?= $filter == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
        </select>
      </div>

      <div class="flex items-center gap-2 w-full lg:w-auto">
        <label for="tanggal" class="text-sm font-semibold text-gray-700">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" value="<?= $tanggal ?>" class="rounded border border-gray-300 p-2 w-full lg:w-auto" />
      </div>

      <button type="submit" class="bg-[#6b4226] text-white px-8 py-2 rounded-lg hover:bg-[#5a3620] transition">
        Tampilkan
      </button>
    </form>

    <!-- Chart Container -->
    <div id="chartContainer" class="bg-[#f0dfc8] p-6 rounded-2xl shadow-xl mb-6">
      <canvas id="laporanChart" height="120"></canvas>
    </div>
    <!-- Diagram Lingkaran -->
<div id="pieChartContainer" class="bg-[#f0dfc8] p-2 rounded-2xl shadow-xl mb-6">
  <canvas id="pieChart" height="500" style="max-width: 500px; max-height: 500px; margin: auto;"></canvas>
</div>

<!-- Tabel Keuangan (Disembunyikan untuk PDF) -->
<div id="hiddenTable" style="display:none; padding: 40px; background-color: white; font-family: Arial, sans-serif;">
  <h2 style="text-align:center; margin-bottom: 20px; font-size: 18px; font-weight: bold;">Laporan Keuangan</h2>
  <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
    <thead>
      <tr style="background-color: #f0dfc8; color: #000;">
        <th style="border: 1px solid #000; padding: 6px 10px; width: 5%;">No</th>
        <th style="border: 1px solid #000; padding: 6px 10px; width: 25%;">Periode</th>
        <th style="border: 1px solid #000; padding: 6px 10px; width: 25%;">Total Modal</th>
        <th style="border: 1px solid #000; padding: 6px 10px; width: 25%;">Total Keuntungan</th>
        <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Total Penjualan</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($labels as $i => $label): ?>
        <tr>
          <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;"><?= $i + 1 ?></td>
          <td style="border: 1px solid #000; padding: 6px 10px;"><?= $label ?></td>
          <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format($totals_modal[$i], 0, ',', '.') ?></td>
          <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format($totals_keuntungan[$i], 0, ',', '.') ?></td>
          <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format($totals_penjualan[$i], 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr style="background-color: #e8d8c3; font-weight: bold;">
        <td colspan="2" style="border: 1px solid #000; padding: 6px 10px; text-align: center;">TOTAL</td>
        <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format(array_sum($totals_modal), 0, ',', '.') ?></td>
        <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format(array_sum($totals_keuntungan), 0, ',', '.') ?></td>
        <td style="border: 1px solid #000; padding: 6px 10px;">Rp <?= number_format(array_sum($totals_penjualan), 0, ',', '.') ?></td>
      </tr>
    </tfoot>
  </table>
</div>

    <!-- Tombol Cetak -->
    <div class="flex justify-center mt-4">
      <button id="btnCetakPDF" class="bg-[#6b4226] text-white px-6 py-2 rounded-lg hover:bg-[#5a3620] transition">
        Download PDF
      </button>
    </div>
  </div>
</div>
</div>
</div>

<!-- Chart.js dan Cetak PDF Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
  function toggleProfile() {
    const modal = document.getElementById('profileModal');
    modal.classList.toggle('hidden');
  }

  document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('laporanChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [
        {
          label: 'Total Modal',
          data: <?php echo json_encode($totals_modal); ?>,
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.2)',
          fill: false,
          tension: 0.3,
          pointRadius: 4,
          pointBackgroundColor: '#16a34a',
        },
        {
          label: 'Total Keuntungan',
          data: <?php echo json_encode($totals_keuntungan); ?>,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.2)',
          fill: false,
          tension: 0.3,
          pointRadius: 4,
          pointBackgroundColor: '#2563eb',
        },
        {
          label: 'Total Penjualan',
          data: <?php echo json_encode($totals_penjualan); ?>,
          borderColor: '#a855f7',
          backgroundColor: 'rgba(168, 85, 247, 0.2)',
          fill: false,
          tension: 0.3,
          pointRadius: 4,
          pointBackgroundColor: '#9333ea',
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

const pieCtx = document.getElementById('pieChart').getContext('2d');

// Ambil total dari masing-masing dataset
const totalModal = <?= array_sum($totals_modal) ?>;
const totalKeuntungan = <?= array_sum($totals_keuntungan) ?>;
const totalPenjualan = <?= array_sum($totals_penjualan) ?>;

new Chart(pieCtx, {
  type: 'pie',
  data: {
    labels: ['Total Modal', 'Total Keuntungan', 'Total Penjualan'],
    datasets: [{
      label: 'Proporsi',
      data: [totalModal, totalKeuntungan, totalPenjualan],
      backgroundColor: [
        'rgba(34, 197, 94, 0.6)',
        'rgba(59, 130, 246, 0.6)',
        'rgba(168, 85, 247, 0.6)'
      ],
      borderColor: [
        '#16a34a',
        '#2563eb',
        '#9333ea'
      ],
      borderWidth: 1
    }]
  },
  options: {
    responsive: false,
    plugins: {
      legend: { position: 'bottom' }
      }
    }
  });
});

  document.getElementById('filter').addEventListener('change', function () {
    const tanggalInput = document.getElementById('tanggal');
    tanggalInput.type = this.value === 'tahunan' ? 'number' : 'date';
    if (this.value === 'tahunan') {
      tanggalInput.placeholder = 'Masukkan tahun (mis: 2025)';
      tanggalInput.value = new Date().getFullYear();
    } else {
      tanggalInput.placeholder = '';
    }
  });

document.addEventListener('DOMContentLoaded', () => {
  const btnCetak = document.getElementById('btnCetakPDF');
  btnCetak.addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const margin = 40;
    const spacing = 20;

    const chartCanvas = document.getElementById('laporanChart');
    const tableElement = document.getElementById('hiddenTable');
    const pieCanvas = document.getElementById('pieChart');

    // Tampilkan tabel untuk dirender
    tableElement.style.display = 'block';
    tableElement.style.position = 'absolute';
    tableElement.style.left = '-9999px';
    tableElement.style.visibility = 'visible';

    // 1️⃣ Render semua elemen dulu untuk menghitung ukuran total
    Promise.all([
      html2canvas(tableElement, { scale: 2, backgroundColor: '#ffffff' }),
      html2canvas(chartCanvas, { scale: 2 }),
      html2canvas(pieCanvas, { scale: 2 })
    ]).then(([tableCanvas, chartCanvasImage, pieCanvasImage]) => {
      
      // Hitung lebar maksimum dari semua elemen
      const maxWidth = Math.max(
        tableCanvas.width,
        chartCanvasImage.width,
        pieCanvasImage.width
      );
      
      // Tentukan lebar PDF berdasarkan konten (minimum 595pt untuk A4)
      const pdfWidth = Math.max(595, (maxWidth / 2) + (margin * 2));
      
      // Hitung tinggi setiap elemen dengan proporsi yang benar
      const tableHeight = (tableCanvas.height * (pdfWidth - margin * 2)) / tableCanvas.width;
      const chartHeight = (chartCanvasImage.height * (pdfWidth - margin * 2)) / chartCanvasImage.width;
      
      // Untuk pie chart, batasi tinggi maksimum tapi tetap proporsional
      const maxPieHeight = 400;
      let pieWidth = pdfWidth - margin * 2;
      let pieHeight = (pieCanvasImage.height * pieWidth) / pieCanvasImage.width;
      
      if (pieHeight > maxPieHeight) {
        pieHeight = maxPieHeight;
        pieWidth = (pieCanvasImage.width * pieHeight) / pieCanvasImage.height;
      }
      
      // Hitung total tinggi yang dibutuhkan
      const totalHeight = margin + tableHeight + spacing + chartHeight + spacing + pieHeight + margin;
      
      // Buat PDF dengan ukuran custom
      const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'pt',
        format: [pdfWidth, totalHeight]
      });

      let yPos = margin;

      // 2️⃣ Tambahkan tabel
      const tableImgData = tableCanvas.toDataURL('image/png');
      pdf.addImage(tableImgData, 'PNG', margin, yPos, pdfWidth - margin * 2, tableHeight);
      yPos += tableHeight + spacing;

      // 3️⃣ Tambahkan grafik
      const chartImgData = chartCanvasImage.toDataURL('image/png');
      pdf.addImage(chartImgData, 'PNG', margin, yPos, pdfWidth - margin * 2, chartHeight);
      yPos += chartHeight + spacing;

      // 4️⃣ Tambahkan pie chart (tengah)
      const pieImgData = pieCanvasImage.toDataURL('image/png');
      const centerX = (pdfWidth - pieWidth) / 2;
      pdf.addImage(pieImgData, 'PNG', centerX, yPos, pieWidth, pieHeight);

      // Simpan PDF
      pdf.save('laporan_keuangan.pdf');

      // Reset tampilan tabel
      tableElement.style.display = 'none';
      tableElement.style.position = '';
      tableElement.style.left = '';
      tableElement.style.visibility = 'hidden';
      
    }).catch(err => {
      console.error('Gagal generate PDF:', err);
      
      // Reset tampilan tabel jika error
      tableElement.style.display = 'none';
      tableElement.style.position = '';
      tableElement.style.left = '';
      tableElement.style.visibility = 'hidden';
    });
  });
}); 
</script>

</body>
</html>