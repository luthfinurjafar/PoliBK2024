<?php
if (!isset($_SESSION)) {
    session_start();
}

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_rm = $_POST['no_rm'];
    $no_ktp = $_POST['no_ktp'];

    // Query untuk mencari pasien berdasarkan No. RM dan No. KTP
    $stmt = $mysqli->prepare("SELECT * FROM pasien WHERE no_rm = ? AND no_ktp = ?");
    $stmt->bind_param("ss", $no_rm, $no_ktp); // Menggunakan 'ss' untuk parameter string
    $stmt->execute();
    $result = $stmt->get_result();

    // Memeriksa apakah pasien ditemukan
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Menyimpan data pasien ke session
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['id_pasien'] = $row['id'];
        
        echo "<script>
        alert('Login Berhasil! Selamat datang, {$row['nama']}');
        document.location='dashboardPasien.php'; // Ganti dengan halaman dashboard pasien
        </script>";
    } else {
        // Jika No. RM atau No. KTP salah
        $error = "No. Rekam Medis atau No. KTP salah";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<a href="index.php" class="text-primary">
    <i class="bi bi-arrow-left-short"></i> 
</a>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pasien - Poliklinik BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-o8Cw+TcbsG7EiODyEfzIqXTYHlg+HTkKYQOeUbKoJ9syPZDy6W1hWkRMT4kFOTPM7VR2FZiyUpxVK7hJhtrGAA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            background: #0c2d55;
        }

        .card {
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: #005c9c;
            color: white;
            font-weight: bold;
            font-size: 1.8rem;
            padding: 1rem;
            text-align: center;
            border-radius: 0.3rem 0.3rem 0 0;
        }

        .btn-primary {
            background-color: #005c9c;
            border-color: #007bff;
            padding: 0.8rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004c99;
            transform: scale(1.05);
        }

        .form-group label {
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
        }

        .alert {
            border-radius: 10px;
        }

        .text-center a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .text-center a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .illustration {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .illustration img {
            width: 120px;
            animation: fadeIn 2s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-user-circle"></i> Login Pasien
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php
                            if (isset($error)) {
                                echo '<div class="alert alert-danger">' . $error . '</div>';
                            }
                            ?>
                            <div class="form-group">
                                <label for="no_rm"><i class="fa fa-id-card"></i> Nomor Rekam Medis (RM)</label>
                                <input type="text" name="no_rm" class="form-control" required placeholder="Masukkan No. RM Anda">
                            </div>
                            <div class="form-group">
                                <label for="no_ktp"><i class="fa fa-id-badge"></i> No. KTP</label>
                                <input type="password" name="no_ktp" class="form-control" required placeholder="Masukkan No. KTP Anda">
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="mt-3">Belum terdaftar? <a href="registerPasien.php">Registrasi</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
