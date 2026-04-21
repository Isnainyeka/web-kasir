<html>
<head>
    <title>Naabelle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Nanum+Myeongjo:wght@400;700&family=Nerko+One&family=Newsreader:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .custom-font {
            font-family: 'Nanum Myeongjo', serif;
        }
        .title-font {
            font-family: 'Nerko One', cursive;
        }
        .logo-font {
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-[#614C3A] p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Logo of Naabelle" class="h-50 w-50" height="40" src="./assets/logo1.png" width="40"/>
        </div>
        <h1 class="text-white text-2xl font-bold logo-font">
            naabelle
        </h1>
        <div>
        <a href="login.php">
            <i class="fas fa-user text-white text-2xl"></i>
        </a>
</div>
    </header>
    <main class="relative">
        <img alt="Three women wearing hijabs, smiling and posing together" class="w-full h-auto" height="600" src="./assets/landing.jpg" width="1200"/>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-4">
            <h2 class="text-4xl font-bold mb-4 title-font">
                Welcome to naabelle!
            </h2>
            <p class="text-lg custom-font">
                Look Graceful &amp; Confident with Our Best Hijab Collection!<br/>
                Comfortable Material | Stylish Model | Exclusive Colors<br/>
                Come on, find your favorite hijab now!
            </p>
        </div>
    </main>
</body>
</html>
