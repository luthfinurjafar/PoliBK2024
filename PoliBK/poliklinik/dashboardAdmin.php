<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: loginUser.php"); // Arahkan ke halaman login jika belum login
    exit();
}

$username = $_SESSION['username'];

// Koneksi ke database
include 'koneksi.php'; // Pastikan file koneksi.php ada dan berisi koneksi yang benar

// Ambil jumlah dokter, pasien, poli, dan obat
$query_dokter = "SELECT COUNT(*) as total_dokter FROM dokter";
$query_pasien = "SELECT COUNT(*) as total_pasien FROM pasien";
$query_poli = "SELECT COUNT(*) as total_poli FROM poli";
$query_obat = "SELECT COUNT(*) as total_obat FROM obat";

$dokter = $mysqli->query($query_dokter)->fetch_assoc();
$pasien = $mysqli->query($query_pasien)->fetch_assoc();
$poli = $mysqli->query($query_poli)->fetch_assoc();
$obat = $mysqli->query($query_obat)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #0056b3;
            padding: 15px 0;
        }

        .navbar-brand {
            color: white !important;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
        }

        .header {
            background-color: #0056b3;
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
        }

        .stat-box i {
            font-size: 2.5rem;
            color: #0056b3;
            margin-bottom: 15px;
        }

        .stat-box h4 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .stat-box p {
            color: #0056b3;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .container {
            margin-bottom: 100px;
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .stat-box {
                padding: 15px;
            }
            
            .stat-box h4 {
                font-size: 1rem;
            }
            
            .stat-box p {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboardAdmin.php">
                <i class="fas fa-hospital-user me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="poli.php"><i class="fas fa-clinic-medical me-1"></i>Poli</a></li>
                    <li class="nav-item"><a class="nav-link" href="pasien.php"><i class="fas fa-users me-1"></i>Pasien</a></li>
                    <li class="nav-item"><a class="nav-link" href="dokter.php"><i class="fas fa-user-md me-1"></i>Dokter</a></li>
                    <li class="nav-item"><a class="nav-link" href="Obat.php"><i class="fas fa-pills me-1"></i>Obat</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-chart-bar me-2"></i>Dashboard Admin</h1>
        <p>Selamat datang di Panel Admin Poliklinik</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-user-md"></i>
                    <h4>Total Dokter</h4>
                    <p><?php echo $dokter['total_dokter']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-users"></i>
                    <h4>Total Pasien</h4>
                    <p><?php echo $pasien['total_pasien']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-clinic-medical"></i>
                    <h4>Total Poli</h4>
                    <p><?php echo $poli['total_poli']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-pills"></i>
                    <h4>Total Obat</h4>
                    <p><?php echo $obat['total_obat']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php" class="text-white">Kebijakan Privasi</a> | <a href="terms.php" class="text-white">Syarat dan Ketentuan</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
