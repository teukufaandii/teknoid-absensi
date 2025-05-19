<?php
require 'vendor/autoload.php';

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$routes = new RouteCollection();

// Rute client
$routes->add('landing_page', new Route('/', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/LandingPage.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('login', new Route('/login', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/login.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('forgot', new Route('/forgot', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/forgot.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('reset', new Route('/reset', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/reset_password.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('dashboard', new Route('/dashboard', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/dashboard.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('setting', new Route('/setting', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/pengaturanAkun.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('pegawai', new Route('/pegawai', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/dataPegawai.php';
        return new Response(ob_get_clean());
    }
]));
//absensi
$routes->add('absensi', new Route('/absensi', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/dataAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('absensi_edit', new Route('/absensi/edit', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/previewDataAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
//absensi
$routes->add('pegawai_edit', new Route('/pegawai/edit', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/editDataPegawai.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('pegawai_tambah', new Route('/pegawai/add', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/tambahPegawai.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('anonim', new Route('/anonim', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/dataAnonim.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('edit-preview_data_absensi', new Route('/absensi/edit/preview', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/editDataAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('dayoff', new Route('/dayoff', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/setDayOff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('dayoff_edit', new Route('/dayoff/edit', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/admin/editDayOff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('pengajuan_cuti', new Route('/pengajuan', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/pages/user/pengajuanCuti.php';
        return new Response(ob_get_clean());
    }
]));

$routes->add('success', new Route('/success', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/success.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('not_authorized', new Route('/unauthorized', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/unauthorized.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('not_found', new Route('/404', [
    '_controller' => function () {
        ob_start();
        include __DIR__ . '/src/not_found.php';
        return new Response(ob_get_clean());
    }
]));


// Routes API
$routes->add('api_auth_login', new Route('/api/auth/login', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/userLogin.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_auth_logout', new Route('/api/auth/logout', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/userLogout.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_auth_forgot', new Route('/api/auth/forgot', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/userForgotPass.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_auth_forgot_password', new Route('/api/auth/forgot-password', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/userForgotPass.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_user', new Route('/api/users/get-users', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getAllUsers.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_current_user', new Route('/api/users/get-current-user', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/fetchCurrentUser.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_details_by_alpha', new Route('/api/details/get-by-alpha', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getAbsenceDetailsByAlpha.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_details_by_presence', new Route('/api/details/get-by-presence', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getAbsenceDetailsByPresence.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_details_by_late', new Route('/api/details/get-by-late', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getAbsenceDetailsByLate.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_chart_admin', new Route('/api/details/get-chart-admin', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getChartAdminDashboard.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_pegawai', new Route('/api/users/get-pegawai', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/fetchDataPegawai.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_update_current', new Route('/api/users/update-current', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/updateMyData.php';
        return new Response(ob_get_clean());
    }
]));
//absensi
$routes->add('api_get_absensi', new Route('/api/users/get-absensi', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getDataAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_generate_absensi_detail', new Route('/api/users/generate-absensi-details', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/generateAbsenceDetails.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_fetch_preview_detail', new Route('/api/users/fetch-preview-detail', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/fetchPreviewData.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_update_preview_absensi', new Route('/api/details/update', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/updateDataAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
//absensi
$routes->add('api_get_pengguna', new Route('/api/users/get-data-pengguna', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/fetchDataPengguna.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_update_pengguna', new Route('/api/users/update-data-pengguna', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/updateDataPengguna.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_hapus_pengguna', new Route('/api/users/delete-user', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/deleteDataPegawai.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_add_pengguna', new Route('api/users/add-user', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/addDataPegawai.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_anonim', new Route('api/anonim/get', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/fetchDataAnonim.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_delete_anonim', new Route('api/anonim/delete', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/deleteDataAnonim.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_dayoff', new Route('api/dayoff/get', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/get_dayoff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_dayoff_by_id', new Route('api/dayoff/get/id', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/get_current_dayoff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_delete_dayoff', new Route('api/dayoff/delete', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/deleteDayoff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_post_dayoff', new Route('api/dayoff/post', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/add_dayoff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_put_dayoff', new Route('api/dayoff/put', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/update_dayoff.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_absen_details', new Route('api/user/get-details', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/getCurrentUserAbsenceDetail.php';
        return new Response(ob_get_clean());
    }
]));
$routes->add('api_get_absen_status', new Route('api/user/get-status', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/get_absen_status.php';
        return new Response(ob_get_clean());
    }
]));
//forgot password
$routes->add('api_forgot_password', new Route('api/auth/reset', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/userResetPass.php';
        return new Response(ob_get_clean());
    }
]));
//untuk download data keseluruhan
$routes->add('api_download_data_karyawan', new Route('api/user/download-karyawan', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/downloadAbsensi.php';
        return new Response(ob_get_clean());
    }
]));
//untuk download data dosen tetap feb ftd
$routes->add('api_download_data_dosen', new Route('api/user/download-dosen', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/downloadDataDosen.php';
        return new Response(ob_get_clean());
    }
]));
//untuk download data per user
$routes->add('api_download_data_user', new Route('api/user/download-data-user', [
    '_controller' => function () {
        include __DIR__ . '/src/db/routes/downloadDataUser.php';
        return new Response(ob_get_clean());
    }
]));



$request = Request::createFromGlobals();
$context = new RequestContext('/teknoid-absensi');
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $response = call_user_func($parameters['_controller']);
} catch (Exception $e) {
    error_log($e->getMessage());

    $response = new Response($e, 404);
    header("Location: 404");
    exit();
}

if (!$response instanceof Response) {
    $response = new Response("Internal Server Error", 500);
}

$response->send();