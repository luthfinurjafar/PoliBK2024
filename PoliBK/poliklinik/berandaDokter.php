<?php
session_start();
include_once("koneksi.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header, .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-title {
            font-weight: 600;
        }

        .btn {
            border-radius: 50px;
            padding: 10px 30px;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003f77;
        }

        .footer {
            margin-top: 50px;
        }

        .footer a {
            color: white;
        }

        .footer a:hover {
            color: #c82333;
        }

        .icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #0056b3;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Dashboard Dokter</h1>
        <p>Selamat datang di sistem layanan kesehatan kami</p>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <?php if (isset($_SESSION['nip'])): ?>
            <div class="row text-center mb-4">
                <div class="col-12">
                    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
                    <hr>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-alt icon"></i>
                            <h5 class="card-title">Jadwal Praktik</h5>
                            <p class="card-text">Atur jadwal praktik Anda</p>
                            <a href="aturJadwalDokter.php" class="btn btn-primary">Atur Jadwal</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-stethoscope icon"></i>
                            <h5 class="card-title">Periksa Pasien</h5>
                            <p class="card-text">Periksa pasien yang terdaftar</p>
                            <a href="periksa.php" class="btn btn-primary">Periksa</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-history icon"></i>
                            <h5 class="card-title">Riwayat Pasien</h5>
                            <p class="card-text">Lihat riwayat pemeriksaan</p>
                            <a href="riwayat.php" class="btn btn-primary">Lihat Riwayat</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="row mt-5 text-center">
                <div class="col-12">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                Silakan Login untuk Mengakses Dashboard
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>

</html>
