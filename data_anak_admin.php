<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success_message = "";
$error_message = "";

function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[$pecah[1]] . ' ' . $pecah[0];
}

$id = 0; $edit_mode = false;
$nik_anak = $nama_anak = $tanggal_lahir = $jenis_kelamin = $nama_ibu = $nama_ayah = $alamat = "";

// Tambah / Edit Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nik_anak = trim($_POST['nik_anak']);
    $nama_anak = trim($_POST['nama_anak']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $nama_ibu = trim($_POST['nama_ibu']);
    $nama_ayah = trim($_POST['nama_ayah']);
    $alamat = trim($_POST['alamat']);

    if ($nik_anak === "" || $nama_anak === "" || $tanggal_lahir === "") {
        $error_message = "NIK, Nama Anak, dan Tanggal Lahir wajib diisi.";
    } else {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE data_anak SET nik_anak=?, nama_anak=?, tanggal_lahir=?, jenis_kelamin=?, nama_ibu=?, nama_ayah=?, alamat=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssssi", $nik_anak, $nama_anak, $tanggal_lahir, $jenis_kelamin, $nama_ibu, $nama_ayah, $alamat, $id);
            if (mysqli_stmt_execute($stmt)) $success_message = "Data berhasil diperbarui!";
            else $error_message = "Gagal memperbarui data: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO data_anak (nik_anak, nama_anak, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssss", $nik_anak, $nama_anak, $tanggal_lahir, $jenis_kelamin, $nama_ibu, $nama_ayah, $alamat);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Data berhasil ditambahkan!";
                $nik_anak = $nama_anak = $tanggal_lahir = $jenis_kelamin = $nama_ibu = $nama_ayah = $alamat = "";
            } else {
                $error_message = "Gagal menambahkan data: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Mode edit
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM data_anak WHERE id = $edit_id LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $nik_anak = $row['nik_anak'];
        $nama_anak = $row['nama_anak'];
        $tanggal_lahir = $row['tanggal_lahir'];
        $jenis_kelamin = $row['jenis_kelamin'];
        $nama_ibu = $row['nama_ibu'];
        $nama_ayah = $row['nama_ayah'];
        $alamat = $row['alamat'];
        $edit_mode = true;
    } else $error_message = "Data anak tidak ditemukan.";
}

// Hapus data
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM data_anak WHERE id = $delete_id")) {
        header("Location: data_anak_admin.php?status=deleted");
        exit;
    } else $error_message = "Gagal menghapus data: " . mysqli_error($conn);
}

// Ambil data
$data = mysqli_query($conn, "SELECT * FROM data_anak ORDER BY nama_anak ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Anak - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    setTimeout(() => {
        const notif = document.querySelector('.notif');
        if (notif) notif.remove();
    }, 3500);

    function toggleForm() {
        const form = document.getElementById("form-tambah");
        form.classList.toggle("hidden");
        form.scrollIntoView({ behavior: 'smooth' });
    }
</script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen p-5">

<header class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3">
    <h1 class="text-2xl font-bold text-green-700">Manajemen Data Anak</h1>
    <div class="flex flex-wrap gap-2 justify-end">
        <a href="admin.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
        <button onclick="toggleForm()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            <i class="fas fa-plus mr-1"></i> Tambah Data Anak Baru
        </button>
    </div>
</header>

<?php if ($success_message): ?>
<div class="notif bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
    <strong>Berhasil!</strong> <?= htmlspecialchars($success_message) ?>
</div>
<?php elseif ($error_message): ?>
<div class="notif bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
    <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
</div>
<?php endif; ?>

<!-- Form -->
<div id="form-tambah" class="bg-white p-6 rounded-lg shadow max-w-3xl mx-auto mb-10 <?= $edit_mode ? '' : 'hidden' ?>">
    <h2 class="text-lg font-semibold mb-4"><?= $edit_mode ? 'Edit Data Anak' : 'Form Tambah Data Anak' ?></h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="font-medium">NIK Anak *</label><input type="text" name="nik_anak" value="<?= htmlspecialchars($nik_anak) ?>" class="w-full mt-1 px-3 py-2 border rounded" required></div>
            <div><label class="font-medium">Nama Anak *</label><input type="text" name="nama_anak" value="<?= htmlspecialchars($nama_anak) ?>" class="w-full mt-1 px-3 py-2 border rounded" required></div>
            <div><label class="font-medium">Tanggal Lahir *</label><input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>" class="w-full mt-1 px-3 py-2 border rounded" required></div>
            <div>
                <label class="font-medium">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full mt-1 px-3 py-2 border rounded">
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki" <?= $jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div><label class="font-medium">Nama Ibu</label><input type="text" name="nama_ibu" value="<?= htmlspecialchars($nama_ibu) ?>" class="w-full mt-1 px-3 py-2 border rounded"></div>
            <div><label class="font-medium">Nama Ayah</label><input type="text" name="nama_ayah" value="<?= htmlspecialchars($nama_ayah) ?>" class="w-full mt-1 px-3 py-2 border rounded"></div>
            <div class="md:col-span-2"><label class="font-medium">Alamat</label><textarea name="alamat" rows="3" class="w-full mt-1 px-3 py-2 border rounded"><?= htmlspecialchars($alamat) ?></textarea></div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <?php if ($edit_mode): ?>
                <a href="data_anak_admin.php" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</a>
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Update</button>
            <?php else: ?>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Tambah</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel -->
<div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">No</th>
                <th class="px-4 py-3 text-left">NIK</th>
                <th class="px-4 py-3 text-left">Nama Anak</th>
                <th class="px-4 py-3 text-left">Tgl. Lahir</th>
                <th class="px-4 py-3 text-left">JK</th>
                <th class="px-4 py-3 text-left">Ibu</th>
                <th class="px-4 py-3 text-left">Ayah</th>
                <th class="px-4 py-3 text-left">Alamat</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2"><?= $no++ ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td>
                <td class="px-4 py-2"><?= formatTanggalIndo($row['tanggal_lahir']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['nama_ibu']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['nama_ayah']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['alamat']) ?></td>
                <td class="px-4 py-2 text-center space-x-2">
                    <a href="?edit=<?= $row['id'] ?>" class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus data ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; if (mysqli_num_rows($data) == 0): ?>
            <tr><td colspan="9" class="text-center px-4 py-6 text-gray-500">Belum ada data anak.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<footer class="text-center text-xs text-gray-500 mt-10">&copy; <?= date('Y') ?> Posyandu Bina Cita</footer>
</body>
</html>

