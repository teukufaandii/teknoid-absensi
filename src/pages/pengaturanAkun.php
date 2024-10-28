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

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan Akun</title>
  <link href="../../css/output.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/dataAbsensi.css">
  <link href="./css/font/poppins-font.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="flex flex-col md:flex-row lg:flex-row h-screen">
    <?php include('./navbar/sidenav.php') ?>

    <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
      <?php include('./navbar/topnav.php') ?>

      <div id="notification" class="hidden fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
        <span id="notification-message"></span>
      </div>


      <!-- Main Content -->
      <main class="flex-1 p-6 bg-mainBgColor mainContent">
        <h1 class="text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold"> Edit Data Pribadi </h1>
        <div class="w-full mx-auto p-6">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
            <input
              type="text"
              name="nomor_kartu"
              placeholder="Masukkan Nomor Kartu"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              value="123456789"
              readonly />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
            <input
              type="text"
              name="username"
              placeholder="Masukkan Nama Lengkap"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              value="<?php echo $username ?>" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input
              type="email"
              name="email"
              placeholder="Masukkan Email"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">NIDN</label>
            <input
              type="text"
              name="noinduk"
              placeholder="Masukkan Nomor Induk"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Jenis Kelamin</label>
            <div class="flex items-center space-x-6">
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="gender"
                  value="Laki-laki"
                  class="hidden"
                  checked />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Laki-Laki
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="gender"
                  value="Perempuan"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Perempuan
              </label>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
          </div>

        </div>
      </main>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $.ajax({
        url: '../db/routes/fetchCurrentUser.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          if (data.error) {
            alert(data.error);
          } else {
            $('input[name="username"]').val(data.nama);
            $('input[name="email"]').val(data.email);
            $('input[name="noinduk"]').val(data.noinduk);
            $('input[name="nomor_kartu"]').val(data.nomor_kartu);
            $('input[name="gender"][value="' + data.jenis_kelamin + '"]').prop('checked', true);
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
          alert('Error fetching user data.');
        }
      });

      $('button:contains("Simpan")').click(function(e) {
        e.preventDefault();

        // Definisikan pola regex untuk validasi email
        const emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|yahoo\.co\.id|outlook\.com|hotmail\.com|icloud\.com)$/;
        const email = $('input[name="email"]').val();

        // Validasi email menggunakan regex
        if (!emailPattern.test(email)) {
          Swal.fire({
            icon: 'error',
            title: 'Email tidak valid',
            text: 'Masukkan email dengan format yang benar'
          });
          return; // Hentikan proses jika email tidak valid
        }

        Swal.fire({
          title: 'Yakin ingin menyimpan perubahan?',
          text: "Perubahan yang Anda buat akan disimpan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#675EFF',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, simpan!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            const updatedData = {
              username: $('input[name="username"]').val(),
              email: email, 
              noinduk: $('input[name="noinduk"]').val(),
              gender: $('input[name="gender"]:checked').val()
            };

            $.ajax({
              url: '../db/routes/updateMyData.php',
              type: 'POST',
              data: updatedData,
              success: function(response) {
                if (response.error) {
                  Swal.fire('Error', response.error, 'error');
                } else {
                  Swal.fire(
                    'Berhasil!',
                    'Data Anda berhasil diperbarui.',
                    'success'
                  );
                }
              },
              error: function(xhr, status, error) {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
              }
            });
          }
        });
      });
    });
  </script>



  <?php include('./navbar/profileInfo.php') ?>
</body>


</html>