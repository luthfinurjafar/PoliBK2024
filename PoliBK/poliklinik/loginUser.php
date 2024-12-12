<?php
session_start();

// Koneksi ke database MySQL
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengambil data user berdasarkan username menggunakan prepared statements
    $query = "SELECT * FROM user WHERE username = ?";
    
    // Coba siapkan query
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $username); // Bind parameter untuk username (tipe data string)

        $stmt->execute();
        $result = $stmt->get_result();

        // Cek apakah query berhasil
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verifikasi password yang disimpan menggunakan password_verify
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username; // Set session
                header("Location: dashboardAdmin.php"); // Arahkan ke dashboard admin
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }

        // Tutup statement
        $stmt->close();
    } else {
        // Jika prepare gagal, tampilkan pesan error
        die("Error preparing query: " . $mysqli->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #0c2d55;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-outline-primary {
            color: #005c9c;
            border-color: #005c9c;
        }
        .btn-outline-primary:hover {
            background-color: #005c9c;
            color: #fff;
        }
        a {
            color: #005c9c;
            text-decoration: none;
        }
        a:hover {
            color: #003b73;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <a href="loginDokter.php" class="text-primary" style="position: absolute; top: 20px; left: 20px;">
        <i class="bi bi-arrow-left-short"></i> 
    </a>
    <div class="container" style="margin-top: 10rem;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center" style="font-weight: bold; font-size: 32px; background-color: #005c9c; color: #fff;">
                        Login Admin
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-center align-items-center px-5 py-4">
                            <div class="col-lg-6">
                                <h1 class="text-center mb-4" style="color: #005c9c;">Masuk</h1>
                                <form method="POST" action="loginUser.php">
                                    <?php
                                    if (isset($error)) {
                                        echo '<div class="alert alert-danger">' . $error . '
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>';
                                    }
                                    ?>
                                    <div class="form-group mb-3">
                                        <label for="username">Username Admin</label>
                                        <input type="text" name="username" class="form-control" required placeholder="Masukkan username Anda">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" required placeholder="Masukkan password Anda">
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-outline-primary px-4 btn-block">Login</button>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <p>Belum punya akun? <a href="registerUser.php">Register</a></p>
                                    <p>Login sebagai dokter? <a href="loginDokter.php">Ya, Saya Dokter</a></p>
                                </div>
                            </div>
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
