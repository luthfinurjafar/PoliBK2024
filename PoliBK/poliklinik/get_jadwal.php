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

if (isset($_POST['id_dokter'])) {
    $id_dokter = $_POST['id_dokter'];

    // Ambil jadwal berdasarkan dokter
    $query = "SELECT id, hari, jam_mulai, jam_selesai, status FROM jadwal_dokter WHERE id_dokter = '$id_dokter'";
    $result = mysqli_query($mysqli, $query);

    $jadwals = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Tentukan status jadwal
        $status_class = ($row['status'] == 1) ? 'text-success' : 'text-danger';
        $status_text = ($row['status'] == 1) ? 'Tersedia' : 'Tidak Tersedia';

        $row['status_class'] = $status_class;
        $row['status_text'] = $status_text;
        $jadwals[] = $row;
    }

    echo json_encode($jadwals);
}
?>
