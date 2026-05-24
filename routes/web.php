<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KehadiranController;

Route::get('/', function () {

    return redirect('/login');

});

Route::get('/login', function () {

    return Inertia::render('auth/Login');

})->name('login');

Route::post('/login', [AuthController::class, 'login']);



/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', function () {

    return Inertia::render('AdminDashboard', [

        'user' => auth()->user(),

    ]);

})->middleware('auth');

Route::get('/dosen/dashboard', function () {

    return Inertia::render('DosenDashboard', [

        'user' => auth()->user(),

    ]);

})->middleware('auth');

Route::get('/mahasiswa/dashboard', function () {

    return Inertia::render('Dashboard', [

        'user' => auth()->user(),

    ]);

})->middleware('auth');



/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout']);



/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::get('/profile', function () {

    return Inertia::render('Profile');

})->middleware('auth');

Route::post('/profile', [AuthController::class, 'updateProfile']);



/*
|--------------------------------------------------------------------------
| AKUN
|--------------------------------------------------------------------------
*/

Route::get('/akun', function () {

    return Inertia::render('Akun', [

        'user' => auth()->user(),

    ]);

})->middleware('auth');

Route::post('/akun/password', [AuthController::class, 'updatePassword']);

Route::post('/akun/photo', [AuthController::class, 'uploadPhoto']);



/*
|--------------------------------------------------------------------------
| MAHASISWA DOSEN
|--------------------------------------------------------------------------
*/

Route::get('/mahasiswa', [MahasiswaController::class, 'index']);

Route::post('/mahasiswa', [MahasiswaController::class, 'store']);

Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy']);



/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/mahasiswa', function () {

    return Inertia::render('AdminMahasiswa', [

        'mahasiswa' => \App\Models\User::where('role', 'mahasiswa')->get(),

    ]);

});

Route::get('/admin/dosen', [AdminController::class, 'dosen']);

Route::post('/admin/dosen', [AdminController::class, 'storeDosen']);

Route::post('/admin/mahasiswa', [AdminController::class, 'storeMahasiswa']);

/*
|--------------------------------------------------------------------------
| KEHADIRAN
|--------------------------------------------------------------------------
*/

// Rekap kehadiran untuk dosen/admin
Route::get('/rekap', [KehadiranController::class, 'index'])
    ->middleware('auth');

// Riwayat absensi mahasiswa
Route::get('/riwayat-absensi', [KehadiranController::class, 'riwayat'])
    ->middleware('auth');