<?php

session_start();
// membatasi halaman sebelum login
if (!isset($_SESSION["login"])) {
    echo "<script>
            alert('login dulu');
            document.location.href = 'login.php';
          </script>";
    exit;
}

// membatasi halaman sesuai user login
if ($_SESSION["level"] != 1 and $_SESSION["level"] != 3) {
    echo "<script>
            alert('Perhatian anda tidak punya hak akses');
            document.location.href = 'crud-modal.php';
          </script>";
    exit;
}

$title = 'Daftar Mahasiswa';

include 'layout/header.php';

// menampilkan data mahasiswa
$data_mahasiswa = select("SELECT * FROM mahasiswa ORDER BY id_mahasiswa DESC");

// data agregat untuk grafik: jumlah mahasiswa per program studi
$data_prodi = select("SELECT prodi, COUNT(*) as jumlah FROM mahasiswa GROUP BY prodi");

$label_prodi  = [];
$jumlah_prodi = [];
foreach ($data_prodi as $p) {
    $label_prodi[]  = $p['prodi'];
    $jumlah_prodi[] = (int)$p['jumlah'];
}

// data agregat untuk grafik: jumlah mahasiswa per jenis kelamin
// pakai LOWER() supaya "Laki-Laki" dan "laki-laki" dihitung sebagai satu kategori yang sama
$data_jk = select("SELECT LOWER(jk) as jk, COUNT(*) as jumlah FROM mahasiswa GROUP BY LOWER(jk)");

$label_jk  = [];
$jumlah_jk = [];
foreach ($data_jk as $j) {
    $label_jk[]  = ucwords($j['jk']);
    $jumlah_jk[] = (int)$j['jumlah'];
}

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        Data Mahasiswa
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Data Mahasiswa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">

            <!-- Grafik Mahasiswa -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Per Program Studi</h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 220px;">
                                <canvas id="chartProdi"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Per Jenis Kelamin</h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 220px;">
                                <canvas id="chartJk"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tabel Data Mahasiswa</h3>
                        </div>
                        <div class="card-body">
                            <a href="tambah-mahasiswa.php" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-plus"></i> Tambah
                            </a>

                            <a href="download-excel-mahasiswa.php" class="btn btn-success btn-sm mb-2">
                                <i class="fas fa-file-excel"></i> Download Excel
                            </a>

                            <a href="download-pdf-mahasiswa.php" class="btn btn-danger btn-sm mb-2">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>

                            <table id="serverside" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Prodi</th>
                                        <th>JK</th>
                                        <th>Telepon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik Pie: Per Program Studi
const ctxProdi = document.getElementById('chartProdi').getContext('2d');
new Chart(ctxProdi, {
    type: 'pie',
    data: {
        labels: <?= json_encode($label_prodi); ?>,
        datasets: [{
            data: <?= json_encode($jumlah_prodi); ?>,
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Grafik Donut: Per Jenis Kelamin
const ctxJk = document.getElementById('chartJk').getContext('2d');
new Chart(ctxJk, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($label_jk); ?>,
        datasets: [{
            data: <?= json_encode($jumlah_jk); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include 'layout/footer.php'; ?>