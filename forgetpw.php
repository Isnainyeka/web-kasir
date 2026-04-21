<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Reset Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
        body {
            background: linear-gradient(to bottom right,rgba(141, 81, 44, 0.5), #723713);
            font-family: 'Newsreader', serif;
        }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-sm p-8 bg-[rgba(217,217,217,0.3)] rounded-2xl shadow-md">
    <div class="flex justify-center mb-6">
      <img alt="Logo" class="w-24 h-24" src="./assets/logo1.png"/>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <form action="sendreset.php" method="POST">
        <div class="mb-4">
          <label class="block text-sm font-bold mb-2" for="email">Email</label>
          <input class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:shadow-outline" id="email" type="email" name="email" required/>
        </div>
        <button class="w-full px-4 py-2 font-bold text-white bg-[#723713] rounded-lg hover:bg-[#614C3A] focus:outline-none focus:shadow-outline" type="submit">Kirim Link Reset</button>
    </form>
    </div>
  </div>

</body>
</html>