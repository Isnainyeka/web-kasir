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

// Ambil ID dari URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM category WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Kategori tidak ditemukan!'); window.location='kategori.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='kategori.php';</script>";
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newCategory = trim($_POST['category']);
    $image = $category['image']; // pakai gambar lama secara default

    // Cek apakah nama kategori sudah digunakan oleh ID lain
    $checkQuery = "SELECT * FROM category WHERE category = '$newCategory' AND id != $id";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Kategori dengan nama tersebut sudah ada!'); window.history.back();</script>";
        exit();
    }

    // Jika ada upload file gambar baru
    if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        $image = time() . "_" . $_FILES['image']['name']; // pakai nama unik
        $target = "assets/" . $image;

        // Upload file ke folder assets
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "<script>alert('Gagal mengupload gambar!');</script>";
        }
    }

    // Update ke database
    $updateQuery = "UPDATE category SET category='$newCategory', image='$image' WHERE id=$id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Kategori berhasil diperbarui!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui kategori!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <div class="absolute top-4 left-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        </div>

        <!-- Konten utama -->
        <div class="flex-1 pr-[100px] flex items-center justify-center">
        <div class="bg-[#BCA48F] p-12 rounded-lg shadow-lg w-96 h-[450px] pb-40 mt-[80px] ml-[80px]">
                <h2 class="text-center text-white mb-4 text-xl">UPDATE KATEGORI</h2>
                <form method="POST" enctype="multipart/form-data">
                    <!-- Gambar yang bisa diklik -->
                    <div class="flex justify-center mb-4">
                        <label for="imageInput">
                            <img id="categoryImage" 
                                 src="assets/<?php echo $category['image'] ? $category['image'] : 'default.png'; ?>" 
                                 class="w-40 h-40 cursor-pointer border-4 border-[#614C3A] shadow-lg object-cover">
                        </label>
                        <input type="file" id="imageInput" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <!-- Input kategori -->
                    <input class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" 
                           placeholder="Kategori" type="text" name="category" 
                           value="<?php echo $category['category']; ?>" required>

                    <!-- Tombol aksi -->
                    <div class="flex justify-between mt-5">
                        <button class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" type="submit">UPDATE</button>
                        <button onclick="window.location.href='kategori.php'" 
                                class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" 
                                type="button">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('categoryImage').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
