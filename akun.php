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
$title = 'Daftar Akun';

include 'layout/header.php'; 

$data_akun = select("SELECT * FROM akun");

// --- Menghitung Jumlah Akun Berdasarkan Level dari $data_akun ---
$admin_count      = 0;
$op_barang_count  = 0;
$op_mhs_count     = 0;

foreach ($data_akun as $akun) {
    if ($akun['level'] == 1) {
        $admin_count++;
    } elseif ($akun['level'] == 2) {
        $op_barang_count++;
    } elseif ($akun['level'] == 3) {
        $op_mhs_count++;
    }
}

$label_level  = ['Admin', 'Operator Barang', 'Operator Mahasiswa'];
$jumlah_level = [$admin_count, $op_barang_count, $op_mhs_count];

// tampil data berdasarkan user login
$id_akun = $_SESSION['id_akun'];
$data_bylogin = select("SELECT * FROM akun WHERE id_akun = $id_akun");

// jika tombol tambah di tekan jalankan script berikut
if (isset($_POST['tambah'])) {
    if (create_akun($_POST) > 0) {
        echo "<script>
                alert('Data Akun Berhasil Ditambahkan');
                document.location.href = 'akun.php';
              </script>";
    } else {
        echo "<script>
                alert('Data Akun Gagal Ditambahkan');
                document.location.href = 'akun.php';
              </script>";
    }
}
// jika tombol ubah di tekan jalankan script berikut
if (isset($_POST['ubah'])) {
    if (update_akun($_POST) > 0) {
        echo "<script>
                alert('Data Akun Berhasil Diubah');
                document.location.href = 'akun.php';
              </script>";
    } else {
        echo "<script>
                alert('Data Akun Gagal Diubah');
                document.location.href = 'akun.php';
              </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="nav-icon fas fa-user-cog"></i>
                        Data Akun
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Data Akun</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Distribusi Level Akun</h3>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 300px;">
                                <canvas id="chartLevel"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tabel Data Akun</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($_SESSION['level'] == 1) : ?>
                            <button type="button" class="btn btn-primary btn-sm mb-2" data-toggle="modal"
                                data-target="#modalTambah"><i class="fas fa-plus"></i> Tambah</button>
                            <?php endif; ?>

                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php if ($_SESSION['level'] == 1) : ?>
                                    <?php foreach ($data_akun as $akun) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $akun['nama']; ?></td>
                                        <td><?= $akun['username']; ?></td>
                                        <td><?= $akun['email']; ?></td>
                                        <td>Password Ter-enkripsi</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#modalUbah<?= $akun['id_akun']; ?>"><i
                                                    class="fas fa-edit"></i> Ubah</button>

                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#modalHapus<?= $akun['id_akun']; ?>"><i
                                                    class="fas fa-trash-alt"></i> Hapus</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else : ?>
                                    <?php foreach ($data_bylogin as $akun) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $akun['nama']; ?></td>
                                        <td><?= $akun['username']; ?></td>
                                        <td><?= $akun['email']; ?></td>
                                        <td>Password Ter-enkripsi</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#modalUbah<?= $akun['id_akun']; ?>"><i
                                                    class="fas fa-edit"></i> Ubah</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-plus"></i> Tambah Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required
                            minlength="5">
                    </div>

                    <div class="mb-3">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="">-- pilih level --</option>
                            <option value="1">Admin</option>
                            <option value="2">Operator Barang</option>
                            <option value="3">Operator Mahasiswa</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left"></i>
                    Kembali</button>
                <button type="submit" name="tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($data_akun as $akun) : ?>
<div class="modal fade" id="modalHapus<?= $akun['id_akun']; ?>" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-trash-alt"></i> Hapus Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Yakin Ingin Menghapus Data Akun : <?= $akun['nama']; ?> .?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                    Batal</button>
                <a href="hapus-akun.php?id_akun=<?= $akun['id_akun']; ?>" class="btn btn-danger"><i
                        class="fas fa-trash-alt"></i> Hapus</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php foreach ($data_akun as $akun) : ?>
<div class="modal fade" id="modalUbah<?= $akun['id_akun']; ?>" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-edit"></i> Ubah Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="id_akun" value="<?= $akun['id_akun']; ?>">

                    <div class="mb-3">
                        <label for="nama<?= $akun['id_akun']; ?>">Nama</label>
                        <input type="text" name="nama" id="nama<?= $akun['id_akun']; ?>" class="form-control"
                            value="<?= $akun['nama']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="username<?= $akun['id_akun']; ?>">Username</label>
                        <input type="text" name="username" id="username<?= $akun['id_akun']; ?>" class="form-control"
                            value="<?= $akun['username']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email<?= $akun['id_akun']; ?>">Email</label>
                        <input type="email" name="email" id="email<?= $akun['id_akun']; ?>" class="form-control"
                            value="<?= $akun['email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password<?= $akun['id_akun']; ?>">Password <small>(Masukan password
                                baru/lama)</small></label>
                        <input type="password" name="password" id="password<?= $akun['id_akun']; ?>"
                            class="form-control" required minlength="5">
                    </div>

                    <?php if ($_SESSION['level'] == 1) : ?>
                    <div class="mb-3">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control" required>
                            <?php $level = $akun['level']; ?>
                            <option value="1" <?= $level == '1' ? 'selected' : null ?>>Admin</option>
                            <option value="2" <?= $level == '2' ? 'selected' : null ?>>Operator Barang</option>
                            <option value="3" <?= $level == '3' ? 'selected' : null ?>>Operator Mahasiswa</option>
                        </select>
                    </div>
                    <?php else : ?>
                    <input type="hidden" name="level" value="<?= $akun['level']; ?>">
                    <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-left"></i>
                    Kembali</button>
                <button type="submit" name="ubah" class="btn btn-success"><i class="fas fa-save"></i> Ubah</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik Pie: Distribusi Level Akun
const ctxLevel = document.getElementById('chartLevel').getContext('2d');
new Chart(ctxLevel, {
    type: 'pie',
    data: {
        labels: <?= json_encode($label_level); ?>,
        datasets: [{
            data: <?= json_encode($jumlah_level); ?>,
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
</script>

<?php include 'layout/footer.php'; ?>