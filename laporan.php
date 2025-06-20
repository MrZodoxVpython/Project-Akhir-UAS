<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php sudah ada dan berfungsi

// Cek login & role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_pengguna.php");
    exit;
}

$nama_admin = $_SESSION['nama'];

// Inisialisasi variabel untuk filter
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query untuk mengambil data laporan dengan JOIN ke tabel imunisasi
// Memilih kolom dari data_anak dan melakukan DISTINCT agar tidak ada duplikasi anak
// jika anak memiliki beberapa imunisasi pada bulan/tahun yang sama.
$query_laporan = "
    SELECT DISTINCT
        da.id,
        da.nik_anak,
        da.nama_anak,
        da.tanggal_lahir,
        da.nama_ibu,
        da.nama_ayah,
        da.alamat
    FROM
        data_anak da
    INNER JOIN
        imunisasi i ON da.id = i.id_anak
";

$where_clauses = []; // Array untuk menampung kondisi WHERE
$param_types = ''; // String for parameter types (e.g., 'is')
$param_values = []; // Array for parameter values

// Tambahkan kondisi filter jika bulan atau tahun dipilih
// Filter diterapkan pada kolom 'tanggal' dari tabel imunisasi
if (!empty($filter_bulan)) {
    $where_clauses[] = "MONTH(i.tanggal) = ?";
    $param_types .= 'i'; // 'i' for integer
    $param_values[] = $filter_bulan;
}
if (!empty($filter_tahun)) {
    $where_clauses[] = "YEAR(i.tanggal) = ?";
    $param_types .= 'i'; // 'i' for integer
    $param_values[] = $filter_tahun;
}

// Gabungkan kondisi WHERE jika ada
if (!empty($where_clauses)) {
    $query_laporan .= " WHERE " . implode(" AND ", $where_clauses);
}

// Tambahkan pengurutan
$query_laporan .= " ORDER BY da.nama_anak ASC";

// Prepare and execute the query using prepared statements
$stmt = mysqli_prepare($conn, $query_laporan);

if ($stmt === false) {
    die("Query preparation failed: " . htmlspecialchars(mysqli_error($conn)));
}

// Bind parameters if there are any filters
if (!empty($param_values)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
}

mysqli_stmt_execute($stmt);
$result_laporan = mysqli_stmt_get_result($stmt);

