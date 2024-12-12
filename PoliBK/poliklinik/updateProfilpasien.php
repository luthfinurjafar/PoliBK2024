<?php
if (!isset($_SESSION)) {
    session_start();
}

// Pastikan pengguna telah login
if (!isset($_SESSION['id_pasien'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); document.location='loginPasien.php';</script>";
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

// Proses pembaruan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    $update_stmt = $mysqli->prepare("UPDATE pasien SET nama = ?, alamat = ?, no_hp = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $nama, $alamat, $no_hp, $id_pasien);

    if ($update_stmt->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!'); document.location='dashboardPasien.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profil Pasien</title>
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

        .form-container {
            max-width: 600px;
            margin: 0 auto 50px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0,86,179,0.25);
        }

        .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003f77;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin: 20px;
            color: #0056b3;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            color: #003f77;
            text-decoration: none;
        }

        .footer {
            background-color: #0056b3;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ccc;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Update Profil Pasien</h1>
        <p>Perbarui informasi pribadi Anda</p>
    </div>

    <a href="dashboardPasien.php" class="back-link">
        &larr; Kembali ke Dashboard
    </a>

    <div class="container">
        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" 
                           value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" 
                              rows="3" required><?php echo htmlspecialchars($row['alamat']); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" 
                           value="<?php echo htmlspecialchars($row['no_hp']); ?>" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="dashboardPasien.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
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
