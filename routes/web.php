<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import semua controller yang digunakan
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PengajuanDashboardController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Npwp\DashboardController as NpwpDashboardController;
use App\Http\Controllers\Ppk\DashboardController as PpkDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rute dashboard utama yang mengarahkan user berdasarkan role setelah login
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    if (in_array($role, ['admin', 'ppk'])) {
        return redirect()->route('admin.pengajuan.index');
    } elseif ($role == 'npwp') {
        return redirect()->route('npwp.dashboard');
    } else {
        return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// Grup rute yang bisa diakses oleh SEMUA user yang sudah login
Route::middleware('auth')->group(function () {
    // Rute untuk manajemen profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute resource untuk pengajuan (Create, Read, Update, Delete) oleh user biasa
    Route::resource('pengajuan', PengajuanController::class);
    
    // Rute pendukung untuk dropdown dinamis (AJAX)
    Route::get('/get-activities/{programId}', [PengajuanController::class, 'getActivities'])->name('getActivities');
    Route::get('/get-kros/{activityId}', [PengajuanController::class, 'getKros'])->name('getKros');
    Route::get('/get-accounts', [PengajuanController::class, 'getAccounts'])->name('getAccounts');
    Route::get('/get-coas/{accountId}', [PengajuanController::class, 'getCoas'])->name('getCoas');
});


// Grup Rute Khusus untuk role 'admin' dan 'ppk'
// Keduanya sekarang dijaga oleh middleware 'is.admin' yang sudah kita perbaiki
Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('programs', ProgramController::class);
    
    // Dashboard utama admin/ppk
    Route::get('/pengajuan', [PengajuanDashboardController::class, 'index'])->name('pengajuan.index');
    
    // Aksi Persetujuan oleh PPK (yang juga admin)
    Route::post('/pengajuan/{pengajuan}/approve-ppk', [PengajuanDashboardController::class, 'approvePpk'])->name('pengajuan.approvePpk');
    Route::post('/pengajuan/{pengajuan}/reject-ppk', [PengajuanDashboardController::class, 'rejectPpk'])->name('pengajuan.rejectPpk');

    // Rute Ekspor
    Route::get('/pengajuan/export-pdf', [PengajuanDashboardController::class, 'exportPDF'])->name('pengajuan.exportPDF');
    Route::get('/pengajuan/export-excel', [PengajuanDashboardController::class, 'exportExcel'])->name('pengajuan.exportExcel');
});


// Grup Rute Khusus untuk role 'npwp'
Route::middleware(['auth', 'is.npwp'])->prefix('npwp')->name('npwp.')->group(function () {
    Route::get('/dashboard', [NpwpDashboardController::class, 'index'])->name('dashboard');

    // GANTI NAMA RUTE LAMA DENGAN NAMA YANG LEBIH SPESIFIK INI
    Route::post('/pengajuan/{pengajuan}/approve-npwp', [NpwpDashboardController::class, 'approveNpwp'])->name('pengajuan.approveNpwp');
    Route::post('/pengajuan/{pengajuan}/reject-npwp', [NpwpDashboardController::class, 'rejectNpwp'])->name('pengajuan.rejectNpwp');
});


require __DIR__.'/auth.php';