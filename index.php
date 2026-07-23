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
if ($_SESSION["level"] != 1 and $_SESSION["level"] != 2) {
    echo "<script>
            alert('Perhatian anda tidak punya hak akses');
            document.location.href = 'akun.php';
          </script>";
    exit;
}

$title = 'Daftar Barang';

include 'layout/header.php'; 

if (isset($_POST['filter'])) {
    $tgl_awal  = strip_tags($_POST['tgl_awal']);
    $tgl_akhir = strip_tags($_POST['tgl_akhir']);

    // Query filter data menggunakan DATE() agar lebih akurat
    $data_barang = select("SELECT * FROM barang WHERE DATE(tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir' ORDER BY id_barang DESC");
} elseif (isset($_GET['cari']) && $_GET['cari'] != '') {
    // Query pencarian data berdasarkan nama barang
    $cari        = strip_tags($_GET['cari']);
    $data_barang = select("SELECT * FROM barang WHERE nama LIKE '%$cari%' ORDER BY id_barang DESC");
} else {
    // Query tampil data dengan pagination
    $jumlahDataPerHalaman = 5 ;
    $jumlahData           = count(select("SELECT * FROM barang"));
    $jumlahHalaman        = ceil($jumlahData / $jumlahDataPerHalaman);
    $halamanAktif         = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
    $awalData             = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

    $data_barang = select("SELECT * FROM barang ORDER BY id_barang DESC LIMIT $awalData, $jumlahDataPerHalaman");
}

// hitung jumlah data untuk kotak dashboard
$jumlah_barang    = count(select("SELECT * FROM barang"));
$jumlah_mahasiswa = count(select("SELECT * FROM mahasiswa"));
$jumlah_pegawai   = count(select("SELECT * FROM pegawai"));
$jumlah_akun      = count(select("SELECT * FROM akun"));

// ambil SEMUA data barang khusus untuk grafik bar (tanpa limit pagination)
$data_chart_barang = select("SELECT * FROM barang ORDER BY id_barang DESC");

$nama_barang         = [];
$jumlah_barang_chart = [];
foreach ($data_chart_barang as $b) {
    $nama_barang[]         = $b['nama'];
    $jumlah_barang_chart[] = $b['jumlah'];
}

// data untuk line chart: tren jumlah barang masuk per tanggal
$data_tren = select("SELECT DATE(tanggal) as tgl, SUM(jumlah) as total FROM barang GROUP BY DATE(tanggal) ORDER BY tgl ASC");

$label_tren  = [];
$jumlah_tren = [];
foreach ($data_tren as $t) {
    $label_tren[]  = date('d/m/Y', strtotime($t['tgl']));
    $jumlah_tren[] = (int)$t['total'];
}

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="nav-icon fas fa-box"></i>
                        Data Barang
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $jumlah_barang; ?></h3>
                            <p>Data Barang</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="index.php" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $jumlah_mahasiswa; ?></h3>
                            <p>Data Mahasiswa</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <a href="mahasiswa.php" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $jumlah_pegawai; ?></h3>
                            <p>Data Pegawai</p>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-users"></i>
                        </div>
                        <a href="pegawai.php" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $jumlah_akun; ?></h3>
                            <p>Data Akun</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <a href="akun.php" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Grafik Barang -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Jumlah Stok Barang</h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 260px;">
                                <canvas id="chartBarang"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Tren Barang Masuk per Tanggal
                            </h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 260px;">
                                <canvas id="chartTren"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Barang</h3>
                        </div>
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                <div>
                                    <a href="tambah-barang.php" class="btn btn-primary btn-sm"><i
                                            class="fas fa-plus"></i>
                                        Tambah</a>

                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#modalFilter">
                                        <i class="fas fa-search"></i> Filter Data
                                    </button>
                                </div>

                                <form action="" method="get" class="form-inline">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" name="cari" class="form-control"
                                            placeholder="Cari nama barang..."
                                            value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-outline-secondary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Barcode</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = isset($awalData) ? $awalData + 1 : 1; ?>
                                    <?php foreach ($data_barang as $barang) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $barang['nama']; ?></td>
                                        <td><?= $barang['jumlah']; ?></td>
                                        <td>Rp. <?= number_format($barang['harga'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <img alt="barcode"
                                                src="barcode.php?codetype=Code128&size=15&text=<?= $barang['barcode']; ?>&print=true" />
                                        </td>
                                        <td><?= date('d/m/Y | H:i:s', strtotime($barang['tanggal'])); ?></td>
                                        <td width="20%" class="text-center">
                                            <a href="ubah-barang.php?id_barang=<?= $barang['id_barang'] ?>"
                                                class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Ubah</a>
                                            <a href="hapus-barang.php?id_barang=<?= $barang['id_barang'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin data Barang Akan Dihapus?.');"><i
                                                    class="fas fa-trash-alt"></i> Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <?php if (!isset($_POST['filter']) and !isset($_GET['cari'])) : ?>
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
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalFilter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-search"></i> Filter Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="tgl_awal">Tanggal Awal</label>
                        <input type="date" name="tgl_awal" id="tgl_awal" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_akhir">Tanggal Akhir</label>
                        <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i
                                class="fas fa-times"></i> Batal</button>
                        <button type="submit" class="btn btn-success btn-sm" name="filter"><i class="fas fa-filter"></i>
                            Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik Bar: Jumlah Stok Barang
const ctxBarang = document.getElementById('chartBarang').getContext('2d');
new Chart(ctxBarang, {
    type: 'bar',
    data: {
        labels: <?= json_encode($nama_barang); ?>,
        datasets: [{
            label: 'Jumlah Stok',
            data: <?= json_encode($jumlah_barang_chart); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
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
                beginAtZero: true
            }
        }
    }
});

// Grafik Line: Tren Barang Masuk per Tanggal
const ctxTren = document.getElementById('chartTren').getContext('2d');
new Chart(ctxTren, {
    type: 'line',
    data: {
        labels: <?= json_encode($label_tren); ?>,
        datasets: [{
            label: 'Jumlah Barang Masuk',
            data: <?= json_encode($jumlah_tren); ?>,
            fill: true,
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(255, 159, 64, 1)'
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
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'layout/footer.php'; ?>