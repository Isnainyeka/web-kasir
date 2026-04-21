<?php
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Koneksi database
$conn = new mysqli("localhost", "root", "", "kasir");

function tambahProdukKeKeranjang($param, $type, $conn) {
    // $type = "i" untuk id (INT), atau "s" untuk barcode (VARCHAR)

    if ($type === "s") {
        // Jika string (barcode), bersihkan input dari karakter asing
        $param = trim(preg_replace('/[^A-Za-z0-9]/', '', $param));
    }

    // Query berdasarkan id atau barcode
    $column = $type === "i" ? "id" : "barcode";

    $stmt = $conn->prepare("SELECT id, product_name, selling_price, image, qty FROM products WHERE $column = ?");
    $stmt->bind_param($type, $param);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    header('Content-Type: application/json');

    if ($result) {
        $productId = $result['id'];

        if (!isset($_SESSION['cart'][$productId]) && count($_SESSION['cart']) >= 5) {
            echo json_encode(['success' => false, 'message' => 'Keranjang maksimal 5 produk berbeda']);
            exit;
        }

        if (isset($_SESSION['cart'][$productId])) {
            if ($_SESSION['cart'][$productId]['qty'] < $result['qty']) {
                $_SESSION['cart'][$productId]['qty'] += 1;
                echo json_encode(['success' => true, 'message' => 'Jumlah produk ditambah']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
        } else {
            if ($result['qty'] > 0) {
                $_SESSION['cart'][$productId] = [
                    'id' => $result['id'],
                    'name' => $result['product_name'],
                    'price' => $result['selling_price'],
                    'image' => $result['image'],
                    'qty' => 1,
                    'expiry_time' => time() + 3600 // 1 jam kedaluwarsa
                ];
                echo json_encode(['success' => true, 'message' => 'Produk ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            }
        }
    } else {
        echo "Produk tidak ditemukan";
    }
    exit;
}

// === PROSES UPDATE JUMLAH DARI TOMBOL + DAN - ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if (isset($_SESSION['cart'][$id])) {
        if ($_POST['action'] === 'increase') {
            // Cek stok dulu
            $check = $conn->prepare("SELECT qty FROM products WHERE id = ?");
            $check->bind_param("i", $id);
            $check->execute();
            $res = $check->get_result()->fetch_assoc();

            if ($_SESSION['cart'][$id]['qty'] < $res['qty']) {
                $_SESSION['cart'][$id]['qty']++;
            }
        } elseif ($_POST['action'] === 'decrease' && $_SESSION['cart'][$id]['qty'] > 1) {
            $_SESSION['cart'][$id]['qty']--;
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

// Menghapus produk dari keranjang
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    echo json_encode(['success' => true]);
    exit;
}

// Hapus item dari keranjang jika produk sudah tidak ada di database
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT id FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $availableIds = [];
    while ($row = $result->fetch_assoc()) {
        $availableIds[] = $row['id'];
    }

    foreach ($_SESSION['cart'] as $key => $item) {
        if (!in_array($key, $availableIds)) {
            unset($_SESSION['cart'][$key]);
        }
    }
}

// PERBAIKAN: Pastikan semua item memiliki expiry_time dan bersihkan yang kedaluwarsa
foreach ($_SESSION['cart'] as $key => $item) {
    // Jika item tidak memiliki expiry_time, tambahkan dengan waktu default 1 jam dari sekarang
    if (!isset($item['expiry_time'])) {
        $_SESSION['cart'][$key]['expiry_time'] = time() + 3600;
    }
    // Hapus item yang sudah kedaluwarsa
    elseif ($item['expiry_time'] <= time()) {
        unset($_SESSION['cart'][$key]);
    }
}

// Tambah produk dari input manual (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    tambahProdukKeKeranjang($_POST['barcode'], "s", $conn);
}

// Tambah produk dari GET
if (isset($_GET['barcode']) || isset($_GET['id'])) {
    if (isset($_GET['id'])) {
        tambahProdukKeKeranjang($_GET['id'], "i", $conn);
    } else {
        tambahProdukKeKeranjang($_GET['barcode'], "s", $conn);
    }
    exit;
}
?>

<!-- Tampilan Keranjang -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="min-h-screen px-4 py-6">

<!-- Tombol Back -->
<div class="absolute top-4 left-4">
  <button onclick="smartBack()" class="text-[#614C3A] hover:text-gray-700 text-3xl">
    <i class="fas fa-arrow-left"></i>
  </button>
</div>

<script>
function smartBack() {
    // Cek apakah halaman sebelumnya adalah transaksi
    if (document.referrer.includes('transaksi.php')) {
        // Jika ya, kembali dengan parameter from=keranjang
        window.location.href = 'transaksi.php?from=keranjang';
    } else {
        // Jika tidak, gunakan history.back()
        history.back();
    }
}
</script>

<!-- Wrapper dua kolom: Input Manual + Daftar Pesanan -->
<div class="flex flex-col md:flex-row justify-center items-start gap-6">

  <!-- Kolom Kanan: Daftar Pesanan -->
<div class="bg-[#f0e0d0]/80 backdrop-blur-sm rounded-xl shadow-md p-6 w-full max-w-3xl mx-auto mt-10">
    
    <!-- Judul -->
    <h1 class="text-2xl font-semibold text-center text-black mt-4">Pesanan Anda</h1>

    <div class="max-w-3xl mx-auto mt-6">
      <?php if (!empty($_SESSION['cart'])): ?>
        <form method="post" action="konfirpmbyrn.php">
          <?php foreach ($_SESSION['cart'] as $item): ?>
            <?php
              // PERBAIKAN: Pastikan expiry_time ada sebelum digunakan
              $expiry_time = isset($item['expiry_time']) ? $item['expiry_time'] : (time() + 3600);
              $time_left = $expiry_time - time();
              $expired = $time_left <= 0;
            ?>
            <div class="relative flex items-center gap-4 bg-[#cba589] w-full max-w-[600px] mx-auto p-4 my-4 rounded-xl shadow-md">
              <!-- Tombol Hapus -->
              <button 
                class="absolute top-2 left-2 text-red-600 text-base hover:scale-110 transition cursor-pointer"
                type="button"
                onclick="hapusProduk('<?= $item['id'] ?>')">
                <i class="fas fa-times"></i>
              </button>

              <!-- Checkbox -->
              <input 
                type="checkbox" 
                name="selected[]" 
                value="<?= $item['id'] ?>" 
                class="absolute top-2 right-2 w-5 h-5 accent-green-600 checkbox-item"
                data-id="<?= $item['id'] ?>"
                data-price="<?= $item['price'] * $item['qty'] ?>"
              >

              <!-- Gambar Produk -->
              <img src="./assets/produk/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-24 h-28 rounded-lg object-cover">

              <!-- Info Produk -->
              <div class="text-sm text-black flex-1 grid grid-cols-2 gap-y-1">
                <p class="font-semibold">No</p> <p>: <?= $item['id'] ?></p>
                <p class="font-semibold">Nama Produk</p> <p>: <?= htmlspecialchars($item['name']) ?></p>
                <p class="font-semibold">Harga</p> <p>: Rp<?= number_format($item['price'], 0, ',', '.') ?></p>
                <p class="font-semibold">Jumlah</p> <p>: <?= $item['qty'] ?></p>
                <p class="font-semibold">Waktu Sisa</p>
                <p id="time-left-<?= $item['id'] ?>" data-timeleft="<?= $time_left ?>">: </p>
                <p class="font-semibold">Total</p> <p>: Rp<?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></p>
              </div>

              <!-- Tombol Jumlah -->
              <div class="absolute bottom-3 right-4 flex items-center space-x-2">
                <button type="button" onclick="updateQty('<?= $item['id'] ?>', 'decrease')" class="bg-[#5c3d2e] text-white px-3 py-1 rounded">-</button>
                <span class="font-semibold"><?= $item['qty'] ?></span>
                <button type="button" onclick="updateQty('<?= $item['id'] ?>', 'increase')" class="bg-[#5c3d2e] text-white px-3 py-1 rounded">+</button>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Total Harga -->
          <div class="flex justify-end mt-4 max-w-[600px] mx-auto">
            <div class="bg-[#eee] text-[#333] px-6 py-2 rounded-lg shadow font-semibold">
              Total Harga: <span id="totalHarga">Rp0</span>
            </div>
          </div>

          <!-- Tombol Bayar -->
          <div class="flex justify-center mt-4">
            <button type="submit" class="bg-[#6a3a20] text-white px-10 py-2 rounded-xl shadow hover:bg-[#4d2e1c] transition">
              Bayar
            </button>
          </div>
        </form>
      <?php else: ?>
        <p class="text-center text-gray-700 text-lg mt-10">Keranjang masih kosong.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function showNotifikasi(pesan, sukses) {
    const notifikasi = document.getElementById("notifikasi");
    notifikasi.textContent = pesan;
    notifikasi.className = sukses
        ? "bg-green-600 mt-4 text-center text-white font-semibold px-4 py-2 rounded-lg"
        : "bg-red-600 mt-4 text-center text-white font-semibold px-4 py-2 rounded-lg";
    notifikasi.style.display = "block";

    // Sembunyikan setelah 3 detik
    setTimeout(() => {
        notifikasi.style.display = "none";
    }, 3000);
}



// Update quantity produk di keranjang
function updateQty(id, action) {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("action", action);

    fetch("keranjang.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Hapus produk dari keranjang
function hapusProduk(id) {
    if (confirm('Yakin ingin menghapus produk ini dari keranjang?')) {
        fetch('?remove=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Format waktu ke hh:mm:ss
function formatTime(t) {
    let hours = Math.floor(t / 3600);
    let minutes = Math.floor((t % 3600) / 60);
    let seconds = t % 60;
    return `: ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// PERBAIKAN: Waktu kedaluwarsa dari PHP session cart dengan pengecekan
const expiryTime = <?php
    $expiry = [];
    foreach ($_SESSION['cart'] as $item) {
        // Pastikan expiry_time ada sebelum diakses
        $expiry_time = isset($item['expiry_time']) ? $item['expiry_time'] : (time() + 3600);
        $expiry[$item['id']] = $expiry_time;
    }
    echo json_encode($expiry);
?>;

// Update timer kedaluwarsa tiap detik
function updateAllTimers() {
    const now = Math.floor(Date.now() / 1000);
    for (const id in expiryTime) {
        const elem = document.getElementById(`time-left-${id}`);
        if (!elem) continue;

        const timeLeft = expiryTime[id] - now;
        elem.textContent = timeLeft <= 0 ? ': Kedaluwarsa' : formatTime(timeLeft);
    }
}

// Restore status checkbox dari localStorage
function restoreCheckboxState() {
    const saved = localStorage.getItem('checkboxStatus');
    if (!saved) return;

    const status = JSON.parse(saved);
    document.querySelectorAll('.checkbox-item').forEach(cb => {
        const itemId = cb.dataset.id;
        if (status[itemId] !== undefined) {
            cb.checked = status[itemId];
        }
    });

    hitungTotal();
}

// Hitung total harga berdasarkan checkbox terpilih
function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.checkbox-item').forEach(cb => {
        if (cb.checked) {
            total += parseInt(cb.dataset.price);
        }
    });
    document.getElementById('totalHarga').textContent = 'Rp' + total.toLocaleString('id-ID');
}

// Setup event listener checkbox dan inisialisasi saat DOM siap
window.addEventListener('DOMContentLoaded', () => {
    restoreCheckboxState();
    hitungTotal();
    updateAllTimers();
    setInterval(updateAllTimers, 1000);

    document.querySelectorAll('.checkbox-item').forEach(cb => {
        cb.addEventListener('change', () => {
            hitungTotal();
            const status = JSON.parse(localStorage.getItem('checkboxStatus') || '{}');
            status[cb.dataset.id] = cb.checked;
            localStorage.setItem('checkboxStatus', JSON.stringify(status));
        });
    });

});
</script>
</body>
</html>