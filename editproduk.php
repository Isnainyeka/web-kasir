<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasir";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID dan data produk
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='kategori.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='kategori.php';</script>";
    exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $qty = intval($_POST['qty']);
    $starting_price = floatval($_POST['starting_price']);
    $selling_price = floatval($_POST['selling_price']);
    $margin = floatval($selling_price - $starting_price);
    $fid_category = $_POST['fid_category'];
    $description = $_POST['description'];
    $image = $product['image'];

    // Cek apakah nama produk sudah digunakan oleh produk lain
    $check = "SELECT * FROM products WHERE product_name = '$product_name' AND id != $id";
    $checkResult = mysqli_query($conn, $check);
    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Nama produk sudah digunakan!'); window.history.back();</script>";
        exit();
    }

    if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image']['name'];
        $target = "assets/produk/" . $image;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "<script>alert('Gagal mengupload gambar!');</script>";
        }
    }

    $updateQuery = "UPDATE products SET 
        product_name='$product_name',
        qty='$qty',
        starting_price='$starting_price',
        selling_price='$selling_price',
        margin='$margin',
        fid_category='$fid_category',
        image='$image',
        description='$description'
        WHERE id=$id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='produk.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Products</title>
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
                        <img id="imagePreview" src="assets/produk/<?= $product['image'] ?>" class="w-32 h-32 object-cover rounded absolute top-0 left-0 <?= $product['image'] ? '' : 'hidden' ?>" />
                    </label>
                    <input type="file" id="gambarInput" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
            </div>

            <div class="bg-[#BCA48F] p-8 rounded-2xl w-[600px] h-[600px]">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Nama</label>
                    <input type="text" name="product_name" value="<?= $product['product_name'] ?>" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Stok</label>
                    <input type="number" name="qty" value="<?= $product['qty'] ?>" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Harga Awal</label>
                    <input type="number" name="starting_price" value="<?= $product['starting_price'] ?>" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Harga Akhir</label>
                    <input type="number" name="selling_price" value="<?= $product['selling_price'] ?>" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Kategori</label>
                    <select name="fid_category" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        $result = $conn->query("SELECT * FROM category");
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id'] == $product['fid_category'] ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>{$row['category']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Deskripsi</label>
                    <input type="text" name="description" value="<?= $product['description'] ?>" required class="w-full p-2 rounded-3xl bg-[#E6D8CD] border-none">
                </div>
                <div class="mt-6 flex justify-center gap-4">
                    <button type="submit" class="w-1/2 bg-[#614C3A] hover:bg-[#723713] text-white px-6 py-2 rounded-3xl">UPDATE</button>
                    <button type="reset" class="w-1/2 bg-[#614C3A] hover:bg-[#723713] text-white px-6 py-2 rounded-3xl">RESET</button>
                </div>
            </div>
            </form>
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