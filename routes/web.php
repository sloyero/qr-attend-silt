<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

use App\Http\Controllers\AuthController;

Route::get('/', function () {

    return Inertia::render('Home');

});

Route::get('/login', function () {

    return Inertia::render('auth/Login');

})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', function () {

    return Inertia::render('Dashboard');

})->middleware('auth');

Route::post('/logout', [AuthController::class, 'logout']);