<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    // Dibatasi 5 percobaan per menit untuk mencegah tebak password.
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');
});

// Semua halaman aplikasi hanya boleh diakses setelah login.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::resource('borrowings', BorrowingController::class)->only(['index', 'show']);
    Route::resource('books', BookController::class);
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');
    Route::resource('members', MemberController::class);

    // Hanya admin yang boleh mengelola user dan mengubah kategori
    // (petugas hanya boleh melihat daftar kategori).
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    });
});
