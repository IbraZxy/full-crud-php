<?php

include "config/app.php";

// pagination 5 data per halaman
$jumlahDataPerHalaman = 5;
$halamanAktif         = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$awalData             = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

$data_pegawai = select("SELECT * FROM pegawai ORDER BY id_pegawai DESC LIMIT $awalData, $jumlahDataPerHalaman");

?>

<?php $no = $awalData + 1; ?>
<?php foreach ($data_pegawai as $pegawai) : ?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $pegawai['nama']; ?></td>
    <td><?= $pegawai['jabatan']; ?></td>
    <td><?= $pegawai['email']; ?></td>
    <td><?= $pegawai['telepon']; ?></td>
    <td><?= $pegawai['alamat']; ?></td>
</tr>
<?php endforeach; ?>