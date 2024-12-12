<?php
session_start();
include_once("koneksi.php");

// Cek apakah dokter sudah login
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='loginDokter.php';</script>";
    exit;
}

$id_dokter = $_SESSION['id'];

// Menyimpan atau mengupdate jadwal
if (isset($_POST['simpanData'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status = $_POST['status'];

    // Validasi input waktu
    if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
        echo "<script>alert('Jam mulai harus lebih awal dari jam selesai.'); window.location.href='aturjadwalDokter.php';</script>";
        exit;
    }

    // Jika status aktif dipilih, nonaktifkan jadwal lain
    if ($status == 1) {
        $stmt = $mysqli->prepare("UPDATE jadwal_dokter SET status = 0 WHERE id_dokter = ?");
        $stmt->bind_param("i", $id_dokter);
        $stmt->execute();
        $stmt->close();
    }

    if (!empty($_POST['id'])) {
        // Update jadwal
        $id = $_POST['id'];
        $stmt = $mysqli->prepare("UPDATE jadwal_dokter SET hari = ?, jam_mulai = ?, jam_selesai = ?, status = ? WHERE id = ? AND id_dokter = ?");
        $stmt->bind_param("sssiii", $hari, $jam_mulai, $jam_selesai, $status, $id, $id_dokter);
    } else {
        // Tambah jadwal baru
        $stmt = $mysqli->prepare("INSERT INTO jadwal_dokter (id_dokter, hari, jam_mulai, jam_selesai, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_dokter, $hari, $jam_mulai, $jam_selesai, $status);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil disimpan.'); window.location.href='aturjadwalDokter.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan jadwal: " . $stmt->error . "'); window.location.href='aturjadwalDokter.php';</script>";
    }
    $stmt->close();
}

// Menghapus jadwal
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $stmt = $mysqli->prepare("DELETE FROM jadwal_dokter WHERE id = ? AND id_dokter = ?");
    $stmt->bind_param("ii", $_GET['id'], $id_dokter);
    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil dihapus.'); window.location.href='aturjadwalDokter.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jadwal: " . $stmt->error . "'); window.location.href='aturjadwalDokter.php';</script>";
    }
    $stmt->close();
}

// Mendapatkan jadwal dokter
$query = "SELECT * FROM jadwal_dokter WHERE id_dokter = $id_dokter";
$result = $mysqli->query($query);

// Periksa apakah query berhasil
if (!$result) {
    die("Error pada query: " . $mysqli->error . "\nQuery: " . $query);
}

// Fetch data jadwal dokter
$jadwal = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Dokter</title>
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
            max-width: 1000px;
            margin: 30px auto;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: none;
        }

        .card-header {
            background-color: #0056b3;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }

        .card-body {
            padding: 25px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0,86,179,0.25);
        }

        .btn {
            border-radius: 50px;
            padding: 10px 30px;
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

        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .btn-warning, .btn-danger {
            padding: 8px 20px;
            margin: 0 5px;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
            color: #000;
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
            margin-bottom: 20px;
            font-weight: 500;
        }

        .back-link:hover {
            color: #003f77;
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
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Manajemen Jadwal Dokter</h1>
        <p>Atur jadwal praktik Anda di sini</p>
    </div>

    <div class="container">
        <a href="berandaDokter.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Pengaturan Jadwal</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <select name="hari" class="form-control" required>
                            <?php
                            $hari_list = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                            foreach ($hari_list as $h) {
                                $selected = (isset($_GET['hari']) && $_GET['hari'] == $h) ? 'selected' : '';
                                echo "<option value='$h' $selected>$h</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jam_mulai">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required value="<?= isset($_GET['jam_mulai']) ? $_GET['jam_mulai'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="jam_selesai">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required value="<?= isset($_GET['jam_selesai']) ? $_GET['jam_selesai'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" name="simpanData" class="btn btn-primary">Simpan</button>
                    <a href="aturjadwalDokter.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Jadwal</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal as $j) : ?>
                            <tr>
                                <td><?= $j['hari'] ?></td>
                                <td><?= $j['jam_mulai'] ?></td>
                                <td><?= $j['jam_selesai'] ?></td>
                                <td><?= $j['status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?></td>
                                <td>
                                    <a href="aturjadwalDokter.php?id=<?= $j['id'] ?>&hari=<?= $j['hari'] ?>&jam_mulai=<?= $j['jam_mulai'] ?>&jam_selesai=<?= $j['jam_selesai'] ?>&status=<?= $j['status'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                                    <a href="aturjadwalDokter.php?aksi=hapus&id=<?= $j['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>

</html>
