<?php
session_start(); // Memulai sesi

// Koneksi database
$databaseHost = 'localhost';
$databaseName = 'poli_bk';
$databaseUsername = 'root';
$databasePassword = '';
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Cek apakah koneksi berhasil
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$success_message = $error = "";

// Periksa apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keluhan = $_POST['keluhan'];
    $id_jadwal = $_POST['id_jadwal'];
    $id_pasien = $_SESSION['id_pasien']; // Mengambil id_pasien dari sesi

    // Cek apakah jadwal dipilih
    if (empty($id_jadwal)) {
        $error = "Jadwal belum dipilih atau tidak tersedia.";
    } else {
        // Hitung nomor antrian berdasarkan jumlah pasien yang sudah mendaftar untuk jadwal ini
        $query_antrian = "SELECT COUNT(*) AS antrian FROM daftar_poli WHERE id_jadwal = '$id_jadwal'";
        $result_antrian = mysqli_query($mysqli, $query_antrian);
        $row_antrian = mysqli_fetch_assoc($result_antrian);
        $no_antrian = $row_antrian['antrian'] + 1; // Nomor antrian bertambah 1

        // Masukkan pendaftaran poli baru ke tabel daftar_poli
        $insert_query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, tanggal, status_periksa) 
                         VALUES ('$id_pasien', '$id_jadwal', '$keluhan', '$no_antrian', NOW(), 'belum diperiksa')";

        if (mysqli_query($mysqli, $insert_query)) {
            // Redirect ke halaman yang sama dengan status sukses di URL
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&no_antrian=$no_antrian");
            exit; // Pastikan skrip tidak dilanjutkan setelah redirect
        } else {
            $error = "Pendaftaran gagal.";
        }
    }
}

// Ambil data poli
$polies = mysqli_query($mysqli, "SELECT * FROM poli");

// Cek status di URL untuk menampilkan pesan
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $success_message = "Pendaftaran berhasil. Nomor antrian: " . $_GET['no_antrian'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Rawat Jalan</title>
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
            max-width: 800px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px auto;
        }

        .form-label {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
        }

        .form-select:focus, .form-control:focus {
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

        .alert {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
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
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pendaftaran Rawat Jalan</h1>
        <p>Silakan lengkapi form pendaftaran di bawah ini</p>
    </div>

    <a href="dashboardPasien.php" class="back-link">
        &larr; Kembali ke Dashboard
    </a>

    <div class="container">
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <div class="text-center">
                    <h4 class="alert-heading mb-3">Pendaftaran Berhasil!</h4>
                    <p><?= $success_message; ?></p>
                    <hr>
                    <div class="mt-3">
                        <a href="dashboardPasien.php" class="btn btn-primary">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="id_poli" class="form-label">Poli</label>
                <select class="form-select" id="id_poli" name="id_poli" required>
                    <option value="">Pilih Poli</option>
                    <?php while ($row = mysqli_fetch_assoc($polies)) { ?>
                        <option value="<?= $row['id']; ?>"><?= $row['nama_poli']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_dokter" class="form-label">Dokter</label>
                <select class="form-select" id="id_dokter" name="id_dokter" required>
                    <option>Pilih Dokter...</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_jadwal" class="form-label">Jadwal</label>
                <select class="form-select" id="id_jadwal" name="id_jadwal" required>
                    <option>Pilih Jadwal...</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="keluhan" class="form-label">Keluhan</label>
                <textarea class="form-control" id="keluhan" name="keluhan" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript code tetap sama seperti sebelumnya
        document.addEventListener("DOMContentLoaded", function() {
            const poliSelect = document.getElementById('id_poli');
            const dokterSelect = document.getElementById('id_dokter');
            const jadwalSelect = document.getElementById('id_jadwal');

            poliSelect.addEventListener('change', function() {
                const poliId = poliSelect.value;
                dokterSelect.innerHTML = '<option>Pilih Dokter...</option>';
                jadwalSelect.innerHTML = '<option>Pilih Jadwal...</option>';

                if (poliId) {
                    fetch('get_dokter.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_poli=' + poliId
                    })
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(dokter => {
                            const option = document.createElement('option');
                            option.value = dokter.id;
                            option.textContent = dokter.nama;
                            dokterSelect.appendChild(option);
                        });
                    });
                }
            });

            dokterSelect.addEventListener('change', function() {
                const dokterId = dokterSelect.value;
                jadwalSelect.innerHTML = '<option>Pilih Jadwal...</option>';

                if (dokterId) {
                    fetch('get_jadwal.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_dokter=' + dokterId
                    })
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(jadwal => {
                            const option = document.createElement('option');
                            option.value = jadwal.id;
                            option.textContent = `${jadwal.hari} - ${jadwal.jam_mulai} - ${jadwal.status_text}`;
                            option.classList.add(jadwal.status_class);

                            if (jadwal.status_class === 'text-danger') {
                                option.disabled = true;
                            }

                            jadwalSelect.appendChild(option);
                        });
                    });
                }
            });
        });

        
    </script>
     <!-- Footer -->
     <div class="footer">
        <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
        <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
    </div>
</body>
</html>
