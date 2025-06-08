<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: unauthorized');
    exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hari Libur</title>
    <link href="/teknoid-absensi/css/output.css" rel="stylesheet">
    <link href="/teknoid-absensi/src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <!-- Side Navigation -->
        <?php include('src/pages/navbar/sidenav.php') ?>

        <div id="content" class="inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('src/pages/navbar/topnav.php') ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-mainBgColor md:mt-0 mainContent">
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Tambah Hari Libur</h1>
                <div class="w-full mx-auto py-6">
                    <form id="holidayForm" novalidate>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Nama Hari Libur <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="nama_hari_libur"
                                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                                placeholder="Masukkan Nama Hari Libur"
                                required />
                            <div class="text-red-600 text-sm hidden error-message" id="error-nama_hari_libur">Nama hari libur harus diisi</div>
                        </div>

                        <div class="">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input
                                    type="date"
                                    name="tanggal_mulai"
                                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                                    required />
                                <div class="text-red-600 text-sm hidden error-message" id="error-tanggal_mulai">Tanggal mulai harus diisi</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tanggal Akhir <span class="text-red-500">*</span></label>
                                <input
                                    type="date"
                                    name="tanggal_akhir"
                                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                                    required />
                                <div class="text-red-600 text-sm hidden error-message" id="error-tanggal_akhir">Tanggal akhir harus diisi</div>
                                <div class="text-red-600 text-sm hidden error-message" id="error-tanggalRange">Tanggal akhir tidak boleh lebih awal dari tanggal mulai</div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-6">
                            <button type="button" class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200" onclick="window.location.href='/teknoid-absensi/dayoff'">Batal</button>
                            <button type="button" class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200" onclick="validateAndSave()">Simpan</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include('src/pages/navbar/profileInfo.php') ?>
</body>

<script>
    function validateAndSave() {
        // Reset error messages
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
        });

        let isValid = true;

        // Validasi input field yang required
        const requiredFields = [{
                name: 'nama_hari_libur',
                label: 'Nama Hari Libur'
            },
            {
                name: 'tanggal_mulai',
                label: 'Tanggal Mulai'
            },
            {
                name: 'tanggal_akhir',
                label: 'Tanggal Akhir'
            }
        ];

        requiredFields.forEach(field => {
            const input = document.querySelector(`[name="${field.name}"]`);
            if (!input.value.trim()) {
                document.getElementById(`error-${field.name}`).classList.remove('hidden');
                isValid = false;
            }
        });

        // Validasi range tanggal
        const tanggal_mulai = document.querySelector('input[name="tanggal_mulai"]').value;
        const tanggal_akhir = document.querySelector('input[name="tanggal_akhir"]').value;

        if (tanggal_mulai && tanggal_akhir) {
            const startDate = new Date(tanggal_mulai);
            const endDate = new Date(tanggal_akhir);

            if (endDate < startDate) {
                document.getElementById('error-tanggalRange').classList.remove('hidden');
                isValid = false;
            }
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Harap isi semua field yang diperlukan dengan benar'
            });
            return;
        }

        saveData();
    }

    function saveData() {
        const formData = new FormData();
        formData.append('nama_hari_libur', document.querySelector('input[name="nama_hari_libur"]').value.trim());
        formData.append('tanggal_mulai', document.querySelector('input[name="tanggal_mulai"]').value.trim());
        formData.append('tanggal_akhir', document.querySelector('input[name="tanggal_akhir"]').value.trim());


        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menambahkan hari libur ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Sesuaikan dengan endpoint API yang ada
                fetch('/teknoid-absensi/api/dayoff/post', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Hari libur berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/teknoid-absensi/dayoff';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menambahkan hari libur',
                                text: data.message || 'Coba lagi.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Coba lagi nanti.'
                        });
                    });
            }
        });
    }

    // Auto-fill tanggal akhir jika sama dengan tanggal mulai
    document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', function() {
        const tanggal_akhir = document.querySelector('input[name="tanggal_akhir"]');
        if (!tanggal_akhir.value) {
            tanggal_akhir.value = this.value;
        }
    });
</script>

</html>