<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$status = "";

// Proses Tambah / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $tempat = $_POST['tempat'] ?? "Puskesmas Bina Cita";
    $keterangan = $_POST['keterangan'] ?? "Jangan lupa datang tepat waktu!";

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update
        $id = intval($_POST['id']);
        $stmt = mysqli_prepare($conn, "UPDATE jadwal_posyandu SET tanggal=?, waktu=?, tempat=?, keterangan=? WHERE id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $tanggal, $waktu, $tempat, $keterangan, $id);
            mysqli_stmt_execute($stmt);
            $status = "Jadwal berhasil diperbarui.";
        } else {
            $status = "Gagal mempersiapkan query update: " . mysqli_error($conn);
        }
    } else {
        // Tambah
        $stmt = mysqli_prepare($conn, "INSERT INTO jadwal_posyandu (tanggal, waktu, tempat, keterangan) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $tanggal, $waktu, $tempat, $keterangan);
            mysqli_stmt_execute($stmt);
            $status = "Jadwal berhasil ditambahkan.";
        } else {
            $status = "Gagal menambahkan jadwal: " . mysqli_error($conn);
        }
    }
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal_posyandu WHERE id = $id");
    header("Location: jadwal_admin.php?deleted=1");
    exit;
}

// Data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM jadwal_posyandu WHERE id = $editId");
    $editData = mysqli_fetch_assoc($res);
}

// Ambil semua jadwal
$jadwal = mysqli_query($conn, "SELECT * FROM jadwal_posyandu ORDER BY tanggal DESC");

// Konversi hari ke Bahasa Indonesia
function hariIndo($tanggal) {
    $day = date('l', strtotime($tanggal));
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    return $hari[$day];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jadwal Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 min-h-screen">
    <div class="max-w-4xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-green-700"><?= $editData ? "Edit Jadwal" : "Tambah Jadwal Posyandu" ?></h1>
            <a href="dashboard_admin.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">← Kembali ke Dashboard</a>
        </div>

        <?php if ($status): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $status ?></div>
        <?php elseif (isset($_GET['deleted'])): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">Jadwal berhasil dihapus.</div>
        <?php endif; ?>

        <!-- Form Tambah/Edit -->
        <form method="POST" class="bg-white p-6 rounded shadow mb-8">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>

            <label class="block mb-2 font-medium">Tanggal Posyandu:</label>
            <input type="date" name="tanggal" required value="<?= $editData['tanggal'] ?? '' ?>" class="w-full mb-4 border rounded px-4 py-2">

            <label class="block mb-2 font-medium">Jam Mulai:</label>
            <input type="time" name="waktu" required value="<?= $editData['waktu'] ?? '' ?>" class="w-full mb-4 border rounded px-4 py-2">

            <label class="block mb-2 font-medium">Tempat:</label>
            <input type="text" name="tempat" value="<?= $editData['tempat'] ?? 'Puskesmas Bina Cita' ?>" class="w-full mb-4 border rounded px-4 py-2">

            <label class="block mb-2 font-medium">Keterangan:</label>
            <textarea name="keterangan" class="w-full mb-4 border rounded px-4 py-2"><?= $editData['keterangan'] ?? 'Jangan lupa datang tepat waktu!' ?></textarea>

            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"><?= $editData ? "Update" : "Simpan Jadwal" ?></button>
            <?php if ($editData): ?>
                <a href="jadwal_admin.php" class="ml-4 text-sm text-green-600 hover:underline">Batal Edit</a>
            <?php endif; ?>
        </form>

        <!-- Daftar Jadwal -->
        <h2 class="text-xl font-semibold text-green-800 mb-2">Daftar Jadwal Posyandu</h2>
        <ul class="bg-white rounded shadow divide-y">
            <?php if (mysqli_num_rows($jadwal) === 0): ?>
                <li class="p-4 text-gray-500">Belum ada jadwal.</li>
            <?php else: ?>
                <?php while ($row = mysqli_fetch_assoc($jadwal)): ?>
                    <?php
                        $hari = hariIndo($row['tanggal']);
                        $tgl_id = date('d-m-Y', strtotime($row['tanggal']));
                    ?>
                    <li class="p-4 flex justify-between items-center">
                        <div>
                            <strong><?= $hari . ", " . $tgl_id ?> - <?= htmlspecialchars($row['waktu']) ?> WIB</strong><br>
                            Lokasi: <?= htmlspecialchars($row['tempat']) ?><br>
                            <small class="text-gray-500"><?= htmlspecialchars($row['keterangan']) ?></small>
                        </div>
                        <div class="space-x-2">
                            <a href="jadwal_admin.php?edit=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="jadwal_admin.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus jadwal ini?')" class="text-red-600 hover:underline">Hapus</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>

