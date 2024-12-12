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
    $nama = mysqli_real_escape_string($mysqli, $_POST['nama']);
    $alamat = mysqli_real_escape_string($mysqli, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($mysqli, $_POST['no_hp']);
    $id_poli = mysqli_real_escape_string($mysqli, $_POST['id_poli']);
    $nip = mysqli_real_escape_string($mysqli, $_POST['nip']);
    $password = $_POST['password'];

    // Hash the password only if it is not empty
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : '';

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = mysqli_real_escape_string($mysqli, $_POST['id']);
        $sql = "UPDATE dokter SET 
                    nama='$nama', 
                    alamat='$alamat', 
                    no_hp='$no_hp', 
                    id_poli='$id_poli', 
                    nip='$nip' ";
        if (!empty($hashed_password)) {
            $sql .= ", password='$hashed_password' ";
        }
        $sql .= "WHERE id='$id'";
        $edit = mysqli_query($mysqli, $sql);

        if ($edit) {
            echo "<script> 
                    alert('Data berhasil diupdate.');
                    document.location='dokter.php';
                  </script>";
        } else {
            echo "<script> 
                    alert('Gagal mengupdate data: " . mysqli_error($mysqli) . "');
                    document.location='dokter.php';
                  </script>";
        }
    } else {
        $sql = "INSERT INTO dokter (nama, alamat, no_hp, id_poli, nip, password) 
        VALUES ('$nama', '$alamat', '$no_hp', '$id_poli', '$nip', '$hashed_password')";
        $tambah = mysqli_query($mysqli, $sql);

        if ($tambah) {
            echo "<script> 
                    alert('Data berhasil ditambahkan.');
                    document.location='dokter.php';
                  </script>";
        } else {
            echo "<script> 
                    alert('Gagal menambahkan data: " . mysqli_error($mysqli) . "');
                    document.location='dokter.php';
                  </script>";
        }
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = mysqli_real_escape_string($mysqli, $_GET['id']);
    $hapus = mysqli_query($mysqli, "DELETE FROM dokter WHERE id = '$id'");

    if ($hapus) {
        echo "<script> 
                alert('Data berhasil dihapus.');
                document.location='dokter.php';
              </script>";
    } else {
        echo "<script> 
                alert('Gagal menghapus data: " . mysqli_error($mysqli) . "');
                document.location='dokter.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2B7FFF;
            --secondary-color: #F8F9FA;
            --accent-color: #4ECB71;
            --danger-color: #FF6B6B;
            --warning-color: #FFC107;
        }

        body {
            background-color: var(--secondary-color);
            font-family: 'Inter', sans-serif;
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border-radius: 12px;
            margin-bottom: 24px;
            background: white;
        }

        .card-header {
            background-color: var(--primary-color);
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-title {
            color: white;
            font-weight: 600;
            margin: 0;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(43,127,255,0.1);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background-color: var(--primary-color) !important;
            color: white;
            font-weight: 500;
            padding: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e0e0e0;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 4px;
        }

        .btn-warning {
            background-color: var(--warning-color);
            border: none;
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border: none;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .btn-action {
                width: 32px;
                height: 32px;
            }

            .btn-secondary {
                width: 100%;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>

<main id="dokter-page">
    <div class="container-fluid py-4">
        <!-- Tombol Kembali -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="dashboardAdmin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-header">
                <h3 class="text-white mb-0">Manajemen Data Dokter</h3>
            </div>
            <div class="card-body p-4">
                <!-- Form Section -->
                <form action="" method="POST" class="row g-4">
                    <?php
                    $nama = $alamat = $no_hp = $id_poli = $nip = $password = '';
                    $buttonText = 'Tambah';

                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];
                        $query = mysqli_query($mysqli, "SELECT * FROM dokter WHERE id='$id'");
                        $data = mysqli_fetch_assoc($query);
                        if ($data) {
                            $nama = $data['nama'];
                            $alamat = $data['alamat'];
                            $no_hp = $data['no_hp'];
                            $id_poli = $data['id_poli'];
                            $nip = $data['nip'];
                            $password = $data['password']; // You can display the hashed password, but don't show it in the input.
                            $buttonText = 'Update';
                            echo '<input type="hidden" name="id" value="'.$id.'">';
                        }
                    }
                    ?>
                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Dokter" required value="<?php echo $nama; ?>">
                            <label for="nama">Nama Dokter <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <input type="text" name="alamat" class="form-control" id="alamat" placeholder="Alamat" required value="<?php echo $alamat; ?>">
                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <input type="text" name="no_hp" class="form-control" id="no_hp" placeholder="No. HP" required value="<?php echo $no_hp; ?>">
                            <label for="no_hp">No. HP <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
    <div class="form-floating">
        <select name="id_poli" class="form-select" id="id_poli" required>
            <option value="">Pilih Poli</option>
            <?php
            // Query untuk mengambil data poli
            $query_poli = mysqli_query($mysqli, "SELECT id, nama_poli FROM poli");

            // Cek apakah query berhasil dan ada hasil
            if ($query_poli) {
                while ($poli = mysqli_fetch_assoc($query_poli)) {
                    // Tentukan apakah poli ini yang sudah dipilih (jika ada id_poli yang terisi)
                    $selected = ($poli['id'] == $id_poli) ? 'selected' : '';
                    echo "<option value='{$poli['id']}' $selected>{$poli['nama_poli']}</option>";
                }
            } else {
                echo "<option value=''>Gagal memuat data poli</option>";
            }
            ?>
        </select>
        <label for="id_poli">Poli <span class="text-danger">*</span></label>
    </div>
</div>

                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <input type="number" name="nip" class="form-control" id="nip" placeholder="NIP" required value="<?php echo $nip; ?>">
                            <label for="nip">NIP <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required value="<?php echo $password; ?>">
                            <label for="password">Password <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" name="simpanData" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i><?php echo $buttonText; ?>
                        </button>
                    </div>
                </form>

                <!-- Table Section -->
                <div class="table-responsive mt-5">
                    <table class="table table-hover align-middle" id="dokterTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th>ID Poli</th>
                                <th>NIP</th>
                                <th>Password</th>
                                <th class="text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($mysqli, "SELECT * FROM dokter");
                            $no = 1;
                            while ($data = mysqli_fetch_array($result)) :
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo $data['nama']; ?></td>
                                    <td><?php echo $data['alamat']; ?></td>
                                    <td><?php echo $data['no_hp']; ?></td>
                                    <td><?php echo $data['id_poli']; ?></td>
                                    <td><?php echo $data['nip']; ?></td>
                                    <td><?php echo $data['password']; ?></td>
                                    <td class="text-center">
                                        <a href="dokter.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-action">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="dokter.php?id=<?php echo $data['id']; ?>&aksi=hapus" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                                           class="btn btn-danger btn-action">
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
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
