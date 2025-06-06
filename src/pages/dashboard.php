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

date_default_timezone_set('Asia/Jakarta');
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
$years = date('Y');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="src/pages/admin/css/dataAbsensi.css">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="src/pages/css/dashboard.css">
    <!-- Chart  -->
    <script src="node_modules/chart.js/dist/chart.umd.js"></script>
    <script src="src/pages/js/chartUser.js"></script>
    <script src="src/pages/js/chartAdmin.js"></script>
    <style>
        .chart-container {
            height: 600px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            background: white;
            padding: 20px;
        }

        /* Enhanced Card Styles */
        .dashboard-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Updated card icon styles - positioned at top right */
        .card-icon {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        /* Adjust card content to make room for icon */
        .card-content {
            padding-right: 70px;
            /* Make space for icon */
            position: relative;
            z-index: 5;
        }

        .card-stats {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 400px;
                padding: 16px;
            }

            .card-icon {
                width: 40px;
                height: 40px;
                top: 12px;
                right: 12px;
            }

            .card-content {
                padding-right: 55px;
            }
        }
    </style>
</head>

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <!-- Side Navigation -->
        <?php include('src/pages/navbar/sidenav.php') ?>

        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('src/pages/navbar/topnav.php') ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-mainBgColor mainContent">
                <?php if ($role === 'admin'): ?>
                    <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold"> Dashboard </h1>
                    <?php echo '<h1 class="text-base sm:text-lg md:text-2xl mt-6 font-Poppins font-normal"> Data Bulan <strong> ' . $months[date('n')] . ' </strong> </h1>'; ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <div class='dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag'>
                            <div class="card-icon">
                                <i class="fas fa-user-check text-white text-xl"></i>
                            </div>
                            <div class="card-content">
                                <h2 class='text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1'>Total Hadir</h2>
                                <p id='total-hadir' class='text-white text-xl sm:text-2xl font-bold'>Loading...</p>
                                <div class="card-stats text-white">
                                </div>
                            </div>
                        </div>

                        <div class='dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag'>
                            <div class="card-icon">
                                <i class="fas fa-user-times text-white text-xl"></i>
                            </div>
                            <div class="card-content">
                                <h2 class='text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1'>Total Tidak Hadir</h2>
                                <p id='total-absen' class='text-white text-xl sm:text-2xl font-bold'>Loading...</p>
                                <div class="card-stats text-white">
                                </div>
                            </div>
                        </div>

                        <div class='dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag'>
                            <div class="card-icon">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <div class="card-content">
                                <h2 class='text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1'>Total Terlambat Hadir</h2>
                                <p id='total-telat' class='text-white text-xl sm:text-2xl font-bold'>Loading...</p>
                                <div class="card-stats text-white">
                                </div>
                            </div>
                        </div>

                        <div class='dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag'>
                            <div class="card-icon">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div class="card-content">
                                <h2 class='text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1'>Total Karyawan</h2>
                                <p id='total-karyawan' class='text-white text-xl sm:text-2xl font-bold'>Loading...</p>
                                <div class="card-stats text-white">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container w-full flex justify-center bg-white rounded-lg mt-8">
                        <canvas id="absenceChartAdmin"></canvas>
                    </div>
                <?php elseif ($role === 'user'): ?>
                    <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold"> Dashboard </h1>
                    <?php echo '<h1 class="text-base sm:text-lg md:text-2xl mt-6 font-Poppins font-normal"> Data Bulan <strong> ' . $months[date('n')] . ' </strong> </h1>'; ?>
                    <div class="separator">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                            <div class="dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag">
                                <div class="card-icon">
                                    <i class="fas fa-sign-in-alt text-white text-xl"></i>
                                </div>
                                <div class="card-content">
                                    <h2 class="text-sm sm:text-lg font-medium mb-2 border-b-2 pb-1 border-white text-white">Total Masuk</h2>
                                    <p id="totalMasuk" class="text-white text-xl sm:text-2xl font-bold">Loading...</p>
                                    <div class="card-stats text-white">
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag">
                                <div class="card-icon">
                                    <i class="fas fa-thermometer-half text-white text-xl"></i>
                                </div>
                                <div class="card-content">
                                    <h2 class="text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Total Sakit</h2>
                                    <p id="totalSakit" class="text-white text-xl sm:text-2xl font-bold">Loading...</p>
                                    <div class="card-stats text-white">
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag">
                                <div class="card-icon">
                                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                                </div>
                                <div class="card-content">
                                    <h2 class="text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Total Izin</h2>
                                    <p id="totalIzin" class="text-white text-xl sm:text-2xl font-bold">Loading...</p>
                                    <div class="card-stats text-white">
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-card bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow-dashboardTag">
                                <div class="card-icon">
                                    <i class="fas fa-umbrella-beach text-white text-xl"></i>
                                </div>
                                <div class="card-content">
                                    <h2 class="text-sm sm:text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Total Cuti</h2>
                                    <p id="totalCuti" class="text-white text-xl sm:text-2xl font-bold">Loading...</p>
                                    <div class="card-stats text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="status-absen">
                            Status Absen Hari Ini:
                        </div>
                        <div class="chart-container w-full flex justify-center p-8 bg-white rounded-lg">
                            <canvas id="absenceChartUser" class=""></canvas>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include('src/pages/navbar/profileInfo.php') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($role === 'admin'): ?>
                const endpoints = {
                    'total-karyawan': 'api/users/get-users',
                    'total-absen': 'api/details/get-by-alpha',
                    'total-hadir': 'api/details/get-by-presence',
                    'total-telat': 'api/details/get-by-late'
                };

                function fetchData(elementId, url) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            const totalKey = elementId === 'total-telat' ? 'total_telat' : 'total';
                            if (data[totalKey] !== undefined) {
                                $('#' + elementId).text(data[totalKey]);
                            } else {
                                $('#' + elementId).text('Invalid data format');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error fetching data: ', textStatus, errorThrown);
                            $('#' + elementId).text('Error loading data');
                        }
                    });
                }

                for (const [id, url] of Object.entries(endpoints)) {
                    fetchData(id, url);
                }
            <?php elseif ($role === 'user'): ?>
                $(document).ready(function() {
                    $.ajax({
                        url: 'api/user/get-status',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('.status-absen').text('Status Absen Hari Ini: ' + response.message)
                                    .removeClass('bg-red-500 bg-green-500')
                                    .addClass(response.color);
                            } else {
                                console.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error: ' + status + error);
                        }
                    });
                });

                $.ajax({
                    url: 'api/users/get-current-user',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            $('#totalCuti').text(data.jatah_cuti);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });

                $.ajax({
                    url: 'api/user/get-details',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            $('#totalMasuk').text(data.card.total_hadir);
                            $('#totalSakit').text(data.card.total_sakit);
                            $('#totalIzin').text(data.card.total_izin);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
            <?php endif; ?>
        });
    </script>

</body>

</html>