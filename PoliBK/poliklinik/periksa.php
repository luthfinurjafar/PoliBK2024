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

$id_dokter = $_SESSION['id'];  // Ambil ID dokter dari session

// Aksi hapus pasien
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_pasien = mysqli_query($mysqli, "DELETE FROM daftar_poli WHERE id = '$id'");
    if ($delete_pasien) {
        echo "<script>
            alert('Pasien berhasil dihapus');
            window.location.href='periksa.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus pasien');
        </script>";
    }
}

// Tangani pengiriman form
if (isset($_POST['submit'])) {
    $catatan = mysqli_real_escape_string($mysqli, $_POST['catatan']);
    $resep = $_POST['resep'];
    $biaya_periksa = 150000; // Biaya pemeriksaan tetap
    $biaya_obat = 0;

    // Hitung biaya obat berdasarkan resep yang dipilih
    foreach ($resep as $id_obat) {
        $obatData = mysqli_query($mysqli, "SELECT harga FROM obat WHERE id = '$id_obat'");
        $hargaObat = mysqli_fetch_assoc($obatData)['harga'];
        $biaya_obat += $hargaObat;
    }

    // Total biaya (biaya pemeriksaan + biaya obat)
    $total_biaya = $biaya_periksa + $biaya_obat;
    $tgl_periksa = date('Y-m-d H:i:s');

    // Ambil ID pasien dari parameter URL
    $id_daftar_poli = $_GET['id'];

    // Simpan ke tabel periksa
    $insertPeriksa = mysqli_query($mysqli, "
        INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa, biaya_obat, total_biaya) 
        VALUES ('$id_daftar_poli', '$tgl_periksa', '$catatan', '$biaya_periksa', '$biaya_obat', '$total_biaya')
    ");

    // Perbarui status pasien di tabel daftar_poli
    $updateStatus = mysqli_query($mysqli, "UPDATE daftar_poli SET status_periksa = 'sudah diperiksa' WHERE id = '$id_daftar_poli'");

    if ($insertPeriksa && $updateStatus) {
        // Redirect ke halaman rincian biaya
        header("Location: biayaPeriksa.php?id=$id_daftar_poli&biaya_periksa=$total_biaya&catatan=" . urlencode($catatan));
        exit;
    } else {
        echo "<script>
            alert('Terjadi kesalahan saat menyimpan data pemeriksaan');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periksa Pasien - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 15px 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #0056b3;
            color: white;
            font-weight: 500;
            border: none;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .btn {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
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

        .footer {
            margin-top: 50px;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .footer a:hover {
            color: #c82333;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .btn {
                padding: 6px 15px;
                font-size: 14px;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Daftar Pasien</h1>
        <p>Kelola daftar pasien yang perlu diperiksa</p>
    </div>

    <div class="container">
        <a href="berandaDokter.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Pasien yang Terdaftar</h5>
            </div>
            <div class="card-body">
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
                            // Query untuk mengambil pasien yang terdaftar pada poli dokter yang login
                            $query = "
                                SELECT daftar_poli.*, pasien.nama AS nama, 
                                       jadwal_dokter.hari, jadwal_dokter.jam_mulai, jadwal_dokter.jam_selesai 
                                FROM daftar_poli 
                                JOIN pasien ON daftar_poli.id_pasien = pasien.id
                                JOIN jadwal_dokter ON daftar_poli.id_jadwal = jadwal_dokter.id
                                WHERE jadwal_dokter.id_dokter = '$id_dokter'
                                ORDER BY daftar_poli.no_antrian ASC
                            ";

                            // Eksekusi query
                            $result = mysqli_query($mysqli, $query);

                            // Cek apakah query berhasil
                            if (!$result) {
                                echo "<tr><td colspan='8' class='text-center'>Gagal mengambil data pasien: " . mysqli_error($mysqli) . "</td></tr>";
                            } elseif (mysqli_num_rows($result) == 0) {
                                echo "<tr><td colspan='8' class='text-center'>Tidak ada pasien yang terdaftar.</td></tr>";
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
                                            <td>";
                                    // Menampilkan status pemeriksaan
                                    if ($row['status_periksa'] === 'belum diperiksa') {
                                        echo "<span class='badge badge-warning'>Belum Diperiksa</span>";
                                    } else {
                                        echo "<span class='badge badge-success'>Sudah Diperiksa</span>";
                                    }
                                    echo "</td>
                                            <td>";
                                    // Aksi untuk periksa dan hapus pasien
                                    if ($row['status_periksa'] === 'belum diperiksa') {
                                        echo "<a href='periksaPasien.php?id={$row['id']}' class='btn btn-success btn-sm'>Periksa</a>
                                              <a href='periksa.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus pasien ini?\")'>Hapus</a>";
                                    } else {
                                        echo "<span class='text-muted'>✅️</span>";
                                    }
                                    echo "</td>
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
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>
</html>
