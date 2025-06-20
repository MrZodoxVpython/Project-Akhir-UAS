<?php
session_start();
include 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Tambah data
if (isset($_POST['tambah'])) {
    $id_anak = $_POST['id_anak'];
    $jenis = $_POST['jenis_imunisasi'];
    $tanggal = $_POST['tanggal'];
    $berat = $_POST['berat_badan'];
    $tinggi = $_POST['tinggi_badan'];
    $diagnosa = $_POST['diagnosa'];
    $ket = $_POST['keterangan'];

    mysqli_query($conn, "INSERT INTO imunisasi (id_anak, jenis_imunisasi, tanggal, berat_badan, tinggi_badan, diagnosa, keterangan)
                         VALUES ('$id_anak', '$jenis', '$tanggal', '$berat', '$tinggi', '$diagnosa', '$ket')");
    header("Location: imunisasi_admin.php");
    exit;
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_anak = $_POST['id_anak'];
    $jenis = $_POST['jenis_imunisasi'];
    $tanggal = $_POST['tanggal'];
    $berat = $_POST['berat_badan'];
    $tinggi = $_POST['tinggi_badan'];
    $diagnosa = $_POST['diagnosa'];
    $ket = $_POST['keterangan'];

    mysqli_query($conn, "UPDATE imunisasi SET id_anak='$id_anak', jenis_imunisasi='$jenis', tanggal='$tanggal', berat_badan='$berat', tinggi_badan='$tinggi', diagnosa='$diagnosa', keterangan='$ket'
                         WHERE id=$id");
    header("Location: imunisasi_admin.php");
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM imunisasi WHERE id=$id");
    header("Location: imunisasi_admin.php");
    exit;
}

// Data anak untuk dropdown
$data_anak = mysqli_query($conn, "SELECT * FROM data_anak ORDER BY nama_anak ASC");

// Ambil data imunisasi
$data_imunisasi = mysqli_query($conn, "SELECT i.*, a.nama_anak, a.nik_anak FROM imunisasi i JOIN data_anak a ON i.id_anak = a.id ORDER BY i.tanggal DESC");

// Untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM imunisasi WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Imunisasi Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen px-4 py-6 text-gray-800">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-green-700">Manajemen Imunisasi</h1>
      <a href="admin.php" class="text-green-600 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- FORM TAMBAH / EDIT -->
    <div class="bg-white p-6 rounded-lg shadow border border-green-100">
      <h2 class="text-xl font-semibold mb-4"><?= $edit_data ? 'Edit Data Imunisasi' : 'Tambah Data Imunisasi' ?></h2>
      <form method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php if ($edit_data): ?>
          <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        <div>
          <label class="block mb-1">Nama Anak</label>
          <select name="id_anak" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Anak --</option>
            <?php while ($anak = mysqli_fetch_assoc($data_anak)): ?>
              <option value="<?= $anak['id'] ?>" <?= $edit_data && $edit_data['id_anak'] == $anak['id'] ? 'selected' : '' ?>>
                <?= $anak['nama_anak'] ?> (<?= $anak['nik_anak'] ?>)
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div>
          <label class="block mb-1">Jenis Imunisasi</label>
          <input type="text" name="jenis_imunisasi" class="w-full border rounded px-3 py-2" required value="<?= $edit_data['jenis_imunisasi'] ?? '' ?>">
        </div>
        <div>
          <label class="block mb-1">Tanggal</label>
          <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required value="<?= $edit_data['tanggal'] ?? '' ?>">
        </div>
        <div>
          <label class="block mb-1">Berat Badan (kg)</label>
          <input type="number" name="berat_badan" step="0.1" class="w-full border rounded px-3 py-2" value="<?= $edit_data['berat_badan'] ?? '' ?>">
        </div>
        <div>
          <label class="block mb-1">Tinggi Badan (cm)</label>
          <input type="number" name="tinggi_badan" step="0.1" class="w-full border rounded px-3 py-2" value="<?= $edit_data['tinggi_badan'] ?? '' ?>">
        </div>
        <div>
          <label class="block mb-1">Diagnosa</label>
          <input type="text" name="diagnosa" class="w-full border rounded px-3 py-2" value="<?= $edit_data['diagnosa'] ?? '' ?>">
        </div>
        <div class="sm:col-span-2">
          <label class="block mb-1">Keterangan</label>
          <textarea name="keterangan" class="w-full border rounded px-3 py-2"><?= $edit_data['keterangan'] ?? '' ?></textarea>
        </div>
        <div class="sm:col-span-2 flex gap-2">
          <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
            <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Imunisasi' ?>
          </button>
          <?php if ($edit_data): ?>
            <a href="imunisasi_admin.php" class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100">Batal</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- TABEL -->
    <div class="bg-white p-4 rounded-lg shadow border border-green-100 overflow-x-auto">
      <h2 class="text-xl font-semibold mb-3">Data Imunisasi</h2>
      <table class="min-w-full border text-sm text-left">
        <thead class="bg-green-100 text-green-800">
          <tr>
            <th class="border px-4 py-2">No</th>
            <th class="border px-4 py-2">Nama Anak</th>
            <th class="border px-4 py-2">Jenis</th>
            <th class="border px-4 py-2">Tanggal</th>
            <th class="border px-4 py-2">Berat</th>
            <th class="border px-4 py-2">Tinggi</th>
            <th class="border px-4 py-2">Diagnosa</th>
            <th class="border px-4 py-2">Keterangan</th>
            <th class="border px-4 py-2 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($data_imunisasi)): ?>
          <tr class="hover:bg-green-50">
            <td class="border px-4 py-2"><?= $no++ ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['jenis_imunisasi']) ?></td>
            <td class="border px-4 py-2"><?= $row['tanggal'] ?></td>
            <td class="border px-4 py-2"><?= $row['berat_badan'] ?></td>
            <td class="border px-4 py-2"><?= $row['tinggi_badan'] ?></td>
            <td class="border px-4 py-2"><?= $row['diagnosa'] ?></td>
            <td class="border px-4 py-2"><?= $row['keterangan'] ?></td>
            <td class="border px-4 py-2 text-center">
              <a href="?edit=<?= $row['id'] ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
              <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

