<?php
session_start();
$host = "localhost";
$dbname = "kasir";
$username_db = "root";
$password_db = "";
$conn = new mysqli($host, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil semua kategori
$categories = $conn->query("SELECT * FROM category");

// Ambil ID kategori dari URL (misal: kategoribergo.php?id=1)
$selected_category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil produk berdasarkan kategori
$products = $conn->query("SELECT id, product_name, selling_price, image FROM products WHERE fid_category = $selected_category_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products per Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="min-h-screen px-6 py-8">

    <!-- Back dan Tombol Kategori -->
<div class="flex justify-center gap-10 mb-10">
    <!-- Tombol Back (tetap di kiri) -->
    <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Tombol Kategori (terpusat) -->
    <div class="flex flex-wrap justify-center gap-4 w-full">
        <?php while ($row = $categories->fetch_assoc()): ?>
            <button onclick="window.location.href='produk_perkategori.php?id=<?= $row['id'] ?>'"
                class="px-6 py-2 rounded-full text-white <?= $row['id'] == $selected_category_id ? 'bg-[#723713]' : 'bg-[#614C3A] hover:bg-[#723713]' ?>">
                <?= htmlspecialchars($row['category']) ?>
            </button>
        <?php endwhile; ?>
    </div>
</div>

    <!-- Produk -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <?php while ($row = $products->fetch_assoc()): ?>
            <div class="bg-[#BCA48F] p-4 rounded w-64 mx-auto">
                <img src="./assets/produk/<?= htmlspecialchars($row['image']) ?>" class="w-full h-48 object-cover rounded-lg mb-4" />
                <h3 class="text-center text-black font-bold"><?= htmlspecialchars($row['product_name']) ?></h3>
                <p class="text-center text-black">Rp<?= number_format($row['selling_price'], 0, ',', '.') ?></p>
                <button onclick="window.location.href='detailproduk.php?id=<?= $row['id'] ?>'" class="bg-[#614C3A] hover:bg-[#723713] text-white w-full py-2 rounded-3xl mt-2">Details</button>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>

<?php $conn->close(); ?>
