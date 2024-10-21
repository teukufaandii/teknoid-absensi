<?php
session_start();
include '../../db/db_connect.php';

$output = '';
if(isset($_POST['query'])) {
    $search = $_POST['query'];
    // Query untuk mencari pegawai berdasarkan nomor induk atau nama
    $query = "SELECT * FROM tb_pengguna 
              WHERE noinduk LIKE '%$search%' OR nama LIKE '%$search%' OR role LIKE '%$search%'";
} else {
    // Jika tidak ada query pencarian, tampilkan semua data pegawai
    $query = "SELECT * FROM tb_pengguna";
}

$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0) {
    $counter = 1;
    $output .= '<table id="tablePegawai" class="min-w-full bg-white border">';
    $output .= '<thead>
                    <tr class="bg-purpleNavbar text-white rounded-t-lg">
                        <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Induk</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>';
    while($row = mysqli_fetch_array($result)) {
        $output .= '<tbody>';
        $output .= '<tr class="bg-gray-100">';
        $output .= '<td class="px-6 py-2 text-center">'. $counter++ .'</td>';
        $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['noinduk']) .'</td>';
        $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['nama']) .'</td>';
        $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['role']) .'</td>';
        $output .= '<td class="px-6 py-2 text-center">
                      <a href="editDataPegawai.php?id_pg='. $row['id_pg'] .'">
                        <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Edit</button>
                      </a>
                    </td>';
        $output .= '</tr>';
        $output .= '</tbody>';
    }
    $output .= '</table>';
    echo $output;
} else {
    echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
}
?>
