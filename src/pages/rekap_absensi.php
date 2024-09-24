<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];


//mengambil data pengguna
?>

<!DOCTYPE html>





<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body class="flex flex-row h-screen">
    <!-- Side Navigation -->
    <?php include('navbar/sidenav.php') ?>

    <div class="inline-flex flex-col flex-1">
        <!-- Top Navigation -->
        <?php include('navbar/topnav.php') ?>

        <!-- Main Content -->
        <main class="flex-1 h-full p-6 bg-mainBgColor">
            <h1 class="text-3xl border-b py-2 font-Poppins font-semibold">Rekap Data Absensi</h1>
                        <button class="bg-gray-300 text-black px-4 py-4 mt-5 rounded-full text-base font-medium hover:bg-gray-400">
                            Tambah
                        </button>
                        <?php
                                $conn = mysqli_connect("localhost", "root", "", "db_absensi");
                                if ($conn-> connect_error) {
                                }
                                
                                 // pengaturan baris
                                $start = 0;
                                $rows_per_page = 10;

                                // total nomor baris
                                $records = mysqli_query($conn, "SELECT * FROM tb_pengguna");
                                $nr_of_rows = $records->num_rows;

                                // kalkulasi nomor per halaman
                                $pages = ceil($nr_of_rows / $rows_per_page);

                                // start point
                                if(isset($_GET['page-nr'])){
                                    $page = $_GET['page-nr'] - 1;
                                    $start = $page * $rows_per_page;
                                }

                                // tabel db suratmasuk
                                $stmt=$conn->prepare("SELECT * FROM  tb_pengguna LIMIT $start, $rows_per_page");
                                $stmt->execute();
                                $result = $stmt->get_result();
                            ?>
                                                        
                            <table class="min-w-full divide-y divide-gray-200 mt-10">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $counter = $start + 1;
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td class="px-1 py-4 whitespace-nowrap text-center"><?php echo $counter++; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $row["nama"]; ?></td>
                                            <td class="text-right py-4 whitespace-nowrap">
                                                <a href="#.php?ids=<?php echo $row['id_pg']; ?>">
                                                    <button class="bg-blue-500 text-white px-4 py-2 mr-4 rounded-full text-xs font-bold hover:bg-blue-600" title="Edit">
                                                        Edit
                                                    </button>
                                                </a>
                                            </td>
                                    </tr>
                                    <?php 
                                        }}
                                    
                                    ?>
                                </tbody>
                            </table>
            </div>
        </main>
    </div>
</body>

</html>