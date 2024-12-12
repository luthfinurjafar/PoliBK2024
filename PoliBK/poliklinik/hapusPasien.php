<?php
session_start();
include_once("koneksi.php");


//MENGAHPUS PASIEN YANG BELUM DI PERIKSA

if (!isset($_SESSION['id']) || !isset($_SESSION['nama'])) {
    echo "<script>
            alert('Anda harus login terlebih dahulu');
            window.location.href='index.php';
          </script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_daftar_poli = $_GET['id'];

    // Hapus pasien yang belum diperiksa
    $deleteQuery = "DELETE FROM daftar_poli WHERE id = '$id_daftar_poli' AND status_periksa = 'belum diperiksa'";
    $result = mysqli_query($mysqli, $deleteQuery);

    if ($result) {
        echo "<script>
                alert('Pasien berhasil dihapus');
                window.location.href='periksa.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus pasien');
                window.location.href='periksa.php';
              </script>";
    }
}
?>
