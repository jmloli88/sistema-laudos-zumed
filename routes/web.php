<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth.session');
Route::get('/ver-pdf/{id_clinica}/{id_laudo}', [DashboardController::class, 'verPDF'])
    ->name('ver.pdf');
Route::get('/download-pdf/{id_clinica}/{id_laudo}', [DashboardController::class, 'downloadPDF'])
    ->name('download.pdf');
