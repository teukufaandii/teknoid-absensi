<?php
require_once 'src/db/db_connect.php';

$id_pg = isset($_GET['id_pg']) ? $_GET['id_pg'] : '';
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = 10;

// Ambil nama pengguna
$namaQuery = "SELECT nama FROM tb_pengguna WHERE id_pg = ?";
$namaStmt = $conn->prepare($namaQuery);
$namaStmt->bind_param("s", $id_pg);
$namaStmt->execute();
$namaResult = $namaStmt->get_result();
$namaRow = $namaResult->fetch_assoc();

if (!$namaRow) {
    echo json_encode([
        'status' => 'error',
        'message' => "Pengguna dengan ID PG: " . htmlspecialchars($id_pg) . " tidak ditemukan"
    ]);
    exit();
}

$nama_pengguna = $namaRow['nama'];

// Hitung jumlah total data
$countQuery = "
    SELECT COUNT(*) AS total_rows 
    FROM tb_detail 
    INNER JOIN tb_pengguna ON tb_detail.id_pg = tb_pengguna.id_pg 
    WHERE tb_detail.id_pg = ?
";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("s", $id_pg);
$countStmt->execute();
$countResult = $countStmt->get_result();
$rowCount = $countResult->fetch_assoc();
$nr_of_rows = $rowCount['total_rows'];

if ($nr_of_rows == 0) {
    echo json_encode([
        'status' => 'success', // Change this to 'success' to indicate no data found
        'total' => $nr_of_rows,
        'preview_data_absensi' => [] // Return an empty array
    ]);
    exit();
}

$pages = ceil($nr_of_rows / $limit);

// Ambil data dari tabel dengan batasan jumlah per halaman
$dataQuery = "
    SELECT tb_detail.*, tb_pengguna.nama 
    FROM tb_detail 
    INNER JOIN tb_pengguna ON tb_detail.id_pg = tb_pengguna.id_pg 
    WHERE tb_detail.id_pg = ? 
    ORDER BY tb_detail.tanggal ASC
    LIMIT ?, ?
";
$dataStmt = $conn->prepare($dataQuery);
$dataStmt->bind_param("sii", $id_pg, $start, $limit);
$dataStmt->execute();
$result = $dataStmt->get_result();

$preview_data_absensi = [];
while ($row = $result->fetch_assoc()) {
    $scanMasuk = $row["scan_masuk"] ? date('H:i', strtotime($row["scan_masuk"])) : '-';
    $scanKeluar = $row["scan_keluar"] ? date('H:i', strtotime($row["scan_keluar"])) : '-';

    if (!empty($row["scan_keluar"])) {
        $durasi = floor((strtotime($row["scan_keluar"]) - strtotime($row["scan_masuk"])) / 60);
    } else {
        $durasi = '-';
    }

    $preview_data_absensi[] = [
        'id' => $row["id"], 
        'tanggal' => htmlspecialchars($row["tanggal"]),
        'jam_kerja' => htmlspecialchars($row["jam_kerja"]),
        'scan_masuk' => htmlspecialchars($scanMasuk),
        'scan_keluar' => htmlspecialchars($scanKeluar),
        'durasi' => $durasi === '-' ? '-' : $durasi . " menit",
        'keterangan' => htmlspecialchars($row["keterangan"]),
        'nama' => htmlspecialchars($row["nama"]),
        'id_pg' => $row['id_pg']
    ];
}

// Kirim hasil sebagai JSON
echo json_encode([
    'status' => 'success',
    'preview_data_absensi' => $preview_data_absensi,
    'total' => $nr_of_rows,
    'pages' => $pages
]);

$dataStmt->close();
$conn->close();