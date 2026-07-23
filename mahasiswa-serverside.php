<?php
include 'config/database.php';

if (isset($_GET['action']) && $_GET['action'] == "table_data") {

    // list kolom sesuai urutan header tabel
    $columns = array(
        0 => 'id_mahasiswa',
        1 => 'nama',
        2 => 'prodi',
        3 => 'jk',
        4 => 'telepon',
        5 => 'id_mahasiswa'
    );

    // menghitung jumlah total data dari tabel mahasiswa
    $querycount = $db->query("SELECT count(id_mahasiswa) as jumlah FROM mahasiswa");
    $datacount  = $querycount->fetch_array();
    $totalData  = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit  = $_POST['length'];
    $start  = $_POST['start'];
    $order  = $columns[$_POST['order']['0']['column']];
    $dir    = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        // query jika tidak ada pencarian
        $query = $db->query("SELECT id_mahasiswa, nama, prodi, jk, telepon FROM mahasiswa ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        // query jika ada pencarian
        $search = $_POST['search']['value'];
        $query = $db->query("SELECT id_mahasiswa, nama, prodi, jk, telepon FROM mahasiswa WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%' ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount    = $db->query("SELECT count(id_mahasiswa) as jumlah FROM mahasiswa WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%'");
        $datacount     = $querycount->fetch_array();
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch_array()) {
            $nestedData['no']      = $no;
            $nestedData['nama']    = $value['nama'];
            $nestedData['prodi']   = $value['prodi'];
            $nestedData['jk']      = $value['jk'];
            $nestedData['telepon'] = $value['telepon'];

            // TOMBOL AKSI DIPROSES DI SINI (HTML)
            $nestedData['aksi']    = '
                <a href="detail-mahasiswa.php?id_mahasiswa='.$value['id_mahasiswa'].'" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i> Detail</a>
                <a href="ubah-mahasiswa.php?id_mahasiswa='.$value['id_mahasiswa'].'" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Ubah</a>
                <a href="hapus-mahasiswa.php?id_mahasiswa='.$value['id_mahasiswa'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin data Mahasiswa Akan Dihapus?.\');"><i class="fas fa-trash-alt"></i> Hapus</a>
            ';

            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ];

    echo json_encode($json_data);
}