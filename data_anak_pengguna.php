<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi ke database berhasil

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_pengguna = $_SESSION['nama'];

// Ambil data anak milik user yang sedang login
$query = "
    SELECT nik_anak, nama_anak, tanggal_lahir, nama_ibu, nama_ayah, alamat
    FROM data_anak
    WHERE user_id = ?
    ORDER BY nama_anak ASC
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$data_anak_result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Fungsi untuk format tanggal
function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[$pecah[1]] . ' ' . $pecah[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anak Anda - Posyandu Bina Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-green-50 min-h-screen text-gray-800">

<header class="bg-white shadow-lg px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
    <div class="flex items-center mb-3 sm:mb-0">
        <svg class="h-10 w-10 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.195-1.269-.545-1.782l-2.617-3.664M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.195-1.269.545-1.782l2.617-3.664m0 0L9.433 10.9A4.004 4.004 0 0111 6h2c1.455 0 2.723.738 3.486 1.89l.437.755M14.5 9.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
        </svg>
        <h1 class="text-2xl font-bold text-green-800">Data Anak Anda</h1>
    </div>
    <a href="dashboard.php" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition duration-300 ease-in-out flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
    </a>
</header>

<main class="p-6 md:p-10 max-w-7xl mx-auto">
    <p class="mb-8 text-lg text-gray-700 text-center">Halo <span class="font-bold text-green-700"><?= htmlspecialchars($nama_pengguna) ?></span>, berikut data anak Anda di Posyandu Bina Cita.</p>

    <div class="overflow-x-auto bg-white rounded-xl shadow-2xl border border-green-200">
        <table class="min-w-full text-sm table-auto divide-y divide-gray-200">
            <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider rounded-tl-xl">No</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">NIK Anak</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Nama Anak</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Tanggal Lahir</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Nama Ibu</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Nama Ayah</th>
                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider rounded-tr-xl">Alamat</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php if (mysqli_num_rows($data_anak_result) > 0): ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($data_anak_result)): ?>
                    <tr class="hover:bg-green-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4"><?= $no++ ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nik_anak']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nama_anak']) ?></td>
                        <td class="px-6 py-4"><?= formatTanggalIndo($row['tanggal_lahir']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nama_ibu']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nama_ayah']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['alamat']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-gray-500 bg-green-50">
                        <i class="fas fa-info-circle mr-2"></i> Belum ada data anak terdaftar atas nama Anda.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="bg-white border-t border-green-200 py-6 mt-10">
    <div class="text-center text-sm text-gray-600">
        &copy; <?= date('Y') ?> Posyandu Bina Cita. Semua Hak Dilindungi.
    </div>
</footer>

</body>
</html>

