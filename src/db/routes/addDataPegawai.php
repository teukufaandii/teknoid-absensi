<?php

include '../../db/db_connect.php';
session_start();

// Periksa apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari body request
    $input = json_decode(file_get_contents('php://input'), true);

    $nomor_kartu = $input['nomorKartu'];
    $nama = $input['namaLengkap'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_BCRYPT);
    $noinduk = $input['noInduk'];
    $tempat_lahir = $input['tempatLahir'];
    $tanggal_lahir = $input['tanggalLahir'];
    $jenis_kelamin = isset($input['gender']) ? $input['gender'] : null;
    $jabatan = $input['jabatan'];

    // Lakukan validasi dan proses simpan ke database
    // Contoh query untuk menyimpan data ke database (disesuaikan dengan database Anda)
    $query = "INSERT INTO tb_pengguna (id_pg, nomor_kartu, nama, email, password, noinduk, tempat_lahir, tanggal_lahir, jenis_kelamin, jabatan) VALUES (UUID(), '$nomor_kartu', '$nama', '$email', '$password', '$noinduk', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$jabatan')";

    // Eksekusi query dan kirim respon
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } elseif (!$nomor_kartu || !$nama || !$email || !$password || !$noinduk || !$tempat_lahir || !$tanggal_lahir || !$jenis_kelamin || !$jabatan) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit();
    } elseif (!mysqli_query($conn, $query)) {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
}
