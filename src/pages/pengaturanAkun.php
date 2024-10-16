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
</head>

<body>
  <div class="flex flex-col md:flex-row lg:flex-row h-screen">
    <!-- Side Navigation -->
    <?php include('./navbar/sidenav.php') ?>

    <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
      <!-- Top Navigation -->
      <?php include('./navbar/topnav.php') ?>


      <!-- Main Content -->
      <main class="flex-1 p-6 bg-mainBgColor mainContent">
        <h1 class="text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold"> Edit Data Pribadi </h1>
        <div class="w-full mx-auto p-6">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
            <input
              type="text"
              placeholder="Masukkan Nomor Kartu"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              defaultValue="123456789" />
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

          <div class="flex justify-between mt-6">
            <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200">Batal</button>
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
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
          alert('Error fetching user data.');
        }
      });
    });
  </script>

  <?php include('./navbar/profileInfo.php') ?>
</body>


</html>