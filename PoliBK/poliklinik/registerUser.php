<?php
// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password dan konfirmasi password
    if ($password != $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk memasukkan data
        $query = "INSERT INTO user (username, password) VALUES (?, ?)";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                // Redirect ke halaman login
                header("Location: loginUser.php");
                exit();
            } else {
                $error = "Terjadi kesalahan saat pendaftaran, coba lagi.";
            }
        } else {
            $error = "Gagal menyiapkan query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
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

        .btn-primary {
            background-color: #005c9c;
            border-color: #005c9c;
        }

        .btn-primary:hover {
            background-color: #003b73;
            border-color: #003b73;
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
<a href="loginUser.php" class="text-primary">
    <i class="bi bi-arrow-left-short"></i> 
</a>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="font-weight: bold; font-size: 32px; background-color: #005c9c; color: #fff;">
                        Register Admin
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php
                            if (isset($error)) {
                                echo '<div class="alert alert-danger">' . $error . '</div>';
                            }
                            ?>
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" required placeholder="Masukkan username Anda">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                            </div>
                            <div class="form-group mb-3">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required placeholder="Masukkan konfirmasi password">
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>Sudah Punya Akun? <a href="loginUser.php">Login</a></p>
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