if (!$result_laporan) {
    die("Query execution failed: " . htmlspecialchars(mysqli_error($conn)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Imunisasi Anak - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .navbar-blur { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.7); }

        /* Styles for printing */
        @media print {
            .no-print {
                display: none !important; /* Hide elements with this class when printing */
            }
            body {
                background-color: #fff !important; /* White background for print */
                color: #000 !important; /* Black text for print */
                margin: 0;
                padding: 0;
            }
            main {
                padding: 1rem !important; /* Adjust main padding for print */
            }
            .shadow-2xl, .shadow-md, .shadow-lg, .shadow-inner {
                box-shadow: none !important; /* Remove shadows for print */
            }
            .border, .border-green-200, .divide-y {
                border-color: #ccc !important; /* Lighter borders for print */
            }
            h2 {
                color: #000 !important; /* Black heading for print */
            }
            p.text-gray-600.mb-8 {
                color: #333 !important; /* Darker gray for readability */
            }
            /* Ensure table is fully visible and readable on print */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 1rem; /* Space below header info */
            }
            th, td {
                border: 1px solid #ccc;
                padding: 8px;
                text-align: left;
                font-size: 10pt; /* Smaller font for print */
                vertical-align: top; /* Align content to top for multi-line cells */
            }
            thead {
                background-color: #f2f2f2;
            }
            tr {
                page-break-inside: avoid; /* Avoid breaking rows across pages */
            }
            /* Reset specific tailwind classes that might interfere with print */
            .flex, .grid, .gap-4, .gap-6, .items-end, .justify-between, .justify-end, .ml-auto {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .w-full, .sm:w-1/3, .md:grid-cols-3, .sm:flex-row {
                width: auto !important;
            }
            .rounded-xl, .rounded-lg, .rounded-md {
                border-radius: 0 !important; /* Remove rounded corners for print */
            }
            .p-6, .p-8, .px-4, .py-3, .py-2, .px-6 {
                padding: 0.25rem 0.5rem !important; /* Reduce padding for print */
            }
        }

        /* Responsive Table Styles for smaller screens (mobile-first approach) */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block; /* Make table elements behave like blocks */
            }

            thead tr {
                position: absolute; /* Hide table headers visually but keep for screen readers */
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #e2e8f0; /* Light gray border for each "card" row */
                margin-bottom: 1rem; /* Space between rows */
                border-radius: 0.5rem; /* Rounded corners for row cards */
                overflow: hidden; /* Ensures rounded corners are visible */
                box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Subtle shadow for row cards */
            }

            td {
                border: none; /* Remove default cell borders */
                border-bottom: 1px solid #edf2f7; /* Light border between data points */
                position: relative;
                padding-left: 50%; /* Space for the custom label */
                text-align: right;
                font-size: 0.95rem; /* Slightly larger text for data */
                padding-top: 0.8rem; /* Consistent padding */
                padding-bottom: 0.8rem;
                white-space: normal; /* Allow content to wrap */
                word-break: break-word; /* Break long words if necessary */
            }

            td:last-child {
                border-bottom: 0; /* No border for the last cell in a row */
            }

            td:before {
                position: absolute;
                top: 0;
                left: 0; /* Align label to the left edge of padding */
                width: 48%; /* Adjust width for label */
                padding-right: 0.5rem;
                padding-left: 1rem; /* Add padding to label */
                white-space: nowrap; /* Keep label on one line */
                text-align: left;
                font-weight: 600; /* Semibold for labels */
                color: #10b981; /* Tailwind's green-500 for labels */
                padding-top: 0.8rem; /* Match data padding */
                padding-bottom: 0.8rem;
            }

            /* Assigning data labels using content property */
            td:nth-of-type(1):before { content: "No."; }
            td:nth-of-type(2):before { content: "NIK Anak"; }
            td:nth-of-type(3):before { content: "Nama Anak"; }
            td:nth-of-type(4):before { content: "Tgl. Lahir"; }
            td:nth-of-type(5):before { content: "Nama Ibu"; }
            td:nth-of-type(6):before { content: "Nama Ayah"; }
            td:nth-of-type(7):before { content: "Alamat"; }

            /* Specific adjustment for the last cell (Alamat) to prevent label overlap if content is too long */
            td:nth-of-type(7) {
                text-align: right; /* Keep content aligned right */
            }
            td:nth-of-type(7):before {
                white-space: normal; /* Allow label to wrap if needed */
                overflow: hidden; /* Hide overflow if it still happens */
                text-overflow: ellipsis; /* Add ellipsis for overflow */
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 text-gray-800 antialiased">

    <header class="fixed top-0 left-0 right-0 z-50 navbar-blur shadow-lg no-print">
        <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 py-3">
            <div class="flex items-center gap-3 mb-3 sm:mb-0">
                <img src="https://i.imgur.com/9rjiL5N.jpg" alt="Logo Posyandu" class="w-12 h-12 rounded-full object-cover border-2 border-green-600 shadow-md" />
                <span class="text-xl font-bold text-green-800">Dashboard Kader Posyandu</span>
            </div>
            <div class="text-sm sm:text-base text-green-800 font-semibold flex items-center">
                <i class="fas fa-hand-sparkles text-yellow-500 mr-2"></i> Selamat datang, <span class="font-bold ml-1 mr-4"><?= htmlspecialchars($nama_admin) ?></span>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300 ease-in-out transform hover:scale-105 flex items-center text-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="pt-24 pb-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl border border-green-200">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
                <h2 class="text-3xl font-extrabold text-green-800 flex items-center mb-4 sm:mb-0">
                    <i class="fas fa-chart-line text-green-600 mr-3"></i> Laporan Data Imunisasi Anak
                </h2>
                <a href="admin.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center text-sm no-print">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>

            <p class="text-gray-600 mb-8 text-center sm:text-left">
                Tinjau data anak-anak yang telah melakukan imunisasi pada bulan dan tahun tertentu.
            </p>

            <div class="mb-8 bg-green-50 p-6 rounded-xl border border-green-200 shadow-inner no-print">
                <form action="laporan.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Filter Bulan Imunisasi:</label>
                        <select id="bulan" name="bulan" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">-- Semua Bulan --</option>
                            <?php
                            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            for ($i = 1; $i <= 12; $i++) {
                                $month_num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $selected = ($filter_bulan == $month_num) ? 'selected' : '';
                                echo "<option value=\"$month_num\" $selected>" . $months[$i-1] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Filter Tahun Imunisasi:</label>
                        <select id="tahun" name="tahun" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">-- Semua Tahun --</option>
                            <?php
                            $current_year = date('Y');
                            // Start from 5 years ago, up to current year
                            $start_year_dropdown = $current_year - 5;
                            $end_year_dropdown = $current_year;

                            for ($y = $end_year_dropdown; $y >= $start_year_dropdown; $y--) {
                                $selected = ($filter_tahun == $y) ? 'selected' : '';
                                echo "<option value=\"$y\" $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex flex-row gap-2 w-full md:w-auto">
                        <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition duration-300 flex-grow sm:flex-grow-0 flex items-center justify-center shadow-md">
                            <i class="fas fa-filter mr-2"></i> Terapkan
                        </button>
                        <a href="laporan.php" class="bg-gray-400 text-white px-6 py-2.5 rounded-lg hover:bg-gray-500 transition duration-300 flex-grow sm:flex-grow-0 flex items-center justify-center shadow-md">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>


            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">No.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">NIK Anak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Anak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Tgl. Lahir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Ibu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Ayah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Alamat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result_laporan) > 0) {
                            while ($row = mysqli_fetch_assoc($result_laporan)) {
                                ?>
                                <tr class="hover:bg-green-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nik_anak']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_anak']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars(date('d F Y', strtotime($row['tgl_lahir']))) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_ibu']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_ayah']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['alamat']) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center font-semibold">
                                    <i class="fas fa-info-circle mr-2"></i> Tidak ada data anak yang melakukan imunisasi pada periode ini.
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out flex items-center no-print">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan
                </button>
            </div>

        </div>
    </main>

    <footer class="bg-white border-t border-green-200 py-6 no-print">
        <div class="text-center text-sm text-gray-600">
            &copy; <?= date('Y') ?> Posyandu Bina Cita. Semua Hak Dilindungi.
        </div>
    </footer>

</body>
</html>
