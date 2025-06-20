<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Halaman Edukasi Ibu - Posyandu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0fdf4; /* Slightly lighter green background */
    }
    /* Header Enhancements */
    header {
      background-color: #047857; /* A deeper emerald green */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    header .container {
      display: flex;
      justify-content: space-between; /* Space out title and button */
      align-items: center;
    }
    header h1 {
      font-size: 2.5rem; /* Larger heading */
      font-weight: 700;
      letter-spacing: -0.025em; /* Slightly tighter letter spacing */
    }
    header p {
      font-size: 1rem;
      opacity: 0.9;
    }

    /* Back to Home Button */
    .back-home-btn {
        background-color: #059669; /* Brighter emerald for the button */
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 9999px; /* Fully rounded pill shape */
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .back-home-btn:hover {
        background-color: #047857; /* Darker emerald on hover */
        transform: translateY(-2px); /* Slight lift on hover */
    }

    /* Main Section Padding */
    main {
      padding-top: 3rem;
      padding-bottom: 3rem;
    }

    /* Article Card Enhancements */
    .article-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
        border-radius: 0.75rem; /* More pronounced rounded corners */
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); /* Softer, more spread out shadow */
    }

    .article-card:hover {
        transform: translateY(-5px); /* Lift effect on hover */
        box-shadow: 0 15px 20px rgba(0, 0, 0, 0.15); /* More prominent shadow on hover */
    }

    .article-card img {
        flex-shrink: 0;
        width: 100%;
        height: 200px; /* Slightly taller images for better impact */
        object-fit: cover;
        border-top-left-radius: 0.75rem; /* Apply border-radius to image corners */
        border-top-right-radius: 0.75rem;
    }

    .article-card .p-4 {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem; /* More padding inside cards */
    }

    .article-card h2 {
        font-size: 1.25rem; /* Slightly larger title */
        font-weight: 600;
        color: #047857; /* Match header green */
        margin-bottom: 0.75rem;
    }

    .article-card p {
        font-size: 0.95rem; /* Slightly larger body text */
        line-height: 1.6;
        color: #4b5563; /* Darker gray for better readability */
        margin-bottom: 1rem; /* More space before the link */
    }

    .article-card a {
        display: inline-block;
        margin-top: auto; /* Push link to the bottom */
        color: #059669; /* A brighter emerald green for links */
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .article-card a:hover {
        text-decoration: underline;
        color: #047857; /* Darker green on hover */
    }

    /* Footer Enhancements */
    footer {
      background-color: #e0f2f7; /* Lighter, subtle blue-green for footer */
      padding: 1.5rem;
      border-top: 1px solid #d1d5db; /* Light border above footer */
      text-align: center; /* Centered footer text */
    }
    footer p {
      color: #6b7280;
      font-size: 0.9rem;
    }
  </style>
</head>
<body class="bg-green-50">

  <header class="text-white py-6 shadow">
    <div class="container mx-auto px-4 flex items-center justify-between"> <div>
        <h1 class="font-bold">Edukasi Ibu - Posyandu Bina Cita</h1>
        <p class="text-sm">Artikel kesehatan dan tumbuh kembang anak</p>
      </div>
      <a href="index.php" class="back-home-btn">Kembali ke Halaman Utama</a>
    </div>
  </header>

  <main class="container mx-auto px-4 py-8">
    <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-8"> <div class="rounded-xl article-card">
        <img src="https://imgur.com/PNydUvi.jpg" alt="Pentingnya ASI Eksklusif" class="object-cover">
        <div class="p-4">
          <h2 class="font-bold">Pentingnya ASI Eksklusif</h2>
          <p class="text-gray-700">ASI eksklusif sangat penting untuk meningkatkan imun dan perkembangan bayi secara optimal.</p>
          <a href="https://upk.kemkes.go.id/new/ketahui-manfaat-asi-eksklusif-bagi-bayi-dan-ibu">Baca Selengkapnya</a>
        </div>
      </div>

      <div class="rounded-xl article-card">
        <img src="https://imgur.com/lZWI2Sm.jpg" alt="Gizi Anak" class="object-cover">
        <div class="p-4">
          <h2 class="font-bold">Gizi Seimbang untuk Balita</h2>
          <p class="text-gray-700">Penuhi kebutuhan gizi harian anak agar tumbuh sehat dan aktif sejak usia dini.</p>
          <a href="https://ayosehat.kemkes.go.id/list-perangkat-ajar/makan-gizi-seimbang">Baca Selengkapnya</a>
        </div>
      </div>

      <div class="rounded-xl article-card">
        <img src="https://imgur.com/L1Hk8i8.jpg" alt="Imunisasi" class="object-cover">
        <div class="p-4">
          <h2 class="font-bold">Imunisasi Lengkap untuk Perlindungan Optimal </h2>
          <p class="text-gray-700">Kenali jadwal imunisasi lengkap dan manfaatnya untuk mencegah berbagai penyakit.</p>
          <a href="https://primaku.com/berita/momdad--yuk-kenali-jenis-posyandu-beserta-manfaatnya-">Baca Selengkapnya</a>
        </div>
      </div>

      <div class="rounded-xl article-card">
        <img src="https://imgur.com/P7b0GKS.jpg" alt="Pola Asuh" class="object-cover">
        <div class="p-4">
          <h2 class="font-bold">Pola Asuh Positif</h2>
          <p class="text-gray-700">Pola asuh yang hangat dan tegas akan membantu anak tumbuh percaya diri dan mandiri.</p>
          <a href="https://hellosehat.com/parenting/anak-6-sampai-9-tahun/perkembangan-anak/pengasuhan-positif-parenting/">Baca Selengkapnya</a>
        </div>
      </div>

    </div>
  </main>

  <footer>
    <p>&copy; 2025 Posyandu Bina Cita</p>
  </footer>

</body>
</html>
