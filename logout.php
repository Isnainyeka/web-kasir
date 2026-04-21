<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warning Dialog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #F5E6CA;
            font-family: 'Newsreader', serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-[#b89b7b] p-8 rounded-lg text-center w-96 md:w-[32rem]">
        <h1 class="text-red-600 font-bold text-lg mb-4">WARNING!</h1>
        <p class="text-black font-semibold mb-6">ANDA YAKIN INGIN KELUAR?</p>
        <div class="flex justify-center gap-4 mt-4">
            <button onclick="window.location.href='logout_session.php'" class="bg-red-500 text-white font-semibold py-2 px-3 rounded-3xl w-20">
                YA
            </button>
            <button onclick="window.location.href='dashboard.php'" class="bg-blue-500 text-white font-semibold py-2 px-3 rounded-3xl w-20">
                TIDAK
            </button>
        </div>
    </div>
</body>
</html>
