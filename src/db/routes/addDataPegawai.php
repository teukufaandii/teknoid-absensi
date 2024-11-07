<?php

include 'src/db/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $nomor_kartu = $input['nomorKartu'];
    $nama = $input['namaLengkap'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_BCRYPT);
    $noinduk = $input['noInduk'];
    $tempat_lahir = $input['tempatLahir'];
    $tanggal_lahir = $input['tanggalLahir'];
    $jenis_kelamin = isset($input['jenis_kelamin']) ? $input['jenis_kelamin'] : null;
    $jabatan = $input['jabatan'];
    $role = $input['role'];
    
     // Cek apakah nomor_kartu ada di tabel anonim
    $checkQuery = "SELECT * FROM tb_anonim WHERE nomor_kartu = '$nomor_kartu'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Jika ada, hapus nomor_kartu dari tabel anonim
        $deleteQuery = "DELETE FROM tb_anonim WHERE nomor_kartu = '$nomor_kartu'";
        mysqli_query($conn, $deleteQuery);
    }

    $query = "INSERT INTO tb_pengguna (id_pg, nomor_kartu, nama, email, password, noinduk, tempat_lahir, tanggal_lahir, jenis_kelamin, jabatan, role) VALUES (UUID(), '$nomor_kartu', '$nama', '$email', '$password', '$noinduk', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$jabatan', '$role')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } elseif (!$nomor_kartu || !$nama || !$email || !$password || !$noinduk || !$tempat_lahir || !$tanggal_lahir || !$jenis_kelamin || !$jabatan || !$role) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit();
    } elseif (!mysqli_query($conn, $query)) {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
}
