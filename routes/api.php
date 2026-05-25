<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Kehadiran;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// REST API endpoints
Route::get('/mahasiswa', function () {
    return response()->json(
        User::where('role', 'mahasiswa')->get()
    );
});

Route::get('/absensi', function () {
    return response()->json(
        Kehadiran::with(['user', 'mataKuliah'])->latest()->get()
    );
});
