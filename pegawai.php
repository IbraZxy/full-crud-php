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
            document.location.href = 'akun.php';
          </script>";
    exit;
}

$title = 'Daftar Pegawai';

include 'layout/header.php';

// data untuk grafik: jumlah pegawai per jabatan (statis, dihitung sekali saat halaman dibuka)
$data_jabatan = select("SELECT jabatan, COUNT(*) as jumlah FROM pegawai GROUP BY jabatan");

$label_jabatan  = [];
$jumlah_jabatan = [];
foreach ($data_jabatan as $j) {
    $label_jabatan[]  = $j['jabatan'];
    $jumlah_jabatan[] = (int)$j['jumlah'];
}

// hitung total halaman untuk pagination (5 data per halaman)
$jumlahDataPerHalaman = 5;
$jumlahData           = count(select("SELECT * FROM pegawai"));
$jumlahHalaman        = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif         = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="nav-icon fas fa-users"></i>
                        Data Pegawai
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Data Pegawai</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Grafik Pegawai per Jabatan -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Jumlah Pegawai per Jabatan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 260px;">
                                <canvas id="chartPegawai"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tabel Data Pegawai</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!--<a href="download-excel-pegawai.php" class="btn btn-success mb-2">
                                <i class="fas fa-file-excel"></i> Download Excel
                                </a>
                                <a href="download-pdf-pegawai.php" class="btn btn-danger mb-2">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </a>-->

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody id="live_data">
                                </tbody>
                            </table>

                            <div class="mt-2 justify-content-end d-flex">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <?php if ($halamanAktif > 1) : ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?halaman=<?= $halamanAktif - 1 ?>"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                                        <?php if ($i == $halamanAktif) : ?>
                                        <li class="page-item active"><a class="page-link"
                                                href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
                                        <?php else : ?>
                                        <li class="page-item"><a class="page-link"
                                                href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
                                        <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($halamanAktif < $jumlahHalaman) : ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?halaman=<?= $halamanAktif + 1 ?>"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const halamanAktif = <?= $halamanAktif; ?>;

$('document').ready(function() {
    setInterval(function() {
        getPegawai()
    }, 0) // dilay per s
});

function getPegawai() {
    $.ajax({
        url: "realtime-pegawai.php",
        type: "GET",
        data: {
            halaman: halamanAktif
        },
        success: function(response) {
            $('#live_data').html(response)
        }
    });
}

// Grafik Pegawai per Jabatan (statis, dihitung sekali saat halaman dibuka)
const ctxPegawai = document.getElementById('chartPegawai').getContext('2d');
new Chart(ctxPegawai, {
    type: 'bar',
    data: {
        labels: <?= json_encode($label_jabatan); ?>,
        datasets: [{
            label: 'Jumlah Pegawai',
            data: <?= json_encode($jumlah_jabatan); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include 'layout/footer.php'; ?>