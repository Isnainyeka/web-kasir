<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasir";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
        // Cek apakah produk sudah dipakai
        $check = "SELECT * FROM products WHERE product_name = '$product_name'";
        $result = mysqli_query($conn, $check);
    
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Nama produk sudah digunakan!'); window.history.back();</script>";
            exit();
        }
    $qty = intval($_POST['qty']);
    $starting_price = floatval($_POST['starting_price']);
    $selling_price = floatval($_POST['selling_price']);
    $margin = $selling_price - $starting_price;
    $fid_category = $_POST['fid_category'];
    $description = $_POST['description'];

    // Upload Gambar
    $target_dir = "assets/produk/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image = $_FILES['image']['name'];
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

// Simpan ke Database tanpa barcode dulu
$stmt = $conn->prepare("INSERT INTO products (product_name, qty, starting_price, selling_price, margin, fid_category, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sidddiss", $product_name, $qty, $starting_price, $selling_price, $margin, $fid_category, $image, $description);

if ($stmt->execute()) {
    // Ambil ID yang baru saja dimasukkan
    $last_id = $conn->insert_id;

    // Format barcode jadi 5 digit, misal: 00001
    $barcode = str_pad($last_id, 5, '0', STR_PAD_LEFT);

    // Update barcode di record tersebut
    $update = $conn->prepare("UPDATE products SET barcode = ? WHERE id = ?");
    $update->bind_param("si", $barcode, $last_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Produk berhasil ditambahkan!'); window.location='produk.php';</script>";
} else {
    echo "<script>alert('Gagal menambahkan produk!');</script>";
}

$stmt->close();

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="flex min-h-screen">
<div class="flex-1 flex items-start justify-center py-4">
        <!-- Tombol Back -->
        <div class="p-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

            <!-- Content -->
            <div class="flex-1 pr-[100px] flex items-center justify-center">
                <div class="flex gap-8">
                    <div class="bg-[#614C3A] w-40 h-40 rounded-lg flex items-center justify-center mt-[200px] mb-[80px] mx-10">
                        <form method="POST" enctype="multipart/form-data">
                            <label for="gambarInput" class="cursor-pointer relative">
                                <span id="iconContainer" class="material-icons-outlined w-32 h-32 bg-[#614C3A] p-4 rounded text-white text-[7rem] flex items-center justify-center">add_photo_alternate</span>
                                <img id="imagePreview" class="w-32 h-32 object-cover rounded hidden absolute top-0 left-0" />
                            </label>
                            <input type="file" id="gambarInput" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>

                    <div class="bg-[#BCA48F] p-8 rounded-2xl w-[600px] h-[600px]">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Nama</label>
                            <input type="text" name="product_name" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Stok</label>
                            <input type="number" name="qty" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Modal</label>
                            <input type="number" name="starting_price" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Harga</label>
                            <input type="number" name="selling_price" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Kategori</label>
                            <select name="fid_category" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $result = $conn->query("SELECT * FROM category");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">Deskripsi</label>
                            <input type="text" name="description" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        </div>
                        <div class="mt-6 flex justify-center gap-4">
                            <button onclick="window.location.href='produk.php'" type="submit" class="w-1/2 bg-[#614C3A] hover:bg-[#723713] text-white px-6 py-2 text-white rounded-3xl">ADD</button>
                            <button onclick="window.location.href='produk.php'" type="button" class="w-1/2 bg-[#614C3A] hover:bg-[#723713] text-white px-6 py-2 text-white rounded-3xl">RESET</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = URL.createObjectURL(event.target.files[0]);
            imagePreview.classList.remove('hidden');
        }
    </script>
</body>

</html>
