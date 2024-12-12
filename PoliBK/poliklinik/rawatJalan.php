<?php
if (!isset($_SESSION)) {
    session_start();
}

// Pastikan pengguna telah login
if (!isset($_SESSION['id_pasien'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); document.location='loginPasien';</script>";
    exit;
}

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "poli_bk");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Ambil informasi pasien
$id_pasien = $_SESSION['id_pasien'];
$stmt = $mysqli->prepare("SELECT * FROM pasien WHERE id = ?");
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('Data pasien tidak ditemukan!'); document.location='loginPasien.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keluhan = $_POST['keluhan'];
    $id_jadwal = $_POST['id_jadwal'];

    // Cek jika pasien sudah terdaftar
    $check_query = "SELECT * FROM daftar_poli WHERE id_pasien = '".$_SESSION['id_pasien']."'";
    $check_result = $mysqli->query($check_query);
    
    // Cek jika data tidak kosong dan mendapatkan nomor antrian
    $query = "SELECT MAX(no_antrian) as max_no FROM daftar_poli WHERE id_jadwal = '$id_jadwal'";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    $no_antrian = $row['max_no'] !== null ? $row['max_no'] + 1 : 1;

    // Insert data pendaftaran
    $insert_query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, tanggal) VALUES ('".$_SESSION['id_pasien']."', '$id_jadwal', '$keluhan', '$no_antrian', NOW())";
    if (mysqli_query($mysqli, $insert_query)) {
        header("Location: rawatJalan.php?no_antrian=$no_antrian");

    } else {
        $error = "Pendaftaran gagal";
    }
}

?>

<main id="rawatjalan-page">
    <div class="container" style="margin-top: 5.5rem;">
        <div class="row justify-content-center">
            <h2 class="text-center mb-4">Pendaftaran Rawat Jalan</h2>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center fw-bold" style="font-size: 1.5rem;">Pilih Poli dan Dokter</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php
                            if (isset($error)) {
                                echo '<div class="alert alert-danger">' . $error . '</div>';
                            }

                            if (isset($_GET['no_antrian'])) {
                                echo '<div class="alert alert-success">Nomor antrian anda adalah ' . $_GET['no_antrian'] . '</div>';
                            }
                            ?>
                            <div class="row">
                                <div class="col-6">
                                    <div class="dropdown mb-3">
                                        <label for="id_poli">Poli Dokter <span class="text-danger">*</span></label>
                                        <select class="form-select" name="id_poli" id="id_poli" aria-label="id_poli">
                                            <option value="" selected>Pilih Poli...</option>
                                            <?php
                                            $result = mysqli_query($mysqli, "SELECT * FROM poli");
                                            while ($data = mysqli_fetch_assoc($result)) {
                                                echo "<option value='" . $data['id'] . "'>" . $data['nama_poli'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="dropdown mb-3">
                                        <label for="id_dokter">Dokter <span class="text-danger">*</span></label>
                                        <select disabled class="form-select" name="id_dokter" id="id_dokter" aria-label="id_dokter">
                                            <option value="" selected>Pilih Dokter...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group mb-3" id="jadwal_list">
                                <li class="list-group-item disabled text-center">Pilih Poli dan Dokter terlebih dahulu</li>
                            </ul>
                            <div class="mb-3">
                                <label for="keluhan">Keluhan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="keluhan" id="keluhan" style="resize: none; height: 8rem" required></textarea>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-outline-primary px-4 btn-block" disabled>Daftar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.querySelector("select[name='id_poli']").addEventListener('change', function() {
    let id_poli = this.value;
    let dokterDropdown = document.querySelector("select[name='id_dokter']");
    let jadwalList = document.getElementById('jadwal_list');
    
    if (id_poli != "") {
        dokterDropdown.disabled = false;

        fetch('get_dokter.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_poli=' + id_poli
        })
        .then(response => response.json())
        .then(data => {
            dokterDropdown.innerHTML = "<option value='' selected>Pilih Dokter...</option>";
            data.forEach(function(dokter) {
                var option = document.createElement('option');
                option.value = dokter.id;
                option.text = dokter.nama;
                dokterDropdown.add(option);
            });
        });
    } else {
        dokterDropdown.disabled = true;
    }
});

document.querySelector("select[name='id_dokter']").addEventListener('change', function() {
    let id_dokter = this.value;
    let jadwalList = document.getElementById('jadwal_list');
    
    if (id_dokter != "") {
        fetch('get_jadwal.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_dokter=' + id_dokter
        })
        .then(response => response.json())
        .then(data => {
            jadwalList.innerHTML = "";
            data.forEach(function(jadwal) {
                let listItem = document.createElement('li');
                listItem.className = "list-group-item d-flex justify-content-between mb-1";
                let status = (jadwal.statues != 1) ? '<p class="bg-danger text-white border rounded p-1 mb-0">Inactive</p>' : '<p class="bg-success text-white border rounded p-1 mb-0">Active</p>';
                listItem.innerHTML = '<div><input class="form-check-input me-1" type="radio" name="id_jadwal" value="' + jadwal.id + '" ' + (jadwal.statues != 1 ? 'disabled' : '') + '><label class="form-check-label">' + jadwal.hari + ', ' + jadwal.jam_mulai + ' - ' + jadwal.jam_selesai + '</label></div>' + status;
                jadwalList.appendChild(listItem);
            });

            document.querySelectorAll("input[type=radio]").forEach(function(radio) {
                radio.addEventListener("change", function() {
                    document.querySelector("button[type=submit]").removeAttribute('disabled');
                });
            });
        });
    } else {
        jadwalList.innerHTML = "<li class='list-group-item disabled text-center'>Pilih Jadwal</li>";
    }
});
</script>
