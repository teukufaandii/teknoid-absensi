<?php
include $_SERVER['DOCUMENT_ROOT'] . '/teknoid-absensi/src/db/db_connect.php';

date_default_timezone_set('Asia/Jakarta');


$tanggal = date('Y-m-d');

// Function to generate random ID
function generateRandomId($length = 20) {
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}

// Query to fetch users
$queryPengguna = "SELECT id_pg, nomor_kartu FROM tb_pengguna";
$resultPengguna = $conn->query($queryPengguna);

if ($resultPengguna->num_rows > 0) {
    while ($row = $resultPengguna->fetch_assoc()) {
        $id_pg = $row['id_pg'];
        $nomor_kartu = $row['nomor_kartu'];

        // Generate random ID
        $randomId = generateRandomId(20);

        // Insert into tb_detail
        $insertDetail = "INSERT INTO tb_detail (id, id_pg, nomor_kartu, tanggal, keterangan)
                         VALUES ('$randomId', '$id_pg', '$nomor_kartu', '$tanggal', 'Alpha')";

        if ($conn->query($insertDetail) === TRUE) {
            echo "Absence details added with id: $randomId for id_pg: $id_pg\n";
        } else {
            echo "Error: " . $insertDetail . "\n" . $conn->error;
        }
    }
} else {
    echo "No users found in tb_pengguna.\n";
}

// Close connection
$conn->close();
?>