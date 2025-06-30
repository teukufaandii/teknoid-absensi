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

// Ambil nomor kartu dari URL
$nomorKartu = isset($_GET['nomor_kartu']) ? htmlspecialchars($_GET['nomor_kartu']) : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Pegawai</title>
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
        <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Tambah Data Pegawai</h1>
        <div class="w-full mx-auto py-6">
          <form id="pegawaiForm" novalidate>
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu <span class="text-red-500">*</span></label>
              <input
                type="text"
                name="nomorKartu"
                value="<?php echo $nomorKartu; ?>"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                placeholder="Masukkan Nomor Kartu"
                required />
              <div class="text-red-600 text-sm hidden error-message" id="error-nomorKartu">Nomor kartu harus diisi</div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
              <input
                type="text"
                name="namaLengkap"
                placeholder="Masukkan Nama Lengkap"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
              <div class="text-red-600 text-sm hidden error-message" id="error-namaLengkap">Nama lengkap harus diisi</div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Email <span class="text-red-500">*</span></label>
              <input
                type="email"
                name="email"
                placeholder="Masukkan Email"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
              <div class="text-red-600 text-sm hidden error-message" id="error-email">Email harus diisi dengan format yang benar</div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Password <span class="text-red-500">*</span></label>
              <input
                type="text"
                name="password"
                placeholder="Masukkan Password"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
              <div class="text-red-600 text-sm hidden error-message" id="error-password">Password harus diisi</div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">NIDN <span class="text-red-500">*</span></label>
              <input
                type="text"
                name="noInduk"
                placeholder="Masukkan NIDN"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                required
                onkeypress="return isNumberKey(event)"
                title="Hanya angka yang diperbolehkan" />
              <div class="text-red-600 text-sm hidden error-message" id="error-noInduk">NIDN harus diisi</div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
              <div class="flex items-center space-x-6">
                <label class="flex items-center text-gray-600">
                  <input
                    type="radio"
                    name="gender"
                    value="Laki-laki"
                    class="hidden"
                    checked required />
                  <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                  Laki-Laki
                </label>
                <label class="flex items-center text-gray-600">
                  <input
                    type="radio"
                    name="gender"
                    value="Perempuan"
                    class="hidden" required />
                  <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                  Perempuan
                </label>
              </div>
            </div>

            <label class="block text-gray-700 font-semibold mb-2">Tempat, Tanggal Lahir <span class="text-red-500">*</span></label>

            <div class="mb-4 grid" style="grid-template-columns: 1fr 1fr; gap: 6px; grid-template-areas: 'menu main';">
              <div style="grid-area: menu;">
                <input
                  type="text"
                  name="tempatLahir"
                  placeholder="Masukkan Nama Tempat Lahir"
                  class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
                <div class="text-red-600 text-sm hidden error-message" id="error-tempatLahir">Tempat lahir harus diisi</div>
              </div>
              <div style="grid-area: main;">
                <div class="relative">
                  <input
                    type="date"
                    name="tanggalLahir"
                    placeholder="Masukkan Tanggal Lahir"
                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
                  <div class="text-red-600 text-sm hidden error-message" id="error-tanggalLahir">Tanggal lahir harus diisi</div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Jabatan <span class="text-red-500">*</span></label>
              <div class="relative w-full">
                <select name="jabatan" class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required>
                  <option value="" hidden>Pilih Jabatan</option>
                  <option value="Pimpinan">Pimpinan</option>
                  <option value="Dosen Struktural">Dosen Struktural</option>
                  <option value="Dosen Tetap FEB">Dosen Tetap FEB</option>
                  <option value="Dosen Tetap FTD">Dosen Tetap FTD</option>
                  <option value="Karyawan">Karyawan</option>
                  <option value="Customer Service">Customer Service</option>
                </select>
                <div class="text-red-600 text-sm hidden error-message" id="error-jabatan">Jabatan harus dipilih</div>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Hak Akses <span class="text-red-500">*</span></label>
              <div class="relative w-full">
                <select name="role" class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required>
                  <option value="" hidden>Pilih Hak Akses</option>
                  <option value="admin">Admin</option>
                  <option value="user">User</option>
                </select>
                <div class="text-red-600 text-sm hidden error-message" id="error-role">Hak akses harus dipilih</div>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="flex justify-between mt-6">
              <button type="button" class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200" onclick="window.location.href='/teknoid-absensi/pegawai'">Batal</button>
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
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    return true;
  }

  function validateAndSave() {
    document.querySelectorAll('.error-message').forEach(el => {
      el.classList.add('hidden');
    });

    let isValid = true;

    // Validasi input field
    const requiredFields = [{
        name: 'nomorKartu',
        label: 'Nomor Kartu'
      },
      {
        name: 'namaLengkap',
        label: 'Nama Lengkap'
      },
      {
        name: 'email',
        label: 'Email'
      },
      {
        name: 'password',
        label: 'Password'
      },
      {
        name: 'noInduk',
        label: 'NIDN'
      },
      {
        name: 'tempatLahir',
        label: 'Tempat Lahir'
      },
      {
        name: 'tanggalLahir',
        label: 'Tanggal Lahir'
      }
    ];

    requiredFields.forEach(field => {
      const input = document.querySelector(`[name="${field.name}"]`);
      if (!input.value.trim()) {
        document.getElementById(`error-${field.name}`).classList.remove('hidden');
        isValid = false;
      }
    });

    // Validasi select fields
    const selectFields = ['jabatan', 'role'];
    selectFields.forEach(field => {
      const select = document.querySelector(`select[name="${field}"]`);
      if (!select.value) {
        document.getElementById(`error-${field}`).classList.remove('hidden');
        isValid = false;
      }
    });

    // Validasi format email
    const emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|yahoo\.co\.id|outlook\.com|hotmail\.com|icloud\.com)$/;
    const email = document.querySelector('input[name="email"]').value;

    if (email.trim() && !emailPattern.test(email)) {
      document.getElementById('error-email').textContent = 'Format email tidak valid';
      document.getElementById('error-email').classList.remove('hidden');
      isValid = false;
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
    const data = {
      nomorKartu: document.querySelector('input[name="nomorKartu"]').value.trim(),
      namaLengkap: document.querySelector('input[name="namaLengkap"]').value.trim(),
      email: document.querySelector('input[name="email"]').value.trim(),
      password: document.querySelector('input[name="password"]').value.trim(),
      noInduk: document.querySelector('input[name="noInduk"]').value.trim(),
      tempatLahir: document.querySelector('input[name="tempatLahir"]').value.trim(),
      tanggalLahir: document.querySelector('input[name="tanggalLahir"]').value.trim(),
      jenis_kelamin: document.querySelector('input[name="gender"]:checked').value,
      jabatan: document.querySelector('select[name="jabatan"]').value,
      role: document.querySelector('select[name="role"]').value
    };

    Swal.fire({
      title: 'Konfirmasi',
      text: "Apakah Anda yakin ingin menambahkan data pegawai baru?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, tambahkan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('/teknoid-absensi/api/users/add-user', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Data berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.href = '/teknoid-absensi/pegawai';
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal menambahkan data',
                text: data.message || 'Coba lagi.'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Email Sudah Terdaftar',
            });
          });
      }
    });
  }
</script>

</html>