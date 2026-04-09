<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;

// ── Public routes (tanpa auth) ─────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// ── Protected routes (semua butuh login) ───────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard & Logout
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.index');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });

    // Kelas
    Route::get('/kelas',              [App\Http\Controllers\Apps\KelasController::class, 'index'])->name('Kelas.index');
    Route::get('/kelas/add',          [App\Http\Controllers\Apps\KelasController::class, 'create'])->name('Kelas.create');
    Route::post('/kelas/add',         [App\Http\Controllers\Apps\KelasController::class, 'store'])->name('Kelas.store');
    Route::get('/kelas/{id}/edit',    [App\Http\Controllers\Apps\KelasController::class, 'edit'])->name('Kelas.edit');
    Route::put('/kelas/{id}',         [App\Http\Controllers\Apps\KelasController::class, 'update'])->name('Kelas.update');
    Route::delete('/kelas/{id}',      [App\Http\Controllers\Apps\KelasController::class, 'destroy'])->name('Kelas.destroy');

    // Periode Akademik
    Route::get('/periodeakademik',           [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'index'])->name('PeriodeAkademik.index');
    Route::get('/periodeakademik/add',       [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'create'])->name('PeriodeAkademik.create');
    Route::post('/periodeakademik/add',      [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'store'])->name('PeriodeAkademik.store');
    Route::get('/periodeakademik/{id}/edit', [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'edit'])->name('PeriodeAkademik.edit');
    Route::put('/periodeakademik/{id}',      [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'update'])->name('PeriodeAkademik.update');
    Route::delete('/periodeakademik/{id}',   [App\Http\Controllers\Apps\PeriodeAkademikController::class, 'destroy'])->name('PeriodeAkademik.destroy');

    // Absensi
    Route::prefix('absensi')->name('Absensi.')->group(function () {
        Route::get('/',          [App\Http\Controllers\Apps\AbsensiController::class, 'index'])->name('index');
        Route::get('/add',       [App\Http\Controllers\Apps\AbsensiController::class, 'create'])->name('create');
        Route::post('/add',      [App\Http\Controllers\Apps\AbsensiController::class, 'store'])->name('store');
        Route::get('/{id}/isi',  [App\Http\Controllers\Apps\AbsensiController::class, 'isiAbsensi'])->name('isiAbsensi');
        Route::post('/{id}/isi', [App\Http\Controllers\Apps\AbsensiController::class, 'storeDetail'])->name('storeDetail');
        Route::get('/{id}',      [App\Http\Controllers\Apps\AbsensiController::class, 'show'])->name('show');
        Route::delete('/{id}',   [App\Http\Controllers\Apps\AbsensiController::class, 'destroy'])->name('destroy');
    });

    // Keterlambatan
    Route::prefix('keterlambatan')->name('Keterlambatan.')->group(function () {
        Route::get('/',        [App\Http\Controllers\Apps\KeterlambatanController::class, 'index'])->name('index');
        Route::get('/add',     [App\Http\Controllers\Apps\KeterlambatanController::class, 'create'])->name('create');
        Route::post('/add',    [App\Http\Controllers\Apps\KeterlambatanController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\Apps\KeterlambatanController::class, 'destroy'])->name('destroy');
    });

    // Dispensasi
    Route::prefix('dispensasi')->name('Dispensasi.')->group(function () {
        Route::get('/',                    [App\Http\Controllers\Apps\DispensasiController::class, 'index'])->name('index');
        Route::get('/add',                 [App\Http\Controllers\Apps\DispensasiController::class, 'create'])->name('create');
        Route::post('/add',                [App\Http\Controllers\Apps\DispensasiController::class, 'store'])->name('store');
        Route::get('/{id}',                [App\Http\Controllers\Apps\DispensasiController::class, 'show'])->name('show');
        Route::delete('/{id}',             [App\Http\Controllers\Apps\DispensasiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/detail',        [App\Http\Controllers\Apps\DispensasiController::class, 'storeDetail'])->name('storeDetail');
        Route::delete('/detail/{id}',      [App\Http\Controllers\Apps\DispensasiController::class, 'destroyDetail'])->name('destroyDetail');
        Route::patch('/{id}/verifikasi', [App\Http\Controllers\Apps\DispensasiController::class, 'verifikasi'])->name('verifikasi');
    });

    // Organisasi
    Route::get('/organisasi',                    [App\Http\Controllers\Apps\OrganisasiController::class, 'index'])->name('Organisasi.index');
    Route::get('/organisasi/add',                [App\Http\Controllers\Apps\OrganisasiController::class, 'create'])->name('Organisasi.create');
    Route::post('/organisasi/add',               [App\Http\Controllers\Apps\OrganisasiController::class, 'store'])->name('Organisasi.store');
    Route::get('/organisasi/{id}/show',          [App\Http\Controllers\Apps\OrganisasiController::class, 'show'])->name('Organisasi.show');
    Route::get('/organisasi/{id}/edit',          [App\Http\Controllers\Apps\OrganisasiController::class, 'edit'])->name('Organisasi.edit');
    Route::put('/organisasi/{id}',               [App\Http\Controllers\Apps\OrganisasiController::class, 'update'])->name('Organisasi.update');
    Route::delete('/organisasi/{id}',            [App\Http\Controllers\Apps\OrganisasiController::class, 'destroy'])->name('Organisasi.destroy');
    Route::post('/organisasi/{id}/anggota',               [App\Http\Controllers\Apps\OrganisasiController::class, 'anggotaStore'])->name('Organisasi.anggota.store');
    Route::delete('/organisasi/{id}/anggota/{anggotaId}', [App\Http\Controllers\Apps\OrganisasiController::class, 'anggotaDestroy'])->name('Organisasi.anggota.destroy');

    // Guru
    Route::get('/guru',         [App\Http\Controllers\Admin\GuruController::class, 'index'])->name('Guru.index');
    Route::get('/guru/add',     [App\Http\Controllers\Admin\GuruController::class, 'create'])->name('Guru.create');
    Route::post('/guru/add',    [App\Http\Controllers\Admin\GuruController::class, 'store'])->name('Guru.store');
    Route::get('/guru/{id}',    [App\Http\Controllers\Admin\GuruController::class, 'edit'])->name('Guru.edit');
    Route::put('/guru/{id}',    [App\Http\Controllers\Admin\GuruController::class, 'update'])->name('Guru.update');
    Route::delete('/guru/{id}', [App\Http\Controllers\Admin\GuruController::class, 'destroy'])->name('Guru.destroy');

    // Siswa
    Route::get('/siswa',         [App\Http\Controllers\Admin\SiswaController::class, 'index'])->name('Siswa.index');
    Route::get('/siswa/add',     [App\Http\Controllers\Admin\SiswaController::class, 'create'])->name('Siswa.create');
    Route::post('/siswa/add',    [App\Http\Controllers\Admin\SiswaController::class, 'store'])->name('Siswa.store');
    Route::get('/siswa/{id}',    [App\Http\Controllers\Admin\SiswaController::class, 'edit'])->name('Siswa.edit');
    Route::put('/siswa/{id}',    [App\Http\Controllers\Admin\SiswaController::class, 'update'])->name('Siswa.update');
    Route::delete('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'destroy'])->name('Siswa.destroy');

    // Staff
    Route::get('/staff',              [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('Staff.index');
    Route::get('/staff/add',          [App\Http\Controllers\Admin\StaffController::class, 'create'])->name('Staff.create');
    Route::post('/staff/add',         [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('Staff.store');
    Route::get('/staff/{id}/edit',    [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('Staff.edit');
    Route::put('/staff/{id}',         [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('Staff.update');
    Route::delete('/staff/{id}',      [App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('Staff.destroy');

    // Kegiatan
    Route::get('/kegiatan',       [App\Http\Controllers\Admin\KegiatanController::class, 'index'])->name('Kegiatan.index');
    Route::get('/kegiatan/add',   [App\Http\Controllers\Admin\KegiatanController::class, 'create'])->name('Kegiatan.create');
    Route::post('/kegiatan/add',  [App\Http\Controllers\Admin\KegiatanController::class, 'store'])->name('Kegiatan.store');
    Route::get('/kegiatan/{id}',  [App\Http\Controllers\Admin\KegiatanController::class, 'edit'])->name('Kegiatan.edit');
    Route::post('/kegiatan/{id}', [App\Http\Controllers\Admin\KegiatanController::class, 'update'])->name('Kegiatan.update');
    Route::put('/kegiatan/{id}',  [App\Http\Controllers\Admin\KegiatanController::class, 'destroy'])->name('Kegiatan.destroy');

    // User Management
    Route::get('/user-management',           [App\Http\Controllers\UserManagement\UserManagementController::class, 'index'])->name('UserManagement.index');
    Route::get('/user-management/add',       [App\Http\Controllers\UserManagement\UserManagementController::class, 'create'])->name('UserManagement.create');
    Route::post('/user-management/add',      [App\Http\Controllers\UserManagement\UserManagementController::class, 'store'])->name('UserManagement.store');
    Route::get('/user-management/{id}/edit', [App\Http\Controllers\UserManagement\UserManagementController::class, 'edit'])->name('UserManagement.edit');
    Route::put('/user-management/{id}',      [App\Http\Controllers\UserManagement\UserManagementController::class, 'update'])->name('UserManagement.update');
    Route::delete('/user-management/{id}',   [App\Http\Controllers\UserManagement\UserManagementController::class, 'destroy'])->name('UserManagement.destroy');

});