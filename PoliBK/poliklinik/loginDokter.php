<?php
if (!isset($_SESSION)) session_start();
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = $_POST['nip'];
    $password = $_POST['password'];

    if (empty($nip) || empty($password)) {
        $error = "NIP atau password tidak boleh kosong";
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM dokter WHERE nip = ?");
        $stmt->bind_param("i", $nip);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['nip'] = $row['nip'];
                $_SESSION['nama'] = $row['nama'];
                header("Location: berandaDokter.php");
                exit();
            } else {
                $error = "Password salah";
            }
        } else {
            $error = "User tidak ditemukan";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0c2d55; color: #fff; }
        .card { background: rgba(255, 255, 255, 0.9); }
        .btn-outline-primary { color: #005c9c; border-color: #005c9c; }
        .btn-outline-primary:hover { background-color: #005c9c; color: #fff; }
    </style>
</head>
<body>
<main id="logindokter-page">
    <div class="container" style="margin-top: 10rem;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center" style="font-weight: bold; background-color: #005c9c; color: #fff;">
                        Login Dokter
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <div class="form-group mb-3">
                                <label for="nip">NIP Dokter</label>
                                <input type="text" name="nip" class="form-control" required placeholder="Masukkan NIP">
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
                            <p>Login sebagai admin? <a href="loginUser.php">Ya, Saya Admin</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
