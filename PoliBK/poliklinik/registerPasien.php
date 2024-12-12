<?php
// Mulai session
if (!isset($_SESSION)) session_start();

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

// Mengecek koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$error = "";
$success = "";

// Proses pendaftaran pasien
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    // Format tahun dan bulan saat pendaftaran
    $currentMonthYear = date('Ym'); // Format: YYYYMM, contoh: 202411

    // Query untuk menghitung jumlah pasien yang terdaftar pada bulan dan tahun yang sama
    $query = "SELECT COUNT(*) as total FROM pasien WHERE DATE_FORMAT(tanggal_daftar, '%Y%m') = '$currentMonthYear'";
    $result = $mysqli->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $totalPasien = $row['total'];

        // Membuat nomor RM dengan format tahunbulan-001 (3 digit)
        $no_rm = $currentMonthYear . '-' . str_pad($totalPasien + 1, 3, '0', STR_PAD_LEFT);

        // Query untuk menyimpan data pasien
        $stmt = $mysqli->prepare("INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);

        if ($stmt->execute()) {
            $success = "Pasien berhasil terdaftar. No RM: " . $no_rm;
        } else {
            $error = "Gagal mendaftar pasien.";
        }

        $stmt->close();
    } else {
        $error = "Terjadi kesalahan saat mengambil data pasien.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container" style="margin-top: 2rem;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center" style="font-weight: bold; font-size: 32px; background-color: #005c9c; color: #fff;">
                        Pendaftaran Pasien
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <label for="nama">Nama Pasien</label>
                                <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama pasien">
                            </div>
                            <div class="form-group mb-3">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" required placeholder="Masukkan alamat pasien">
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_ktp">No KTP</label>
                                <input type="text" name="no_ktp" class="form-control" required placeholder="Masukkan no KTP pasien">
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_hp">No HP</label>
                                <input type="text" name="no_hp" class="form-control" required placeholder="Masukkan no HP pasien">
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Daftar Pasien</button>
                            </div>
                        </form>
                    </div>
                    <a href="loginPasien.php" class="btn btn-primary mt-2">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
