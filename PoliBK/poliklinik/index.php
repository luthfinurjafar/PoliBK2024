<?php
if (!isset($_SESSION)) {
  session_start();
}
include_once("koneksi.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Poliklinik BK - Layanan Kesehatan Terdepan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-o8Cw+TcbsG7EiODyEfzIqXTYHlg+HTkKYQOeUbKoJ9syPZDy6W1hWkRMT4kFOTPM7VR2FZiyUpxVK7hJhtrGAA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #005c9c;
    }

    .navbar-brand {
      color: white;
      font-weight: bold;
      font-size: 1.5rem;
    }

    .navbar a {
      color: white;
      font-size: 1rem;
    }

    .hero {
      height: 100vh;
      background: #0c2d55;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: bold;
      animation: fadeInDown 2s;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      animation: fadeInUp 2s;
    }

    .btn {
      padding: 0.8rem 2rem;
      border-radius: 50px;
      margin: 0.5rem;
      font-size: 1.2rem;
      animation: fadeIn 2.5s;
    }

    .btn-success:hover,
    .btn-primary:hover,
    .btn-secondary:hover {
      transform: scale(1.1);
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }

    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
    }

    .about-section,
    .services-section {
      padding: 4rem 2rem;
      background-color: #f1f1f1;
    }

    .about-section h2,
    .services-section h2 {
      font-size: 2.5rem;
      color: #00264d;
      margin-bottom: 1rem;
    }

    .about-section p,
    .services-section p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #4a4a4a;
    }

    .services-section .service {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }


    .contact-section {
      background-color: #00264d;
      color: white;
      padding: 4rem 2rem;
    }

    .contact-section h2 {
      font-size: 2.5rem;
      color: #f8f9fa;
      margin-bottom: 1rem;
    }

    .contact-section a {
      color: #ffdd57;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background-color: #001f3f;
      color: white;
      font-size: 0.9rem;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">Poliklinik BK</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero">
    <h1>Selamat Datang di Poliklinik BK</h1>
    <p>Pilih login sesuai dengan peran Anda</p>
    <div>
      <a href="loginPasien.php" class="btn btn-success">Login Pasien</a>
      <a href="loginDokter.php" class="btn btn-primary">Login Dokter</a>
    </div>
  </div>

  <!-- About Section -->
  <div class="about-section text-center">
    <h2>Tentang Kami</h2>
    <p>
      Poliklinik BK adalah layanan kesehatan modern yang didukung oleh teknologi canggih dan tim medis profesional.
      Kami berdedikasi untuk memberikan perawatan terbaik bagi pasien dengan standar internasional.
    </p>
  </div>


  <!-- Contact Section -->
  <div class="contact-section text-center">
    <h2>Hubungi Kami</h2>
    <p>
      <strong>Alamat:</strong> Jalan Kesehatan No. 123, Semarang, Jawa Tengah<br>
      <strong>Telepon:</strong> (024) 123-4567<br>
      <strong>Email:</strong> <a href="mailto:info@poliklinikbk.com">info@poliklinikbk.com</a>
    </p>
    <p>
      <strong>Jam Operasional:</strong><br>
      Senin - Jumat: 08.00 - 17.00 WIB<br>
      Sabtu: 08.00 - 12.00 WIB<br>
      Minggu: Libur
    </p>
  </div>

  <!-- Footer -->
  <footer>
    &copy; 2024 Poliklinik BK. All Rights Reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
