<?php
ob_start();
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

include 'src/db/db_connect.php';

$filter = $_GET['filter'] ?? 'harian';
$jabatan = $_GET['jabatan'] ?? 'dosenTetap';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="data_absensi_dosen_' . date('Y-m-d') . '.xls";');

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta http-equiv="Content-type" content="text/html;charset=utf-8" />';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'table, th, td { border: 1px solid black; padding: 5px; text-align: center; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
echo '.header { font-size: 16pt; font-weight: bold; text-align: center; }';
echo '.subheader { font-size: 12pt; font-weight: bold; text-align: center; }';
echo '.holiday { background-color: #f99; color: red; }';
echo '</style>';
echo '</head>';
echo '<body>';

// ambil tanggal-tanggal libur
$libur = [];
$qLibur = mysqli_query($conn, "SELECT tanggal_mulai, tanggal_akhir FROM tb_dayoff WHERE (tanggal_mulai <= '$end' AND tanggal_akhir >= '$start')");
while ($row = mysqli_fetch_assoc($qLibur)) {
    $periodeLibur = new DatePeriod(new DateTime($row['tanggal_mulai']), new DateInterval('P1D'), (new DateTime($row['tanggal_akhir']))->modify('+1 day'));
    foreach ($periodeLibur as $tglLibur) {
        $libur[] = $tglLibur->format('Y-m-d');
    }
}

// generate tanggal-tanggal periode yang diminta
$period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), (new DateTime($end))->modify('+1 day'));

$total_days = 0;
foreach ($period as $date) {
    if ($date->format('w') != 0 && !in_array($date->format('Y-m-d'), $libur)) {
        $total_days++;
    }
}
$colspan = count(iterator_to_array($period)) + 3;

echo '<table>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="header">HUMAN RESOURCE DEPARTMENT</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">LAPORAN ABSENSI DOSEN</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">ITB AHMAD DAHLAN JAKARTA</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">Mulai Tanggal ' . $start . ' s/d ' . $end . '</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;"></td></tr>';

// header tabel
echo '<tr>';
echo '<th>No</th>';
echo '<th>Nama</th>';
echo '<th>Jabatan</th>';

$dates = []; // simpan tanggal, hari Minggu & libur
$period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), (new DateTime($end))->modify('+1 day'));

foreach ($period as $date) {
    $tgl = $date->format('Y-m-d');
    $isSunday = $date->format('w') == 0;
    $isHoliday = in_array($tgl, $libur);
    $dates[] = [
        'date' => $tgl,
        'day' => $date->format('d'),
        'isSunday' => $isSunday,
        'isHoliday' => $isHoliday
    ];

    $style = ($isSunday || $isHoliday) ? 'class="holiday"' : '';
    echo '<th ' . $style . '>' . $date->format('d') . '</th>';
}
echo '</tr>';

function fetch_and_display_data($conn, $jabatan, $dates)
{
    $colspan = count($dates) + 3;
    echo '<tr><td colspan="' . $colspan . '" class="subheader">' . $jabatan . '</td></tr>';

    $start = $dates[0]['date'];
    $end = end($dates)['date'];

    $query = "
        SELECT 
            p.nama,
            p.id_pg,
            p.jabatan
        FROM tb_pengguna p
        WHERE p.jabatan = '$jabatan'
        ORDER BY p.nama
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . $row['nama'] . '</td>';
            echo '<td>' . $jabatan . '</td>';

            foreach ($dates as $d) {
                if ($d['isSunday'] || $d['isHoliday']) {
                    echo '<td class="holiday"></td>';
                } else {
                    $tgl = $d['date'];
                    $id_pg = $row['id_pg'];
                    $q = mysqli_query($conn, "
                        SELECT keterangan FROM tb_detail 
                        WHERE id_pg='$id_pg' AND tanggal='$tgl' 
                        LIMIT 1
                    ");
                    $data = mysqli_fetch_assoc($q);
                    $val = ($data && $data['keterangan'] == 'hadir') ? '1' : '0';
                    echo '<td>' . $val . '</td>';
                }
            }

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="' . $colspan . '">Tidak ada data untuk ' . $jabatan . '</td></tr>';
    }
}

fetch_and_display_data($conn, "Dosen Tetap FEB", $dates);
fetch_and_display_data($conn, "Dosen Tetap FTD", $dates);

echo '</table>';
echo '</body>';
echo '</html>';
exit();
?>
