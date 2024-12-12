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

// Ambil ID daftar poli dari parameter
$id_daftar_poli = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_daftar_poli) {
    echo "<script>
        alert('ID pasien tidak ditemukan');
        window.location.href='periksa.php';
    </script>";
    exit;
}

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

// Ambil data obat
$obatQuery = mysqli_query($mysqli, "SELECT * FROM obat");
$obatList = [];
while ($row = mysqli_fetch_assoc($obatQuery)) {
    $obatList[] = $row;
}

// Tangani pengiriman form
if (isset($_POST['submit'])) {
    $catatan = mysqli_real_escape_string($mysqli, $_POST['catatan']);
    $resep = $_POST['resep'];
    $biaya_periksa = 150000; // Biaya pemeriksaan tetap 150.000
    $biaya_obat = 0;

    foreach ($resep as $id_obat) {
        $obatData = mysqli_query($mysqli, "SELECT harga FROM obat WHERE id = '$id_obat'");
        $hargaObat = mysqli_fetch_assoc($obatData)['harga'];
        $biaya_obat += $hargaObat;
    }

    $total_biaya = $biaya_periksa + $biaya_obat; // Total biaya pemeriksaan + obat

    $tgl_periksa = date('Y-m-d H:i:s');

    // Simpan ke tabel periksa, gabungkan biaya_periksa dan biaya_obat ke dalam kolom biaya_periksa
    $insertPeriksa = mysqli_query($mysqli, "
        INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa, biaya_obat, total_biaya) 
        VALUES ('$id_daftar_poli', '$tgl_periksa', '$catatan', '$biaya_periksa', '$biaya_obat', '$total_biaya')
    ");

    // Perbarui status pasien di tabel daftar_poli
    $updateStatus = mysqli_query($mysqli, "UPDATE daftar_poli SET status_periksa = 'sudah diperiksa' WHERE id = '$id_daftar_poli'");

    if ($insertPeriksa && $updateStatus) {
        // Redirect ke halaman rincian biaya
        header("Location: biayaPeriksa.php?id=$id_daftar_poli&total_biaya=$total_biaya&catatan=" . urlencode($catatan));
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: block;
        }

        .container {
            max-width: 1000px;
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

        .section-title {
            color: #0056b3;
            font-weight: 600;
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #0056b3;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 30%;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0,86,179,0.25);
        }

        textarea.form-control {
            min-height: 120px;
        }

        .btn {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003d82;
            transform: translateY(-2px);
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

        .text-muted {
            font-size: 0.875rem;
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
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <i class="fas fa-stethoscope"></i>
        <h1>Pemeriksaan Pasien</h1>
        <p>Form pemeriksaan dan pemberian resep</p>
    </div>

    <div class="container">
        <a href="periksa.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pasien
        </a>

        <!-- Card content remains the same -->
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-stethoscope me-2"></i>
                    Pemeriksaan Pasien
                </h2>
            </div>
            <div class="card-body">
                <!-- Detail Pasien -->
                <h4>Detail Pasien</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Nama</th>
                        <td><?= $pasien['nama']; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= $pasien['alamat']; ?></td>
                    </tr>
                    <tr>
                        <th>Keluhan</th>
                        <td><?= $pasien['keluhan']; ?></td>
                    </tr>
                </table>

                <!-- Form Pemeriksaan -->
                <h4>Form Pemeriksaan</h4>
                <form method="POST">
                    <div class="form-group">
                        <label for="catatan">Catatan Dokter:</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="resep">Pilih Obat (bisa lebih dari satu):</label>
                        <select class="form-control" id="resep" name="resep[]" multiple>
                            <?php
                            // Ambil data obat dari tabel obat
                            $obatQuery = mysqli_query($mysqli, "SELECT * FROM obat");
                            while ($obat = mysqli_fetch_assoc($obatQuery)) {
                                echo "<option value='{$obat['id']}'>{$obat['nama_obat']} - {$obat['kemasan']} (Rp " . number_format($obat['harga'], 0, ',', '.') . ")</option>";
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">Tekan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</small>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
                </form>
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
