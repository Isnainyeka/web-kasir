<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "kasir"); // sesuaikan dengan konfigurasi kamu

// Ambil ID produk dari URL
$id = $_GET['id'];

// Query data produk berdasarkan ID
$query = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $query);

// Error handling
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="min-h-screen">
<div class="flex-1 flex items-start justify-center py-4">
    <!-- Tombol Back -->
    <div class="p-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-center w-full mb-10 pl-[20px]">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="bg-[#BCA48F] p-4 rounded-lg shadow-lg w-[800px] h-[750px] pb-40">
                <!-- Gambar Produk -->
                <div class="flex justify-center mb-6">
                    <img src="assets/produk/<?php echo $row['image']; ?>" 
                        alt="<?php echo $row['product_name']; ?>" 
                        class="rounded-lg w-[200px] h-[200px] object-cover border-4 border-white shadow-md"/>
                </div>

                <!-- Tabel Info Produk -->
                <table class="text-left w-full rounded-xl overflow-hidden mb-6">
                    <tbody>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black w-1/3">Nama</th>
                            <td class="p-3 bg-[#D8CFC8]"><?php echo $row['product_name']; ?></td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Stok</th>
                            <td class="p-3 bg-[#D8CFC8]"><?php echo $row['qty']; ?></td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Modal</th>
                            <td class="p-3 bg-[#D8CFC8]">Rp <?php echo number_format($row['starting_price'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Harga</th>
                            <td class="p-3 bg-[#D8CFC8]">Rp <?php echo number_format($row['selling_price'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Keuntungan</th>
                            <td class="p-3 bg-[#D8CFC8]">Rp <?php echo number_format($row['selling_price'] - $row['starting_price'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Kategori</th>
                            <td class="p-3 bg-[#D8CFC8]"><?php echo $row['fid_category']; ?></td>
                        </tr>
                        <tr>
                            <th class="p-3 font-bold bg-[#A38D77] border-r border-black">Deskripsi</th>
                            <td class="p-3 bg-[#D8CFC8]"><?php echo $row['description']; ?></td>
                        </tr>
                    </tbody>
                </table>

<!-- Barcode di bagian bawah kotak -->
<div class="flex justify-center mt-8">
    <svg id="barcode-<?php echo $row['id']; ?>"></svg>
</div>

<script>
   JsBarcode("#barcode-<?php echo $row['id']; ?>", "<?php echo $row['barcode']; ?>", {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 80,
        displayValue: true
    });
</script>

    </div>
    
            </div>
        <?php } ?>
    </main>

</body>
</html>
