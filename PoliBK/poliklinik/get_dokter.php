<?php
// Koneksi database
$databaseHost = 'localhost';
$databaseName = 'poli_bk';
$databaseUsername = 'root';
$databasePassword = '';
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if (isset($_POST['id_poli'])) {
    $id_poli = $_POST['id_poli'];

    // Ambil dokter berdasarkan poli
    $query = "SELECT id, nama FROM dokter WHERE id_poli = '$id_poli'";
    $result = mysqli_query($mysqli, $query);

    $dokters = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dokters[] = $row;
    }

    echo json_encode($dokters);
}
?>
