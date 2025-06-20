<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="2.5;url=indext.php"> <!-- Redirect setelah 2.5 detik -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout Berhasil</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 flex items-center justify-center h-screen">
  <div class="bg-green-100 border border-green-300 text-green-900 px-6 py-4 rounded-xl shadow-md text-center">
    <h1 class="text-2xl font-semibold mb-2">Logout Berhasil</h1>
    <p class="text-green-800">Anda akan diarahkan kembali ke halaman utama...</p>
    <div class="mt-4">
      <svg class="animate-spin h-6 w-6 text-green-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
      </svg>
    </div>
  </div>
</body>
</html>


