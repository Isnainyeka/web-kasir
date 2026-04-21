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
    $category = $_POST['category'];
        // Cek apakah kategori sudah dipakai
        $check = "SELECT * FROM category WHERE category = '$category'";
        $result = mysqli_query($conn, $check);
    
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Kategori sudah digunakan!'); window.history.back();</script>";
            exit();
        }

    // Upload Gambar
    $target_dir = "assets/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image = $_FILES['image']['name'];
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Simpan ke Database
    $sql = "INSERT INTO category (category, image) VALUES ('$category', '$image')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('kategori berhasil ditambahkan!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan kategori!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
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
        <div class="absolute top-4 left-4">
        <a href="javascript:history.back()" class="text-[#614C3A nw] hover:text-gray-700 text-3xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        </div>

        <!-- Konten utama -->
        <div class="flex-1 pr-[100px] flex items-center justify-center">
        <div class="bg-[#BCA48F] p-12 rounded-lg shadow-lg w-96 h-[400px] pb-40 mt-[100px] ml-[80px]">
            <h2 class="text-center text-white mb-4 text-xl">ADD CATEGORY</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="flex justify-center mb-4">
                    <label for="gambarInput" class="cursor-pointer relative">
                        <span id="iconContainer" class="w-32 h-32 bg-[#614C3A] p-4 rounded text-white text-[7rem] flex items-center justify-center"></span>
                        <img id="imagePreview" class="w-32 h-32 object-cover rounded hidden absolute top-0 left-0"/>
                    </label>
                    <input type="file" id="gambarInput" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                </div>

                <input class="w-full mb-4 p-2 rounded-3xl bg-[#E6D8CD] text-black" name="category" placeholder="Category" type="category" required/>

                <div class="flex justify-between mt-5">
                    <button class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" type="submit">ADD</button>
                    <button onclick="window.location.href='admin.php'" class="bg-[#614C3A] hover:bg-[#723713] text-white py-2 px-8 rounded-3xl" type="button">Cancel</button>
                </div>
            </form>
        </div>
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