<?php
session_start(); // Memulai session

// Pastikan pengguna telah login
if (!isset($_SESSION['id_pasien'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); document.location='index.php';</script>";
    exit;
}

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Ambil informasi pasien
$id_pasien = $_SESSION['id_pasien'];
$stmt = $mysqli->prepare("SELECT * FROM pasien WHERE id = ?");
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('Data pasien tidak ditemukan!'); document.location='loginPasien.php';</script>";
    exit;
}

// Proses logout jika tombol logout ditekan
if (isset($_POST['logout'])) {
    // Hapus semua variabel session
    session_unset();
    // Hapus session
    session_destroy();
    // Arahkan pengguna ke halaman login
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header, .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px;
        }

        .header h1, .footer h5 {
            margin: 0;
        }

        

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .card-title {
            font-weight: 600;
        }

        .card-body p {
            font-size: 1rem;
            color: #495057;
        }

        .card-group {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-group .card {
            max-width: 350px;
            margin: 10px;
        }

        .btn {
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-primary:hover {
            background-color: #003f77;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
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
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header text-center">
        <h1>Dashboard Pasien</h1>
        <p>Selamat datang di sistem layanan kesehatan kami</p>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h1 class="header-title">Selamat Datang, <?php echo htmlspecialchars($row['nama']); ?>!</h1>
                <p class="sub-header">Nomor Rekam Medis (RM): <?php echo htmlspecialchars($row['no_rm']); ?></p>
                <hr>
            </div>
        </div>

        <!-- Card Group -->
        <div class="card-group">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daftar Poli</h5>
                    <p class="card-text">Lihat jadwal Dokter atau pemeriksaan Anda.</p>
                    <a href="cekjadwal.php" class="btn btn-primary">Cek Jadwal</a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Riwayat Periksa</h5>
                    <p class="card-text">Lihat riwayat pemeriksaan medis Anda.</p>
                    <a href="rincianBiayaPasien.php" class="btn btn-primary">Lihat Riwayat</a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Update Profil</h5>
                    <p class="card-text">Perbarui informasi pribadi Anda.</p>
                    <a href="updateProfilpasien.php" class="btn btn-primary">Perbarui Profil</a>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="row mt-5 text-center">
            <div class="col-12">
                <form method="POST">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
