<?php
session_start();
include 'koneksi.php';

// Cek login sebagai peserta
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengguna') {
  header("Location: login.php");
  exit;
}

// Ambil semua jadwal
$jadwal = mysqli_query($conn, "SELECT * FROM jadwal_posyandu ORDER BY tanggal DESC");

function hariIndo($tanggal) {
  $hari = date('l', strtotime($tanggal));
  $indo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
  ];
  return $indo[$hari];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Jadwal Posyandu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen text-gray-800">
  <div class="max-w-4xl mx-auto p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-green-700">Jadwal Posyandu</h1>
      <a href="dashboard.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">← Kembali ke Dashboard</a>
    </div>

    <ul class="bg-white rounded shadow divide-y">
      <?php while ($row = mysqli_fetch_assoc($jadwal)):
        $hari = hariIndo($row['tanggal']);
        $tgl_id = date('d-m-Y', strtotime($row['tanggal']));
      ?>
      <li class="p-4 hover:bg-green-50">
        <strong><?= $hari . ", " . $tgl_id ?> - <?= htmlspecialchars($row['waktu']) ?> WIB</strong><br>
        Lokasi: <?= htmlspecialchars($row['tempat']) ?><br>
        <span class="text-sm text-gray-600"><?= htmlspecialchars($row['keterangan']) ?></span>
      </li>
      <?php endwhile; ?>
      <?php if (mysqli_num_rows($jadwal) === 0): ?>
      <li class="p-4 text-center text-gray-500">Belum ada jadwal Posyandu.</li>
      <?php endif; ?>
    </ul>
  </div>
</body>
</html>

