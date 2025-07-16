<?php
ob_start();
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

include 'src/db/db_connect.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$filter = $_GET['filter'] ?? 'harian';
$jabatan = $_GET['jabatan'] ?? 'dosenTetap';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

function getLastColumnLetter($totalCols)
{
    $letters = range('A', 'Z');
    $lastColIndex = $totalCols - 1;
    if ($lastColIndex < 26) {
        return $letters[$lastColIndex];
    } else {
        $firstLetter = $letters[intval($lastColIndex / 26) - 1];
        $secondLetter = $letters[$lastColIndex % 26];
        return $firstLetter . $secondLetter;
    }
}

// Get holiday dates
$libur = [];
$qLibur = mysqli_query($conn, "SELECT tanggal_mulai, tanggal_akhir FROM tb_dayoff WHERE (tanggal_mulai <= '$end' AND tanggal_akhir >= '$start')");
while ($row = mysqli_fetch_assoc($qLibur)) {
    $periodeLibur = new DatePeriod(new DateTime($row['tanggal_mulai']), new DateInterval('P1D'), (new DateTime($row['tanggal_akhir']))->modify('+1 day'));
    foreach ($periodeLibur as $tglLibur) {
        $libur[] = $tglLibur->format('Y-m-d');
    }
}

$period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), (new DateTime($end))->modify('+1 day'));
$dates = [];
$total_days = 0;
foreach ($period as $date) {
    $tgl = $date->format('Y-m-d');
    $isSunday = $date->format('w') == 0;
    $isHoliday = in_array($tgl, $libur);
    $dates[] = ['date' => $tgl, 'day' => $date->format('d'), 'isSunday' => $isSunday, 'isHoliday' => $isHoliday];
    if (!$isSunday && !$isHoliday) $total_days++;
}

$query = "SELECT p.nama, p.id_pg, p.jabatan, p.kampus FROM tb_pengguna p WHERE p.jabatan IN ('Dosen Tetap FEB', 'Dosen Tetap FTD') ORDER BY p.kampus, p.nama";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Data tidak ditemukan untuk filter ini.";
    exit();
}

$dataCiputat = [];
$dataKarawaci = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kampus = strtolower(trim($row['kampus']));
    if ($kampus === 'ciputat') {
        $dataCiputat[] = $row;
    } elseif ($kampus === 'karawaci') {
        $dataKarawaci[] = $row;
    }
}

$spreadsheet = new Spreadsheet();
$date_info = "Mulai Tanggal $start s/d $end";

function createSheetHeader($sheet, $kampusName, $date_info, $dates)
{
    $totalCols = 3 + count($dates);
    $lastCol = getLastColumnLetter($totalCols);

    $sheet->setCellValue('A1', 'HUMAN RESOURCE DEPARTMENT');
    $sheet->mergeCells("A1:$lastCol" . "1");
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A2', 'LAPORAN ABSENSI DOSEN');
    $sheet->mergeCells("A2:$lastCol" . "2");
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A3', 'ITB AHMAD DAHLAN JAKARTA');
    $sheet->mergeCells("A3:$lastCol" . "3");
    $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A4', 'KAMPUS ' . strtoupper($kampusName));
    $sheet->mergeCells("A4:$lastCol" . "4");
    $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A5', $date_info);
    $sheet->mergeCells("A5:$lastCol" . "5");
    $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->fromArray(['No', 'Nama', 'Jabatan'], null, 'A7');
    $col = 'D';
    foreach ($dates as $d) {
        $sheet->setCellValue($col . '7', $d['day']);
        $sheet->getStyle($col . '7')->getFont()->setBold(true);
        $sheet->getStyle($col . '7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $color = $d['isSunday'] || $d['isHoliday'] ? 'f99' : '4CAF50';
        $fontColor = $d['isSunday'] || $d['isHoliday'] ? 'FF0000' : 'FFFFFF';
        $sheet->getStyle($col . '7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
        $sheet->getStyle($col . '7')->getFont()->getColor()->setRGB($fontColor);
        $col++;
    }
    $sheet->getStyle("A7:$lastCol" . "7")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    return 8;
}

function fillSheetData($sheet, $data, $startRow, $dates, $conn)
{
    $row = $startRow;
    $no = 1;
    $grouped = ['Dosen Tetap FEB' => [], 'Dosen Tetap FTD' => []];
    foreach ($data as $d) $grouped[$d['jabatan']][] = $d;

    foreach ($grouped as $jabatan => $items) {
        if (empty($items)) continue;
        $lastCol = getLastColumnLetter(3 + count($dates));

        $sheet->setCellValue("A$row", $jabatan);
        $sheet->mergeCells("A$row:$lastCol$row");
        $sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
        $row++;

        foreach ($items as $item) {
            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $item['nama']);
            $sheet->setCellValue("C$row", $jabatan);

            $col = 'D';
            foreach ($dates as $d) {
                $val = '';
                if (!$d['isSunday'] && !$d['isHoliday']) {
                    $tgl = $d['date'];
                    $id_pg = $item['id_pg'];
                    $q = mysqli_query($conn, "SELECT keterangan FROM tb_detail WHERE id_pg='$id_pg' AND tanggal='$tgl' LIMIT 1");
                    $dataDetail = mysqli_fetch_assoc($q);
                    $val = ($dataDetail && $dataDetail['keterangan'] == 'hadir') ? '1' : '0';
                }
                $sheet->setCellValue($col . $row, $val);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $sheet->getStyle("A$row:$lastCol$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }
    }
    return $row;
}

$sheetIndex = 0;
foreach ([['data' => $dataCiputat, 'name' => 'Ciputat'], ['data' => $dataKarawaci, 'name' => 'Karawaci']] as $kampusData) {
    if (empty($kampusData['data'])) continue;
    $sheet = $sheetIndex > 0 ? $spreadsheet->createSheet() : $spreadsheet->getActiveSheet();
    $sheet->setTitle('Kampus ' . $kampusData['name']);
    $startRow = createSheetHeader($sheet, $kampusData['name'], $date_info, $dates);
    fillSheetData($sheet, $kampusData['data'], $startRow, $dates, $conn);

    $totalCols = 3 + count($dates);
    for ($i = 0; $i < $totalCols; $i++) {
        $col = $i < 26 ? chr(65 + $i) : chr(64 + floor($i / 26)) . chr(65 + $i % 26);
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $sheetIndex++;
}

$spreadsheet->setActiveSheetIndex(0);
$filename = 'data_absensi_dosen_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
