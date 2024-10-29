<?php
include '../../db/db_connect.php';


$search = isset($_POST['query']) ? trim($_POST['query']) : '';

// Pastikan koneksi ke database sudah benar
if ($conn) {
    // Mencari data anonim berdasarkan input pencarian
    $query = "SELECT * FROM tb_anonim WHERE nomor_kartu LIKE ? OR jam LIKE ? OR tanggal LIKE ? LIMIT 5";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $counter = 1;
    $output = '<table id="tableAnonim" class="min-w-full bg-white border">';
    $output .= '<thead>
                    <tr class="bg-purpleNavbar text-white rounded-t-lg">
                        <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Kartu</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>'; // Pindahkan pembukaan <tbody> ke sini

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $output .= '<tr class="bg-gray-100">';
            $output .= '<td class="px-6 py-2 text-center">'. $counter++ .'</td>';
            $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['nomor_kartu']) .'</td>';
            $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['jam']) .'</td>';
            $output .= '<td class="px-6 py-2 text-center">'. htmlspecialchars($row['tanggal']) .'</td>';
            $output .= '<td class="px-6 py-2 text-center">
                          <a href="#">
                            <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Delete</button>
                          </a>
                        </td>';
            $output .= '</tr>';
        }
    } else {
        $output .= '<tr><td colspan="5" class="text-center">Tidak ada data ditemukan</td></tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';
    echo $output;
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
}

$conn->close();
?>