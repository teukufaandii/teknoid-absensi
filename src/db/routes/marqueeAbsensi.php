<?php
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/../db_connect.php';

$months = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$currentMonth = date('n');
$currentYear = date('Y');
$today = date('Y-m-d');
$lastDayThisMonth = date('Y-m-t');
$hMinus7 = date('Y-m-d', strtotime("$lastDayThisMonth -7 days"));

// Paksa tampil untuk testing
$marqueeMessage = '';

// if (true) { <-- force testing
    if ($today >= $hMinus7) {
    $nextMonth = ($currentMonth == 12) ? 1 : $currentMonth + 1;
    $nextYear = ($currentMonth == 12) ? $currentYear + 1 : $currentYear;

    $startNextMonth = "$nextYear-" . str_pad($nextMonth, 2, '0', STR_PAD_LEFT) . "-01";
    $endNextMonth = date("Y-m-t", strtotime($startNextMonth));

    $sql = "SELECT COUNT(*) as total FROM tb_detail WHERE tanggal BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startNextMonth, $endNextMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data['total'] == 0) {
        $marqueeMessage = "Data Bulan " . $months[$nextMonth] . " Belum di-generate, Silahkan Generate!";
    }

    $stmt->close();
}

if (!empty($marqueeMessage)) {
    echo '<marquee behavior="scroll" direction="left" loop="infinite" class="text-red-600 font-medium text-lg sm:text-xl md:text-3xl py-2">' . $marqueeMessage . '</marquee>';
} else {
    echo ''; // jangan kosong/null response
}
