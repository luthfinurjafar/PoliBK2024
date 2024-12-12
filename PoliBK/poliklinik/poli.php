<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include koneksi database
require_once 'koneksi.php';  // Sesuaikan path file koneksi Anda


if (!isset($_SESSION['username'])) {
    header("Location: loginUser.php");
    exit;
}

if (isset($_POST['simpanData'])) {
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];
   
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $mysqli->prepare("UPDATE poli SET nama_poli=?, keterangan=? WHERE id=?");
        $stmt->bind_param("ssi", $nama_poli, $keterangan, $id);
        $edit = $stmt->execute();

        if ($edit) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: 'Data berhasil diubah',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'index.php?page=poli';
                    });
                  </script>";
        }
    } else {
        $stmt = $mysqli->prepare("INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_poli, $keterangan);
        $tambah = $stmt->execute();

        if ($tambah) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: 'Data berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'index.php?page=poli';
                    });
                  </script>";
        }
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM poli WHERE id = ?");
    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: 'Data berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php?page=poli';
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Poli - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
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
        <i class="fas fa-clinic-medical"></i>
        <h1>Manajemen Data Poli</h1>
        <p>Kelola data poli dengan mudah dan efisien</p>
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
                    <h3 class="card-title mb-0">Data Poli</h3>
                </div>
                <div class="card-body">
                    <!-- Form Section -->
                    <form action="" method="POST" onsubmit="return(validate());">
                        <?php
                        $nama_poli = '';
                        $keterangan = '';
                        if (isset($_GET['id'])) {
                            $stmt = $mysqli->prepare("SELECT * FROM poli WHERE id=?");
                            $stmt->bind_param("i", $_GET['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                $nama_poli = $row['nama_poli'];
                                $keterangan = $row['keterangan'];
                            }
                        ?>
                            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                        <?php
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama_poli" class="form-label">Nama Poli <span class="text-danger">*</span></label>
                                <input type="text" name="nama_poli" class="form-control" required value="<?php echo $nama_poli ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <textarea name="keterangan" class="form-control" required><?php echo $keterangan ?></textarea>
                            </div>
                        </div>
                        <button type="submit" name="simpanData" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                    </form>

                    <!-- Table Section -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Poli</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($mysqli, "SELECT * FROM poli ORDER BY nama_poli ASC");
                                $no = 1;
                                while ($data = mysqli_fetch_array($result)) :
                                ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><?php echo $data['nama_poli'] ?></td>
                                        <td><?php echo $data['keterangan'] ?></td>
                                        <td>
                                            <a href="poli.php?id=<?php echo $data['id'] ?>" class="btn btn-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-action" 
                                               onclick="confirmDelete('poli.php?id=<?php echo $data['id'] ?>&aksi=hapus')">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validate() {
            if (document.forms[0].nama_poli.value.trim() === "") {
                Swal.fire({
                    title: 'Error!',
                    text: 'Nama Poli tidak boleh kosong',
                    icon: 'error'
                });
                document.forms[0].nama_poli.focus();
                return false;
            }
            if (document.forms[0].keterangan.value.trim() === "") {
                Swal.fire({
                    title: 'Error!',
                    text: 'Keterangan tidak boleh kosong',
                    icon: 'error'
                });
                document.forms[0].keterangan.focus();
                return false;
            }
            return true;
        }

        function confirmDelete(url) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html>