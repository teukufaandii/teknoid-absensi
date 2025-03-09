<?php
session_start();
require_once 'src/db/db_connect.php';

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header('Location: unauthorized');
    exit();
}

$option = $_GET['option'] ?? null;

if ($option) {
    if ($option === 'bulanan') {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
    } elseif ($option === 'mingguan') {
        $startDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Ngawur lu?']));
    }

    $dayoffQuery = "SELECT tanggal_mulai, tanggal_akhir FROM tb_dayoff WHERE tanggal_mulai <= ? AND tanggal_akhir >= ?";
    $stmt = $conn->prepare($dayoffQuery);
    $stmt->bind_param('ss', $endDate, $startDate);
    $stmt->execute();
    $dayoffResult = $stmt->get_result();

    $dayOffDates = [];
    while ($row = $dayoffResult->fetch_assoc()) {
        $start = new DateTime($row['tanggal_mulai']);
        $end = new DateTime($row['tanggal_akhir']);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

        foreach ($period as $date) {
            $dayOffDates[] = $date->format('Y-m-d');
        }
    }

    $usersQuery = "SELECT id_pg, nomor_kartu FROM tb_pengguna WHERE role = 'user'";
    $usersResult = $conn->query($usersQuery);

    if ($usersResult->num_rows > 0) {
        $insertQuery = "INSERT INTO tb_detail (id, id_pg, nomor_kartu, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        $dataExists = false;
        $dataInserted = false;

        while ($user = $usersResult->fetch_assoc()) {
            $id_pg = $user['id_pg'];
            $nomor_kartu = $user['nomor_kartu'];

            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                if (date('N', strtotime($currentDate)) == 7 || in_array($currentDate, $dayOffDates)) {
                    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                    continue;
                }

                // Cek apakah data sudah ada
                $checkQuery = "SELECT COUNT(*) as total FROM tb_detail WHERE id_pg = ? AND tanggal = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param('ss', $id_pg, $currentDate);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $row = $checkResult->fetch_assoc();

                if ($row['total'] > 0) {
                    $dataExists = true;
                } else {
                    $details_id = 'details_' . uniqid();
                    $keterangan = 'alpha';
                    $insertStmt->bind_param('sssss', $details_id, $id_pg, $nomor_kartu, $currentDate, $keterangan);
                    if ($insertStmt->execute()) {
                        $dataInserted = true;
                    }
                }

                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        $insertStmt->close();

        if ($dataExists && !$dataInserted) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal generate, semua data dalam rentang tanggal sudah ada.']);
        } elseif ($dataInserted) {
            echo json_encode(['status' => 'success', 'message' => 'Detail absensi berhasil dihasilkan.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada pengguna dengan role user.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Hayolo mau ngapain di sini']);
}
