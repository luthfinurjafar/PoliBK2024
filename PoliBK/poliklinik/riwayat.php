<?php
session_start();
include_once("koneksi.php");

// Cek apakah pengguna sudah login sebagai dokter
if (!isset($_SESSION['id']) || !isset($_SESSION['nama'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu');
        window.location.href='loginDokter.php';
    </script>";
    exit;
}

$id_dokter = $_SESSION['id'];

// Perbaikan fungsi hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus data dari tabel periksa terlebih dahulu (karena ada foreign key)
    $delete_periksa = mysqli_query($mysqli, "DELETE FROM periksa WHERE id_daftar_poli = '$id'");
    
    // Setelah itu baru hapus dari tabel daftar_poli
    $delete_pasien = mysqli_query($mysqli, "DELETE FROM daftar_poli WHERE id = '$id'");
    
    if ($delete_periksa && $delete_pasien) {
        echo "<script>
            alert('Data berhasil dihapus');
            window.location.href='riwayat.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
            window.location.href='riwayat.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pasien - Poliklinik</title>
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
            max-width: 1200px;
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
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 15px;
        }

        .badge-success {
            background-color: #4ECB71;
            color: white;
            padding: 8px 15px;
            border-radius: 50px;
        }

        .btn-danger {
            background-color: #FF6B6B;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
        }

        .back-link {
            color: #0056b3;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin: 20px 0;
            font-weight: 500;
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

        .footer a:hover {
            color: #ccc;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <i class="fas fa-history"></i>
        <h1>Riwayat Pemeriksaan Pasien</h1>
        <p>Daftar pasien yang telah diperiksa</p>
    </div>

    <div class="container">
        <a href="berandaDokter.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali ke Dashboard
        </a>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Pemeriksaan Pasien
                </h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>No. Antrian</th>
                                <th>Keluhan</th>
                                <th>Hari</th>
                                <th>Jam Periksa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "
                                SELECT daftar_poli.*, pasien.nama AS nama, 
                                       jadwal_dokter.hari, jadwal_dokter.jam_mulai, jadwal_dokter.jam_selesai 
                                FROM daftar_poli 
                                JOIN pasien ON daftar_poli.id_pasien = pasien.id
                                JOIN jadwal_dokter ON daftar_poli.id_jadwal = jadwal_dokter.id
                                WHERE jadwal_dokter.id_dokter = '$id_dokter' AND daftar_poli.status_periksa = 'sudah diperiksa' 
                                ORDER BY daftar_poli.id ASC
                            ";

                            $result = mysqli_query($mysqli, $query);

                            if (!$result) {
                                echo "<tr><td colspan='8' class='text-center'>Gagal mengambil data pasien: " . mysqli_error($mysqli) . "</td></tr>";
                            } elseif (mysqli_num_rows($result) == 0) {
                                echo "<tr><td colspan='8' class='text-center'>Tidak ada pasien yang sudah diperiksa.</td></tr>";
                            } else {
                                $no = 1;
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['nama']}</td>
                                            <td>{$row['no_antrian']}</td>
                                            <td>{$row['keluhan']}</td>
                                            <td>{$row['hari']}</td>
                                            <td>{$row['jam_mulai']} - {$row['jam_selesai']}</td>
                                            <td><span class='badge badge-success'>Sudah Diperiksa</span></td>
                                            <td>
                                                <a href='riwayat.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus pasien ini?\")'>
                                                    <i class='fas fa-trash me-1'></i>Hapus
                                                </a>
                                            </td>
                                        </tr>";
                                    $no++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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
