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
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE obat SET nama_obat='$nama_obat', kemasan='$kemasan', harga='$harga' WHERE id='$id'";
        $edit = mysqli_query($mysqli, $sql);

        echo "<script> 
                alert('Data berhasil diupdate.');
                document.location='obat.php';
              </script>";
    } else {
        $sql = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES ('$nama_obat', '$kemasan', '$harga')";
        $tambah = mysqli_query($mysqli, $sql);

        echo "<script> 
                alert('Data berhasil ditambahkan.');
                document.location='obat.php';
              </script>";
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    $hapus = mysqli_query($mysqli, "DELETE FROM obat WHERE id = '$id'");

    if ($hapus) {
        echo "<script> 
                alert('Data berhasil dihapus.');
                document.location='obat.php';
              </script>";
    } else {
        echo "<script> 
                alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
                document.location='obat.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Obat - Poliklinik</title>
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
        <i class="fas fa-pills"></i>
        <h1>Manajemen Data Obat</h1>
        <p>Kelola data obat dengan mudah dan efisien</p>
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
                    <h3 class="card-title mb-0">Data Obat</h3>
                </div>
                <div class="card-body">
                    <!-- Form Section -->
                    <form action="" method="POST" onsubmit="return(validate());">
                        <?php
                        $nama_obat = $kemasan = $harga = '';
                        $buttonText = 'Tambah';

                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $query = mysqli_query($mysqli, "SELECT * FROM obat WHERE id='$id'");
                            $data = mysqli_fetch_assoc($query);
                            if ($data) {
                                $nama_obat = $data['nama_obat'];
                                $kemasan = $data['kemasan'];
                                $harga = $data['harga'];
                                $buttonText = 'Update';
                                echo '<input type="hidden" name="id" value="'.$id.'">';
                            }
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                                <input type="text" name="nama_obat" class="form-control" id="nama_obat" required value="<?php echo $nama_obat; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kemasan <span class="text-danger">*</span></label>
                                <input type="text" name="kemasan" class="form-control" id="kemasan" required value="<?php echo $kemasan; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga" class="form-control" id="harga" required value="<?php echo $harga; ?>">
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
                                    <th>Nama Obat</th>
                                    <th>Kemasan</th>
                                    <th>Harga (Rp)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($mysqli, "SELECT * FROM obat");
                                $no = 1;
                                while ($data = mysqli_fetch_array($result)) :
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $data['nama_obat']; ?></td>
                                        <td><?php echo $data['kemasan']; ?></td>
                                        <td><?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <a href="obat.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="obat.php?id=<?php echo $data['id']; ?>&aksi=hapus" 
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
        // Validate Form
        function validate() {
            const harga = document.getElementById('harga').value;

            if (isNaN(harga) || harga <= 0) {
                alert("Harga harus berupa angka positif");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
