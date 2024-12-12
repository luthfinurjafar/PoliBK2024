<?php 
// Include database connection
include 'koneksi.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: loginUser.php");
    exit;
}

if (isset($_POST['simpanData'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $hashed_ktp = password_hash($no_ktp, PASSWORD_DEFAULT);

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE pasien SET nama='$nama', alamat='$alamat', no_ktp='$hashed_ktp', no_hp='$no_hp' WHERE id='$id'";
        $edit = mysqli_query($mysqli, $sql);

        echo "<script> 
                alert('Data berhasil diupdate.');
                document.location='pasien.php';
              </script>";
    } else {
        $result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM pasien");
        $row = mysqli_fetch_assoc($result);
        $totalPasien = $row['total'];

        $no_rm = date('Y-m-d') . '-' . ($totalPasien + 1);
        $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES ('$nama', '$alamat', '$hashed_ktp', '$no_hp', '$no_rm')";
        $tambah = mysqli_query($mysqli, $sql);

        echo "<script> 
                alert('Data berhasil ditambahkan.');
                document.location='pasien.php';
              </script>";
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    $deleteDetailPeriksa = mysqli_query($mysqli, "DELETE FROM detail_periksa WHERE id_periksa IN (SELECT id FROM periksa WHERE id_daftar_poli IN (SELECT id FROM daftar_poli WHERE id_pasien = '$id'))");
    $deletePeriksa = mysqli_query($mysqli, "DELETE FROM periksa WHERE id_daftar_poli IN (SELECT id FROM daftar_poli WHERE id_pasien = '$id')");
    $deleteDaftarPoli = mysqli_query($mysqli, "DELETE FROM daftar_poli WHERE id_pasien = '$id'");

    if ($deleteDetailPeriksa && $deletePeriksa && $deleteDaftarPoli) {
        $hapus = mysqli_query($mysqli, "DELETE FROM pasien WHERE id = '$id'");

        if ($hapus) {
            echo "<script> 
                    alert('Data berhasil dihapus.');
                    document.location='pasien.php';
                  </script>";
        } else {
            echo "<script> 
                    alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
                    document.location='pasien.php';
                  </script>";
        }
    } else {
        echo "<script> 
                alert('Gagal menghapus data terkait: " . mysqli_error($mysqli) . "');
                document.location='pasien.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Pasien - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #0056b3;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .back-link:hover {
            background-color: #f8f9fa;
            color: #004494;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .content {
            flex: 1;
            padding: 20px 0;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #0056b3;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0,86,179,0.25);
        }

        .table th {
            background-color: #0056b3;
            color: white;
            padding: 15px;
        }

        .table td {
            vertical-align: middle;
            padding: 15px;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 3px;
        }

        .footer {
            background-color: #0056b3;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ccc;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <i class="fas fa-users"></i>
        <h1>Manajemen Data Pasien</h1>
        <p>Kelola data pasien dengan mudah dan efisien</p>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Back Button -->
            <a href="dashboardAdmin.php" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke Dashboard
            </a>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Data Pasien</h3>
                </div>
                <div class="card-body">
                    <!-- Form Section -->
                    <form action="" method="POST" onsubmit="return(validate());">
                        <?php
                        $nama = $alamat = $no_ktp = $no_hp = '';
                        $buttonText = 'Tambah';

                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $query = mysqli_query($mysqli, "SELECT * FROM pasien WHERE id='$id'");
                            $data = mysqli_fetch_assoc($query);
                            if ($data) {
                                $nama = $data['nama'];
                                $alamat = $data['alamat'];
                                $no_ktp = $data['no_ktp'];
                                $no_hp = $data['no_hp'];
                                $buttonText = 'Update';
                                echo '<input type="hidden" name="id" value="'.$id.'">';
                            }
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pasien <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" id="nama" required value="<?php echo $nama; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <input type="text" name="alamat" class="form-control" id="alamat" required value="<?php echo $alamat; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. KTP <span class="text-danger">*</span></label>
                                <input type="text" name="no_ktp" class="form-control" id="no_ktp" required value="<?php echo $no_ktp; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp" class="form-control" id="no_hp" required value="<?php echo $no_hp; ?>">
                            </div>
                        </div>
                        <button type="submit" name="simpanData" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $buttonText; ?>
                        </button>
                    </form>

                    <!-- Table Section -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Alamat</th>
                                    <th>No. KTP</th>
                                    <th>No. HP</th>
                                    <th>No. RM</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($mysqli, "SELECT * FROM pasien");
                                $no = 1;
                                while ($data = mysqli_fetch_array($result)) :
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $data['nama']; ?></td>
                                        <td><?php echo $data['alamat']; ?></td>
                                        <td><?php echo $data['no_ktp']; ?></td>
                                        <td><?php echo $data['no_hp']; ?></td>
                                        <td><?php echo $data['no_rm']; ?></td>
                                        <td>
                                            <a href="pasien.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="pasien.php?id=<?php echo $data['id']; ?>&aksi=hapus" 
                                               class="btn btn-danger btn-action"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <h5>&copy; 2024 Sistem Layanan Kesehatan</h5>
            <p><a href="privacy.php">Kebijakan Privasi</a> | <a href="terms.php">Syarat dan Ketentuan</a></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validate() {
            var nama = document.getElementById("nama").value;
            var alamat = document.getElementById("alamat").value;
            var no_ktp = document.getElementById("no_ktp").value;
            var no_hp = document.getElementById("no_hp").value;

            if (nama == "" || alamat == "" || no_ktp == "" || no_hp == "") {
                alert("Semua field harus diisi!");
                return false;
            }

            // Validasi format No. KTP (16 digit)
            if (no_ktp.length !== 16 || isNaN(no_ktp)) {
                alert("No. KTP harus 16 digit angka!");
                return false;
            }

            // Validasi format No. HP
            var phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(no_hp)) {
                alert("Format No. HP tidak valid! (10-13 digit angka)");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>