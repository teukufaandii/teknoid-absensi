<?php
session_start();
include '../db_connect.php';

$id_pg = $_GET['id_pg'];

// Ambil nama pengguna
$nama_stmt = $conn->prepare("SELECT nama FROM tb_pengguna WHERE id_pg = ?");
$nama_stmt->bind_param("s", $id_pg);

if (!$nama_stmt->execute()) {
    die('Query Error: ' . $nama_stmt->error);
}

$nama_result = $nama_stmt->get_result();
$nama_row = $nama_result->fetch_assoc();

if (!$nama_row) {
    echo "<tr><td colspan='7' class='text-center'>Pengguna dengan ID PG: " . htmlspecialchars($id_pg) . " tidak ditemukan</td></tr>";
    exit();
}

$nama_pengguna = $nama_row['nama'];

$start = 0;
$rows_per_page = 10;

// Hitung jumlah baris di tb_detail
$count_stmt = $conn->prepare("
    SELECT COUNT(*) AS total_rows 
    FROM tb_detail 
    INNER JOIN tb_pengguna ON tb_detail.id_pg = tb_pengguna.id_pg 
    WHERE tb_detail.id_pg = ?
");
$count_stmt->bind_param("s", $id_pg);

if (!$count_stmt->execute()) {
    die('Query Error: ' . $count_stmt->error);
}

$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$nr_of_rows = $row_count['total_rows'];

if ($nr_of_rows == 0) {
    echo "<tr><td colspan='7' class='text-center py-6'>Tidak ada data untuk pengguna: " . htmlspecialchars($nama_pengguna) . "</td></tr>";
    exit();
}

$pages = ceil($nr_of_rows / $rows_per_page);

if (isset($_GET['page-nr'])) {
    $page = $_GET['page-nr'] - 1;
    $start = $page * $rows_per_page;
} else {
    $page = 0;
}

// Ambil data dari tabel dengan batasan jumlah per halaman
$data_stmt = $conn->prepare("
    SELECT tb_detail.*, tb_pengguna.nama 
    FROM tb_detail 
    INNER JOIN tb_pengguna ON tb_detail.id_pg = tb_pengguna.id_pg 
    WHERE tb_detail.id_pg = ? 
    LIMIT ?, ?
");
$data_stmt->bind_param("sii", $id_pg, $start, $rows_per_page);

if (!$data_stmt->execute()) {
    die('Execute Error: ' . $data_stmt->error);
}

$result = $data_stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format scan_masuk dan scan_keluar
        $scanMasuk = $row["scan_masuk"] ? date('H:i', strtotime($row["scan_masuk"])) : '-';
        $scanKeluar = $row["scan_keluar"] ? date('H:i', strtotime($row["scan_keluar"])) : '-';

        // Hitung durasi jika scan_keluar ada
        if (!empty($row["scan_keluar"])) {
            $durasi = floor((strtotime($row["scan_keluar"]) - strtotime($row["scan_masuk"])) / 60); // Menggunakan floor untuk mendapatkan nilai bulat
        } else {
            $durasi = '-';
        }

        echo "<tr class='bg-gray-100'>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($row["tanggal"]) . "</td>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($row["jam_kerja"]) . "</td>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($scanMasuk) . "</td>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($scanKeluar) . "</td>
                <td class='px-6 py-2 text-center'>" . ($durasi === '-' ? '-' : $durasi . " menit") . "</td>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($row["keterangan"]) . "</td>
                <td class='px-6 py-2 text-center'>" . htmlspecialchars($row["nama"]) . "</td>
                <td class='px-6 py-2 text-center'>
                    <a href='previewDataAbsensi.php?id_pg=" . $row['id_pg'] . "'>
                        <button class='bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition'>Edit</button >
                    </a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>Tidak ada data</td></tr>";
}

$data_stmt->close();
$conn->close();
?>