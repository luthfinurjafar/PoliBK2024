<?php
session_start();
include_once("koneksi.php");

// Cek apakah pasien sudah login
if (!isset($_SESSION['id_pasien'])) {
    echo "<script>
        alert('Anda harus login sebagai pasien terlebih dahulu');
        window.location.href = 'loginPasien.php';
    </script>";
    exit;
}

$id_pasien = $_SESSION['id_pasien'];

// Ambil data pemeriksaan berdasarkan pasien
$query = mysqli_query($mysqli, "
    SELECT 
        daftar_poli.tanggal AS tgl_periksa,
        poli.nama_poli,
        dokter.nama AS nama_dokter,
        periksa.biaya_periksa,
        periksa.biaya_obat,
        periksa.total_biaya,
        periksa.catatan
    FROM periksa
    JOIN daftar_poli ON periksa.id_daftar_poli = daftar_poli.id
    JOIN jadwal_dokter ON daftar_poli.id_jadwal = jadwal_dokter.id
    JOIN dokter ON jadwal_dokter.id_dokter = dokter.id
    JOIN poli ON dokter.id_poli = poli.id
    WHERE daftar_poli.id_pasien = '$id_pasien'
    ORDER BY daftar_poli.tanggal DESC
");


if (!$query || mysqli_num_rows($query) === 0) {
    echo "<script>
        alert('Tidak ada data pemeriksaan ditemukan');
        window.location.href = 'dashboardPasien.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Biaya Pemeriksaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header, .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px;
        }

        .header h1, .footer h5 {
            margin: 0;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
        }

        .footer a {
            color: white;
        }

        .footer a:hover {
            color: #c82333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: #0056b3;
            color: white;
        }

        .table th {
            font-weight: 600;
            padding: 15px;
            border: none;
        }

        .table td {
            padding: 12px 15px;
            border-color: #eee;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #003f77;
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #0056b3;
            text-decoration: none;
            font-weight: 600;
        }

        .back-button:hover {
            color: #003f77;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .table-responsive {
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Riwayat Pemeriksaan</h1>
        <p>Rincian biaya dan catatan pemeriksaan Anda</p>
    </div>

    <div class="container">
        <a href="dashboardPasien.php" class="back-button">
            &larr; Kembali ke Dashboard
        </a>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal Periksa</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Biaya Pemeriksaan</th>
                            <th>Biaya Obat</th>
                            <th>Total Biaya</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tgl_periksa']); ?></td>
                                <td><?= htmlspecialchars($row['nama_poli']); ?></td>
                                <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                                <td>Rp <?= number_format($row['biaya_periksa'], 0, ',', '.'); ?></td>
                                <td>Rp <?= number_format($row['biaya_obat'], 0, ',', '.'); ?></td>
                                <td class="fw-bold text-primary">
                                    Rp <?= number_format($row['total_biaya'], 0, ',', '.'); ?>
                                </td>
                                <td><?= htmlspecialchars($row['catatan']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 <!-- Footer -->
 <div class="footer">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
