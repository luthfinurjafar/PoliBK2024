<?php 
session_start();
include_once("koneksi.php");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id']) || !isset($_SESSION['nama'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu');
        window.location.href='loginDokter.php';
    </script>";
    exit;
}

// Validasi parameter GET
if (!isset($_GET['id']) || !isset($_GET['catatan'])) {
    echo "<script>
        alert('Data tidak lengkap');
        window.location.href='periksa.php';
    </script>";
    exit;
}

$id_daftar_poli = $_GET['id'];
$catatan = $_GET['catatan'];

// Ambil data pasien berdasarkan ID
$pasienQuery = mysqli_query($mysqli, "
    SELECT daftar_poli.*, pasien.nama AS nama, pasien.alamat 
    FROM daftar_poli 
    JOIN pasien ON daftar_poli.id_pasien = pasien.id 
    WHERE daftar_poli.id = '$id_daftar_poli'
");

if (!$pasienQuery || mysqli_num_rows($pasienQuery) === 0) {
    echo "<script>
        alert('Pasien tidak ditemukan');
        window.location.href='periksa.php';
    </script>";
    exit;
}

$pasien = mysqli_fetch_assoc($pasienQuery);

// Ambil data biaya dari tabel periksa berdasarkan id_daftar_poli
$periksaQuery = mysqli_query($mysqli, "
    SELECT biaya_periksa, biaya_obat 
    FROM periksa 
    WHERE id_daftar_poli = '$id_daftar_poli'
");

if (!$periksaQuery || mysqli_num_rows($periksaQuery) === 0) {
    echo "<script>
        alert('Data pemeriksaan tidak ditemukan');
        window.location.href='periksa.php';
    </script>";
    exit;
}

$periksaData = mysqli_fetch_assoc($periksaQuery);

// Total biaya
$total_biaya = $periksaData['biaya_periksa'] + $periksaData['biaya_obat'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Biaya - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background-color: #0056b3;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 30px;
            background: white;
        }

        .card-header {
            background-color: #0056b3;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            text-align: center;
        }

        .success-icon {
            font-size: 2.5rem;
            color: #4ECB71;
            margin-bottom: 15px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 35%;
            padding: 15px;
        }

        .table td {
            padding: 15px;
        }

        .total-row {
            background-color: #f0f8ff;
        }

        .total-row th,
        .total-row td {
            color: #0056b3;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn {
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
            padding: 10px 25px;
        }

        .btn-back {
            background-color: #6c757d;
            border: none;
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .back-link {
            color: #0056b3;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin: 20px 0;
            font-weight: 500;
        }

        .back-link:hover {
            color: #003d82;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            display: none;
        }

        .print-header img {
            max-width: 80px;
            margin-bottom: 10px;
        }

        .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        @media print {
            body {
                background-color: white;
            }
            
            .print-header {
                display: block;
            }

            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none;
                border: none;
            }

            .card-header {
                background-color: white !important;
                color: black !important;
                padding: 0 !important;
            }

            .table th, .table td {
                border: 1px solid #ddd;
            }

            .total-row {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }

            .signature-section {
                margin-top: 50px;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header no-print">
        <i class="fas fa-file-invoice"></i>
        <h1>Rincian Biaya Pemeriksaan</h1>
        <p>Detail pembayaran dan catatan pemeriksaan</p>
    </div>

    <div class="container">
        <a href="periksa.php" class="back-link no-print">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>

        <!-- Print Header -->
        <div class="print-header">
            <h2>POLIKLINIK BIMBINGAN KARIR</h2>
            <p>Jl. Raya Universitas Dian Nuswantoro, Kota Semarang</p>
            <p>Telp: (021) 123456</p>
            <hr>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Rincian Biaya Pemeriksaan</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Nama Pasien</th><td><?= htmlspecialchars($pasien['nama']); ?></td></tr>
                    <tr><th>Alamat</th><td><?= htmlspecialchars($pasien['alamat']); ?></td></tr>
                    <tr><th>Biaya Pemeriksaan</th><td>Rp <?= number_format($periksaData['biaya_periksa'], 0, ',', '.'); ?></td></tr>
                    
                    <!-- Rincian Biaya Obat -->
                    <tr><th>Biaya Obat</th><td>Rp <?= number_format($periksaData['biaya_obat'], 0, ',', '.'); ?></td></tr>
                    
                    <!-- Total Biaya -->
                    <tr><th>Total Biaya</th><td>Rp <?= number_format($total_biaya, 0, ',', '.'); ?></td></tr>

                    <!-- Catatan Dokter -->
                    <tr><th>Catatan Dokter</th><td><?= htmlspecialchars($catatan); ?></td></tr>
                </table>

                <div class="text-center mt-4 mb-4">
                    <button onclick="window.print()" class="btn btn-primary no-print">
                        <i class="fas fa-print me-2"></i>
                        Cetak Rincian
                    </button>
                    <a href="periksa.php" class="btn btn-secondary btn-back no-print ms-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>

                <!-- Signature Section -->
                <div class="signature-section print-header">
                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <p>Pasien</p>
                            <br><br><br>
                            <p><?= htmlspecialchars($pasien['nama']); ?></p>
                        </div>
                        <div class="col-6 text-center">
                            <p>Dokter</p>
                            <img src="assets/img/ttd-dokter.png" alt="Tanda Tangan Dokter" class="signature-img">
                            <br><br><br>
                            <p><?= htmlspecialchars($_SESSION['nama']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer no-print">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
    </div>
</body>
</html>
